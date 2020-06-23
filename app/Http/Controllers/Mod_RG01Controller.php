<?php
namespace App\Http\Controllers;

use App;
use App\Helpers\BalanzaImport;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
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
        $errores = 0;
        $arr = array(); 
        $cont = 0;       
         \Storage::disk('balanzas')->put(Input::get('date').'.xls',  \File::get($request->file('archivo')));
        config(['excel.import.startRow' => 62 ]);
        config(['excel.import.heading' => false ]);
        
         if($request->hasFile('archivo')){
            $path = public_path('balanzas/').Input::get('date').'.xls';
           // $data = Excel::load()->get();
            $data = Excel::selectSheets('balanzas itek')->load($path)
            ->limit(1500, 1) //limits rows on read
            ->limitColumns(8, 0) //limits columns on read
            ->ignoreEmpty(true)
            ->toArray();
            if(count($data) > 0){
                $buscaejercicio = DB::table('RPT_BalanzaComprobacion')->where("BC_Ejercicio", $ejercicio)->count();
                
                DB::beginTransaction();
                foreach ($data as $value) { 
                  // dd($value);
                    if (strlen($value[0]) < 5 || is_null($value[0])) {
                        Session::flash('error',' Hay una cuenta invalida en la fila '.( 62 + $cont));
                        break;    
                    }
                    
                    if ($buscaejercicio > 0) {
                          DB::table('RPT_BalanzaComprobacion')
                            ->where('BC_Cuenta_Id', $value[0])
                            ->where('BC_Ejercicio', $ejercicio)
                            ->update(
                                [                                                    
                                'BC_Saldo_Inicial' => ($value[2] * 1) + ($value[3] * 1),
                                'BC_Movimiento_'.$periodo => ($value[4] * 1) - ($value[5] * 1),
                                'BC_Saldo_Final' => ($value[6] * 1) + ($value[7] * 1),                       
                                ]
                            );
                            $cont++;
                    }else {
                        DB::table('RPT_BalanzaComprobacion')->insert(
                            [                        
                        'BC_Ejercicio' => $ejercicio,
                        'BC_Cuenta_Id' => $value[0],
                        'BC_Cuenta_Nombre' => $value[1],
                        'BC_Saldo_Inicial' => ($value[2] * 1) + ($value[3] * 1),
                        'BC_Movimiento_'.$periodo => ($value[4] * 1) - ($value[5] * 1),
                        'BC_Saldo_Final' => ($value[6] * 1) + ($value[7] * 1),                       
                        ]
                        );
                        $cont++;
                    }
                    
                    if (false) {//saldoInicial + Movimiento != SaldoFinal
                        # code...//hacer tabla de log
                        DB::table('RPT_Log')->insert(
                            ['LOG_fecha' => date("Y-m-d"),
                            'LOG_user' => Auth::user()->nomina,
                            'LOG_tipo' => 'error',
                            'LOG_descripcion' => 'Cuenta '.$value[0].' tiene diferencia en saldo final.']
                        );
                        $errores = 1;
                        break;
                    }
                    
                }

                if($errores == 0){                  
                    Session::flash('mensaje',$cont.' filas guardadas !!.');
                    DB::commit();                    
                }else {
                    DB::rollBack();
                    Session::flash('error','Error en saldoFinal, Cuenta #'.$value[0]. '.');
                }
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
