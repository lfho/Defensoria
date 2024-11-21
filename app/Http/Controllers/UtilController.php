<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\State;
use App\Models\City;
use App\User;
use Modules\Intranet\Models\Citizen;
use Modules\NotificacionesIntraweb\Models\NotificacionesMailIntraweb;
use Illuminate\Support\Facades\Response;
class UtilController extends AppBaseController {

   /**
     * Obtiene todos los elementos existentes por pais
     *
     * @author Carlos Moises Garcia T. - Jun. 08 - 2021
     * @version 1.0.0
     *
     * @return Response
     */
   public function getStatesByCountry($countryID) {
      $states = State::where('country_id', $countryID)->orderBy("name")->get();
      return $this->sendResponse($states->toArray(), trans('data_obtained_successfully'));
   }

   /**
     * Obtiene todos los elementos existentes por estado
     *
     * @author Carlos Moises Garcia T. - Jun. 08 - 2021
     * @version 1.0.0
     *
     * @return Response
     */
   public function getCitiesByState($stateID) {
      $cities = City::where('state_id', $stateID)->get();
      return $this->sendResponse($cities->toArray(), trans('data_obtained_successfully'));
   }

   public function cancelSubscriptionView(){
      return view('cancel_subscription', [
         'appName' => env('APP_NAME'),
     ]); 
   }

   public function cancelSubscriptionProcess(Request $request)
   {

      try {

         $input = $request->toArray();

         $email = decrypt($input['email']);
   
         $user = User::where('email', $email)->update([
            'sendEmail' => 0
         ]);
   
         return response()->json(['success' => true, 'message' => 'Email notifications have been cancelled.']);

      } catch (\Illuminate\Database\QueryException $error) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('Controllers', 'app\Http\Controllers\UtilController - Error: '.$error->getMessage());
            // Retorna mensaje de error de base de datos
            return $this->sendSuccess(config('constants.support_message'), 'info');
      } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('Controllers', 'app\Http\Controllers\UtilController - Error: '.$e->getMessage());
            // Retorna error de tipo logico
            return $this->sendSuccess(config('constants.support_message'), 'info');
      }
   }

   public function trackMail(Request $request) {

      $input = $request->toArray();
      $ip = $this->detectIP();

         // Se realiza conteo de notificación
        $intento =  NotificacionesMailIntraweb::where('id_mail',$input['c'])->first()->intento;
        NotificacionesMailIntraweb::where('id_mail',$input['c']) ->update([
         'intento' => $intento+1 
         ]);


         //Una vez se envia se actualiza el campo nuevamente
         NotificacionesMailIntraweb::where('id_mail',$input['c'])->
         where('intento',1)
         ->update([
            'leido'=>'Si',
            'fecha_hora_leido'=>date('Y-m-d H:i:s'),
            'ip_leido' => $ip,
            'agente_navegador' => 'Google'
         ]);


      $this->generateSevenLog('SnSController', 'app\Http\Controllers\SnSController - . JsonData: ' . $input['c']);
       // Crear una imagen en gris de 1x1 píxeles
       $image = imagecreatetruecolor(1, 1);
       $gray = imagecolorallocate($image, 192, 192, 192); // Color gris
       imagefill($image, 0, 0, $gray);

       // Devolver la imagen como respuesta
       ob_start();
       imagepng($image);
       $imageData = ob_get_clean();

       // Liberar memoria
       imagedestroy($image);

       // Devolver la imagen con cabeceras de tipo 'image/png'
       return Response::make($imageData, 200, [
           'Content-Type' => 'image/png',
           'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
       ]);
      
   }
   
}
