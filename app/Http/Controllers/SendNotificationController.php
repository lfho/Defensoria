<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Str;
use Auth;
use DB;
use Modules\NotificacionesIntraweb\Models\NotificacionesMailIntraweb;
use Modules\NotificacionesIntraweb\Models\ListadoCorreosCheckeos;
use Response;
use GuzzleHttp\Client;
use App\User;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;


class SendNotificationController extends AppBaseController {

    /**
     * Funcion general para el envio de correos del aplicativo intraweb
     *
     * @author Johan David Velasco Rios - 29/05/2024
     * @version 1.0.0
     *
     * @param $plantilla = Ruta blade donde esta creada la plantilla del mail - String
     * @param $asunto = Asunto del correo electronico - Json
     * @param $datosEmail = Contiene los datos del registro de donde salio la correspondencia - Array
     * @param $destinatario = Contiene los correos electronicos de las personas la cuales se le enviara una notificacion - Array/string
     * @param $modulo = Modulo de donde proviene la notificación - String
     * @param $update = Valida si el registro se esta actualizando o creado - String
     * @param $idCorreo = Valida si el registro trae el id del correo - String
     *
     */
    public static function SendNotification($plantilla, $asunto, $datosEmail, $destinatario,$modulo,$update = null, $idCorreo = null)
    {
        //Validadores del correo electronico cuando no son de pago
        $validator = new EmailValidator();
        $response = "";
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);

        //Convierte el array $datosEmail en Json para poder ser almacenado en la tabla
        $jsonDatosEmail = json_encode($datosEmail);

