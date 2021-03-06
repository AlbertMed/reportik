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
use Carbon\Carbon;

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
                        if ($value["m_" . $index] != 0) {
                            $cbo_periodos[] = $value["BC_Ejercicio"].'-'.$index;
                        }
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
                                ,[BC_Movimiento_".$periodo. "] * conf.RGC_multiplica as movimiento   
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
       
       $data_inventarios_4 = DB::select("SELECT COALESCE([IC_Ejercicio], 0) AS IC_Ejercicio
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
                    and ct.RGC_hoja = '4' and RGC_tipo_renglon ='LOCALIDAD'
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
        /*  
        dd($data_inventarios);
        foreach ($data_inventarios as $value) {
            DB::table('RPT_RG_ConfiguracionTabla')->insert(
                    ['RGC_BC_Cuenta_Id' => $value->RGC_BC_Cuenta_Id, 
                    'RGC_tipo_renglon' => $value->RGC_tipo_renglon,
                    'RGC_hoja' => 4,
                    'RGC_tabla_titulo' => $value->RGC_tabla_titulo,
                    'RGC_tabla_linea' => $value->RGC_tabla_linea,
                    'RGC_descripcion_cuenta' => $value->RGC_descripcion_cuenta,
                    'RGC_valor_default' => $value->RGC_valor_default,
                    'RGC_fecha_alta' => date('Ymd h:m:s'),
                    'RGC_mostrar' => $value->RGC_mostrar,
                    'RGC_estilo' => $value->RGC_estilo,
                    'RGC_multiplica' => 1                   
                    ]
                );
        }
        */
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
       $custom = DB::select("select * from [dbo].[RPT_ConfiguracionPersonalizacionReportes] where CPR_modulo = 'RG_03'");
       $personalizacion = [];
       foreach ($custom as $p) {
           $personalizacion[trim(str_replace (' ', '',$p->CPR_id)).''] = $p->CPR_valor.'';
       }
        
   // dd(array_map('trim',array_pluck($data_inventarios, 'RGC_tabla_titulo')), $personalizacion);
   $box = array(); 
       foreach ($box_config as $value) {
              $box[$value->RGV_alias] = $value->RGV_valor_default;
        }
        
       //ponemos las variables del usuario e la caja             
       $box['input_mo'] = (is_null(Input::get('mo'))||Input::get('mo') == '')?0:Input::get('mo');
       $box['input_indirectos'] = (is_null(Input::get('indirectos'))|| Input::get('indirectos') == '')?0:Input::get('indirectos');
       $box['mp_ot'] = (is_null(Input::get('mp_ot'))||Input::get('mp_ot') == '')?0:Input::get('mp_ot');

        $mo = DB::table('RPT_RG_Ajustes') // Guardamos los valores
            ->where('AJU_Id', 'mo')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['input_mo'], 
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
             if ($mo == 0) {
              DB::table('RPT_RG_Ajustes')->insert(
                    ['AJU_Id' => 'mo', 
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['input_mo'],
                    'AJU_descripcion' => 'valor sumado a mano obra',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
          }
       $indirectos = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'ind')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['input_indirectos'],            
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
            if ($indirectos == 0) {
              DB::table('RPT_RG_Ajustes')->insert(
                    ['AJU_Id' => 'ind', 
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['input_indirectos'],
                    'AJU_descripcion' => 'valor restado a indirectos',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
            }

       $mp_ot = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_periodo', $periodo)
            ->update(['AJU_valor' => $box['mp_ot'],
            'AJU_fecha_actualizado' => date('Ymd h:m:s')]);
          if ($mp_ot == 0) {
              DB::table('RPT_RG_Ajustes')->insert(
                    ['AJU_Id' => 'mp_ot', 
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['mp_ot'],
                    'AJU_descripcion' => 'valor sumado a PP',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
          }
                   
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
            //sumamos a pp ini el valor capturado del mes anteior
            $fecha = $ejercicio . '/' . $periodo . '/01';
            $fecha = Carbon::parse($fecha);
            $fecha = $fecha->subMonth();
            $periodo_anterior = $fecha->format('m');
            $ejercicio_anterior = $fecha->format('Y');
            $mp_ot_perido_anterior = DB::table('RPT_RG_Ajustes')
                ->where('AJU_Id', 'mp_ot')
                ->where('AJU_ejercicio', $ejercicio_anterior)
                ->where('AJU_periodo', $periodo_anterior)
                ->value('AJU_valor');
            $mp_ot_perido_anterior = (is_null($mp_ot_perido_anterior)) ? 0 : $mp_ot_perido_anterior;

        $box['pp_ini'] += $mp_ot_perido_anterior;

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
        
        //dd(array_pluck($data_formulas_33, 'RGC_BC_Cuenta_Id'), array_pluck($data_formulas_33, 'RGC_valor_default'), array_pluck($data_formulas_33, 'RGC_multiplica'));
        foreach ($data_formulas_33 as $value) {    
            eval("\$box['".$value->RGC_BC_Cuenta_Id."'] = (".$value->RGC_valor_default. ")*".$value->RGC_multiplica.";");            
        }
       //Hoja 4 usa $data_inventarios
        
        $total_inventarios = array_sum(array_pluck($data_inventarios, 'IC_COSTO_TOTAL'));
        //dd($data_inventarios);
        $total_inventarios_4 = array_sum(array_pluck($data_inventarios_4, 'IC_COSTO_TOTAL'));

        $titulos_inventarios= array_pluck($data_inventarios, 'RGC_tabla_titulo');                 
        //suma las variablesReportes de las Localidades, en este caso solo habia una que es la de mp_ot, y la suma a el total de inventarios
        foreach ($data_formulas_33 as $value) {
            if(in_array($value->RGC_tabla_titulo, $titulos_inventarios)){
                eval('$total_inventarios += (('.$value->RGC_valor_default. ') *'.$value->RGC_multiplica.');');

            }            
        }
              
        //INICIA Gtos Fab - Hoja 5
        $grupos_hoja5 = array_unique(array_pluck($hoja5, 'RGC_tabla_titulo'));  
        $totales_hoja5 = [];
        $acumulados_hoja5 = [];
        $acumuladosxcta_hoja5 = [];        
        foreach ($grupos_hoja5 as $key => $val) {
            $items = array_where($hoja5, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            }); 
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {  
                //OBTENER SALDO ACUMULADO    
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);   
                if (is_null($sum)) {
                    Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                    $sum = 0;
                }else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO EN CERO
                    unset($hoja5[$key]);
                    unset($items[$key]);
                }else {
                    //SE GUARDA ACUMULADO DE ESA CUENTA           
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja5[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;                    
                } 
            }
            $totales_hoja5 [$val] = array_sum(array_pluck($items, 'movimiento'));
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
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {
                //OBTENER SALDO ACUMULADO                
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja6[$key]);
                    unset($items[$key]);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja6[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
                }   
            }            
            $totales_hoja6 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $acumulados_hoja6 [$val] = $sum_acumulado;
        }
        //INICIA Gtos Ventas - Hoja 7
        $grupos_hoja7 = array_unique(array_pluck($hoja7, 'RGC_tabla_titulo'));  
        $totales_hoja7 = [];
        $acumulados_hoja7 = [];
        $acumuladosxcta_hoja7 = [];        
        foreach ($grupos_hoja7 as $key => $val) {
            $items = array_where($hoja7, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {
                //OBTENER SALDO ACUMULADO                
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                    Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                    $sum = 0;
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja7[$key]);
                    unset($items[$key]);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA               
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja7[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
                }
            }
            $totales_hoja7 [$val] = array_sum(array_pluck($items, 'movimiento'));            
            $acumulados_hoja7 [$val] = $sum_acumulado;
        }
        //INICIA Gtos Financieros - Hoja 8
        $grupos_hoja8 = array_unique(array_pluck($hoja8, 'RGC_tabla_titulo'));  
        $totales_hoja8 = [];
        $acumulados_hoja8 = [];
        $acumuladosxcta_hoja8 = [];        
        foreach ($grupos_hoja8 as $key => $val) {
            $items = array_where($hoja8, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {
                //OBTENER SALDO ACUMULADO                   
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo);
                if (is_null($sum)) {
                    Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                    $sum = 0;
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja8[$key]);
                    unset($items[$key]);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA               
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja8[trim($value->BC_Cuenta_Id)] = $sum * $value->RGC_multiplica;
                }
            }
            $totales_hoja8 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $acumulados_hoja8 [$val] = $sum_acumulado;
        }
        //inicia reportes adicionales
        $docs = DB::select("SELECT * FROM RPT_RG_Documentos WHERE DOC_ejercicio = ? AND DOC_periodo = ?",[$ejercicio, $periodo]);
       // dd( $data_inventarios);
       
       //obtener fecha de actualizacion 
        $fechaA = DB::table('RPT_RG_Fechas')
            ->where('RGF_EjercicioPeriodo', Input::get('cbo_periodo'))
            ->value('RGF_FechaActualizado');
        $fechaA = (is_null($fechaA)) ? '' : 'Actualizado: '. $helper->getHumanDate($fechaA);


    $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
        $nombrePeriodo = $helper->getNombrePeriodo($periodo);
        $params = compact('fechaA','personalizacion', 'actividades', 'ultimo', 'ejercicio', 'utilidadEjercicio', 'nombrePeriodo', 'periodo',
        'acumuladosxcta_hoja1', 'hoja1',
        'acumulados_hoja2', 'totales_hoja2', 'acumuladosxcta', 'hoja2', 'ue_ingresos', 'ue_gastos_costos',
        'ctas_hoja3', 'total_inventarios', 'llaves_invFinal', 'inv_Final', 'data_formulas_33', 'box',
        'data_inventarios_4', 'total_inventarios_4',
        'acumulados_hoja5', 'totales_hoja5', 'acumuladosxcta_hoja5', 'hoja5',
        'acumulados_hoja6', 'totales_hoja6', 'acumuladosxcta_hoja6', 'hoja6',
        'acumulados_hoja7', 'totales_hoja7', 'acumuladosxcta_hoja7', 'hoja7',
        'acumulados_hoja8', 'totales_hoja8', 'acumuladosxcta_hoja8', 'hoja8',
        'data_inventarios', 'mp_ini', 'mp_fin', 'pp_ini', 'pp_fin', 'pt_ini', 'pt_fin', 
        'input_indirectos', 'input_mo', 'docs');
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
