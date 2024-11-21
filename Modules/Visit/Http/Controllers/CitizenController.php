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

        return view('visit::citizens.index');

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
        $citizens = Citizen:: with(['otrasVictimasList', 'cuestionario'])->first()->get();
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
        // Nombre de archivo con tiempo de creacion
        $fileName = time().'-'.trans('citizens').'.'.$fileType;

        // Valida si el tipo de archivo es pdf
        if (strcmp($fileType, 'pdf') == 0) {
            // Guarda el archivo pdf en ubicacion temporal
            // Excel::store(new GenericExport($input['data']), $fileName, 'temp', \Maatwebsite\Excel\Excel::DOMPDF);

            // Descarga el archivo generado
            return Excel::download(new GenericExport($input['data']), $fileName, \Maatwebsite\Excel\Excel::DOMPDF);
        } else if (strcmp($fileType, 'xlsx') == 0) { // Valida si el tipo de archivo es excel
            // Guarda el archivo excel en ubicacion temporal
            // Excel::store(new GenericExport($input['data']), $fileName, 'temp');

            // Descarga el archivo generado
            return Excel::download(new GenericExport($input['data']), $fileName);
        }
    }
}