        $user = Auth::user();
        try {

            //Valida el tipo de dato que es $destinatario
            if (gettype($destinatario) == "array") {

                //Recorreo cada correo para asignarle un id unico al correo y generar un registro en la tabla de notificaciones
                foreach ($destinatario as $correo) {

                    $datosEmail['mail'] = $correo;

                    // Crear una instancia del controlador
                    $instance = new self();
                    //Genera el id para el correo
                    $trackingId = $instance->generateTrackingId($correo);

                    $datosEmail['trackingId'] = config('app.url') . '/track-mail?c=' .$trackingId;

                    //Llama la clase sendmail y la funcion build para construir toda la data que lleva el correo
                    $data = new SendMail($plantilla, $datosEmail, $asunto, $trackingId);
                    $data->build();

                    //Consulta si existe algun usuario con el correo ingresado
                    $user = User::where('email',$correo)->get()->first();

                    $estado = "";
                    $mensajeServidor = "";

                    if (empty($user)) {

                        $checkMail = ListadoCorreosCheckeos::where('email',$correo)->get()->first();

                        if (!empty($checkMail)) {
                            if ($checkMail['estado'] == 'Valida') {

                                Mail::to($correo)->send($data);
                                $estado = "Entregado";
                                $mensajeServidor = "Notificación enviada con éxito.";
                            } else {
                                $estado = "No entregado";
                                $mensajeServidor = "Lamentamos informarle que no pudimos enviar la notificación debido a que la dirección de correo electrónico ingresada no se encuentra en nuestra base de datos. Esto puede deberse a un error de tipeo o a que el correo aún no ha sido validado. Le solicitamos se ponga en contacto con su administrador para solucionar este inconveniente.";
                            }

                        }else{
                            if ($validator->isValid($correo, $multipleValidations)) {
                                $curl = curl_init();

                                curl_setopt_array($curl, [
                                  CURLOPT_URL => "https://api.usebouncer.com/v1.1/email/verify?email=".$correo."",
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => "",
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 30,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => "GET",
                                  CURLOPT_HTTPHEADER => [
                                    "x-api-key: ".env('BOUNCER_API_KEY').""
                                  ],
                                ]);

                                $response = curl_exec($curl);
                                $err = curl_error($curl);

                                if ($err) {
                                    throw new Exception("cURL Error #:" . $err, 1);
                                } else {

                                $resultMail = json_decode($response,true);

                                    if ($resultMail['status'] == "deliverable") {

                                        Mail::to($correo)->send($data);
                                        $estado = "Entregado";
                                        $mensajeServidor = "Notificación enviada con éxito.";

                                        ListadoCorreosCheckeos::create([
                                            'email' => $correo,
                                            'estado' => 'Valida',
                                            'fecha_verificacion' => date('Y-m-d H:i:s'),
                                            'user_id' => Auth::check() ? Auth::user()->id : 0,
                                            'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                        ]);

                                    }else{

                                        $estado = "No entregado";
                                        $mensajeServidor = "La notificación no pudo ser enviada porque la dirección de correo ingresada no es válida o no es entregable. Por favor, verifique el correo y vuelva a intentarlo.";

                                        ListadoCorreosCheckeos::create([
                                            'email' => $correo,
                                            'estado' => 'Inválido',
                                            'fecha_verificacion' => date('Y-m-d H:i:s'),
                                            'user_id' => Auth::check() ? Auth::user()->id : 0,
                                            'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                        ]);

                                    }
                                }

                            } else {
                                $estado = "No entregado";
                                $mensajeServidor = "La notificación no pudo ser enviada porque el dominio del correo electrónico no es válido.";
                            }
                        }

                    } else {
                        if ($user['sendEmail'] == 1) {

                            $checkMail = ListadoCorreosCheckeos::where('email',$correo)->get()->first();

                            if (!empty($checkMail)) {
                                if ($checkMail['estado'] == 'Valida') {

                                    Mail::to($correo)->send($data);
                                    $estado = "Entregado";
                                    $mensajeServidor = "Notificación enviada con éxito.";
                                } else {
                                    $estado = "No entregado";
                                    $mensajeServidor = "Lamentamos informarle que no pudimos enviar la notificación debido a que la dirección de correo electrónico ingresada no se encuentra en nuestra base de datos. Esto puede deberse a un error de tipeo o a que el correo aún no ha sido validado. Le solicitamos se ponga en contacto con su administrador para solucionar este inconveniente.";
                                }

                            }else{
                                if ($validator->isValid($correo, $multipleValidations)) {
                                    $curl = curl_init();

                                    curl_setopt_array($curl, [
                                      CURLOPT_URL => "https://api.usebouncer.com/v1.1/email/verify?email=".$correo."",
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => "",
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 30,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => "GET",
                                      CURLOPT_HTTPHEADER => [
                                        "x-api-key: ".env('BOUNCER_API_KEY').""
                                      ],
                                    ]);

                                    $response = curl_exec($curl);
                                    $err = curl_error($curl);

                                    if ($err) {
                                        throw new Exception("cURL Error #:" . $err, 1);
                                    } else {

                                    $resultMail = json_decode($response,true);

                                        if ($resultMail['status'] == "deliverable") {

                                            Mail::to($correo)->send($data);
                                            $estado = "Entregado";
                                            $mensajeServidor = "Notificación enviada con éxito.";

                                            ListadoCorreosCheckeos::create([
                                                'email' => $correo,
                                                'estado' => 'Valida',
                                                'fecha_verificacion' => date('Y-m-d H:i:s'),
                                                'user_id' => Auth::check() ? Auth::user()->id : 0,
                                                'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                            ]);

                                        }else{

                                            $estado = "No entregado";
                                            $mensajeServidor = "La notificación no pudo ser enviada porque la dirección de correo ingresada no es válida o no es entregable. Por favor, verifique el correo y vuelva a intentarlo.";

                                            ListadoCorreosCheckeos::create([
                                                'email' => $correo,
                                                'estado' => 'Invalido',
                                                'fecha_verificacion' => date('Y-m-d H:i:s'),
                                                'user_id' => Auth::check() ? Auth::user()->id : 0,
                                                'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                            ]);

                                        }
                                    }

                                } else {
                                    $estado = "No entregado";
                                    $mensajeServidor = "La notificación no pudo ser enviada porque el dominio del correo electrónico no es válido.";
                                }
                            }
                        }else{
                            $estado = "No entregado";
                            $mensajeServidor = "La notificación no fue enviada porque el ".$user['user_type']." no tiene habilitada la opción de recibir notificaciones por correo electrónico.";
                        }
                    }



                    // Inicia la transacción
                    DB::beginTransaction();


                    //Genera un registro en la tabla de notificaciones
                    NotificacionesMailIntraweb::create([
                        'id_mail' => $trackingId,
                        'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                        'user_id' => Auth::check() ? Auth::user()->id : 0,
                        'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión',
                        'modulo' => $modulo,
                        'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                        'estado_comunicacion' => $datosEmail['state'] ?? null,
                        'correo_destinatario' => $correo,
                        'datos_mail' => $jsonDatosEmail,
                        'leido' => 'No',
                        'estado_notificacion' => $estado,
                        'asunto_notificacion' => $asunto->subject,
                        'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                        'plantilla_notificacion' => $plantilla,
                        'mensaje_cliente' => $mensajeServidor ?? null
                    ]);


                    // Efectua los cambios realizados
                    DB::commit();
                }

            } else {

                $datosEmail['mail'] = $destinatario;


                //Llama la clase sendmail y la funcion build para construir toda la data que lleva el correo
                if(empty($idCorreo)){
                    // Crear una instancia del controlador
                    $instance = new self();
                    //Genera el id para el correo
                    $trackingId = $instance->generateTrackingId($destinatario);

                    $datosEmail['trackingId'] = config('app.url') . '/track-mail?c=' .$trackingId;

                    $data = new SendMail($plantilla, $datosEmail, $asunto, $trackingId);

                }else{

                    $datosEmail['trackingId'] = config('app.url') . '/track-mail?c=' .$idCorreo;

                    $data = new SendMail($plantilla, $datosEmail, $asunto, $idCorreo);

                }
                $data->build();

                //Obtiene la información la cual indica si el correo es entregable o no
                $dataCorreo = json_decode($response, true);

                //Consulta si existe algun usuario con el correo ingresado
                $user = User::where('email',$destinatario)->get()->first();

                $estado = "";
                $mensajeServidor = "";

                if (empty($user)) {

                    $checkMail = ListadoCorreosCheckeos::where('email',$destinatario)->get()->first();

                    if (!empty($checkMail)) {
                        if ($checkMail['estado'] == 'Valida') {

                            Mail::to($destinatario)->send($data);
                            $estado = "Entregado";
                            $mensajeServidor = "Notificación enviada con éxito.";
                        } else {
                            $estado = "No entregado";
                            $mensajeServidor = "Lamentamos informarle que no pudimos enviar la notificación debido a que la dirección de correo electrónico ingresada no se encuentra en nuestra base de datos. Esto puede deberse a un error de tipeo o a que el correo aún no ha sido validado. Le solicitamos se ponga en contacto con su administrador para solucionar este inconveniente.";
                        }

                    }else{
                        if ($validator->isValid($destinatario, $multipleValidations)) {
                            $curl = curl_init();

                            curl_setopt_array($curl, [
                              CURLOPT_URL => "https://api.usebouncer.com/v1.1/email/verify?email=".$destinatario."",
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => "",
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 30,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => "GET",
                              CURLOPT_HTTPHEADER => [
                                "x-api-key: ".env('BOUNCER_API_KEY').""
                              ],
                            ]);

                            $response = curl_exec($curl);
                            $err = curl_error($curl);

                            if ($err) {
                                throw new Exception("cURL Error #:" . $err, 1);
                            } else {

                            $resultMail = json_decode($response,true);

                                if ($resultMail['status'] == "deliverable") {

                                    Mail::to($destinatario)->send($data);
                                    $estado = "Entregado";
                                    $mensajeServidor = "Notificación enviada con éxito.";

                                    ListadoCorreosCheckeos::create([
                                        'email' => $destinatario,
                                        'estado' => 'Valida',
                                        'fecha_verificacion' => date('Y-m-d H:i:s'),
                                        'user_id' => Auth::check() ? Auth::user()->id : 0,
                                        'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                    ]);

                                }else{

                                    $estado = "No entregado";
                                    $mensajeServidor = "La notificación no pudo ser enviada porque la dirección de correo ingresada no es válida o no es entregable. Por favor, verifique el correo y vuelva a intentarlo.";

                                    ListadoCorreosCheckeos::create([
                                        'email' => $destinatario,
                                        'estado' => 'Inválido',
                                        'fecha_verificacion' => date('Y-m-d H:i:s'),
                                        'user_id' => Auth::check() ? Auth::user()->id : 0,
                                        'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                    ]);

                                }
                            }

                        } else {
                            $estado = "No entregado";
                            $mensajeServidor = "La notificación no pudo ser enviada porque el dominio del correo electrónico no es válido.";
                        }
                    }

                } else {
                    if ($user['sendEmail'] == 1) {

                        $checkMail = ListadoCorreosCheckeos::where('email',$destinatario)->get()->first();

                        if (!empty($checkMail)) {
                            if ($checkMail['estado'] == 'Valida') {

                                Mail::to($destinatario)->send($data);
                                $estado = "Entregado";
                                $mensajeServidor = "Notificación enviada con éxito.";
                            } else {
                                $estado = "No entregado";
                                $mensajeServidor = "Lamentamos informarle que no pudimos enviar la notificación debido a que la dirección de correo electrónico ingresada no se encuentra en nuestra base de datos. Esto puede deberse a un error de tipeo o a que el correo aún no ha sido validado. Le solicitamos se ponga en contacto con su administrador para solucionar este inconveniente.";
                            }

                        }else{
                            if ($validator->isValid($destinatario, $multipleValidations)) {
                                $curl = curl_init();

                                curl_setopt_array($curl, [
                                  CURLOPT_URL => "https://api.usebouncer.com/v1.1/email/verify?email=".$destinatario."",
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_ENCODING => "",
                                  CURLOPT_MAXREDIRS => 10,
                                  CURLOPT_TIMEOUT => 30,
                                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                  CURLOPT_CUSTOMREQUEST => "GET",
                                  CURLOPT_HTTPHEADER => [
                                    "x-api-key: ".env('BOUNCER_API_KEY').""
                                  ],
                                ]);

                                $response = curl_exec($curl);
                                $err = curl_error($curl);



                                if ($err) {
                                    throw new Exception("cURL Error #:" . $err, 1);
                                } else {


                                $resultMail = json_decode($response,true);

                                    if ($resultMail['status'] == "deliverable") {

                                        Mail::to($destinatario)->send($data);
                                        $estado = "Entregado";
                                        $mensajeServidor = "Notificación enviada con éxito.";

                                        ListadoCorreosCheckeos::create([
                                            'email' => $destinatario,
                                            'estado' => 'Valida',
                                            'fecha_verificacion' => date('Y-m-d H:i:s'),
                                            'user_id' => Auth::check() ? Auth::user()->id : 0,
                                            'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                        ]);

                                    }else{

                                        $estado = "No entregado";
                                        $mensajeServidor = "La notificación no pudo ser enviada porque la dirección de correo ingresada no es válida o no es entregable. Por favor, verifique el correo y vuelva a intentarlo.";

                                        ListadoCorreosCheckeos::create([
                                            'email' => $destinatario,
                                            'estado' => 'Invalido',
                                            'fecha_verificacion' => date('Y-m-d H:i:s'),
                                            'user_id' => Auth::check() ? Auth::user()->id : 0,
                                            'user_name' => Auth::check() ? Auth::user()->name: 'Sin sesión'
                                        ]);

                                    }
                                }

                            } else {
                                $estado = "No entregado";
                                $mensajeServidor = "La notificación no pudo ser enviada porque el dominio del correo electrónico no es válido.";
                            }
                        }
                    }else{
                        $estado = "No entregado";
                        $mensajeServidor = "La notificación no fue enviada porque el ".$user['user_type']." no tiene habilitada la opción de recibir notificaciones por correo electrónico.";
                    }
                }

                // Inicia la transaccion
                DB::beginTransaction();

                //Valida si se requiere crear o actualizar el registro de la notificacion
                if (empty($update)) {
                    //Genera un registro en la tabla de notificaciones
                    NotificacionesMailIntraweb::create([
                        'id_mail' => $trackingId,
                        'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                        'user_id' => !empty($user) ? $user->id : null,
                        'user_name' => !empty($user) ? $user->name : null,
                        'modulo' => $modulo,
                        'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                        'estado_comunicacion' => $datosEmail['state'] ?? null,
                        'correo_destinatario' => $destinatario,
                        'leido' => 'No',
                        'datos_mail' => $jsonDatosEmail,
                        'estado_notificacion' => $estado,
                        'asunto_notificacion' => $asunto->subject,
                        'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                        'plantilla_notificacion' => $plantilla,
                        'mensaje_cliente' => $mensajeServidor ?? null

                    ]);
                }else{

                    //Una vez se envia se actualiza el campo nuevamente
                    NotificacionesMailIntraweb::where('id_mail',$idCorreo)->update([
                        'estado_notificacion'=>$estado,
                        'correo_destinatario'=>$destinatario,
                        'respuesta_servidor_notificacion' => $mensajeServidor ?? null
                    ]);

                }



                // Efectua los cambios realizados
                DB::commit();
            }

        } catch (\Illuminate\Database\QueryException $error) {

            // Inserta el error en el registro de log
            AppBaseController::generateSevenLog('SendNotificationController', 'App\Http\Controllers\SendNotificationController - '.(!empty($user) ? $user->name : null).' -  Error: '.$error->getMessage());

            if (gettype($destinatario) == "array") {
                //Recorreo cada correo para asignarle un id unico al correo y generar un registro en la tabla de notificaciones
                foreach ($destinatario as $correo) {
                        //Genera un registro en la tabla de notificaciones
                        NotificacionesMailIntraweb::create([
                            'id_mail' => $trackingId,
                            'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                            'user_id' => !empty($user) ? $user->id : null,
                            'user_name' => !empty($user) ? $user->name : null,
                            'modulo' => $modulo,
                            'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                            'estado_comunicacion' => $datosEmail['state'] ?? null,
                            'correo_destinatario' => $correo,
                            'datos_mail' => $jsonDatosEmail,
                            'estado_notificacion' => 'Entregado',
                            'asunto_notificacion' => $asunto->subject,
                            'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                            'plantilla_notificacion' => $plantilla
                        ]);
                }

            } else {
                //Genera un registro en la tabla de notificaciones
                NotificacionesMailIntraweb::create([
                    'id_mail' => $trackingId,
                    'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                    'user_id' => !empty($user) ? $user->id : null,
                    'user_name' => !empty($user) ? $user->name : null,
                    'modulo' => $modulo,
                    'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                    'estado_comunicacion' => $datosEmail['state'] ?? null,
                    'correo_destinatario' => $destinatario,
                    'datos_mail' => $jsonDatosEmail,
                    'estado_notificacion' => 'Entregado',
                    'asunto_notificacion' => $asunto->subject,
                    'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                    'plantilla_notificacion' => $plantilla
                ]);
            }
        } catch (\Exception $e) {

            // Inserta el error en el registro de log
            AppBaseController::generateSevenLog('SendNotificationController', 'App\Http\Controllers\SendNotificationController - '.(!empty($user) ? $user->name : null).' -  Error: '.$e->getMessage() .' -  linea: '.$e->getLine());

            if (gettype($destinatario) == "array") {
                //Recorreo cada correo para asignarle un id unico al correo y generar un registro en la tabla de notificaciones
                foreach ($destinatario as $correo) {
                        //Genera un registro en la tabla de notificaciones
                        NotificacionesMailIntraweb::create([
                            'id_mail' => $trackingId,
                            'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                            'user_id' => !empty($user) ? $user->id : null,
                            'user_name' => !empty($user) ? $user->name : null,
                            'modulo' => $modulo,
                            'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                            'estado_comunicacion' => $datosEmail['state'] ?? null,
                            'correo_destinatario' => $correo,
                            'datos_mail' => $jsonDatosEmail,
                            'estado_notificacion' => 'No entregado',
                            'asunto_notificacion' => $asunto->subject,
                            'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                            'plantilla_notificacion' => $plantilla,
                            'respuesta_servidor_notificacion' => $e->getMessage() ?? null
                        ]);
                }

            } else {
                //Genera un registro en la tabla de notificaciones
                NotificacionesMailIntraweb::create([
                    'id_mail' => $trackingId,
                    'id_comunicacion_oficial' => $datosEmail['id'] ?? null,
                    'user_id' => !empty($user) ? $user->id : null,
                    'user_name' => !empty($user) ? $user->name : null,
                    'modulo' => $modulo,
                    'consecutivo' => $datosEmail['consecutive'] ?? $datosEmail['consecutivo'] ?? $datosEmail['pqr_id'] ?? null,
                    'estado_comunicacion' => $datosEmail['state'] ?? null,
                    'correo_destinatario' => $destinatario,
                    'datos_mail' => $jsonDatosEmail,
                    'estado_notificacion' => 'No entregado',
                    'asunto_notificacion' => $asunto->subject,
                    'mensaje_notificacion' => $datosEmail['mensaje'] ?? null,
                    'plantilla_notificacion' => $plantilla,
                    'respuesta_servidor_notificacion' => $e->getMessage() ?? null
                ]);
            }
        }

    }



    // Generate a unique tracking ID
    public function generateTrackingId($id)
    {
        try {

            $randomString = Str::random();
            $time = time();
            return hash('sha256', $id . $randomString . $time);

        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            AppBaseController::generateSevenLog('SendNotificationController', 'App\Http\Controllers\SendNotificationController - '. (Auth::check() ? Auth::user()->name: 'Sin sesión').' -  Error: '.$e->getMessage());
        }

    }

}
