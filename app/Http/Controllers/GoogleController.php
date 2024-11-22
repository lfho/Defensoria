<?php

namespace App\Http\Controllers;

use App\IntegracionGoogle\Google;
use DateInterval;
use Hamcrest\Arrays\IsArray;
use Illuminate\Support\Facades\Storage;
use Modules\Correspondence\Models\InternalSignHistory;

use Modules\Correspondence\Models\InternalRead;

class GoogleController extends Google

{
    public function __construct()
    {
        // Se instancia el cliente de la API de Google
        $this->client = $this->client();
        // Se instancia el servicio Drive.
        $this->drive = $this->drive($this->client);
        // Se instancia el servicio Drive File.
        $this->drive_file = $this->driveFile($this->client);
        // Se instancia el servicio Google_Service_Sheets
        $this->sheets = $this->sheets($this->client);
        // Se instancia el servicio Google_Service_Sheets_ValueRange
        $this->sheets_value_range = $this->sheets_value_range($this->client);
        // Se instancia el servicio Google_Service_Sheets_ValueRange
        $this->drive_permission = $this->drive_permission($this->client);

        // Se instancia el servicio Google_Service_Docs
        $this->docs_cons = $this->docs($this->client);

        // Se instancia el servicio Google_Service_YouTube
        $this->youtube = $this->youtube($this->client);
    }

    /**
     * Establece valores en un rango de una hoja de cálculo,
     * especificando el ID de la hoja de cálculo, el rango y los valores a reemplazar.
     *
     * @param string $fileID El ID de la hoja del documento a actualizar.
     * @param string $range La notación A1 de los valores a actualizar. Ej: Prueba!A28, nombre hoja = prueba, celda = A28
     * @param array $valuesToReplace Matriz de valores para reemplazar en el rango (range).
     * @param boolean $copyTemplate Valida si se debe de hacer una copia al documento en Google Drive.
     * @return $fileID ID del documento original (plantilla) o de copia de Google Drive
     */
    public function editFileExcel($fileID, $range, $valuesToReplace, $copyTemplate = false)
    {
        if($copyTemplate) {
            $createdFile = $this->drive->files->copy($fileID, $this->drive_file);

            $fileID = $createdFile->id;
        }
        // Objeto - Rango de valores
        $ValueRange = $this->sheets_value_range;
        // Configuración de los datos de reemplazo
        $ValueRange->setValues($valuesToReplace);
        // Especificamos en las opciones para procesar los datos del usuario
        $options = ['valueInputOption' => 'USER_ENTERED'];
        // We make a request indicating in the second parameter the name of the sheet and the starting cell to fill
        $this->sheets->spreadsheets_values->update($fileID, $range, $ValueRange, $options);
        // El ID del documento original (plantilla) o de copia de Google Drive
        return $fileID;
    }

    /**
     * Establece valores en un rango de una hoja de cálculo,
     * especificando el ID de la hoja de cálculo, el rango y los valores a reemplazar.
     *
     * @param string $fileID El ID de la hoja del documento a actualizar.
     * @param array $valuesToReplace Matriz de valores para reemplazar en la propiedad data.
     * @param boolean $copyTemplate Valida si se debe de hacer una copia al documento en Google Drive.
     * @return $fileID ID del documento original (plantilla) o de copia de Google Drive
     */
    public function editFileExcelBatch($fileID, $valuesToReplace, $copyTemplate = false)
    {
        if($copyTemplate) {
            $createdFile = $this->drive->files->copy($fileID, $this->drive_file);

            $fileID = $createdFile->id;
        }

        // Additional ranges to update ...
        $body = $this->sheets_batch_update_values_request([
            'valueInputOption' => 'USER_ENTERED',
            'data' => $valuesToReplace
        ]);

        $this->sheets->spreadsheets_values->batchUpdate($fileID, $body);
        // El ID del documento original (plantilla) o de copia de Google Drive
        return $fileID;
    }

