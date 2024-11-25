<?php

namespace Modules\visit\Http\Controllers;

use App\Exports\GenericExport;
use Modules\visit\Http\Requests\CreateCitizenRequest;
use Modules\visit\Http\Requests\UpdateCitizenRequest;
use Modules\visit\Repositories\CitizenRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Auth;
use DB;
use Modules\visit\Models\Citizen;
use Modules\visit\Models\OtrasVictimas;
use Modules\visit\Models\Cuestionario;
use Modules\visit\Models\ConfigurationMail;
use App\Exports\RequestExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;


  

/**
 * Descripcion de la clase
 *
 * @author Desarrollador Seven - 2022
 * @version 1.0.0
 */
class CitizenController extends AppBaseController {

    /** @var  CitizenRepository */
    private $citizenRepository;

    /**
     * Constructor de la clase
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     */
    public function __construct(CitizenRepository $citizenRepo) {
        $this->citizenRepository = $citizenRepo;
    }

    /**
     * Muestra la vista para el CRUD de Citizen.
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request) {

        $email = ConfigurationMail::select('email')
            ->first()
            ->email ?? '';

        return view('visit::citizens.index')->with('email',$email );

    }

    /**
     * Obtiene todos los elementos existentes
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @return Response
     */
    public function all() {
        $citizens = Citizen:: with(['otrasVictimasList', 'cuestionario'])->get();
        return $this->sendResponse($citizens->toArray(), trans('data_obtained_successfully'));
    }

