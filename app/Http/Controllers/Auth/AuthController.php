<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Hash;
use Input;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Session;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

//entre comillas la ruta a la que deseas redireccionar
    protected $redirectTo = 'home';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function postLogin(Request $request)
    {
            try {
                //dd($request->all());
                $rpt_user = DB::table('RPT_Usuarios')
                    ->where('nomina', $request->get('id'))
                    ->first();
                $rpt_exist = count($rpt_user);
                //revisar si esta el usuario en muliix
                //->where('USU_Contrasenia', $request->get('password'))
                $muliix_user = DB::table('usuarios')
                ->join('Empleados', 'EMP_EmpleadoId','=', 'USU_EMP_EmpleadoId')
                ->where('USU_Nombre', $request->get('id'))
                ->where('USU_Activo', 1)
                ->where('EMP_Activo', 1)
                ->select('USU_Nombre', 'USU_Contrasenia', 'USU_Activo', 'EMP_Nombre', 'EMP_PrimerApellido')
                ->first();
                $muliix_exist = count($muliix_user);
                
                if ($muliix_exist == 1 && $rpt_exist == 0) {
                    //se da de alta el usuario en RPT
                    $nuevoUser = new User;
                    $nuevoUser->name = $muliix_user->EMP_Nombre. ' '. $muliix_user->EMP_PrimerApellido;
                    $nuevoUser->nomina = $request->get('id');
                    $nuevoUser->status = 1;
                    $nuevoUser->password = Hash::make($request->get('password'));
                    $nuevoUser->save();
                } else if($muliix_exist == 1 && $rpt_exist == 1){
                    if (!Hash::check($muliix_user->USU_Contrasenia, $rpt_user->password)) {
                        $password = Hash::make($muliix_user->USU_Contrasenia);
                        DB::table('dbo.RPT_Usuarios')
                            ->where('nomina', $request->get('id'))
                            ->update(['password' => $password]);
                    }
                }
                
                if (Auth::attempt(['nomina' => $request->get('id'), 
                                    'password'   => $request->get('password')])) {
                    return redirect()->intended('home');
                }else{  
                    $mensaje='';
                        if ($muliix_exist == 0) {
                            $mensaje = 'Empleado no activo en Muliix';
                        } else {
                            $mensaje = 'Usuario/contraseña inválidos';
                        }                        
                        return redirect($this->loginPath())
                        ->withInput($request->only($this->loginUsername(), 'remember'))
                        ->withErrors($mensaje);
                }
                
            } catch(\Exception $e) {
                echo ''. $e->getMessage();
            }
    }
   
}
