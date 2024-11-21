<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AppBaseController;
use Modules\Intranet\Repositories\UserHistoryRepository;
use Modules\Intranet\Repositories\UserRepository;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Validation\Rules\Password;
use Modules\Intranet\Http\Controllers\UtilController;

class ProfileController extends AppBaseController {

    /**
     * Constructor de la clase
     *
     * @author Jhoan Sebastian Chilito S. - Jul. 27 - 2020
     * @version 1.0.0
     */
    public function __construct(
        UserHistoryRepository $userHistoryRepo,
        UserRepository $userRepo
    ) {
        $this->userHistoryRepository = $userHistoryRepo;
        $this->userRepository = $userRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile.index')
            ->with('sidebarHide', true)
            ->with('contentCssClass', 'hljs-wrapper');
    }

    /**
     * Obtiene todos los elementos existentes
     *
     * @author Jhoan Sebastian Chilito S. - Jun. 20 - 2020
     * @version 1.0.0
     *
     * @return Response
     */
    public function getAuthUser() {

        // Obtiene usuario logueado
        $user = Auth::user();
        // Cargo
        $user->positions;
        // Dependencia
        $user->dependencies;
        // Roles
        $user->roles;
        // Grupos de trabajo
        $user->workGroups;
        // Path de imagen de perfil
        $user->profile_img_preview = asset('storage').'/'.$user->url_img_profile;

        return $this->sendResponse(json_decode($user), trans('data_obtained_successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, $id) {
        $input = $request->except(['created_at', 'updated_at', '_method', 'id']);
        // Se eliminan los sgntes del array input, ya que genera error a la hora de guardarlos en la BD en formato (0000-00-00)
        unset($input["contract_start"]);
        unset($input["contract_end"]);
        /** @var User $user */
        $oldUser = $this->userRepository->find($id);

        // Valida si existe el usuario
        if (empty($oldUser)) {
            return $this->sendError(trans('not_found_element'), 200);
        }

        /*
            // Obtiene la imagen de perfil que se va a reemplazar
            $oldProfileImg = $oldUser->url_img_profile;
            // Elimina imagen de perfil anterior
            Storage::delete('/public' . $oldUser->url_img_profile);
        */
        // Valida si se debe actualizar la contraseña
        if (!empty($input['password'])) {
            $request->validate([
                'password' => ['required', 'string',  Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(), 
                'confirmed']
            ]);
            // Encripta la contrasena
            $input['password'] = Hash::make($input['password']);
        } else {
            // Si no va a actualizar la contraseña, se elimina el campo password del array de campos a actualizar
            unset($input['password']);
        }
        // Valida si se debe actualizar la segunda contraseña (esto para la publicación de documentos)
        if (!empty($input['second_password'])) {
            $request->validate([
                'second_password' => 'required|confirmed|min:6'
            ]);
            // Encripta la segunda contraseña
            $input['second_password'] = Hash::make($input['second_password']);
        }

        // Inicia la transaccion
        DB::beginTransaction();

        try {
            // Organiza campos booleanos
            $input['sendEmail'] = $this->toBoolean($input['sendEmail']);
            $input['block'] = $this->toBoolean($input['block']);
            // Valida si habilitó la segunda contraseña
            if(isset($input['enable_second_password']))
                // Habilitar la segunda contraseña para la publicación de documentos
                $input['enable_second_password'] = $this->toBoolean($input['enable_second_password']);
            
            // Valida si se ingresa la imagen de perfil
            if ($request->hasFile('url_img_profile')) {
                $input['url_img_profile'] = substr($input['url_img_profile']->store('public/users/avatar'), 7);
            }

            // Valida si se ingresa la imagen de perfil
            if ($request->hasFile('url_digital_signature')) {
                $input['url_digital_signature'] = substr($input['url_digital_signature']->store('public/users/signature'), 7);
            }

            // Actualiza datos de usuario
            $user = $this->userRepository->update($input, $id);

            // Prepara los datos de usuario para comparar
            $arrDataUser = $user->replicate()->toArray(); // Datos actuales de usuario
            $arrDataOldUser = $oldUser->replicate()->toArray(); // Datos antiguos de usuario

            // Datos diferenciados
            $arrDiff = $this-> array_diff_recursive($arrDataUser, $arrDataOldUser);
            // Valida si los datos antiguos son diferentes a los actuales
            if ( count($arrDiff) > 0) {
                // Asigna el id del usuario para el registro del historial
                $input['users_id'] = $user->id;
                // Agrega el id del usuario que crea el registro
                $input['cf_user_id'] = Auth::id();

                $input['roles'] = null;
                $input['work_groups'] = null;
                // Crea un nuevo registro de historial
                // dd($input);
                $this->userHistoryRepository->create($input);
            }

            /** Obtiene relaciones de usuario */
            // Obtiene el historial de cambios del usuario
            $user->usersHistory;
            // Obtiene cargo
            $user->positions;
            // Obtiene dependencia
            $user->dependencies;
            // Obtiene roles
            $user->roles;
            // Obtiene grupos de trabajo
            $user->workGroups;
            // Path de imagen de perfil
            $user->profile_img_preview = asset('storage').'/'.$user->url_img_profile;

        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Envia mensaje de error
            return $this->sendError(trans('msg_error_update'));
        }
        // Efectua los cambios realizados
        DB::commit();

        return $this->sendResponse($user->toArray(), trans('msg_success_update'));
    }

    public function array_diff_recursive($array1, $array2) {
        $result = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $result[$key] = $value;
                } else {
                    $recursiveDiff = $this->array_diff_recursive($value, $array2[$key]);
                    if (!empty($recursiveDiff)) {
                        $result[$key] = $recursiveDiff;
                    }
                }
            } else {
                if (!isset($array2[$key]) || $array2[$key] !== $value) {
                    $result[$key] = $value;
                }
            }
        }
      
        return $result;
    }

}
