<?php
namespace App\Http\Controllers;

use App;
use App\Helpers\AppHelper;
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
class Mod_RG03Controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $periodos = DB::select("select 
                                    BC_Ejercicio,	
                                    sum(BC_Movimiento_01) m_01,	
                                    sum(BC_Movimiento_02)m_02,	
                                    sum(BC_Movimiento_03)m_03,	
                                    sum(BC_Movimiento_04)m_04, 
                                    sum(BC_Movimiento_05)m_05,	
                                    sum(BC_Movimiento_06)m_06,	
                                    sum(BC_Movimiento_07)m_07,	
                                    sum(BC_Movimiento_08)m_08,	
                                    sum(BC_Movimiento_09)m_09, 
                                    sum(BC_Movimiento_10)m_10,	
                                    sum(BC_Movimiento_11)m_11,	
                                    sum(BC_Movimiento_12)m_12 from RPT_BalanzaComprobacion 
                                    group by BC_Ejercicio");
                        
            $cbo_periodos = [];
            foreach ($periodos as  $value) {
                $value = collect($value);

                for ($i=1; $i <= 12; $i++) { 
                    if ($i < 10) {
                        $index = '0'.$i;
                    }else {
                        $index = $i;
                    }
                    if (!is_null($value["m_".$index])) {
                       $cbo_periodos[] = $value["BC_Ejercicio"].'-'.$index;
                    }
                }
            }
            return view('Mod_RG.RG03', compact('actividades', 'ultimo', 'cbo_periodos'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function reporte(Request $request)
    {
        
     //  dd($request->all());
     
        $periodo = explode('-', Input::get('cbo_periodo'));
       // dd(Input::all() );
        
        $ejercicio = $periodo[0];
        $periodo = $periodo[1];
        $data = DB::select("
                                SELECT TOP (1000) [BC_Id]
                                ,[BC_Ejercicio]
                                ,[BC_Cuenta_Id]
                                ,[BC_Cuenta_Nombre]
                                ,[BC_Saldo_Inicial]
                                ,[BC_Saldo_Final]
                                ,[BC_Movimiento_".$periodo."]  as movimiento   
                                ,[RGC_hoja]
                                ,[RGC_tabla_titulo]
                                ,[RGC_tabla_linea]
                            FROM [itekniaDB].[dbo].[RPT_BalanzaComprobacion] bg
                            inner join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
                            WHERE [BC_Ejercicio] = ?
                            order by RGC_hoja, RGC_tabla_linea
                                    ",[$ejercicio]);
        $hoja1 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 1;
        });
        $hoja2 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 2;
        });
        // INICIA ER - Hoja2
        $grupos_hoja2 = array_unique(array_pluck($hoja2, 'RGC_tabla_titulo'));      
        $totales_hoja2 = [];
        $acumulados_hoja2 = [];
        $acumuladosxcta = [];
        $helper = AppHelper::instance();
        foreach ($grupos_hoja2 as $key => $val) {
            $items = array_where($hoja2, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja2 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);               
               $sum_acumulado += ($sum > 0) ? $sum : 0;
               $acumuladosxcta[$value->BC_Cuenta_Id] = ($sum > 0) ? $sum : 0;
            }

            $acumulados_hoja2 [$val] = $sum_acumulado;
        }
       // INICIA EC - Hoja3
       $mo = (is_null(Input::get('mo')))?0:Input::get('mo');
       $indirectos = (is_null(Input::get('indirectos')))?0:Input::get('indirectos');
       

        $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
        $nombrePeriodo = $helper->getNombrePeriodo($periodo);
        return view('Mod_RG.RG03_reporte', 
        compact('actividades', 'ultimo', 'data', 'ejercicio', 'totales_hoja2', 'acumulados_hoja2',
        'indirectos', 'mo' ,'nombrePeriodo', 'acumuladosxcta', 'hoja1', 'hoja2', 'periodo'));
    }
    
}