    /**
     * Guarda un nuevo registro al almacenamiento
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param CreateCitizenRequest $request
     *
     * @return Response
     */
    public function store(CreateCitizenRequest $request) {

        $input = $request->all();

        $input['users_id'] = Auth::user()->id;
        $input['nombre_usuario'] = Auth::user()->name;

           
        

        
        // Inicia la transaccion
        DB::beginTransaction();
        try {
            // Inserta el registro en la base de datos
            $citizen = $this->citizenRepository->create($input);

            if ($input['otras_victimas'] == 'Si') {

                foreach ($input['otras_victimas_list'] as $otasVictimas) {
                    $otasVictimas = json_decode($otasVictimas);
        
                    OtrasVictimas::create([
                        "nombres" => $otasVictimas->nombres ?? '',
                        "apellidos" => $citizenotasVictimas->apellidos ?? '',
                        "tipo_documento" => $otasVictimas->tipo_documento ?? '',
                        "numero_documento" => $otasVictimas->numero_documento ?? '',
                        "parentezco" => $otasVictimas->parentezco ?? '',
                        "tipo_victima" => $otasVictimas->otasVictimas ?? '',
                        "ciudadano_id" => $citizen->id ?? '',
                        "nombre_referido" => $citizen->nombres
                    ]);
                }
            }

            Cuestionario::create([
                "ciudadano_id" => $citizen->id,
                "tipo_declaracion" => $input["tipo_declaracion"],
                "hechos" => $input["hechos"],
                "secuelas_generadas" => $input["secuelas_generadas"],
                "lesiones_fisicass" => $input["lesiones_fisicass"],
                "lesiones_psicologicas" => $input["lesiones_psicologicas"],
                "patrimonios_afectados" => $input["patrimonios_afectados"],
                "descripcion" => $input["descripcion"],
                "tiempo_acto" => $input["tiempo_acto"],
                "descripcio_hecho_principal" => $input["descripcio_hecho_principal"]
            ]);


            $subjectText = 'Datos de visita de'.$input['nombres'] . ' '. $input['numero_documento'] ;
            $data = Citizen::with(['otrasVictimasList', 'cuestionario'])->where('id',$citizen->id)->first();
            
            $this->_sendEmails($input["email"], $data,'visit::citizens.layout_mail', $subjectText);
            

            // Efectua los cambios realizados
            DB::commit();

            return $this->sendResponse($citizen->toArray(), trans('msg_success_save'));
        } catch (\Illuminate\Database\QueryException $error) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$error->getMessage());
            // Retorna mensaje de error de base de datos
            return $this->sendSuccess(config('constants.support_message'), 'info');
        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$e->getMessage());
            // Retorna error de tipo logico
            return $this->sendSuccess(config('constants.support_message'), 'info');
        }
    }

    /**
     * Actualiza un registro especifico.
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param int $id
     * @param UpdateCitizenRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCitizenRequest $request) {

        $input = $request->all();

        /** @var Citizen $citizen */
        $citizen = $this->citizenRepository->find($id);

        if (empty($citizen)) {
            return $this->sendError(trans('not_found_element'), 200);
        }
        
        // Inicia la transaccion
        DB::beginTransaction();
        try {
            // Actualiza el registro
            $citizen = $this->citizenRepository->update($input, $id);

            if ($input['otras_victimas'] == 'Si') {

                // Elimina los ciudadanos para poner los nuevos
                OtrasVictimas::where("ciudadano_id", $id)->delete();
                foreach ($input['otras_victimas_list'] as $otasVictimas) {
                    $otasVictimas = json_decode($otasVictimas);
        
                    OtrasVictimas::create([
                        "nombres" => $otasVictimas->nombres ?? '',
                        "apellidos" => $citizenotasVictimas->apellidos ?? '',
                        "tipo_documento" => $otasVictimas->tipo_documento ?? '',
                        "numero_documento" => $otasVictimas->numero_documento ?? '',
                        "parentezco" => $otasVictimas->parentezco ?? '',
                        "tipo_victima" => $otasVictimas->otasVictimas ?? '',
                        "ciudadano_id" => $citizen->id ?? '',
                        "nombre_referido" => $citizen->nombres
                    ]);
                }
            }

            $cuestionario = Cuestionario::where('ciudadano_id',$citizen->id)->first();

            if ($cuestionario) {
                $cuestionario->update([
                    "tipo_declaracion" => $input["tipo_declaracion"],
                    "hechos" => $input["hechos"],
                    "secuelas_generadas" => $input["secuelas_generadas"],
                    "lesiones_fisicass" => $input["lesiones_fisicass"],
                    "lesiones_psicologicas" => $input["lesiones_psicologicas"],
                    "patrimonios_afectados" => $input["patrimonios_afectados"],
                    "descripcion" => $input["descripcion"],
                    "tiempo_acto" => $input["tiempo_acto"],
                    "descripcio_hecho_principal" => $input["descripcio_hecho_principal"]
                ]);
            }

            // Efectua los cambios realizados
            DB::commit();
        
            return $this->sendResponse($citizen->toArray(), trans('msg_success_update'));
        } catch (\Illuminate\Database\QueryException $error) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$error->getMessage());
            // Retorna mensaje de error de base de datos
            return $this->sendSuccess(config('constants.support_message'), 'info');
        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$e->getMessage());
            // Retorna error de tipo logico
            return $this->sendSuccess(config('constants.support_message'), 'info');
        }
    }

    /**
     * Elimina un Citizen del almacenamiento
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id) {

        /** @var Citizen $citizen */
        $citizen = $this->citizenRepository->find($id);

        if (empty($citizen)) {
            return $this->sendError(trans('not_found_element'), 200);
        }

        // Inicia la transaccion
        DB::beginTransaction();
        try {
            // Elimina el registro
            $citizen->delete();

            // Efectua los cambios realizados
            DB::commit();

            return $this->sendSuccess(trans('msg_success_drop'));
        } catch (\Illuminate\Database\QueryException $error) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$error->getMessage());
            // Retorna mensaje de error de base de datos
            return $this->sendSuccess(config('constants.support_message'), 'info');
        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'Modules\visit\Http\Controllers\CitizenController - '. Auth::user()->name.' -  Error: '.$e->getMessage());
            // Retorna error de tipo logico
            return $this->sendSuccess(config('constants.support_message'), 'info');
        }
    }

    /**
     * Organiza la exportacion de datos
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param Request $request datos recibidos
     */
    public function export(Request $request) {
        $input = $request->all();
        // Tipo de archivo (extencion)
        $fileType = $input['fileType'];
        // $fileName = date('Y-m-d H:i:s').'-'.trans('request_authorizations').'.'.$fileType;
        $fileName = 'setting.' . $fileType;
        return Excel::download(new RequestExport('visit::citizens.report_excel', $input['data'], 'g'), $fileName);
    }

    /**
     * Organiza la exportacion de datos
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param Request $request datos recibidos
     */
    public function getConfigurateEmail(Request $request) {
        ConfigurationMail:: create(['email'=>$request->email]);
        return $this->sendSuccess(trans('Correo guardado exitosamente'));
        
    
    }

    private static function _sendEmails(string $mail,object $data,string $locationTemplate,string $subjectText) : void{
        $custom = json_decode('{"subject":"'.$subjectText.'"}');

        Mail::to($mail)->send(new SendMail($locationTemplate, $data, $custom, '1'));
    }

     /**
     * Organiza la exportacion de datos
     *
     * @author Desarrollador Seven - 2022
     * @version 1.0.0
     *
     * @param Request $request datos recibidos
     */
    public function exportIndicators (Request $request) {
        $input = $request->all();

        $inputFileType = 'Xlsx';
        $inputFileName =  dirname(app_path()).'/Modules/Visit/Resources/views/citizens/excel_estadistico.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);
        $spreadsheet->setActiveSheetIndex(0);

        $queryDemograficos = '
        SUM(CASE WHEN genero = "MASCULINO" THEN 1 ELSE 0 END) AS item1,
        SUM(CASE WHEN genero = "FEMENINO" THEN 1 ELSE 0 END) AS item2,
        SUM(CASE WHEN genero = "Intersexual" THEN 1 ELSE 0 END) AS item3,
        SUM(CASE WHEN ciclo_vital = "0-5 años primera infancia" THEN 1 ELSE 0 END) AS item4,
        SUM(CASE WHEN ciclo_vital = "6-11 años niños niñas" THEN 1 ELSE 0 END) AS item5,
        SUM(CASE WHEN ciclo_vital = "12-17 adolescentes" THEN 1 ELSE 0 END) AS item6,
        SUM(CASE WHEN ciclo_vital = "18-28 jóvenes" THEN 1 ELSE 0 END) AS item7,
        SUM(CASE WHEN ciclo_vital = "29-59 adultos" THEN 1 ELSE 0 END) AS item8,
        SUM(CASE WHEN ciclo_vital = "mayor de 60 adulto mayor" THEN 1 ELSE 0 END) AS item9,
        SUM(CASE WHEN etnia = "Adulto Mayor" THEN 1 ELSE 0 END) AS item10,
        SUM(CASE WHEN etnia = "Víctimas del conflicto Armado" THEN 1 ELSE 0 END) AS item11,
        SUM(CASE WHEN etnia = "Población sexualmente diversa" THEN 1 ELSE 0 END) AS item12,
        SUM(CASE WHEN etnia = "Niños, Niñas y Adolescentes" THEN 1 ELSE 0 END) AS item13,
        SUM(CASE WHEN etnia = "Indígenas" THEN 1 ELSE 0 END) AS item14,
        SUM(CASE WHEN etnia = "Afrodescendiente" THEN 1 ELSE 0 END) AS item15,
        SUM(CASE WHEN etnia = "Rom" THEN 1 ELSE 0 END) AS item16,
        SUM(CASE WHEN etnia = "Raizal" THEN 1 ELSE 0 END) AS item17,
        SUM(CASE WHEN etnia = "Palanquero" THEN 1 ELSE 0 END) AS item18,
        SUM(CASE WHEN etnia = "Migrantes Venezolanos" THEN 1 ELSE 0 END) AS item19,
        SUM(CASE WHEN etnia = "Migrantes" THEN 1 ELSE 0 END) AS item20,
        SUM(CASE WHEN etnia = "Discapacitado" THEN 1 ELSE 0 END) AS item21,
        SUM(CASE WHEN etnia = "Mujer cabeza de familia" THEN 1 ELSE 0 END) AS item22,
        SUM(CASE WHEN etnia = "Persona en estado de gestación" THEN 1 ELSE 0 END) AS item23,
        SUM(CASE WHEN etnia = "Persona con Discapacidad" THEN 1 ELSE 0 END) AS item24,
        SUM(CASE WHEN etnia = "Colombianos Retornados" THEN 1 ELSE 0 END) AS item25,
    SUM(CASE WHEN etnia = "Ninguno" THEN 1 ELSE 0 END) AS item26 ';

    // consulta con los datos demográficos 
    $resultados = Citizen::selectRaw($queryDemograficos)->get();
      
    // -----inicio de asignación de datos en la hoja 1-------
    $columna = 'D';
    $inicio = 9;

    for ($i = 1; $i <= 26; $i++) {
        $itemKey = 'item' . $i;
        $spreadsheet->getActiveSheet()->setCellValue($columna . $inicio, $resultados[0][$itemKey]);
        $inicio++;
    }
        //------- fin inserción datos en la hoja 4--------  

        // Se activa de nuevo la primer pagina para que se vea al descargar el reporte.
        $spreadsheet->setActiveSheetIndex(0);

        //Configuraciones de los encabezados del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //   header('Content-Disposition: attachment;filename="Reporte_indicadores_cumplimiento.xlsx"');
        header('Content-Disposition: attachment;filename="' . 'leo' . '.xlsx"');
          header('Cache-Control: max-age=0');
  
          // Exportacion del archivo
          $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $inputFileType);
          $writer->save('php://output');
          exit;
  
          return $this->sendResponse($writer, trans('msg_success_update'));
    
      
    }
    


    
}
