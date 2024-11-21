<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Support\Facades\Storage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use SoapClient;
use App\Http\Controllers\JwtController as JWT;
use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;
use Modules\Configuracion\Models\ConfigurationGeneral;
use Modules\Intranet\Models\User;

use Illuminate\Support\Str;
use Modules\Configuracion\Models\Variables;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendResponse($result, $message, $typeMessage = null)
    {
        $token = JWT::generateToken($result);

        return [
            'success' => true,
            'data'    => $token,
            'message' => $message,
            'type_message' => $typeMessage,
        ];
    }

    public function sendResponseAvanzado($result, $message, $typeMessage = null, $dataExtra = [])
    {
        $token = JWT::generateToken($result);

        return [
            'success' => true,
            'data'    => $token,
            'message' => $message,
            'type_message' => $typeMessage,
            'data_extra' => $dataExtra,
        ];
    }

    public function sendError($error, $data = [], $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error, $data), $code);
    }

    public function sendErrorData($message, $data = null, $code = 404) {
        return Response::json([
            'success' => false,
            'data'    => $data,
            'message' => $message,
        ], $code);
    }

    public function sendSuccess($message, $typeMessage = null)
    {
        return Response::json([
            'success' => true,
            'message' => $message,
            'type_message' => $typeMessage,
        ], 200);
    }

    public function sendValidationError($message, $errors, $code = 404)
    {
        return Response::json([
            'success' => false,
            'errors'  => $errors,
            'message' => $message,
        ], $code);
    }

    /**
     * Convierte texto a boleano
     */
    function toBoolean($value) {
        return $value === 'true';
    }

    /**
     * Obtiene la cantidad limite que puede ocupar el sitio con la base de datos y adjuntos
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return int
     */
    public function getStorageCapacityLimit(): int{
        $storageCapacityLimit = ConfigurationGeneral::select("limite_almacenamiento")->first()->limite_almacenamiento;
        return $storageCapacityLimit;
    }

    public function getStorageConsumed(): float{
        return $this->getAmountFileStorageConsumed() + $this->getAmountDatabaseStorageConsumed();
    }

        /**
     * Obtiene la cantidad de funcionarios
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return int
     */
    public function getEmployees(): int {
        $availableUsers = User::whereNotNull('id_cargo')->count();
        return $availableUsers;
    }

    /**
     * Obtiene la cantidad limite de funcionarios
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return int
     */
    public function getLimitNumberEmployees(): int {
        $userCapacity = ConfigurationGeneral::select("limite_usuarios")->first()->limite_usuarios;
        return $userCapacity;
    }

    /**
     * Valida si hay usuarios disponibles para crear
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return bool
     */
    public function noUsersAvailable(): bool{
        return $this->getEmployees() >= $this->getLimitNumberEmployees();
    }

    /**
     * Obtiene la cantidad consumida en los adjuntos del storage
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return float
     */
    public function getAmountFileStorageConsumed(): float {
        $rutaCarpeta = 'public';

        // Obtiene el tamaño de la carpeta en bytes
        $archivos = Storage::disk('local')->allFiles($rutaCarpeta);

        $pesoTotal = 0;
        foreach ($archivos as $archivo) {
            $pesoTotal += Storage::disk('local')->size($archivo);
          }

        // Convierte el tamaño a megabytes
        $pesoMb = ($pesoTotal / 1024) / 1024;

        return $pesoMb;
    }

    /**
     * Obtiene la cantidad consumida en la base de datos
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return float
     */
    public function getAmountDatabaseStorageConsumed() : float{
        $totalSize = DB::table('information_schema.tables')
        ->where('table_schema', env("DB_DATABASE"))
        ->select(DB::raw('SUM(ROUND(data_length + index_length) / 1024 / 1024) AS total_size_mb'))
        ->first();

        return (float) $totalSize->total_size_mb;
    }

    /**
     * Obtiene el objeto de la lista ingresada,
     * filtrando por las condiciones y la llave especifica
     *
     * @param Array $list lista de objetos a filtrar
     * @param String $keyObject nombre del valor objeto a filtrar
     * @param String $condition condicion de retorno de objeto
     */
    public static function getObjectOfList($list, $keyObject, $condition) {
        foreach ($list as $key => $value) {
            if ($value->{$keyObject} == $condition) {
                return $list[$key];
            }
        }
    }

    /**
     * Obtiene la lista ingresada,
     * filtrando por las condiciones y la llave especifica
     *
     * @param Array $list lista de objetos a filtrar
     * @param String $keyObject nombre del valor objeto a filtrar
     * @param String $condition condicion de retorno de objeto
     */
    public static function getListOfReferedObject($list, $keyObject, $condition) {

        $data = array();
        foreach ($list as $key => $value) {
            if ($value->{$keyObject} == $condition) {
                $data[] = $value;
            }
        }
        return $data;
    }

    /**
     * Genera un log dinamico
     *
     * @author Carlos Moises Garcia T. - May. 31 - 2021
     * @version 1.0.0
     *
     * @param String $name nombre del archivo log
     * @param String $message informacion del log
     */
    public static function generateSevenLog($name, $message) {
        $log = new Logger('local');
        $log->pushHandler(new StreamHandler(storage_path().'/logs/'.$name.'.log'));
        $log->info($message);
    }

    /**
     * Obtiene las fechas futuras a partir de una y del calendario laboral
     *
     * @author Carlos Moises Garcia T. - Oct. 29 - 2020
     * @version 1.0.0
     *
     * @param Array $arrayDateNoWorks lista de fechas de no trabajo
     * @param Date $date fecha base para calcular la fecha futura
     * @param String $unitTerm unidad de calculo (dias | horas)
     * @param String $termType tipo de calculo (calendario | calendario laboral)
     * @param Int $timeAttention demora del tiempo de atencion (plazo)
     * @param Object $workingHours establece el horario laboral
     *              star_time_am hora de inicio del horario laboral de la manana
     *              end_time_am hora de finalizacion del horario laboral de la manana
     *              star_time_pm hora de inicio del horario laboral de la tarde
     *              end_time_pm hora de finalizacion del horario laboral de la tarde
     *
     * @return Response
     */
    public function calculateFutureDate($arrayDateNoWorks, $date, $unitTerm, $termType, $timeAttention, $workingHours = null){

        // dd($arrayDateNoWorks, $date, $unitTerm, $termType, $timeAttention, $workingHours);
        $created = $date;
        $l1 = explode(" ", $created);
        $l2 = explode("-",$l1[0]);
        $l3 = mktime(0,0,0,$l2[1],$l2[2],$l2[0]);
        $mcreated0 = $l3;
        $mcreated = $l3;
        $dias = 0;

        switch($unitTerm){
            case "Días":
                while($dias < $timeAttention){
                    $date = date("Y-m-d",$mcreated);
                    if(in_array($date, $arrayDateNoWorks) == false or $termType == "Calendario")
                    $dias++;
                    $l1 = explode(" ",$date);
                    $l2 = explode("-",$l1[0]);
                    $l3 = mktime(23,59,59,$l2[1],$l2[2]+1,$l2[0]);
                    $mcreated = $l3;
                }

                //verificar que la fecha de vencimiento no esté entre los dias no habiles, si lo está buscar el siguiente dia habil
                $date = date("Y-m-d", $mcreated);
                if(in_array($date, $arrayDateNoWorks) and $termType != "Calendario"){
                    while(in_array($date, $arrayDateNoWorks)){
                        $l1 = explode(" ",$date);
                        $l2 = explode("-",$l1[0]);
                        $l3 = mktime(23,59,59,$l2[1],$l2[2]+1,$l2[0]);
                        $mcreated = $l3;
                        $date = date("Y-m-d",$mcreated);
                    }
                }

                $datos[] = date("Y-m-d H:i:s",$mcreated);
                $datos[] = null;

                return $datos;
            break;
            case "Horas":
                //OBTENER LA FECHA Y HORA DE VENCIMIENTO

                //Horario que se ha definido en el calendario y queda guardado en la tabla horario_laboral
                $horainiam = date("H:i", strtotime($workingHours->star_time_am));
                $horafinam = date("H:i", strtotime($workingHours->time_end_am));
                $horainipm = date("H:i", strtotime($workingHours->star_time_pm));
                $horafinpm = date("H:i", strtotime($workingHours->time_end_pm));

                //MOVERSE POR CADA MINUTO

                //OBTENER LA FECHA Y HORA INICIAL, PREVINIENDO QUE SE HAYA GENERADO EL PQR EN UNA HORA NO LABORAL ENTONCES SE UBICA
                //LA FECHA Y HORA DE CREACIÓN EN UNA HORA LABORAL DEFINIDA

                $cf_created = $date;
                $created_lista = explode(" ",$cf_created);

                $created_dia  = $created_lista[0];
                $created_hora = $created_lista[1];

                $dia_lista = explode("-",$created_dia);
                $hora_lista = explode(":",$created_hora);

                //CONSTRUIR EL MKTIME PARA OBTENER EL TIEMPO EN SEGUNDOS DE LA FECHA DE CREACIÓN DE PQR Y ASI MOVERSE MAS FACIL Y
                //OBTENER LA FECHA DE VENCIMIENTO

                $created_mktime = mktime($hora_lista[0],$hora_lista[1],$hora_lista[2],$dia_lista[1],$dia_lista[2],$dia_lista[0]);

                //EL WHILE SEGUIRÁ HASTA QUE EL TIEMPO QUE HAYA SUMADO A CREATED_MKTIME HAYA IGUALADO AL TIEMPO DE PLAZO (timeAttention)

                //CONVERTIR A diasatencion A SEGUNDOS (TIEMPO ABSOLUTO OSEA SIN DIAS FESTIVOS NI RANGOS YA QUE ESTE TIEMPO ES EL REAL DE TRABAJO)
                //DIASATENCION ESTA EN HORAS ENTONCES CONVERTIR HORAS A SEGUNDOS

                //1 HORA -> 60 MINUTOS -> 1 MINUTO -> 60 SEGUNDOS = 3600 SEGUNDOS

                // dd($timeAttention);

                $segundos_atencion = $timeAttention * 60 * 60;

                //CREAR VARIABLE QUE CUENTE LOS SEGUNDOS Y SE COMPARE CON SEGUNDOS_ATENCION

                $segundos_recorridos = 0;

                $lastdiff = 0;

                //OBTIENE LA FECHA Y HORA INICIAL LABORAL
                $inicial_mktime = NULL;

                //ARRAY DE HORAS QUE CONTENDRÁ LOS TIEMPOS ABSOLUTOS Y RELATIVOS DE CADA HORA HABIL ENTRE LA FECHA DE CREACIÓN Y LA FECHA CALCULADA
                //DE VENCIMIENTO.
                $HORAS = array();

                while($segundos_recorridos < $segundos_atencion){

                    $date = date("Y-m-d",$created_mktime);

                    //ESTO VA AFUERA DEL IF PORQUE INDEPENDIENTE DE QUE SEA DIA HABIL O NO, DEBE CALCULARSEN LOS RANGOS

                    //OBTENER MKTIMES DE LIMITES DE HORARIO LABORAL PARA USARLOS EN LA VALIDACION DE HORAS
                    //HAY QUE HACERLO DENTRO DEL WHILE PORQUE LAS HORAS LABORALES NO CONTIENEN LOS DIAS ENTONCES HAY QUE CALCULARLOS
                    //LOS MKTIMES POR CADA DIA EN EL QUE SE VA MOVIENDO
                    $date_lista = explode("-",$date);

                    //OBTENER HORA Y MINUTOS DE PARAMETROS
                    $horainiam_lista = explode(":",$horainiam);
                    $horafinam_lista = explode(":",$horafinam);
                    $horainipm_lista = explode(":",$horainipm);
                    $horafinpm_lista = explode(":",$horafinpm);

                    //NO TIENEN SEGUNDOS ENTONCES SE LES AGREGA TENIENDO EN CUENTA QUE LA HORAS INICIALES ARRANCAN EN CERO Y LAS FINALES
                    //TERMINAN EN 59 PARA ABARCAR TODO EL RANGO HASTA LOS SEGUNDOS
                    $horainiam_mktime = mktime($horainiam_lista[0],$horainiam_lista[1],0,$date_lista[1],$date_lista[2],$date_lista[0]);
                    $horafinam_mktime = mktime($horafinam_lista[0],$horafinam_lista[1],59,$date_lista[1],$date_lista[2],$date_lista[0]);
                    $horainipm_mktime = mktime($horainipm_lista[0],$horainipm_lista[1],0,$date_lista[1],$date_lista[2],$date_lista[0]);
                    $horafinpm_mktime = mktime($horafinpm_lista[0],$horafinpm_lista[1],59,$date_lista[1],$date_lista[2],$date_lista[0]);



                    //NO ES UN DIA FESTIVO O EL TIPO DE PLAZO ES CALENDARIO ENTONCES NO IMPORTA QUE SEA FESTIVO ?
                    if(in_array($date, $arrayDateNoWorks) == false or $termType == "Calendario"){
                        //SI EL TIPO DE PLAZO ES CALENDARIO
                        if($termType == "Calendario"){
                            //CONTAR EL TIEMPO EN 24 HORAS

                            //ALMACENA EN HORAS EL TIEMPO ACTUAL
                            $HORAS[] = $created_mktime."-3600";
                            //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                            $created_mktime += 3600;
                            //SE INCREMENTA UNA HORA EL CONTADOR
                            $segundos_recorridos += 3600;

                        }
                        else{
                            //CALCULAR QUE EL ACTUAL CREATED_MKTIME NO ESTA FUERA DE LOS RANGOS HORARIOS

                            //POR LA MAÑANA
                            if($horainiam_mktime <= $created_mktime and $created_mktime <= $horafinam_mktime){
                                //ALMACENA EN HORAS EL TIEMPO ACTUAL
                                $HORAS[] = $created_mktime."-3600";
                                //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                $created_mktime += 3600;
                                //SE INCREMENTA UNA HORA EL CONTADOR
                                $segundos_recorridos += 3600;
                            }
                            else{
                                //ESTA FUERA DEL RANGO DE POR LA MAÑANA O ES DE LA TARDE ?
                                if($horainipm_mktime <= $created_mktime and $created_mktime <= $horafinpm_mktime){
                                    //ALMACENA EN HORAS EL TIEMPO ACTUAL
                                    $HORAS[] = $created_mktime."-3600";
                                    //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                    $created_mktime += 3600;
                                    //SE INCREMENTA UNA HORA EL CONTADOR
                                    $segundos_recorridos += 3600;
                                }
                                else{
                                    //ESTA FUERA DE LOS RANGOS
                                    //DE CUAL ? DE LOS DOS ? O ESTA EN EL MEDIO DE LOS DOS O ANTES DEL PRIMERO O DESPUES DEL SEGUNDO RANGO ?

                                    //EL SEGUNDERO segundos_recorridos NO SE INCREMENTA EN ESTOS CASOS
                                    //PRIMER CASO: ESTA ANTES DEL PRIMERO, EJEMPLO: CREATED (6:30) < 7:00 , ENTONCES SE INCREMENTA EL CREATED
                                    if($horainiam_mktime > $created_mktime){
                                        //SE MUEVE A LA SIGUIENTE HORA

                                        //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                        $created_mktime += 3600;
                                    }
                                    else{
                                        //SEGUNDO CASO: ESTA EN EL MEDIO, EJEMPLO CREATED (13:30) > 12:00 Y ES < 14:00, ENTONCES SE INCREMENTA EL CREATED


                                        if($horafinam_mktime < $created_mktime and $created_mktime < $horainipm_mktime){
                                            //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                            $created_mktime += 3600;
                                        }
                                        else{
                                            //TERCER CASO: ESTA DESPUES DEL FINAL DEL SEGUNDO RANGO, EJEMPLO CREATED (19:00) > 18:00, ENTONCES
                                            //SE MUEVE EL CREATED AL DIA SIGUIENTE HABIL A LAS 7, SI EL DIA ES HABIL O NO NO SE CALCULA AQUI SINO ARRIBA
                                            //EN EL IF PRINCIPAL DENTRO DEL WHILE, ENTONCES SOLO SUMAMOS 1 DIA PERO NO AL CREATED PORQUE DEBE QUEDAR
                                            //EL DIA A LA HORA INICIAL DE LA MAÑANA, ENTONCES USAMOS A horainiam_mktime Y SE LO ASIGNAMOS AL CREATED CON
                                            //UN DIA MAS (1 DIA -> 24 HORAS * 60 MINUTOS * 60 SEGUNDOS)
                                            //TENIENDO EN CUENTA QUE HAY QUE REVERTIR AL CONTADOR segundos_recorridos EL TIEMPO QUE SE PASÓ PARA QUE DE
                                            //EL TIEMPO EXACTO

                                            if($horafinpm_mktime < $created_mktime){

                                                //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                                $created_mktime += 3600;
                                            }
                                            else{
                                                //	ESTA EN OTRO UNIVERSO PARALELO
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                        $created_mktime += 3600;
                    }

                    if($inicial_mktime == NULL){
                        $inicial_mktime = $created_mktime;
                    }
                }

                //SI EL TIPO DE PLAZO ES LABORAL
                if($termType == "Laboral"){

                    do{

                        //VERIFICAR PRIMERO QUE LA FECHA OBTENIDA SEA HABIL
                        $date = date("Y-m-d",$created_mktime);

                        //ESTO VA AFUERA DEL IF PORQUE INDEPENDIENTE DE QUE SEA DIA HABIL O NO, DEBE CALCULARSEN LOS RANGOS

                        //OBTENER MKTIMES DE LIMITES DE HORARIO LABORAL PARA USARLOS EN LA VALIDACION DE HORAS
                        //HAY QUE HACERLO DENTRO DEL WHILE PORQUE LAS HORAS LABORALES NO CONTIENEN LOS DIAS ENTONCES HAY QUE CALCULARLOS
                        //LOS MKTIMES POR CADA DIA EN EL QUE SE VA MOVIENDO

                        $date_lista = explode("-",$date);

                        //OBTENER HORA Y MINUTOS DE PARAMETROS
                        $horainiam_lista = explode(":",$horainiam);
                        $horafinam_lista = explode(":",$horafinam);
                        $horainipm_lista = explode(":",$horainipm);
                        $horafinpm_lista = explode(":",$horafinpm);

                        //NO TIENEN SEGUNDOS ENTONCES SE LES AGREGA TENIENDO EN CUENTA QUE LA HORAS INICIALES ARRANCAN EN CERO Y LAS FINALES
                        //TERMINAN EN 59 PARA ABARCAR TODO EL RANGO HASTA LOS SEGUNDOS
                        $horainiam_mktime = mktime($horainiam_lista[0],$horainiam_lista[1],0,$date_lista[1],$date_lista[2],$date_lista[0]);
                        $horafinam_mktime = mktime($horafinam_lista[0],$horafinam_lista[1],59,$date_lista[1],$date_lista[2],$date_lista[0]);
                        $horainipm_mktime = mktime($horainipm_lista[0],$horainipm_lista[1],0,$date_lista[1],$date_lista[2],$date_lista[0]);
                        $horafinpm_mktime = mktime($horafinpm_lista[0],$horafinpm_lista[1],59,$date_lista[1],$date_lista[2],$date_lista[0]);


                        //NO ES UN DIA FESTIVO O EL TIPO DE PLAZO ES CALENDARIO ENTONCES NO IMPORTA QUE SEA FESTIVO ?
                        $habil = false;


                        //CALCULAR QUE EL ACTUAL CREATED_MKTIME NO ESTA FUERA DE LOS RANGOS HORARIOS
                        //POR LA MAÑANA
                        if($horainiam_mktime <= $created_mktime and $created_mktime <= $horafinam_mktime){

                            // Valida si la fecha no esta entre los dias no habiles
                            if (in_array($date, $arrayDateNoWorks) == false) {
                                //SE SALE PORQUE ES HABIL
                                $habil = true;
                            } else {
                                //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                $created_mktime += 3600;
                                $habil = false;
                            }
                        }
                        else{
                            //ESTA FUERA DEL RANGO DE POR LA MAÑANA O ES DE LA TARDE ?
                            if($horainipm_mktime <= $created_mktime and $created_mktime <= $horafinpm_mktime){
                                // Valida si la fecha no esta entre los dias no habiles
                                if (in_array($date, $arrayDateNoWorks) == false) {
                                    //SE SALE PORQUE ES HABIL
                                    $habil = true;
                                } else {
                                    //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                    $created_mktime += 3600;
                                    $habil = false;
                                }
                            }
                            else{
                                //ESTA FUERA DE LOS RANGOS
                                //DE CUAL ? DE LOS DOS ? O ESTA EN EL MEDIO DE LOS DOS O ANTES DEL PRIMERO O DESPUES DEL SEGUNDO RANGO ?

                                //EL SEGUNDERO segundos_recorridos NO SE INCREMENTA EN ESTOS CASOS
                                //PRIMER CASO: ESTA ANTES DEL PRIMERO, EJEMPLO: CREATED (6:30) < 7:00 , ENTONCES SE INCREMENTA EL CREATED
                                if($horainiam_mktime > $created_mktime){
                                    //SE MUEVE A LA SIGUIENTE HORA

                                    //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                    $created_mktime += 3600;
                                    $habil = false;
                                }
                                else{
                                    //SEGUNDO CASO: ESTA EN EL MEDIO, EJEMPLO CREATED (13:30) > 12:00 Y ES < 14:00, ENTONCES SE INCREMENTA EL CREATED

                                    if($horafinam_mktime < $created_mktime and $created_mktime < $horainipm_mktime){
                                        //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                        $created_mktime += 3600;
                                        $habil = false;

                                    }
                                    else{
                                        //TERCER CASO: ESTA DESPUES DEL FINAL DEL SEGUNDO RANGO, EJEMPLO CREATED (19:00) > 18:00, ENTONCES
                                        //SE MUEVE EL CREATED AL DIA SIGUIENTE HABIL A LAS 7, SI EL DIA ES HABIL O NO NO SE CALCULA AQUI SINO ARRIBA
                                        //EN EL IF PRINCIPAL DENTRO DEL WHILE, ENTONCES SOLO SUMAMOS 1 DIA PERO NO AL CREATED PORQUE DEBE QUEDAR
                                        //EL DIA A LA HORA INICIAL DE LA MAÑANA, ENTONCES USAMOS A horainiam_mktime Y SE LO ASIGNAMOS AL CREATED CON
                                        //UN DIA MAS (1 DIA -> 24 HORAS * 60 MINUTOS * 60 SEGUNDOS)
                                        //TENIENDO EN CUENTA QUE HAY QUE REVERTIR AL CONTADOR segundos_recorridos EL TIEMPO QUE SE PASÓ PARA QUE DE
                                        //EL TIEMPO EXACTO

                                        if($horafinpm_mktime < $created_mktime){

                                            //SE INCREMENTA UNA HORA (3600 SEGUNDOS)
                                            $created_mktime += 3600;

                                            $habil = false;
                                        }
                                        else{
                                            //ESTA EN OTRO UNIVERSO PARALELO
                                            $habil = false;
                                        }

                                    }
                                }
                            }
                        }
                    }
                    while( ( in_array($date, $arrayDateNoWorks) == true or $habil == false ) and $termType != "Calendario");

                }//SALE DEL WHILE Y SE OBTIENE LA FECHA Y HORA FINAL DEL CREATED_MKTIME


                $mcreated  = date("Y-m-d H:i",$created_mktime).":59";

                $datos[] = $mcreated;
                $datos[] = implode(",",$HORAS);

                return $datos;
            break;
        }
    }

    /**
     * Carga al servidor los adjuntos donde viene una ruta
     *
     * @author Carlos Moises Garcia T. - Mar. 29 - 2021
     * @version 1.0.0
     *
     * @param Request $request
     *
     * @return Response
     */
    public function uploadInputFile(Request $request) {

        // Valida si el archivo ocupa mas que el espacio de almacenamiento
        if($this->exceedsSpaceLimit($request->file->getSize() / 1024 / 1024)){
            return $this->sendResponse("No hay espacio disponible en el almacenamiento", trans('data_obtained_successfully'));
        }

        // Valida si viene un archivo
        if ($request->file) {
            // Se obtiene el archivo adjunto
            $file = $request->file('file');
            // Valida si existe un nombre de archivo
            if ($request->file_name_real == "true") {
                // Se obtiene el nombre real del documento adjunto
                $fileName = $file->getClientOriginalName();
                // Limpiar el nombre del archivo
                $cleanedFileName = $this->cleanFileName($fileName);
                if(Storage::disk('public')->exists(substr($request->file_path."/".$cleanedFileName,7))) {
                    $numero = rand(1, 999);
                    $letra = chr(rand(65, 90));
                    $custom_file_name = $numero.$letra.$cleanedFileName;

                }else{
                    // Agrega el tiempo que se esta subiendo el archivo al nombre
                    $custom_file_name = $cleanedFileName;
                }
                // Verifica si la variable 'TENANCY_HABILITADO' del .env es = true, significa que es un sitio multitenancy, adicionalmente se verifica que sea un inquilino
                if(env("TENANCY_HABILITADO") && Tenant::current()) {
                    // Formatea la ruta del adjunto antes de guardarlo, validando si tiene la palabra "public" para ser eliminada de la ruta
                    $filePath = strpos($request->file_path, "public") !== false ? substr($request->file_path, 7) : $request->file_path;
                    // Obtiene información del tenant actual
                    $tenant = app('currentTenant');
                    // Sube el archivo al servidor del tenant (carpeta) correspondiente
                    $file = "tenant_".$tenant["id"]."/".$request->file->storeAs($filePath, $custom_file_name, 'tenant');
                } else {
                    // Sube el archivo al servidor, validando si tiene la palabra "public" para ser eliminada de la ruta
                    $file = strpos($request->file_path, "public") !== false ? substr($request->file->storeAs($request->file_path, $custom_file_name), 7) : $request->file->storeAs($request->file_path, $custom_file_name);
                }
            } else {
                // Verifica si la variable 'TENANCY_HABILITADO' del .env es = true, significa que es un sitio multitenancy, adicionalmente se verifica que sea un inquilino
                if(env("TENANCY_HABILITADO") && Tenant::current()) {
                    // Formatea la ruta del adjunto antes de guardarlo, validando si tiene la palabra "public" para ser eliminada de la ruta
                    $filePath = strpos($request->file_path, "public") !== false ? substr($request->file_path, 7) : $request->file_path;
                    // Obtiene información del tenant actual
                    $tenant = app('currentTenant');
                    // Sube el archivo al servidor al tenant (carpeta) correspondiente
                    $file = "tenant_".$tenant["id"]."/".$request->file->store($filePath, 'tenant');
                } else {
                    // Sube el archivo al servidor
                    $file = substr($request->file->store($request->file_path), 7);
                }
            }
            // Retorna el nombre del archivo
            return $this->sendResponse($file, trans('msg_success_save'));
        }
    }

    public function uploadFile(object $file, string $storageLocation){
        if(!$this->exceedsSpaceLimit($file->getSize() / 1024 / 1024)){
            $fileLocation = substr($file->store($storageLocation), 7);
            return $fileLocation;
        }
        return null;
    }

    public function exceedsSpaceLimit($fileSize){
        return $this->getStorageCapacityLimit() < $this->getStorageConsumed() + $fileSize;
    }

    /**
     * Elimina del servidor determinado archivo
     *
     * @author Carlos Moises Garcia T. - Mar. 29 - 2021
     * @version 1.0.0
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteInputFile(Request $request) {
        // Valida si existe el archivo
        if (Storage::disk('public')->exists($request->file_path)){
            // Elimina el archivo del servidor
            /**
             * Se documenta la siguiente linea, para que no se elimine el adjunto del servidor,
             * esto porque se puede ejecutar la acción de eliminar y no guardar el registro de donde se esta ejecutando dicha acción
             */
            // Storage::disk('public')->delete($request->file_path);
            // Retorna el mensaje de eliminacion exitosa
            return $this->sendSuccess(trans('msg_success_drop'));
        } else {
            // Retorna el mensaje de error cuando no encuenta el archivo
            return $this->sendSuccess(trans('error'));
        }
    }

    /**
     * Verifica el estado actual de la sesión del usuario, true = sesión activa; false = sesión inactiva o expirada
     *
     * @return void
     */
    public function checkSession () {
        // Obtiene el estado actual de la sesión del usuario y lo retorna
        return $this->sendResponse(Auth::check(), "Estado actual de la sesión del usuario");
    }

    public function getVigencias($nombreTabla, $campoTabla) {
        // Consulta las vigencias disponibles o asignadas a la tabla del trámite obtenido por parámetro
        $vigencias = DB::table($nombreTabla)->select(array(DB::raw($campoTabla.' AS valor, '.$campoTabla)))->distinct()->orderBy($campoTabla, "DESC")->get()->map(fn ($row) => get_object_vars($row))->toArray();
        // Agrega en la primer posición la vigencia de todas, con el valor de vacio
        array_unshift($vigencias, ["valor" => "", $campoTabla => "Todas"]);
        return $this->sendResponse($vigencias, "PQR destacado con éxito");
    }

    /**
     * Carga a la nube (bucket S3) los adjuntos donde viene una ruta
     *-
     * @author Seven Soluciones Informáticas S.A.S. - May. 09 - 2024
     * @version 1.0.0
     *
     * @param Request $request
     * @param $nombreBucket nombre del bucket donde se guardan los documentos adjuntos
     *
     * @return Response
     */
    public function uploadInputFileAWS(Request $request) {
        // Valida si el archivo ocupa mas que el espacio de almacenamiento
        if($this->exceedsSpaceLimit($request->file->getSize() / 1024 / 1024)){
            return $this->sendResponse("No hay espacio disponible en el almacenamiento", trans('data_obtained_successfully'));
        }

        // Valida el tipo de almacenamiento general, si es AWS, obtiene el nombre del bucket para guardar el adjunto
        if(env("TIPO_ALMACENAMIENTO_GENERAL") == "AWS") {
            // Consulta el nombre del bucket configurado en las variables de entorno .env
            $nombreBucket = env("AWS_BUCKET");
            if(!$nombreBucket) {
                return $this->sendResponse(null, "Se requiere configurar el nombre del contenedor. Para recibir asistencia, contacte al equipo de soporte técnico de Intraweb", "warning");
            }
            // Verifica si la variable 'TENANCY_HABILITADO' del .env es = true, significa que es un sitio multitenancy, adicionalmente se verifica que sea un inquilino
            if(env("TENANCY_HABILITADO") && Tenant::current()) {
                // Obtiene información del tenant actual
                $tenant = app('currentTenant');
                // Crea la ruta del adjunto con el prefijo del tenant, validando si tiene la palabra "public" para ser eliminada de la ruta
                $filePath = "tenant_".$tenant["id"]."/".(strpos($request->file_path, "public") !== false ? substr($request->file_path, 7) : $request->file_path);
            } else { // Si el tipo de almacenamiento es AWS, los adjuntos se guardaran según el nombre del bucket
                // Formatea la ruta del adjunto antes de guardarlo, validando si tiene la palabra "public" para ser eliminada de la ruta
                $filePath = strpos($request->file_path, "public") !== false ? substr($request->file_path, 7) : $request->file_path;
            }
        } else {
            // Si el tipo de almacenamiento es diferente de AWS, invoca la función inicial para guardar el adjunto de manera local (en el servidor)
            return $this->uploadInputFile($request);
        }

        // Valida si viene un archivo
        if ($request->file) {
            // Se obtiene el archivo adjunto
            $file = $request->file('file');
            // Crea un nuevo cliente para comunicarse con el bucket S3 de AWS
            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Valida si el documento adjunto se debe guardar con el nombre real del mismo
            if ($request->file_name_real == "true") {
                // Se obtiene el nombre real del documento adjunto
                $fileName = $file->getClientOriginalName();
                // Limpiar el nombre del archivo
                $cleanedFileName = $this->cleanFileName($fileName);
                try {
                    // Se arma la ruta completa del adjunto con la ruta donde se guardará el mismo
                    $ruta_archivo = $filePath ."/". $cleanedFileName;
                    // A partir del nombre del bucket y la ruta completa del adjunto, se obtiene el adjunto desde el S3
                    $s3->headObject([
                        'Bucket' => $nombreBucket,
                        'Key' => $ruta_archivo
                    ]);
                    // Si el nombre del archivo ya existe en la nube, se pone como prefijo una cadena de números y carácteres para no repetir el nombre del adjunto
                    $numero = rand(1, 999);
                    $letra = chr(rand(65, 90));
                    $fileName = $numero.$letra.$cleanedFileName;
                } catch (\Exception $exception) {
                    // Si el archivo no existe, se pone el nombre original que trae desde el inicio
                    $fileName = $cleanedFileName;
                }
            } else {
                // Si el adjunto no debe quedar con el nombre original, se genera una cadena aleatoria para el nombre del documento con el siguiente formato:
                // YmdHisCadenaAleatoria (Año, mes, día, hora, minutos y segundos + Cadena aleatoria)
                $cadenaAleatoria = Str::random(5); // Generar una cadena aleatoria
                $fileName = date("YmdHis").$cadenaAleatoria.".".pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            }

            $ruta_archivo = $filePath ."/". $fileName;
            // Se guarda el adjunto en el bucket S3 de AWS, según el nombre del bucket y ruta indicada anteriormente
            $file = $s3->putObject([
                'Bucket'     => $nombreBucket,
                'Key'        => $ruta_archivo,
                'SourceFile' => $file->getPathname(),
                'StorageClass' => 'GLACIER_IR',
                'ContentType' => $file->getMimeType(),
            ]);
            // Retorna el nombre del archivo
            return $this->sendResponse($ruta_archivo, trans('msg_success_save'));
        }
    }

    /**
     * Obtiene y descarga el adjunto de un contenedor S3 de AWS, según el bucket indicado en el .env
     *
     * @author Seven Soluciones Informáticas S.A.S. - May. 10 - 2024
     * @version 1.0.0
     *
     * @param Request $request
     *
     * @return Response
     */
    public function readObjectAWS(Request $request) {
        // Consulta el nombre del bucket configurado en las variables de entorno .env
        $nombreBucket = env("AWS_BUCKET");

        // Configura las credenciales de acceso a AWS
        $credentials = [
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ];

        // Crea una instancia del cliente S3
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => env('AWS_DEFAULT_REGION'),
            'credentials' => $credentials,
        ]);

        // Ruta del objeto
        $objectKey = $request["path"];

        try {
            if($request["tipoURL"] == "temporal_aws") {
                // Genera una URL temporal para el objeto
                $command = $s3->getCommand('GetObject', [
                    'Bucket' => $nombreBucket,
                    'Key'    => $objectKey,
                ]);
                // Genera una URL prefirmada
                $presignedRequest = $s3->createPresignedRequest($command, '+1 minutes'); // La URL expirará en 1 minuto
                // Valida la extensión del archivo a mostrar, si es un word, excel o powerpoint ingresa al if
                if (empty($request["descargarArchivo"]) && (str_ends_with($objectKey, '.doc') || str_ends_with($objectKey, '.docx') || str_ends_with($objectKey, '.xls') || str_ends_with($objectKey, '.xlsx') || str_ends_with($objectKey, '.ppt') || str_ends_with($objectKey, '.pptx'))) {
                    // Obtiene la URL temporal y la codifica para que el visor la pueda interpretar correctamente
                    $tempFilePath = urlencode((string) $presignedRequest->getUri());
                } else {
                    // Obtiene la URL temporal
                    $tempFilePath = (string) $presignedRequest->getUri();
                }

            } else {
                // Obtiene la ruta temporal de la carpeta donde se guardará el archivo
                $directorioTemporal = storage_path("app/public/temp");
                !file_exists($directorioTemporal) ? mkdir($directorioTemporal, 0755) : null;

                // Ruta donde se almacenará temporalmente el archivo descargado
                $rutaArchivoTemporal = $directorioTemporal ."/".basename($objectKey);

                // Descarga el objeto desde S3 al servidor local
                $result = $s3->getObject([
                    'Bucket' => $nombreBucket,
                    'Key'    => $objectKey,
                    'SaveAs' => $rutaArchivoTemporal,
                ]);
                // Verifica si la descarga se realizó correctamente
                if ($result['@metadata']['statusCode'] === 200 && file_exists($rutaArchivoTemporal)) {
                    // Muestra el archivo PDF en el iframe
                    $tempFilePath = $request->header('origin')."/storage/temp/".basename($objectKey);
                } else {
                    echo "Error al descargar el archivo desde Amazon S3.";
                }
            }
            // Ahora puedes usar $presignedUrl para acceder al objeto durante el tiempo especificado
            return $this->sendResponse($tempFilePath, "Documento obtenido con éxito");

        } catch (\Exception $e) {
            // Maneja cualquier excepción
            echo $e->getMessage();
        }
    }

    /**
     * Reemplaza los caracteres especiales y las vocales con tildes en el nombre del archivo.
     *
     * @param string $filename
     * @return string
     */
    function cleanFileName($filename) {
        // Array de caracteres a reemplazar
        $unwantedArray = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            'ç' => 'c', 'Ç' => 'C', ',' => '_'
            // Puedes agregar más caracteres aquí
        ];

        // Reemplazar caracteres especiales y espacios
        $cleaned = strtr($filename, $unwantedArray);

        // Eliminar caracteres no deseados adicionales, los caractéres permitidos son los del primer parámetro de la función preg_replace
        $cleaned = preg_replace('/[^A-Za-z0-9_\-\. ]/', '', $cleaned);

        return $cleaned;
    }

    /**
     * Genera la estructura necesaria de un objeto tipo file para ser enviado a la función 'uploadInputFileAWS' y posteriormente almacenado
     *
     * @author Seven Soluciones Informáticas S.A.S. - May. 29 - 2024
     * @version 1.0.0
     *
     * @param UploadedFile $file - Archivo a almacenar
     * @param String $file_path - Ruta donde se va almacenar el archivo
     * @param Boolean $file_name_real - Define si el archivo se almacenará con el nombre original o con un nombre generado automáticamente
     *
     * @return String - Ruta completa del archivo almacenado
     */
    public function storeFileFromController(UploadedFile $file, String $file_path, bool $file_name_real = false) {
        // Crea una instancia de tipo Request para enviar a la función 'readObjectAWS'
        $request = new Request();
        // Agrega el archivo como tipo file
        $request->files->set('file', $file);
        // Ruta donde se va a guardar el archivo
        $request["file_path"] = $file_path;
        // Define si el archivo se almacenará con el nombre original o con un nombre generado automáticamente
        $request["file_name_real"] = $file_name_real;
        // Se hace la solicitud a la función 'uploadInputFileAWS' para almacenar el archivo según el tipo de almacenamiento general
        $file_data = $this->uploadInputFileAWS($request);
        // Se decodifica la URL
        $file_path_final = Jwt::decodeToken($file_data['data']);
        // Retorna la ruta final del archivo almacenado
        return $file_path_final;
    }

    /**
     * Revisa varias cabeceras HTTP y la variable REMOTE_ADDR para determinar la IP pública del cliente
     *
     * @author Desarrollador Seven - 2024
     * @version 1.0.0
     *
     * @return string - Ip del cliente
     */
    public function detectIP() {
        // Inicializa la variable $publicIp como una cadena vacía.
        $publicIp = "";

        // Normalmente, la superglobal $_SERVER está configurada
        if (isset($_SERVER)) {
            // Verifica si existe la cabecera HTTP_X_FORWARDED_FOR (usada por ejemplo en NginX)
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // Si existe, asigna su valor a $publicIp
                $publicIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            // Verifica si existe la cabecera HTTP_CLIENT_IP (usada por un proxy no transparente)
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                // Si existe, asigna su valor a $publicIp
                $publicIp = $_SERVER['HTTP_CLIENT_IP'];
            }

            // Verifica si existe la variable REMOTE_ADDR (IP del cliente en un servidor no proxy o proxy transparente)
            if (isset($_SERVER['REMOTE_ADDR'])) {
                // Si existe, asigna su valor a $publicIp
                $publicIp = $_SERVER['REMOTE_ADDR'];
            }
        }

        // Devuelve la dirección IP pública detectada
        return $publicIp;
    }
}