    /**
     * Descarga un archivo del drive, enviando el id del documento. Tenga en cuenta que el contenido exportado está limitado a 10 MB.
     * MIME types Google Drive API: https://developers.google.com/drive/api/guides/ref-export-formats
     *
     * @param string $fileID ID del documento en el Drive.
     * @param string $formato_descarga Formato con el que se va a descargar el documento en el equipo.
     * @param string $fileName Nombre del archivo a descargar
     * @param boolean $deleteFile Valida si se debe eliminar el documento de Google Drive, luego de descargarlo
     * @return void
     */
    public function downloadFileGoogleDrive($fileID, $fileName = "", $formato_descarga = "", $deleteFile = false)
    {
        // MIME types según el archivo de Google Drive a descargar
        $mime_types= array(
            // xlsx
            "application/vnd.google-apps.spreadsheet" => [
                "mimetype" => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                "extension" => 'xlsx'
            ],
            // docx
            "application/vnd.google-apps.document" => [
                "mimetype" => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                "extension" => 'docx'
            ]
        );

        // MIME types según el formato a descargar
        $mime_types_downloads= array(
            "pdf"=> ["mimetype" => 'application/pdf', "extension" => 'pdf'],
            "word"=> ["mimetype" => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', "extension" => 'docx'],
            "excel"=> ["mimetype" => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', "extension" => 'xlsx'],
        );

        // Se obtienen las propiedades del archivo del Drive, según el ID recibido por parámetro
        $file = $this->drive->files->get($fileID);
        //dd($formato_descarga ? $mime_types_downloads[$formato_descarga]["mimetype"] : $mime_types[$file->mimeType]["mimetype"], $formato_descarga ? $mime_types_downloads[$formato_descarga]["extension"] : $mime_types[$file->mimeType]["extension"]);
        // Obtiene propiedades del archivo con el ID recibido por parámetro y según el tipo de archivo (mimetype)
        $response = $this->drive->files->export($fileID, $formato_descarga ? $mime_types_downloads[$formato_descarga]["mimetype"] : $mime_types[$file->mimeType]["mimetype"], array('alt' => 'media' ));
        // Se obtiene el contenido del archivo exportado anteriormente
        $content = $response->getBody()->getContents();
        // Valida si se debe eliminar el documento de Google Drive
        if($deleteFile) {
            // Elimina el documento del $fileID recibido por parámetro
            $this->drive->files->delete($fileID);
        }
        // Limpiar el búfer de salida y desactivar el búfer de salida
        if (ob_get_contents()) ob_end_clean();
        // Encabezados para la descarga del archivo
        header('Content-Type: '.($formato_descarga ? $mime_types_downloads[$formato_descarga]["mimetype"] : $mime_types[$file->mimeType]["mimetype"]));
        header('Content-Length: '.strlen( $content ));
        // Si no se recibe el nombre del archivo por parámetro, se le asigna la fecha y hora actual al nombre del archivo
        header('Content-disposition: inline; filename="'.($fileName ? $fileName : date("YmdHis")).'.'.($formato_descarga ? $mime_types_downloads[$formato_descarga]["extension"] : $mime_types[$file->mimeType]["extension"]).'"');

        // Se descarga el archivo creado anteriormente
        echo $content;

    }


    /**
     *
     * MIME types Google Drive API: https://developers.google.com/drive/api/guides/ref-export-formats
     *
     * @param string $fileID ID del documento en el Drive.
     */
    public function deleteFileGoogleDrive($fileID)
    {

        $this->drive->files->delete($fileID);

    }


    /**
     * Descarga un archivo del drive, enviando el id del documento. Tenga en cuenta que el contenido exportado está limitado a 10 MB.
     * MIME types Google Drive API: https://developers.google.com/drive/api/guides/ref-export-formats
     *
     * @param string $fileID ID del documento en el Drive.
     * @param string $formato_descarga Formato con el que se va a descargar el documento en el equipo.
     * @param string $fileName Nombre del archivo a descargar
     * @param string $ruta_guardado Ruta donde se va a guardar el archivo
     * @param boolean $deleteFile Valida si se debe eliminar el documento de Google Drive, luego de descargarlo
     * @return string $ruta_documento ruta del documento almacenado
     */
    public function saveFileGoogleDrive($fileID, $formato_descarga = "", $fileName = "", $ruta_guardado = "" , $deleteFile = false)
    {
        // MIME types según el archivo de Google Drive a descargar
        $mime_types= array(
            // xlsx
            "application/vnd.google-apps.spreadsheet" => [
                "mimetype" => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                "extension" => 'xlsx'
            ],
            // docx
            "application/vnd.google-apps.document" => [
                "mimetype" => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                "extension" => 'docx'
            ],
            // pptx
            "application/vnd.google-apps.presentation" => [
                "mimetype" => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                "extension" => 'pptx'
            ]
        );

        // MIME types según el formato a descargar
        $mime_types_downloads= array(
            "pdf"=> ["mimetype" => 'application/pdf', "extension" => 'pdf'],
            "word"=> ["mimetype" => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', "extension" => 'docx'],
            "excel"=> ["mimetype" => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', "extension" => 'xlsx'],
            "powerpoint" => ["mimetype" => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', "extension" => 'pptx']
        );

        // Se obtienen las propiedades del archivo del Drive, según el ID recibido por parámetro
        $file = $this->drive->files->get($fileID);
        //dd($formato_descarga ? $mime_types_downloads[$formato_descarga]["mimetype"] : $mime_types[$file->mimeType]["mimetype"], $formato_descarga ? $mime_types_downloads[$formato_descarga]["extension"] : $mime_types[$file->mimeType]["extension"]);
        // Obtiene propiedades del archivo con el ID recibido por parámetro y según el tipo de archivo (mimetype)
        $response = $this->drive->files->export($fileID, $formato_descarga ? $mime_types_downloads[$formato_descarga]["mimetype"] : $mime_types[$file->mimeType]["mimetype"], array('alt' => 'media' ));
        // Se obtiene el contenido del archivo exportado anteriormente
        $content = $response->getBody()->getContents();
        // Valida si se debe eliminar el documento de Google Drive
        if($deleteFile) {
            // Elimina el documento del $fileID recibido por parámetro
            $this->drive->files->delete($fileID);
        }
        // Ruta del documento a aguardar
        $ruta_documento = $ruta_guardado."/".($fileName ? $fileName : date("YmdHis")).'.'.($formato_descarga ? $mime_types_downloads[$formato_descarga]["extension"] : $mime_types[$file->mimeType]["extension"]);
        // Se obtiene la información del documento de google y se guarda en el servidor local
        Storage::disk('public')->put($ruta_documento, $content);

        return $ruta_documento;
    }

     /**
     * Establece valores en un rango de una hoja de cálculo,
     * especificando el ID de la hoja de cálculo, el rango y los valores a reemplazar.
     *
     * @param string $fileID El ID de la hoja del documento a actualizar.
     * @param string $range La notación A1 de los valores a actualizar. Ej: Prueba!A28, nombre hoja = prueba, celda = A28
     * @param array $valuesToReplace Matriz de valores para reemplazar en el rango (range).
     * @param boolean $copyTemplate Valida si se debe de hacer una copia al documento en Google Drive.
     * @return $fileID ID del documento original (plantilla) o de copia de Google Drive
     */
    public function editFileDoc($tipoRegistro = '',$idRegistro = 0,$fileID, $templateVariables, $insertInformation, $sumPositionIndex = null, $copyTemplate = false)
    {

        if($copyTemplate) {
            $createdFile = $this->drive->files->copy($fileID, $this->drive_file);

            $fileID = $createdFile->id;
        }
        // Arreglo para guardar las variable que hay en el documento
        $array_variables_documento = [];

        $service = $this -> docs_cons;

        $documentId = $fileID;  //set your document ID here, eg. "j4i1m57GDYthXKqlGce9WKs4tpiFvzl1FXKmNRsTAAlH"

        $errorEcontrado = "";

        try {
            $doc = $service->documents->get($documentId);
        } catch (Exception $e) {

            $error = json_decode($e->getMessage());
            $errorEcontrado = $error->error->code;
            dd("ERROR ENCONTRADO", $errorEcontrado);
        }


        // Collect all pieces of text (see https://developers.google.com/docs/api/concepts/structure to understand the structure)
        $allText = [];
        $contenidoFirma = "";
        $occurrences = [];
        $contenidoTotal = "";


        if(isset($doc->documentStyle->defaultHeaderId)) {

            foreach ($doc->headers[$doc->documentStyle->defaultHeaderId]->content as $structuralElement) {


                if ($structuralElement->table) {

                    foreach ($structuralElement->table->tableRows as $tableRows) {
                        foreach ($tableRows->tableCells as $tableCells) {
                            foreach ($tableCells->content as $content) {
                                foreach ($content->paragraph ? $content->paragraph->elements:[] as $element) {
                                    if ($element->textRun) {

                                        if (strpos($element->textRun->content, "#firmas") !== false) {
                                            $posicionFirma = $element->startIndex + ($sumPositionIndex ? $sumPositionIndex: 0);
                                            // $posicionFirma = $element->startIndex;
                                            $contenidoFirma = $element->textRun->content;
                                        } else {
                                            $allText[] = $element->textRun->content;
                                        }


                                    }
                                }
                            }
                        }
                    }
                }
                if ($structuralElement->paragraph) {
                    foreach ($structuralElement->paragraph->elements as $paragraphElement) {
                        if ($paragraphElement->textRun) {
                            $allText[] = $paragraphElement->textRun->content;
                        }
                    }
                }
            }

        }

        if(isset($doc->documentStyle->defaultFooterId)) {

            foreach ($doc->footers[$doc->documentStyle->defaultFooterId]->content as $structuralElement) {
                if ($structuralElement->paragraph) {
                    foreach ($structuralElement->paragraph->elements as $paragraphElement) {
                        if ($paragraphElement->textRun) {
                            $allText[] = $paragraphElement->textRun->content;
                        }
                    }
                }
            }

        }

        foreach ($doc->body->content as $structuralElement) {

            if ($structuralElement->table) {

                foreach ($structuralElement->table->tableRows as $tableRows) {
                    foreach ($tableRows->tableCells as $tableCells) {
                        foreach ($tableCells->content as $content) {
                            foreach ($content->paragraph ? $content->paragraph->elements:[] as $element) {
                                if ($element->textRun) {

                                    if (strpos($element->textRun->content, "#firmas") !== false) {
                                        $posicionFirma = $element->startIndex + ($sumPositionIndex ? $sumPositionIndex: 0);
                                        // $posicionFirma = $element->startIndex;
                                        $contenidoFirma = $element->textRun->content;
                                    } else {
                                        $allText[] = $element->textRun->content;
                                    }


                                }
                            }
                        }
                    }
                }
            }

            if ($structuralElement->paragraph) {
                foreach ($structuralElement->paragraph->elements as $paragraphElement) {
                    if ($paragraphElement->textRun) {

                        if (strpos($paragraphElement->textRun->content, "#firmas") !== false) {
                            $posicionFirma = $paragraphElement->startIndex + ($sumPositionIndex ? $sumPositionIndex: 0);
                            $contenidoFirma = $paragraphElement->textRun->content;
                        }

                        else {
                            $allText[] = $paragraphElement->textRun->content;
                        }

                    }
                }
            }
        }

        // Se inserta en la última posición del array el contenido de la firma
        $allText[] = $contenidoFirma;

        // $datos_variables =  mysql_fetch_assoc($resultado);
        $datos_variables =  $insertInformation;

        // Go through and create search/replace requests
        $requests = $textsAlreadyDone = [];
        $allText = array_reverse($allText);

        foreach ($allText as $currText) {

            $modifiedText = $currText;


            if (in_array($currText, $textsAlreadyDone, true)) {
                // If two identical pieces of text are found only search-and-replace it once - no reason to do it multiple times
                continue;
            }


            //echo $contador."<br>";
            $consecutivo_name_pdf = "";
            $encontroVariables = false;
            $variables_encontrada = "";
            $variables_encontrada_logo = "";
            $rutaFirma = "";
            $rutaLogo = "";

            foreach ($datos_variables as $clave => $valor) {

                if ($clave == "#N" ) {
                    $consecutivo_name_pdf = $valor;
                }
                if (preg_match_all("/(.*?)(" . $clave . ")(.*?)/", $modifiedText, $matches, PREG_SET_ORDER)) {

                	// Encontró al menos una variable que reemplazar en el documento
                    $encontroVariables = true;
                    // Se guarda la variable que encotró en el documento, para posteriormente validar el total de variables
                    $array_variables_documento[] = $clave;

                    // echo json_encode($matches);
                    //NOTE: for simple static text searching you could of course just use strpos()
                    // - and then loop on $matches wouldn't be necessary, and str_replace() would be simplified
                    if ($clave != "#firmas") {
                        if($clave == "#logo"){

                            $variables_encontrada_logo = $clave;
                            $rutaLogo = $valor;
                        } else {
                            foreach ($matches as $match) {
                                $modifiedText = str_replace($match[0], $match[1] . $valor . $match[3], $modifiedText);
                            }
                        }
                     }else{
                            $variables_encontrada = $clave;
                            $rutaFirma = $valor;
                    }
                }

            }


            if($encontroVariables){

                if ($variables_encontrada == "#firmas") {
                    $datosFirmado = $rutaFirma;

                    // count($datosFirmado) > 0 &&
                    if ($datosFirmado!="Espacio para firmas") {
                        // if(is_array($datosFirmado)){
                            // dd($datosFirmado);
                            $datosFirmado = (array_filter($datosFirmado));

                            $firmas = $datosFirmado;



                            $prom = max(1, round(count($firmas) / 2));
                            $column = count($firmas) > 1 ? 2 : 1;

                            $replaceAllTextRequest = [
                                'insertTable' => [
                                    'location' => ['index' => $posicionFirma],
                                    'columns' => $column,
                                    'rows' => $prom
                                ]
                            ];
                            $requests[] = $this->docs_request($replaceAllTextRequest);

                            $style = [
                                'width' => ['magnitude' => 0, 'unit' => 'PT'],
                                'dashStyle' => 'SOLID',
                                'color' => ['color' => ['rgbColor' => ['blue' => 1, 'green' => 1, 'red' => 1]]]
                            ];

                            $replaceAllTextRequest = [
                                'updateTableCellStyle' => [
                                    'tableCellStyle' => [
                                        'borderBottom' => $style,
                                        'borderLeft' => $style,
                                        'borderRight' => $style,
                                        'borderTop' => $style,
                                        'contentAlignment' => 'bottom',
                                    ],


                                    'tableStartLocation' => ['index' => $posicionFirma + 1],
                                    'fields' => '*'
                                ]
                            ];
                            $requests[] = $this->docs_request($replaceAllTextRequest);



                            $contador = 1;
                            $requestFirmas = array();
                            foreach ($firmas as $key => $value) {
                                // dd($value->users);

                                //para pruebas
                                if(env('APP_ENV') == "local"){
                                    $value->users->url_digital_signature = "https://www.defensoria.gov.co/o/defensoria-del-pueblo-theme/html/assets/img/home/logo_DPC_home_favicon.png";
                                }
                                // dd($value->users);

                                if ($contador==1) {
                                    $posicionFirma = $posicionFirma+4;
                                    // echo "<br>1. ".$posicionFirma. " - ".$contador;

                                }else if(($contador % 2)==0){
                                    //para pruebas

                                    if(env('APP_ENV') == "local"){
                                        $value->users->url_digital_signature =  "https://www.defensoria.gov.co/o/defensoria-del-pueblo-theme/html/assets/img/home/logo_DPC_home_favicon.png";
                                    }


                                    $posicionFirma = $posicionFirma+2;
                                    // echo "<br>par . ".$posicionFirma. " - ".$contador;


                                }else{
                                    $posicionFirma = $posicionFirma+3;
                                    // echo "<br>impar. ".$posicionFirma. " - ".$contador;

                                }



                                $replaceAllTextRequestName = [

                                    'insertText' => array(
                                        'text' => "\n".$value->users->name,
                                        'location' => array(
                                            'index' => $posicionFirma+1
                                        )
                                        )
                                ];
                                $requestFirmas[] = $this->docs_request($replaceAllTextRequestName);


                                $replaceAllTextRequestName = [

                                    'insertText' => array(
                                        'text' => "\n".(isset($value->users->hash) ? $value->users->hash : ''),
                                        'location' => array(
                                            'index' => $posicionFirma+1
                                        )
                                        )
                                ];
                                $requestFirmas[] = $this->docs_request($replaceAllTextRequestName);




                                if(!$value->users->url_digital_signature) {
                                    return $this->sendResponse([], "El usuario ".$value->users->name." no tiene una firma habilitada, debe adjuntar una para continuar con la publicación del documento.",'info');
                                }

                                $replaceAllTextRequest = [
                                    'insertInlineImage' => array(

                                        'uri' => (env('APP_ENV') == "local" ? $value->users->url_digital_signature : config('app.url')."/storage/".$value->users->url_digital_signature),

                                        'location' => array(
                                            'index' => $posicionFirma,
                                        ),
                                        'objectSize' => array(

                                            'width' => array(
                                                'magnitude' => 110,
                                                'unit' => 'PT',
                                            ),
                                        )
                                    )
                                ];
                                $requestFirmas[] = $this->docs_request($replaceAllTextRequest);


                                $contadorLetras = strlen($value->users->name);

                                $contador++;
                            }


                            $requestFirmas = array_reverse($requestFirmas);
                            $requests = array_merge($requests,$requestFirmas);

                                $replaceAllTextRequest = [
                                    'replaceAllText' => [
                                        'replaceText' => "",
                                        'containsText' => [
                                            'text' => "#firmas",
                                            'matchCase' => true,
                                        ],
                                    ],
                                ];

                            $requests[] = $this->docs_request($replaceAllTextRequest);


                    }else{

                        $replaceAllTextRequest = [
                            'replaceAllText' => [
                                'replaceText' => "firmas",
                                'containsText' => [
                                    'text' => "#firmas",
                                    'matchCase' => true,
                                ],
                            ],
                        ];

                        $requests[] = $this->docs_request($replaceAllTextRequest);

                    }
                } else if($variables_encontrada_logo == "#logo"){
                    $variables_encontrada_logo = "";
                    $varLogo = $rutaLogo;
                    $posicionLogo = -2;
                        $varLogo = "https://www.defensoria.gov.co/o/defensoria-del-pueblo-theme/html/assets/img/home/logo_DPC_home_favicon.png";
                        $requestLogo = array();
                        $replaceAllTextRequest =  [
                                'insertInlineImage' => [
                                    'uri' => (env('APP_ENV') == "local" ? $varLogo : config('app.url')."/storage/".$rutaLogo),
                                    'location' => [
                                        'segmentId' => $doc->documentStyle->defaultHeaderId, // Especifica el ID del encabezado
                                        'index' => 0, // Índice donde quieres insertar la imagen en el encabezado
                                    ],
                                    'objectSize' => [
                                        'height' => [
                                            'magnitude' => 100,
                                            'unit' => 'PT',
                                        ],
                                        'width' => [
                                            'magnitude' => 300,
                                            'unit' => 'PT',
                                        ],
                                    ],
                                ],
                        ];

                        $requestLogo[] = $this->docs_request($replaceAllTextRequest);

                        $replaceAllTextRequest = [
                            'replaceAllText' => [
                                'replaceText' => "",
                                'containsText' => [
                                    'text' => "#logo",
                                    'matchCase' => true,
                                ],
                            ],
                        ];

                    $requestLogo = array_reverse($requestLogo);
                    $requests = array_merge($requests,$requestLogo);
                    $requests[] = $this->docs_request($replaceAllTextRequest);
                } else {

                    $replaceAllTextRequest = [
                        'replaceAllText' => [
                            'replaceText' => $modifiedText,
                            'containsText' => [
                                'text' => $currText,
                                'matchCase' => false,
                            ],
                        ],
                    ];
                }



                $requests[] = $this->docs_request($replaceAllTextRequest);

            }

            $textsAlreadyDone[] = $currText;
        }

        $resultado = array_diff($templateVariables, $array_variables_documento);
        // Condición para validar si hubo alguna excepción publicando un documento
        if ($errorEcontrado) {
            return $this->sendResponse([], "Excepción. ".$errorEcontrado,'info');

        }

        // Documento no encontrado
        if ($errorEcontrado == 404) {
            return $this->sendResponse([], "Documento no encontrado. 404",'info');
        // Posiblemente credenciales inválidas o alguna otra excepción encontrada
        } else if ($errorEcontrado) {
            return $this->sendResponse([], "Posiblemente credenciales inválidas o alguna otra excepción encontrada.",'info');

            // Request authorization from the user.
            // $authUrl = $this -> client()->createAuthUrl();
            // header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        } else if (count($resultado) > 0) {

            $variablesFaltantes= implode(", ", $resultado);

            return $this->sendResponse([], "Faltan variables: ".$variablesFaltantes,'info');
        } else {
            //si todo sale bien
            if (count($requests) > 0) {
                // you could dump out to see the changes that would be made
                $batchUpdateRequest = $this->docs_batch_update_document_request(['requests' => $requests]);
                $upd = $this->docs_cons->documents->batchUpdate($documentId, $batchUpdateRequest);
                return $this->sendResponse(['id' => $fileID], trans('msg_success_update'),'success');

                // $content = $this->drive->files->export($documentId, 'application/pdf', array('alt' => 'media'));
                // // Se obtiene el contenido del archivo exportado anteriormente
                // $content = $content->getBody()->getContents();
                // // Limpiar el búfer de salida y desactivar el búfer de salida
                // ob_end_clean();
                // // Encabezados para la descarga del archivo
                // header('Content-Type: application/pdf');
                // header('Content-Length: '.strlen( $content ));
                // // Si no se recibe el nombre del archivo por parámetro, se le asigna la fecha y hora actual al nombre del archivo
                // header('Content-disposition: inline; filename="prueba.pdf"');
                // // Se descarga el archivo creado anteriormente
                // echo $content;

                // header('Location:' . filter_var($url_site."index.php?option=com_formasonline&tmpl=index&formasonlineform=Externa_gas_crear&event=relation_doc_drive&ruta_pdf=" . $consecutivo_name_pdf . "&id_data=" . $_SESSION["id_data"], FILTER_SANITIZE_URL));
            } else if(count($templateVariables) == 0) { // Si se cumple esta condición quiere decir que no hizo ningún reemplazo de variables
                // Retorna el documento tal cual esta en la nube sin nigún reemplazo
                return $this->sendResponse(['id' => $fileID], trans('msg_success_update'),'success');
            } else {
                return $this->sendResponse([], "Error en el reemplazo de variables",'info');
            }
        }
    }


    /**
     *  Get the list of files or folders or both from given folder or root
     *  @param string $search complete or partial name of file or folder to search
     *  @param string $parentId parent folder id or root from which the list of
     *  @param string $type='all' file or folder
     *  @return array list of files or folders or both from given parent directory
     */
    function listFilesFolders($search, $parentId, $type = 'all', $service)
    {
        $query = '';
        // Checking if search is empty the use 'contains' condition if search is empty (to get all files or folders).
        // Otherwise use '='  condition
        $condition = $search != '' ? '=' : 'contains';

        // Search all files and folders otherwise search in root or  any folder
        $query .= $parentId != 'all' ? "'" . $parentId . "' in parents" : "";

        // Check if want to search files or folders or both
        switch ($type) {
            case "files":
                $query .= $query != '' ? ' and ' : '';
                $query .= "mimeType != 'application/vnd.google-apps.folder'
                                and name " . $condition . " '" . $search . "'";
                break;

            case "folders":
                $query .= $query != '' ? ' and ' : '';
                $query .= "mimeType = 'application/vnd.google-apps.folder' and name contains '" . $search . "'";
                break;
            default:
                $query .= "";
                break;
        }

        // Make sure that not list trashed files
        $query .= $query != '' ? ' and trashed = false' : 'trashed = false';
        $optParams = array('q' => $query, 'pageSize' => 1000);

        try {
            // Returns the list of files and folders as object
            $results = $service->files->listFiles($optParams);
        } catch (\Exception $e) {
            $errorCreandoDoc = json_decode($e->getMessage());
            $errorEcontradoCreandoDoc = $errorCreandoDoc->error->code;
            //poner validacion
            return $errorEcontradoCreandoDoc;
        }


        // Return false if nothing is found
        if (count($results->getFiles()) == 0) {
            return array();
        }

        // Converting array to object
        $result = "";
        foreach ($results->getFiles() as $file) {
            $result = $file->getId();
        }
        return $result;
    }

    /**
     * Crear un documento de google a partir de una plantilla cargada
     */
    public function crearDocumentoGoogleDrive($nombreArchivo, $plantilla, $carpetaDrive, $emailsAddress = []) {

        // MIME types según el archivo de Google Drive a descargar
        $mime_types= array(
            "xls" =>'application/vnd.ms-excel',
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => [
                "mimetype" => 'application/vnd.google-apps.spreadsheet',
                "extension" => 'xlsx'
            ],
            "xml" =>'text/xml',
            "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
            "csv"=>'text/plain',
            "tmpl"=>'text/plain',
            'application/pdf' => ["mimetype" => 'application/pdf', "extension" => 'pdf'],
            "php"=>'application/x-httpd-php',
            "jpg"=>'image/jpeg',
            "png"=>'image/png',
            "gif"=>'image/gif',
            "bmp"=>'image/bmp',
            "txt"=>'text/plain',
            "doc"=>'application/msword',
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => [
                "mimetype" => 'application/vnd.google-apps.document',
                "extension" => 'docx'
            ],
            "js"=>'text/js',
            "swf"=>'application/x-shockwave-flash',
            "mp3"=>'audio/mpeg',
            "zip"=>'application/zip',
            "rar"=>'application/rar',
            "tar"=>'application/tar',
            "arj"=>'application/arj',
            "cab"=>'application/cab',
            "html"=>'text/html',
            "htm"=>'text/html',
            "default"=>'application/octet-stream',
            "folder"=>'application/vnd.google-apps.folder'
        );

        $resultado = $this->listFilesFolders($carpetaDrive, "root", "folders", $this->drive);

        if (!$resultado) {

            $fileMetadata = $this->driveFile(array(
                'name' => $carpetaDrive,
                'mimeType' => 'application/vnd.google-apps.folder'
            ));

            // Creating Folder with given Matadata and asking for ID field as result
            $file_folder = $this->drive->files->create($fileMetadata, array('fields' => 'id'));
            $resultado = $file_folder->id;

        // Documento no encontrado
        } else if($resultado == 404) {
            return json_encode(["TYPE_ERROR"=>"ERROR_INDRIVE","MESSAGE"=>"El documento fue eliminado del drive."]);

            exit;

        // Credenciales inválidas
        } else if($resultado == 401) {
            // Request authorization from the user.
            // $authUrl = $this->client->createAuthUrl();
            // header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            return json_encode(["TYPE_ERROR"=>"ERROR_CREDENTIALES","MESSAGE"=>"Credenciales de la API inválidas o vencieron."]);
            exit;
        }


        // First, check if the file exists
        if (file_exists($plantilla))
        {
            // If it exists, get the MIME type
            $extensionArchivoLocal = mime_content_type($plantilla);
        } else {
            // Handle the case where the file does not exist
            return json_encode(["TYPE_ERROR"=>"ERROR_TEMPLATE","MESSAGE"=>"Problemas con la plantilla, no existe o no se encuentra."]);
            exit;
        }

        $fileMetadata = $this->driveFile(array(
            'name' => $nombreArchivo .".". $mime_types[$extensionArchivoLocal]["extension"],
            'parents' => array($resultado),
            'mimeType' => $mime_types[$extensionArchivoLocal]["mimetype"]
        ));

        $content = file_get_contents($plantilla);

        $file = $this->drive->files->create($fileMetadata, array(
            'data' => $content,
            'uploadType' => 'multipart'
        ));

        $id_doc_drive =  $file->getId();

        if(count($emailsAddress)) {
            $newPermission = $this->drive_permission;
            $this->drive_permission->setEmailAddress($emailsAddress);

            $newPermission->setType("user");
            $newPermission->setRole("writer");
        } else {
            $newPermission = $this->drive_permission;

            $newPermission->setType("anyone");
            $newPermission->setRole("writer");
        }

        try {
            $this->drive->permissions->create($id_doc_drive, $newPermission, array(
                'sendNotificationEmail'=>false,
            ));
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

        return $id_doc_drive;
    }

    /**
     * Crear un documento de google en blanco
     *
     * @author Seven Soluciones Informáticas S.A.S. - Mar. 04 - 2024
     * @version 1.0.0
     *
     * @param string $nombreArchivo
     * @param string $tipoArchivo
     * @param string $carpetaDrive
     * @param Array $emailsAddress
     *
     * @return string $id_doc_drive
     */
    public function crearDocumentoEnBlancoGoogleDrive($nombreArchivo, $tipoArchivo, $carpetaDrive, $emailsAddress = []) {

        // MIME types según el archivo de Google Drive a crear
        $mime_types = array(
            "xls" =>'application/vnd.ms-excel',
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => [
                "mimetype" => 'application/vnd.google-apps.spreadsheet',
                "extension" => 'xlsx'
            ],
            "xml" =>'text/xml',
            "ods"=>'application/vnd.oasis.opendocument.spreadsheet',
            "csv"=>'text/plain',
            "tmpl"=>'text/plain',
            'application/pdf' => ["mimetype" => 'application/pdf', "extension" => 'pdf'],
            "php"=>'application/x-httpd-php',
            "jpg"=>'image/jpeg',
            "png"=>'image/png',
            "gif"=>'image/gif',
            "bmp"=>'image/bmp',
            "txt"=>'text/plain',
            "doc"=>'application/msword',
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => [
                "mimetype" => 'application/vnd.google-apps.document',
                "extension" => 'docx'
            ],
            "js"=>'text/js',
            "swf"=>'application/x-shockwave-flash',
            "mp3"=>'audio/mpeg',
            "zip"=>'application/zip',
            "rar"=>'application/rar',
            "tar"=>'application/tar',
            "arj"=>'application/arj',
            "cab"=>'application/cab',
            "html"=>'text/html',
            "htm"=>'text/html',
            "default"=>'application/octet-stream',
            "folder"=>'application/vnd.google-apps.folder',
            "word" => [
                "mimetype" => 'application/vnd.google-apps.document',
                "extension" => 'docx'
            ], // Formato ingresado manualmente por el usuario
            "excel" => [
                "mimetype" => 'application/vnd.google-apps.spreadsheet',
                "extension" => 'xlsx'
            ] // Formato ingresado manualmente por el usuario
        );

        $resultado = $this->listFilesFolders($carpetaDrive, "root", "folders", $this->drive);

        if (!$resultado) {

            $fileMetadata = $this->driveFile(array(
                'name' => $carpetaDrive,
                'mimeType' => 'application/vnd.google-apps.folder'
            ));

            // Creating Folder with given Matadata and asking for ID field as result
            $file_folder = $this->drive->files->create($fileMetadata, array('fields' => 'id'));
            $resultado = $file_folder->id;

        // Documento no encontrado
        } else if($resultado == 404) {
            return json_encode(["TYPE_ERROR"=>"ERROR_INDRIVE","MESSAGE"=>"El documento fue eliminado del drive."]);

            exit;

        // Credenciales inválidas
        } else if($resultado == 401) {
            // Request authorization from the user.
            // $authUrl = $this->client->createAuthUrl();
            // header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            return json_encode(["TYPE_ERROR"=>"ERROR_CREDENTIALES","MESSAGE"=>"Credenciales de la API inválidas o vencieron."]);
            exit;
        }

        $fileMetadata = $this->driveFile(array(
            'name' => $nombreArchivo .".". $mime_types[$tipoArchivo]["extension"],
            'parents' => array($resultado),
            'mimeType' => $mime_types[$tipoArchivo]["mimetype"]
        ));

        $file = $this->drive->files->create($fileMetadata, array(
            'uploadType' => 'multipart'
        ));

        $id_doc_drive =  $file->getId();

        if(count($emailsAddress)) {
            $newPermission = $this->drive_permission;
            $this->drive_permission->setEmailAddress($emailsAddress);

            $newPermission->setType("user");
            $newPermission->setRole("writer");
        } else {
            $newPermission = $this->drive_permission;

            $newPermission->setType("anyone");
            $newPermission->setRole("writer");
        }

        try {
            $this->drive->permissions->create($id_doc_drive, $newPermission, array(
                'sendNotificationEmail'=>false,
            ));
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }

        return $id_doc_drive;
    }

    /**
     * Obtiene información detallada del video de YouTube según el ID recibido
     *
     * @author Seven Soluciones Informáticas S.A.S. - Mar. 12 - 2024
     * @version 1.0.0
     *
     * @param string $videoId, Identificador del video de YouTube
     *
     * @return string $video, información detallada del video
     */
    public function obtenerDetallesVideoYouTube($videoId) {
        $video = $this->youtube->videos->listVideos("snippet,contentDetails", [
            "id" => $videoId,
        ]);

        return $video->getItems()[0];
    }

    /**
     * Formatea la duración del video de YouTube, recibido por parámetro
     *
     * @author Seven Soluciones Informáticas S.A.S. - Mar. 12 - 2024
     * @version 1.0.0
     *
     * @param string $duracion_video
     *
     * @return string $duracion_video en el formato que aplique según la duración del mismo (00:00:00)
     */
    public function parseVideoDuracion(string $duracion_video): string
    {
        // Parsear la duración
        $interval = new DateInterval($duracion_video);

        // Determinar el formato en base a las horas
        if ($interval->h > 0) {
            $formato_horas = '%H:';
        } else {
            $formato_horas = '';
        }

        // Formatear la duración para que sea legible
        $duracion_formateada = $interval->format($formato_horas . '%I:%S');

        // Retorna la duración del video formateada (HH:mm:ss)
        return $duracion_formateada;
    }
}
