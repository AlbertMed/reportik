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
            $cbo_periodos = array_reverse($cbo_periodos);
            return view('Mod_RG.RG03', compact('actividades', 'ultimo', 'cbo_periodos'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function reporte(Request $request)
    {           
     
        $periodo = explode('-', Input::get('cbo_periodo'));
       
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
                                ,[RGC_estilo]
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
            return $value->RGC_hoja == 3 && $value->RGC_tipo_renglon = 'CUENTA';
        });

        $data_inventarios = DB::select("SELECT COALESCE([IC_Ejercicio], 0) AS IC_Ejercicio
        ,COALESCE([IC_periodo], 0) AS IC_periodo
        ,COALESCE(Localidades.LOC_Nombre, 'SIN NOMBRE') AS IC_LOC_Nombre
        ,COALESCE([IC_CLAVE], 'SIN CLAVE') AS IC_CLAVE    
        ,COALESCE([IC_MAT_PRIMA], 0) AS IC_MAT_PRIMA
        ,COALESCE([IC_WIP], 0) AS IC_WIP
        ,COALESCE([IC_PROD_TERM], 0) AS IC_PROD_TERM
        ,COALESCE([IC_COSTO_TOTAL], 0) * ct.RGC_multiplica AS IC_COSTO_TOTAL
        ,ct.*
        ,COALESCE(Localidades.LOC_CodigoLocalidad, RGC_BC_Cuenta_Id) AS LOC_CodigoLocalidad
                            FROM RPT_RG_ConfiguracionTabla ct
							LEFT JOIN RPT_InventarioContable on ct.RGC_BC_Cuenta_Id = IC_CLAVE
							LEFT JOIN Localidades on LOC_LocalidadId = RGC_BC_Cuenta_Id
                    where  (IC_periodo = ? OR IC_periodo IS NULL) and 
                    (IC_Ejercicio = ? OR IC_Ejercicio IS NULL) 
                    and ct.RGC_hoja = '3' and RGC_tipo_renglon ='LOCALIDAD'
                    ORDER BY RGC_tabla_linea",[$periodo, $ejercicio]);
        $data_formulas_33 = DB::select("select * from RPT_RG_ConfiguracionTabla 
where RGC_hoja = '33' and RGC_tipo_renglon IN('FORMULA', 'INPUT') order by RGC_tabla_linea");
        
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
       $box_config = DB::select("select * from [dbo].[RPT_RG_VariablesReporte]");
        foreach ($box_config as $value) {
              $box[$value->RGV_alias] = $value->RGV_valor_default;
        }
       $box = array(); 
       //ponemos las variables del usuario e la caja             
       $box['input_mo'] = (is_null(Input::get('mo'))||Input::get('mo') == '')?0:Input::get('mo');
       $box['input_indirectos'] = (is_null(Input::get('indirectos'))|| Input::get('indirectos') == '')?0:Input::get('indirectos');
       $box['mp_ot'] = (is_null(Input::get('mp_ot'))||Input::get('mp_ot') == '')?0:Input::get('mp_ot');

        DB::table('RPT_RG_Ajustes') // Guardamos los valores
            ->where('AJU_Id', 'mo')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['input_mo'], 
            'AJU_descripcion' => 'valor sumado a mano obra',
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
       DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'ind')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['input_indirectos'],
            'AJU_descripcion' => 'valor restado a indirectos',
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
       DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['mp_ot'],
            'AJU_descripcion' => 'valor sumado a PP',
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
          
        $titulos_hoja3 = 
            array_map('trim', 
            array_pluck($hoja3, 'RGC_tabla_titulo')
        ); 
        $grupos_hoja3 = array_unique($titulos_hoja3);//COMPRAS NETAS, MO, GASTOS IND
        $ctas_hoja3 = [];
       foreach ($grupos_hoja3 as $key => $val) {
           $items = array_where($hoja3, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            }); 
            $ctas_hoja3 [$val] = array_sum(array_pluck($items, 'movimiento')); 
       }
        
       foreach ($box_config as $value) {
           //ponemos las variables de las CUENTAS en la caja
            if (key_exists($value->RGV_tabla_titulo, $ctas_hoja3)) {
                $box[$value->RGV_alias] = $ctas_hoja3[$value->RGV_tabla_titulo];
            }            
        }

       $inv_Inicial = $helper->getInv($periodo, $ejercicio, true, $box_config);          
       foreach ($box_config as $value) {
           //ponemos las variables de LOCALIDADES en la caja
           //dd($value->RGV_alias, $inv_Inicial, key_exists($value->RGV_alias, $inv_Inicial));
           if (key_exists($value->RGV_alias, $inv_Inicial)) {
               $box[$value->RGV_alias] = $inv_Inicial[$value->RGV_alias];
            }           
        }
      
        //dd($box['mp_ini']);

       $inv_Final = $helper->getInv($periodo, $ejercicio, false, $box_config);
       foreach ($box_config as $value) {
            if (key_exists($value->RGV_alias, $inv_Final)) {
                $box[$value->RGV_alias] = $inv_Final[$value->RGV_alias];
            }          
        }

        unset(
            $inv_Final['mp_fin']
            ,$inv_Final['pp_fin']
            ,$inv_Final['pt_fin']
        );
      
        $llaves_invFinal = array_keys($inv_Final);

        
       //Hoja 4 usa $data_inventarios
        $total_inventarios = array_sum(array_pluck($data_inventarios, 'IC_COSTO_TOTAL'));
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

      //  dd($box);
      
    $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
        $nombrePeriodo = $helper->getNombrePeriodo($periodo);
        $params = compact('actividades', 'ultimo', 'data', 'ejercicio', 'utilidadEjercicio', 'nombrePeriodo', 'periodo',
        'acumuladosxcta_hoja1', 'hoja1',
        'acumulados_hoja2', 'totales_hoja2', 'acumuladosxcta', 'hoja2', 'ue_ingresos', 'ue_gastos_costos',
        'ctas_hoja3', 'total_inventarios', 'llaves_invFinal', 'inv_Final', 'data_formulas_33', 'box',
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
        $mp_ot = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $mo = (is_null($mo))?0:$mo;
        $indirectos = (is_null($indirectos))?0:$indirectos;
        $mp_ot = (is_null($mp_ot))?0:$mp_ot;
        return compact('mo', 'indirectos', 'mp_ot');
    }
    public function RGPDF($opcion){         
            $data = Session::get('data_rg');                   
            switch ($opcion) {
                case '0':
                    $vista = 'Mod_RG.';
                    $file_name = "-";
                    break;
                case '1':
                    $vista = 'Mod_RG.RG03_reporte_BG';
                    $file_name = "_BalanzaGeneral";
                    $hoja1_activos = array_where($data['hoja1'], function ($key, $value) {
                        return is_numeric(strpos($value->RGC_tabla_titulo, 'ACTIVO'));
                    });
                    $hoja1_pasivos = array_where($data['hoja1'], function ($key, $value) {
                        return strpos($value->RGC_tabla_titulo, 'ACTIVO') === false;
                    });                    
                    $data["hoja1_activos"] = $hoja1_activos;
                    $data["hoja1_pasivos"] = $hoja1_pasivos;
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
