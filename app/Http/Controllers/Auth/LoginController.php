<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Http;
// use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Hautelook\Phpass\PasswordHash;
use App\Http\Controllers\AppBaseController;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try{
            $input = $request->all();
            /**
             * Se elimina la posición _token y el remember del array input,
             * ya que genera error al obtener la información del usuario por el email que se esta intentando loguear
             */
            unset($input["_token"]);
            unset($input["remember"]);
            // Obtiene los datos del usuario que se esta logueando a partir del correo
            $user = Auth::getProvider()->retrieveByCredentials($input);
            // Cadena de caracteres permitidos para la encriptación de los parámetros para el envio de la sesión en Joomla
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if(!empty($user["block"]) && $user["block"] == 1) {
                return redirect()->to('login')->withErrors(['email' => 'Este usuario se encuentra bloqueado. Para recuperar el acceso, comuníquese con el administrador del sistema.']);
            } else if((!empty($user["contract_start"]) && date("Y-m-d") < $user["contract_start"]) || (!empty($user["contract_end"]) && date("Y-m-d") > $user["contract_end"])) {
                return redirect()->to('login')->withErrors(['email' => 'Su fecha de contrato ha caducado. Por favor, contacte al administrador del sistema para verificar su estado de contrato.']);
            }
            // Si el usuario cuenta con password asignado en la tabla de usuarios, quiere decir que ya tiene una contraseña creada y habilitada
            if($user && $user["password"]) {
                // Intenta levantar la sesión en Laravel
                if(Auth::attempt(['email' => $input["email"], 'password' => $input["password"]])) {

                    return $this->authenticated($request, $user);

                } else {
                    return redirect()->to('login')->withErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
                }
            } else {
                // Si el usuario tiene el valor de username, es un usuario de integración con Joomla, además, si la integración con Joomla esta habilitada
                if($user && $user["username"] && config("app.integracion_sitio_joomla")) {
                    // Integracion de contraseñas
                    if($user["password"] == ""){
                        if($this->isCorrectJoomlaPassword($user["username"],$input["password"])){
                            // Actualiza la contraseña con el hashing de Laravel
                            User::where("id",$user["id"])->update(["password" => Hash::make($input["password"])]);

                            // Obtiene los datos del usuario que se esta logueando a partir del correo
                            $user = Auth::getProvider()->retrieveByCredentials($input);

                            // Loguea el usuario verificado previamente
                            Auth::login($user);

                            // Se redirige a la función de authenticated para posteriormente redirigirlo a la vista de módulos
                            return $this->authenticated($request, $user);
                        }
                        else{
                            return redirect()->to('login')->withErrors(['email' => 'Este usuario no tiene credenciales de acceso válidas, por favor de click en recuperar contraseña o comuníquese con el administrador.']);
                        }
                    }else{
                        return redirect()->to('login')->withErrors(['email' => 'Este usuario no tiene credenciales de acceso válidas, por favor de click en recuperar contraseña o comuníquese con el administrador.']);
                    }
                }else{
                    return redirect()->to('login')->withErrors(['email' => 'Este usuario no tiene credenciales de acceso válidas, por favor de click en recuperar contraseña o comuníquese con el administrador.']);

                }
            }
        }catch (\Exception $e) {
            $appBaseController = new AppBaseController();
            $appBaseController->generateSevenLog('login', 'App\Http\Controllers\Auth\loginController -  Error: '.$e->getMessage() . '. Linea: ' . $e->getLine());
            return redirect()->to('login')->withErrors(['server' => 'Ocurrió un problema al conectarse al servidor. Intente más tarde o contacte al soporte técnico.']);

        }        
    }

    /**
     * Verifica la contraseña de acceso del usuario obtenidas desde el sitio de Joomla
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function isCorrectJoomlaPassword(string $username,string $password) : bool{
            $userPassword = DB::connection('joomla')->table('users')->where('username',$username)->first()->password;

            // Valida si la contraseña tiene un hashing MD5 o Bcrypt
            if(strpos($userPassword,'$P$') === 0){
                $passwordHasher = new PasswordHash(10,true);
                $match = $passwordHasher->CheckPassword($password,$userPassword);
            }
            else{
                $userPassword = explode(":",$userPassword)[0];
                $match = $userPassword == md5($password);
            }
            return $match;
       
    }

    /**
     * Verifica las credenciales de acceso del usuario obtenidas desde el sitio de Joomla,
     * si son correctas, lo loguea, de lo contrario arroja un mensaje de credenciales inválidas
     *
     * @param Request $request
     * @return void
     */
    public function verificarCredencialesAcceso(Request $request)
    {
        $input = $request->all();
        // Obtiene las credenciales de acceso y las decodifica
        $input["email"] = base64_decode(explode(":", $_GET["e"])[0]);
        $input["password"] = base64_decode(explode(":", $_GET["p"])[0]);
        // Elimina las propiedades de credenciales de acceso encriptadas procedentes del sitio de Joomla
        unset($input["e"]);
        unset($input["p"]);
        // Obtiene la información del usuario según el correo electrónico recibido
        $user = Auth::getProvider()->retrieveByCredentials($input);
        // Valida si existe el usuario según el correo obtenido por parámetro desde el sitio de Joomla
        if($user) {
            // Actualiza el password del usuario en la base de datos local (VUV3) del sitio
            User::where("email", $input["email"])->update(["password" => Hash::make($input["password"])]);
            // Loguea el usuario verificado previamente
            Auth::login($user);
            // Se redirige a la función de authenticated para posteriormente redirigirlo a la vista de módulos
            return $this->authenticated($request, $user);
        } else {
            return redirect()->to('login')->withErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
        }
    }

    /**
     * Handle response after user authenticated
     *
     * @param Request $request
     * @param Auth $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        // return redirect()->intended();
        // Valida el rol del usuario y redirecciona al módulo correspondiente
        if(Auth::user()->hasRole('Ciudadano')) {
            return redirect(config("app.url")."/modules");
        } else {
            return redirect(config("app.url")."/dashboard");
        }
    }

    /**
     * Redirecciona a la vista principal dependiento del dominio
     *
     * @return URL de la vista principal
     */
    public function redirectTo()
    {
        //
    }

    /**
     * Cierre la sesión del usuario de la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Se obtienen los datos del usuario en sesión
        $user = Auth::user();

        // Cierra la sesión en laravel
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


            return redirect('/');

    }
}
