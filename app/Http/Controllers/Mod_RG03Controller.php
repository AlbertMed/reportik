<?php
namespace App\Http\Controllers;

use App;
use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
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
        $version = DB::table('RPT_RG_CatalogoVersionCuentas')
                        ->where('CAT_periodo', $periodo)                        
                        ->value('CAT_version');
        $version = (is_null($version)) ? 0 : $version;
        $ejercicio = $periodo[0];
        $periodo = $periodo[1];
        $data = DB::select("SELECT 
                                [BC_Ejercicio]
                                ,[BC_Cuenta_Id]
                                ,[BC_Cuenta_Nombre]
                                ,[BC_Saldo_Inicial]
                                ,[BC_Saldo_Final]
                                ,[BC_Movimiento_".$periodo."]  as movimiento   
                                ,[RGC_hoja]
                                ,[RGC_tabla_titulo]
                                ,[RGC_tabla_linea]
                                ,[RGC_multiplica]
                            FROM RPT_BalanzaComprobacion bg
                            LEFT join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
                            WHERE [BC_Ejercicio] = ?
                            AND [BC_Movimiento_".$periodo."] IS NOT NULL
                            AND (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
                            order by RGC_hoja, RGC_tabla_linea
                                    ",[$ejercicio, $version]);

        $hoja1 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 1;
        });
        $hoja2 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 2;
        });
        $hoja3 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 3 && $value->RGC_tabla_linea <= 7;
        });
        $data_inventarios = DB::select("SELECT RPT_InventarioContable.*, ct.*, Localidades.LOC_CodigoLocalidad
                            FROM [itekniaDB].[dbo].[RPT_InventarioContable]
                            inner join  RPT_RG_ConfiguracionTabla ct on ct.RGC_BC_Cuenta_Id = IC_CLAVE
                            left join Localidades on LOC_LocalidadId = IC_CLAVE
                    where IC_periodo = ? and IC_Ejercicio = ? and ct.RGC_hoja = '3' and RGC_tabla_linea > 7
                    ORDER BY RGC_tabla_linea",[$periodo, $ejercicio]);
        $hoja5 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 5;
        });
        $hoja6 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 6;
        });
        $hoja7 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 7;
        });
        $hoja8 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 8;
        });
        //INICIA BC Hoja 1
        $grupos_hoja1 = array_unique(array_pluck($hoja1, 'RGC_tabla_titulo'));           
        $acumuladosxcta_hoja1 = [];
        $helper = AppHelper::instance();
        foreach ($grupos_hoja1 as $key => $val) {
            $items = array_where($hoja1, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });                       
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
               if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }                                
               $acumuladosxcta_hoja1[$value->BC_Cuenta_Id] = $sum * $value->RGC_multiplica;
            }        
        }
        // INICIA ER - Hoja2
        $grupos_hoja2 = array_unique(array_pluck($hoja2, 'RGC_tabla_titulo'));      
        $totales_hoja2 = [];
        $acumulados_hoja2 = [];
        $acumuladosxcta = [];
        $utilidadEjercicio = 0; $ue_ingresos = 0; $ue_gastos_costos = 0;
        foreach ($grupos_hoja2 as $key => $val) {
            $items = array_where($hoja2, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja2 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);   
               if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }                                           
               $sum_acumulado += $sum * $value->RGC_multiplica;
               $acumuladosxcta[$value->BC_Cuenta_Id] = $sum * $value->RGC_multiplica;
            }

            $acumulados_hoja2 [$val] = $sum_acumulado;
            if (strpos($val, 'INGRESO') === false) {
                $ue_gastos_costos += $sum_acumulado;
            } else {
                $ue_ingresos += $sum_acumulado;
            }            
        }        
         $utilidadEjercicio = $ue_ingresos - $ue_gastos_costos;
       // INICIA EC - Hoja3
       $input_mo = (is_null(Input::get('mo'))||Input::get('mo') == '')?0:Input::get('mo');
       $input_indirectos = (is_null(Input::get('indirectos'))|| Input::get('indirectos') == '')?0:Input::get('indirectos');
       //Hoja 4 usa $data_inventarios
       //INICIA Gtos Fab - Hoja 5
        $grupos_hoja5 = array_unique(array_pluck($hoja5, 'RGC_tabla_titulo'));  
        $totales_hoja5 = [];
        $acumulados_hoja5 = [];
        $acumuladosxcta_hoja5 = [];        
        foreach ($grupos_hoja5 as $key => $val) {
            $items = array_where($hoja5, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja5 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);   
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }            
               $sum_acumulado += $sum * $value->RGC_multiplica;
               $acumuladosxcta_hoja5[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
            }
            
            $acumulados_hoja5 [$val] = $sum_acumulado;
        }
       //INICIA Gtos Admon - Hoja 6
        $grupos_hoja6 = array_unique(array_pluck($hoja6, 'RGC_tabla_titulo'));  
        $totales_hoja6 = [];
        $acumulados_hoja6 = [];
        $acumuladosxcta_hoja6 = [];        
        foreach ($grupos_hoja6 as $key => $val) {
            $items = array_where($hoja6, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja6 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }              
               $sum_acumulado += $sum * $value->RGC_multiplica;
               $acumuladosxcta_hoja6[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
            }
            
            $acumulados_hoja6 [$val] = $sum_acumulado;
        }
        //INICIA Gtos Admon - Hoja 7
        $grupos_hoja7 = array_unique(array_pluck($hoja7, 'RGC_tabla_titulo'));  
        $totales_hoja7 = [];
        $acumulados_hoja7 = [];
        $acumuladosxcta_hoja7 = [];        
        foreach ($grupos_hoja7 as $key => $val) {
            $items = array_where($hoja7, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja7 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }               
               $sum_acumulado += $sum * $value->RGC_multiplica;
               $acumuladosxcta_hoja7[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
            }
            
            $acumulados_hoja7 [$val] = $sum_acumulado;
        }
        //INICIA Gtos Admon - Hoja 7
        $grupos_hoja8 = array_unique(array_pluck($hoja8, 'RGC_tabla_titulo'));  
        $totales_hoja8 = [];
        $acumulados_hoja8 = [];
        $acumuladosxcta_hoja8 = [];        
        foreach ($grupos_hoja8 as $key => $val) {
            $items = array_where($hoja8, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja8 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }               
               $sum_acumulado += $sum * $value->RGC_multiplica;
               $acumuladosxcta_hoja8[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
            }
            
            $acumulados_hoja8 [$val] = $sum_acumulado;
        }
      //  dd();    
       DB::table('RPT_RG_Ajustes') // Guardamos los valores
            ->where('AJU_Id', 'mo')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $input_mo, 'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
       DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'ind')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $input_indirectos, 'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
        // INICIA INVENTARIOS - Hoja3
       $inv_Inicial = $helper->getInv($periodo, $ejercicio, true);
       $inv_Final = $helper->getInv($periodo, $ejercicio, false);
       $ctas_hoja3 = [];
       $grupos_hoja3 = ['COMPRAS NETAS', 'GASTOS IND', 'MO'];
       foreach ($grupos_hoja3 as $key => $val) {
           $items = array_where($hoja3, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            }); 
            $ctas_hoja3 [$val] = array_sum(array_pluck($items, 'movimiento')); 
       }
       $mp_ini = $inv_Inicial['mp'];
       $pp_ini = $inv_Inicial['pp'];
       $pt_ini = $inv_Inicial['pt'];     
       $mp_fin = $inv_Final['mp'];
       $pp_fin = $inv_Final['pp'];
       $pt_fin = $inv_Final['pt'];     
        
        
        $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
        $nombrePeriodo = $helper->getNombrePeriodo($periodo);
        $params = compact('actividades', 'ultimo', 'data', 'ejercicio', 'utilidadEjercicio', 'nombrePeriodo', 'periodo',
        'acumuladosxcta_hoja1', 'hoja1',
        'acumulados_hoja2', 'totales_hoja2', 'acumuladosxcta', 'hoja2', 'ue_ingresos', 'ue_gastos_costos',
        'ctas_hoja3',
        'acumulados_hoja5', 'totales_hoja5', 'acumuladosxcta_hoja5', 'hoja5',
        'acumulados_hoja6', 'totales_hoja6', 'acumuladosxcta_hoja6', 'hoja6',
        'acumulados_hoja7', 'totales_hoja7', 'acumuladosxcta_hoja7', 'hoja7',
        'acumulados_hoja8', 'totales_hoja8', 'acumuladosxcta_hoja8', 'hoja8',
        'data_inventarios', 'mp_ini', 'mp_fin', 'pp_ini', 'pp_fin', 'pt_ini', 'pt_fin', 
        'input_indirectos', 'input_mo');
        Session::put('data_rg', $params);
        return view('Mod_RG.RG03_reporte', $params);
    }
    public function ajustesfill(){
        $mo = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'mo')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $indirectos = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'ind')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $mo = (is_null($mo))?0:$mo;
        $indirectos = (is_null($indirectos))?0:$indirectos;
        return compact('mo', 'indirectos');
    }
    public function RGPDF($opcion){         
            $data = Session::get('data_rg');    
               
            switch ($opcion) {
                case '0':
                    $vista = 'Mod_RG.';
                    $file_name = "-";
                    break;
                case '1':
                    $vista = 'Mod_RG.RG03_reporte_BG01';
                    $file_name = "_BalanzaGeneral";
                    break;
                case '2':
                    $vista = 'Mod_RG.RG03_reporte_ER';
                    $file_name = "_EstadoResultados";
                    break;
                case '3':
                    $vista = 'Mod_RG.RG03_reporte_EC';
                    $file_name = "_EstadoCostos";
                    break;
                case '4':
                    $vista = 'Mod_RG.RG03_reporte_Inv';
                    $file_name = "_Inventario";
                    break;
                case '5':
                    $vista = 'Mod_RG.RG03_reporte_GtosFab';
                    $file_name = "_GtosFabricacion";
                    break;
                case '6':
                    $vista = 'Mod_RG.RG03_reporte_GtosAdmon';
                    $file_name = "_GtosAdmon";
                    break;
                case '7':
                    $vista = 'Mod_RG.RG03_reporte_GtosVentas';
                    $file_name = "GtosVentas";
                    break;
                case '8':
                    $vista = 'Mod_RG.RG03_reporte_GtosFinanzas';
                    $file_name = "GtosFinanzas";
                    break;                
            }
            $data["vista"] = $vista;
             
            $pdf = PDF::loadView('Mod_RG.RG03PDF', $data);
            //$pdf = new FPDF('L', 'mm', 'A4');
            // $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);                        
            $pdf->setOptions(['isPhpEnabled' => true]);             
            
            return $pdf->stream($data["ejercicio"]."_".$data["periodo"].$file_name[1].'.pdf');      
    }
}
