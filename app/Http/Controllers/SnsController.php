<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use DB;
use App\Http\Controllers\AppBaseController;
use Exception;


class SnsController extends AppBaseController
{
    public function handle(Request $request)
    {

        $messageType = $request->header('x-amz-sns-message-type');
        $message = json_decode($request->getContent(), true);

        if ($messageType === 'SubscriptionConfirmation') {
            $this->confirmSubscription($message);
        } elseif ($messageType === 'Notification') {
            $this->processNotification($message);
        }

        return response()->json(['message' => 'Processed successfully'], 200);
    }

    protected function confirmSubscription($message)
    {
        $client = new Client();
        $response = $client->get($message['SubscribeURL']);
        
        if ($response->getStatusCode() == 200) {
            Log::info('SNS subscription confirmed.');
        } else {
            Log::error('Failed to confirm SNS subscription.');
        }
    }

     /**
     * Función para actualizar el estado del correo a rebotado
     *
     * @author Johan David Velasco Rios - 04/06/2024
     * @version 1.0.0
     *
     * @param $message = Trae toda la información del rebote - array
     *
     */
    protected function processNotification(Request $request)
    {
        $message = $request->all();
        //Accede a la data del rebote
        $mainMessage = $message["Message"];

        try {
            //Convierne del json la data
            $varPositions = json_decode($mainMessage, true);

            //obtiene el mensaje del porque se retorno el correo
            $message_englis = json_encode($varPositions["bounce"]["bounceType"]) != "Transient" ? json_encode($varPositions["bounce"]["bouncedRecipients"][0]["diagnosticCode"], true) : "message_transient";

            // Expresión regular para encontrar el primer mensaje después de "550-5.1.1"
            $pattern = '/550-5\.1\.1 ([^.]*\.)/';

            // Aplicar la expresión regular
            if (preg_match($pattern, $message_englis, $matches)) {
                // Extraer el mensaje encontrado
                $message = $matches[1];
            } else {
                $message = $message_englis;
            }

            // Variable para almacenar el valor de X-Endpoint
            $custom_id_value = null;

            //Recorre la data obtenida del robete de AWS y obtiene el X-Custom-ID
            foreach ($varPositions["mail"]["headers"] as $item) {
                if ($item['name'] === 'X-Custom-ID') {
                    $custom_id_value = $item['value'];
                    break;
                }
            }

            $mensajeCliente = "";

            switch (true) {
                case strpos($message, 'Amazon SES has suppressed') !== false:
                    // Acción para 'Amazon SES has suppressed'
                    $mensajeCliente = "Nuestro sistema de envío de correos ha detectado que este correo electrónico ha presentado recientes rebotes, lo cual indica que la dirección no es válida.";
                    break;
            
                case strpos($message, 'smtp;550 5.7.1 TRANSPORT.RULES.RejectMessage') !== false:
                    // Acción para 'smtp;550 5.7.1 TRANSPORT.RULES.RejectMessage'
                    $mensajeCliente = "La notificación fue rechazada debido a las políticas de la organización del destinatario. Por favor, póngase en contacto con el destinatario y solicítele que agregue la dirección 'no-reply@intraweb.com.co' a su lista de remitentes confiables.";
                    break;
            
                case strpos($message, 'The email account that you tried to reach does not exist') !== false:
                    // Acción para 'The email account that you tried to reach does not exist'
                    $mensajeCliente = "La cuenta de correo electrónico a la que intento enviar la notificación no existe";
                    break;
            
                case strpos($message, 'smtp; 554 4.4.7 Message expired') !== false:
                    // Acción para 'No such user here'
                    $mensajeCliente = "La notificación no fue entregada ya que supero el limite de tiempo.";
                    break;

                case strpos($message, 'No such user here') !== false:
                    // Acción para 'No such user here'
                    $mensajeCliente = "La cuenta de correo electrónico a la que intento enviar la notificación no existe";
                    break;
            
                case strpos($message, 'mailbox full') !== false:
                    // Acción para 'mailbox full'
                    $mensajeCliente = "La notificación no pudo ser entregada porque el destinatario tiene su bandeja de entrada llena.";
                    break;
                    
                case strpos($message, 'message_transient') !== false:
                    // Acción para 'mailbox full'
                    $mensajeCliente = "La bandeja de entrada está llena o hay problemas temporales con el servidor receptor.";
                    break;
            
                default:
                    // Acción por defecto si no se encuentra ninguna coincidencia
                    $mensajeCliente = "La notificación no pudo ser entregada. Por favor, comuníquese con el administrador para verificar la causa del problema.";
                    break;
            }

            //Valida que la variable $custom_id_value sea diferente de vacio
            if (!empty($custom_id_value)) {
                //busca el correo por medio del id unico y actualiza el estado
                DB::table('notificaciones_mail_intraweb')->where('id_mail',$custom_id_value)->update([
                    "estado_notificacion" => "Rebote",
                    "respuesta_servidor_notificacion" => $message,
                    "mensaje_cliente" => $mensajeCliente
                ]);

                Log::info("Bounce received for email: " . json_encode($varPositions["bounce"],true));
                
            }else{
                throw new Exception("No se encontró el encabezado 'X-Custom-ID' en los headers del correo.", 1);
            }

        } catch (\Illuminate\Database\QueryException $error) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('module_name', 'app\Http\Controllers\SnSController -  Error: '.$error->getMessage() . '. JsonData: ' . $mainMessage);
            // Retorna mensaje de error de base de datos
            return $this->sendSuccess(config('constants.support_message'), 'info');
        } catch (\Exception $e) {
            // Devuelve los cambios realizados
            DB::rollback();
            // Inserta el error en el registro de log
            $this->generateSevenLog('SnSController', 'app\Http\Controllers\SnSController -  Error: ' . $e->getMessage() . '. JsonData: ' . $mainMessage);

        }

    }

}
