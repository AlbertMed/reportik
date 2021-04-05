<?php
namespace App\Http\Controllers;

use App;
use App\LOG;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_RG01Controller extends Controller
{
    public function index($sociedad = null)
    {
        //dd(Input::all());
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $catalogo = DB::select("SELECT RPT_RG_ConfiguracionTabla.RGC_mostrar FROM RPT_RG_ConfiguracionTabla
                                WHERE RPT_RG_ConfiguracionTabla.RGC_mostrar > 0
                                GROUP BY RPT_RG_ConfiguracionTabla.RGC_mostrar 
                                ORDER BY RPT_RG_ConfiguracionTabla.RGC_mostrar DESC");
            $catalogo = array_pluck($catalogo, 'RGC_mostrar');
            if (is_null($sociedad)) {
                if (Input::has('text_selUno')) {
                    $sociedad = Input::get('text_selUno');
                }else{
                    if (Session::has('sociedad_rg')) {
                        $sociedad = Session::pull('sociedad_rg');
                    }
                }
                
            }
            return view('Mod_RG.RG01', compact('actividades', 'ultimo', 'catalogo', 'sociedad'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function store(Request $request)
    {

        //dd($request->all());
        $sociedad = Input::get('sociedad');
        Session::put('sociedad_rg', $sociedad);
        $validator = Validator::make($request->all(), [
         'archivo' => 'max:5000',
        ]);       
            $validator->after(function ($validator) use ($request){
        if($this->checkExcelFile($request->file('archivo')->getClientOriginalExtension()) == false) {
            //return validator with error by file input name
            $validator->errors()->add('archivo', 'El archivo debe ser de tipo:  xls');
        }
    });
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }
        
        $tableName = DB::table('RPT_Sociedades')
            ->where('SOC_Nombre', Input::get('sociedad'))
            ->where('SOC_Reporte', '01 CAPTURA DE HISTORICO')
            ->value('SOC_AUX_DB');
        $periodo = explode('-', Input::get('date'));
        $ejercicio = $periodo[0];
        $periodo = $periodo[1];
        $errores = '';
        $arr = array(); 
        $cont = 1;  
        $filaInicio = 7;     
       /*
        if(\Storage::disk('balanzas')->has(Input::get('date').'.xls')){
             \Storage::disk('balanzas')->delete(Input::get('date').'.xls');
        }
         \Storage::disk('balanzas')->put(Input::get('date').'.xls',\File::get($request->file('archivo')));       
         */
        
         if($request->hasFile('archivo')){
            $path = $request->file('archivo')->getRealPath();            
            //config(['excel.import.startRow' => $filaInicio]);
            config(['excel.import.heading' => false]);
            
            //$path = public_path('balanzas/').Input::get('date').'.xls';
            //$data = Excel::load()->get();
            
            $data = Excel::selectSheetsByIndex(0)->load($path) //select first sheet            
            //->limit(2000, 1) //limits rows on read
            ->limitColumns(8, 0) //limits columns on read
            ->ignoreEmpty(false)
            ->toArray();
            $fcontpaq = $data[1][0]; //segunda fila, primera columna
            array_splice($data, 0, $filaInicio); //elimina tantos elementos del inicio del array
            //dd($data[0]);
            if(count($data) > 0){ 
                DB::beginTransaction();
                //1.-obtener las cuentas
                $buscaejercicio = DB::table($tableName)->where("BC_Ejercicio", $ejercicio)->count();                
                if ($buscaejercicio > 0) {
                    $fila = [   //hay 12 movimientos en la tabla correspondientes a los 12 periodos                      
                        'BC_Movimiento_'.$periodo => 0                 
                    ];
                    if ($periodo == '01') {                                                            
                        $fila['BC_Saldo_Inicial'] = 0;
                    }    
                    DB::table($tableName)
                    ->where("BC_Ejercicio", $ejercicio)
                    ->update($fila);                    
                    
                    $getCtas = DB::table($tableName)->where("BC_Ejercicio", $ejercicio)
                    ->lists('BC_Cuenta_Id');                       
                    $buscaCta = true;
                }else {
                    $getCtas = [];
                    $buscaCta = false;
                }                
                
                
                $fila_catalogo = [                         
                    'CAT_version' => Input::get('catalogo')           
                ];    
                $exist = DB::table('RPT_RG_CatalogoVersionCuentas')
                ->where('CAT_periodo', Input::get('date'))
                ->count();
                if ($exist == 0) {
                    $fila_catalogo['CAT_periodo'] = Input::get('date');
                    DB::table('RPT_RG_CatalogoVersionCuentas')->insert($fila_catalogo);                
                } else if($exist > 0){//si existe 
                    DB::table('RPT_RG_CatalogoVersionCuentas')
                    ->where('CAT_periodo', Input::get('date'))
                    ->update($fila_catalogo);
                }


                $fila_fechaActualizado = [
                    'RGF_FechaActualizado' => date('Ymd h:m:s'),
                    'RGF_FechaBCContpaq' => $fcontpaq,       
                    'RGF_Sociedad' => $sociedad       
                ];    
                $exist_fecha = DB::table('RPT_RG_FechasActualizadoBalanza')
                ->where('RGF_EjercicioPeriodo', Input::get('date'))
                ->where('RGF_Sociedad', $sociedad)
                ->count();
                if ($exist_fecha == 0) {
                    $fila_fechaActualizado['RGF_EjercicioPeriodo'] = Input::get('date');                    
                    DB::table('RPT_RG_FechasActualizadoBalanza')->insert($fila_fechaActualizado);                
                } else if($exist_fecha > 0){//si existe 
                    DB::table('RPT_RG_FechasActualizadoBalanza')
                    ->where('RGF_EjercicioPeriodo', Input::get('date'))
                    ->update($fila_fechaActualizado);
                }
                
               // dd($data[0]);
                //2.- revisar cta x cta                
                foreach ($data as $value) { 
                    $longitud_cuenta = strlen($value[0]);
                    if ($longitud_cuenta == 0 || is_null($value[0]) || $value[0] =='') {
                    Session::flash('error',' Hay una cuenta invalida en la fila '.( $filaInicio + $cont));
                    break;    
                }else if($longitud_cuenta < 4 && $longitud_cuenta > 0 ){
                    //cuenta invalida no reportar
                }else{
                    //3.- buscar la cuenta
                    $saldoIni = 0;                      
                    $v = trim($value[0]);       
                    if ($buscaCta) {
                        $getCtas = array_map('trim', $getCtas);
                        $conta = array_where($getCtas, function ($key, $value) use ($v){                                
                            return trim($value) == $v;
                            });
                            $buscaCta = (count($conta) > 0)?true:false;                           
                        }
                        //la info de excel se limita a 2 decimales para evitar errores en operaciones
                        $val2 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[2]?: 0) ,'2')));
                        $val3 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[3]?: 0) ,'2')));
                        $val4 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[4]?: 0) ,'2')));
                        $val5 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[5]?: 0) ,'2')));
                        $val6 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[6]?: 0) ,'2')));
                        $val7 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[7]?: 0) ,'2')));
                        
                        $saldoIni = $val2 - $val3; //deudor - acreedor                        
                        $saldoFin = $val6 - $val7; // saldo final del periodo segun la balanzaCom:
                        //clock($v, $value[0]);
                        if (strpos($v, '108-001-') === false) { //si la cuenta no inicia con 108-001-
                            $cargosAbonos = $val4 - $val5; //+cargos -abonos
                        } else {
                            $cargosAbonos = $val4; //soloo cargos
                           
                        }
                                                   
                        $movIni = ($saldoIni * 1) + ($cargosAbonos * 1);                   
                        $movText = $val4.'-'.$val5.'='.$cargosAbonos;
                        
                        //if(false){//if ($saldoFin != $movIni) {                             
                          // $cargosAbonos = ($value[4] * -1) + ($value[5] * 1);// -cargos +abonos
                          // $movText = ($value[4] * -1).'+'.($value[5] * 1).'='.$cargosAbonos;
                        //}                          
                        $fila = [   //hay 12 movimientos en la tabla correspondientes a los 12 periodos                      
                        'BC_Movimiento_'.$periodo => $cargosAbonos                 
                        ];
                        //Si el periodo es 1 entonces se captura Saldo Inicial de la cta
                        if ($periodo == '01') {                                                            
                            $fila['BC_Saldo_Inicial'] = $saldoIni;
                        }

                        $exist = 1;
                        $fila['BC_Fecha_Actualizado'] = date('Ymd h:m:s');
                        if ($buscaCta == false) { 
                            $fila['BC_Ejercicio'] = $ejercicio;
                            $fila['BC_Cuenta_Id'] = trim($value[0]);
                            $fila['BC_Cuenta_Nombre'] = $value[1];
                            $exist = DB::table($tableName)
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)
                                ->count();
                            if ($exist == 0) {
                                //ponemos el saldo inicial en cero cuando no sea el periodo uno
                                if ($periodo != '01' && !array_key_exists('BC_Saldo_Inicial', $fila)) {
                                    $fila['BC_Saldo_Inicial'] = 0;
                                }  
                                if (!array_key_exists('BC_Saldo_Final', $fila)) {
                                    $fila['BC_Saldo_Final'] = 0;
                                }  
                               //cargamos los movimientos en cero con el for siguiente:                               
                                for ($k = 1; $k <= 12; $k++) { // los 12 periodos
                                    $peryodo = ($k < 10) ? '0' . $k : '' . $k; // los periodos tienen un formato a 2 numeros, asi que a los menores a 10 se les antepone un 0                                           
                                    $llave_p = 'BC_Movimiento_' . $peryodo;
                                    if ($periodo != $peryodo && !array_key_exists($llave_p, $fila)) {                                               
                                        $fila[$llave_p] = 0;
                                    }                                             
                                }                                                           
                                DB::table($tableName)->insert($fila);
                                $exist = 1;                                
                            }                                                                         
                        }
                        //dd($fila);
                        if ($exist > 0) {//si existe la cuenta se actuliza
                             DB::table($tableName)
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)
                                ->update($fila);
                        }    
                        $cont++;

                        if (false){//if ($saldoIni == 0 && $periodo <> '01') { //todos los periodos menos el primero
                           $cta = DB::table($tableName)
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)->first();
                            if (!is_null($cta)) { // si existe la cuenta                             
                                if (!is_null($cta->BC_Saldo_Inicial) && $cta->BC_Saldo_Inicial != 0) { // y tiene saldo inicial
                                    $elem = collect($cta); //lo hacemos colleccion para poder invocar los periodos                                                         
                                    $suma = $cta->BC_Saldo_Inicial; //la suma se inicializa en saldo inicial
                                    for ($k=1; $k <= (int)$periodo ; $k++) { // se suman todos los movimientos del 1 al periodo actual
                                      $peryodo = ($k < 10) ? '0'.$k : ''.$k;// los periodos tienen un formato a 2 numeros, asi que a los menores a 10 se les antepone un 0
                                      $movimiento = $elem['BC_Movimiento_'.$peryodo];  
                                      $suma += (is_null($movimiento)) ? 0 : $movimiento;//sumamos periodo/movimiento
                                    }
                                    
                                    if (number_format($suma,'2') != number_format($saldoFin,'2')) { //si el saldo final de la balanza y el calculado es diferente                                       
                                       
                                        $errores = 'Cuenta "'.$value[0].'" tiene diferencia en saldo final. '.$movText;
                                        break;
                                    }
                                }//NO HAY SALDO INICIAL CAPTURADO
                            } 
                        }
                    }//else cuentas validas
                    
                }//fin foreach

                if($errores == ''){                  
                    Session::flash('mensaje',($cont-1).' filas guardadas !!.');
                    DB::commit();                    
                }else {
                    DB::rollBack();
                    $log = LOG::firstOrNew(
                        ['LOG_user' => Auth::user()->nomina,
                        'LOG_tipo' => 'error',
                        'LOG_descripcion' => $errores,
                        'LOG_cod_error' => 'RG01-SALDOFIN']
                    );
                    $log->LOG_fecha = date("Y-m-d H:i:s");
                    $log->save();

                    Session::flash('error', $errores);
                }
            }else {
               Session::flash('error','No encontramos las cuentas, revisa que empiezen en la fila #8, Columna A  
               y que esten en en la primer hoja de tu archivo xls!!.');
            }
        }else {
            Session::flash('error','No recibimos tu archivo!!.');
        }
           return redirect()
                ->back();
    }
     

function checkExcelFile($file_ext){
    $valid=array(
        'xls' // add your extensions here.
    );        
  
    return in_array($file_ext,$valid) ? true : false;
} 

public function checkctas(Request $request){
    $periodo = explode('-', Input::get('date'));
    $ejercicio = $periodo[0];
    $periodo = $periodo[1];

    $tableName = DB::table('RPT_Sociedades')
    ->where('SOC_Nombre', Input::get('sociedad'))
    ->where('SOC_Reporte', '01 CAPTURA DE HISTORICO')
    ->value('SOC_AUX_DB');
        //dd($tableName);
     $buscaejercicio = DB::table($tableName)
     ->where("BC_Ejercicio", $ejercicio)
     ->groupBy('BC_Movimiento_' . $periodo)
     ->having('BC_Movimiento_'.$periodo, '>', 0)     
     ->count();                
                 $respuesta = false;
     if ($buscaejercicio > 0) {
                    $respuesta = true;
                }
                return compact('respuesta');

}
}
