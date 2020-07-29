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

ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_RG01Controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
           
            return view('Mod_RG.RG01', compact('actividades', 'ultimo'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function store(Request $request)
    {
        
     //  dd($request->all());
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
        $periodo = explode('-', Input::get('date'));
        $ejercicio = $periodo[0];
        $periodo = $periodo[1];
        $errores = '';
        $arr = array(); 
        $cont = 0;  
        $filaInicio = 7;     
        if(\Storage::disk('balanzas')->has(Input::get('date').'.xls')){
             \Storage::disk('balanzas')->delete(Input::get('date').'.xls');
        }
         \Storage::disk('balanzas')->put(Input::get('date').'.xls',  \File::get($request->file('archivo')));       
        config(['excel.import.startRow' => $filaInicio ]);
        config(['excel.import.heading' => false ]);
         if($request->hasFile('archivo')){
            $path = public_path('balanzas/').Input::get('date').'.xls';
           // $data = Excel::load()->get();
            $data = Excel::selectSheetsByIndex(0)->load($path) //select first sheet
            ->limit(1500, 1) //limits rows on read
            ->limitColumns(8, 0) //limits columns on read
            ->ignoreEmpty(true)
            ->toArray();
            if(count($data) > 0){ 
               
                //1.-obtener las cuentas
                $buscaejercicio = DB::table('RPT_BalanzaComprobacion')->where("BC_Ejercicio", $ejercicio)->count();                
                if ($buscaejercicio > 0) {
                    $getCtas = DB::table('RPT_BalanzaComprobacion')->where("BC_Ejercicio", $ejercicio)
                        ->lists('BC_Cuenta_Id');                       
                    $buscaCta = true;
                }else {
                    $getCtas = [];
                    $buscaCta = false;
                }                
                DB::beginTransaction();
                //2.- revisar cta x cta
                foreach ($data as $value) { 
                  // dd(in_array($value[0], $getCtas));
                    if (strlen($value[0]) < 5 || is_null($value[0])) {
                        Session::flash('error',' Hay una cuenta invalida en la fila '.( $filaInicio + $cont));
                        break;    
                    }else{
                        //3.- buscar la cuenta
                        $saldoIni = 0;                      
                        if ($buscaCta) {
                            $buscaCta = in_array($value[0], $getCtas);
                        }
                        $saldoIni = ($value[2] * 1) + ($value[3] * 1);
                        $saldoFin = number_format(($value[6] * 1) + ($value[7] * 1),'2', '.',','); // saldo final del periodo segun la balanzaCom
                        $cargosAbonos = ($value[4] * 1) - ($value[5] * 1); //+cargos -abonos
                        $movIni = number_format(($saldoIni) + ($cargosAbonos),'2', '.',',');                   
                        $movText = ($value[4] * 1).'-'.($value[5] * 1).'='.$cargosAbonos;
                        
                        if ($saldoFin != $movIni) { 
                            
                           $cargosAbonos = ($value[4] * -1) + ($value[5] * 1);// -cargos +abonos
                           $movText = ($value[4] * -1).'+'.($value[5] * 1).'='.$cargosAbonos;
                        }                          
                        $fila = [   //hay 12 movimientos en la tabla correspondientes a los 12 periodos                      
                        'BC_Movimiento_'.$periodo => $cargosAbonos                 
                        ];
                        //Si el periodo es 1 entonces se captura Saldo Inicial de la cta
                        if ($periodo == '01') {                                                            
                            $fila['BC_Saldo_Inicial'] = $saldoIni;
                        }    
                        if ($buscaCta) { //si existe la cuenta se actuliza
                            DB::table('RPT_BalanzaComprobacion')
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)
                                ->update($fila);                                                                            
                        }else {                 
                            $fila['BC_Ejercicio'] = $ejercicio;
                            $fila['BC_Cuenta_Id'] = $value[0];
                            $fila['BC_Cuenta_Nombre'] = $value[1];                           
                            DB::table('RPT_BalanzaComprobacion')->insert($fila);
                        }
                        $cont++;

                        if ($saldoIni == 0) { //todos los periodos menos el primero
                           $cta =  DB::table('RPT_BalanzaComprobacion')
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)->first();
                            if (!is_null($cta)) { // si existe la cuenta                             
                                if (!is_null($cta->BC_Saldo_Inicial)) { // y tiene saldo inicial
                                    $elem = collect($cta); //lo hacemos colleccion para poder invocar los periodos                                                         
                                    $suma = $cta->BC_Saldo_Inicial; //la suma se inicializa en saldo inicial
                                    for ($k=1; $k <= (int)$periodo ; $k++) { // se suman todos los movimientos del 1 al periodo actual
                                      $peryodo = ($k < 10) ? '0'.$k : ''.$k;// los periodos tienen un formato a 2 numeros, asi que a los menores a 10 se les antepone un 0
                                      $movimiento = $elem['BC_Movimiento_'.$peryodo];  
                                      $suma += (is_null($movimiento)) ? 0 : $movimiento;//sumamos periodo/movimiento
                                    }
                                    
                                    if (number_format($suma,'2', '.',',') != $saldoFin) { //si el saldo final de la balanza y el calculado es diferente                                       
                                       
                                        $errores = 'Cuenta "'.$value[0].'" tiene diferencia en saldo final. '.$movText;
                                        break;
                                    }
                                }//NO HAY SALDO INICIAL CAPTURADO
                            } 
                        }
                    }
                    
                }//fin foreach

                if($errores == ''){                  
                    Session::flash('mensaje',$cont.' filas guardadas !!.');
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
}
