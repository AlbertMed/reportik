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
    /*
        RGC_motrar, catalogo del Mod_RG.RG01
            manejar cuentas para mostrar, valor en 0 se muestra siempre, 
            20 ejem. solo para el periodo especificado en RPT_RG_CatalogoVersionCuentas
        RGC_sociedad
            maneja para cual sociedad es valida la cta, 0 es una cuenta para cualquier sociedad
        RGC_BC_Cuenta_Id2 proporciona cuentas unicas cuando tenemos ctas iguales en un periodo 

        */
    public function index($sociedad = null)
    {
        if (Auth::check()) { 
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            if (is_null($sociedad)) {
                if (Input::has('text_selUno')) {
                    $sociedad = Input::get('text_selUno');
                } else {
                    if (Session::has('sociedad_rg')) {
                        $sociedad = Session::pull('sociedad_rg');
                    }
                }
            }
            $tableName = DB::table('RPT_Sociedades')
            ->where('SOC_Nombre', $sociedad)
            ->where('SOC_Reporte', 'ReporteGerencial')
            ->value('SOC_AUX_DB');

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
                                    sum(BC_Movimiento_12)m_12 from ". $tableName ." 
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
            return view('Mod_RG.RG03', compact('sociedad','actividades', 'ultimo', 'cbo_periodos'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function reporte(Request $request)
    {
        $sociedad = Input::get('sociedad');
        Session::put('sociedad_rg', $sociedad);
        $periodo = explode('-', Input::get('cbo_periodo'));
        $soc = DB::table('RPT_Sociedades')
        ->where('SOC_Nombre', $sociedad)
        ->where('SOC_Reporte', 'ReporteGerencial')
        ->first();
        $tableName = $soc->SOC_AUX_DB;
        $version = DB::table('RPT_RG_CatalogoVersionCuentas')
                        ->where('CAT_periodo', $periodo)                        
                        ->value('CAT_version');
        $version = (is_null($version)) ? 0 : $version;
        $ejercicio = $periodo[0];
        $periodo = $periodo[1]; 

        $fecha = $ejercicio . '/' . $periodo . '/01';
        $fecha = Carbon::parse($fecha);
        $fecha_m = $fecha->subMonth();
        $periodo_ant = $fecha_m->format('m');
        $ejercicio_ant = $fecha_m->format('Y');
        //clock($fecha);
        $box_config = DB::select("select RGV_alias,
        RGV_valor_default, RGV_tabla_titulo from [dbo].[RPT_RG_VariablesReporte]");
        $custom = DB::select("select CPR_id, CPR_valor from [dbo].[RPT_ConfiguracionPersonalizacionReportes] where CPR_modulo = 'RG_03'");
        $personalizacion = [];
        foreach ($custom as $p) {
            $personalizacion[trim(str_replace(' ', '', $p->CPR_id)) . ''] = $p->CPR_valor . '';
        }
        $box = array();
        foreach ($box_config as $value) {
            $box[$value->RGV_alias] = $value->RGV_valor_default;
            $box[$value->RGV_alias . '_acumulado'] = $value->RGV_valor_default;
        }
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
                                ,[RGC_tipo_renglon]
                                ,[RGC_BC_Cuenta_Id2]
                            FROM ". $tableName ." bg
                            LEFT join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
                            WHERE [BC_Ejercicio] = ?
                            AND [BC_Movimiento_".$periodo."] IS NOT NULL
                            AND (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
                            AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)
                            order by RGC_hoja, RGC_tabla_linea
                                    ",[$ejercicio, $version, $soc->SOC_Id]);
        
        $hoja1 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 1;
        });
        //clock($hoja1);
        $hoja2 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 2;
        });
        //clock($hoja2);
        $hoja3 = array_where($data, function ($key, $value) {
            return $value->RGC_hoja == 3 && $value->RGC_tipo_renglon == 'CUENTA';
        });
        //clock($hoja3);
        $peryodo = [];
        for ($i = 1; $i <= (int) $periodo; $i++) {
            $peryodo[] = ($i < 10) ? '0' . $i : '' . $i;
        }
        $criterioperiodo = implode(",", $peryodo);

        if ($tableName != 'RPT_BalanzaComprobacion') {
            $data_inventarios_acum = DB::select("SELECT	
                COALESCE(AJU_ejercicio, 0) AS IC_Ejercicio
                ,COALESCE(AJU_periodo, 0) AS IC_periodo
                ,COALESCE(AJU_descripcion, 'SIN NOMBRE') AS IC_LOC_Nombre
                ,COALESCE(AJU_valor, 0) AS IC_COSTO_TOTAL
                ,AJU_tabla_titulo AS RGC_tabla_titulo
                ,'' AS LOC_CodigoLocalidad
                ,'' AS RGC_estilo
            FROM RPT_RG_Ajustes 
            WHERE 
            AJU_Id in ('mp', 'pp', 'pt') 
            AND AJU_periodo in ($criterioperiodo)
            AND (AJU_ejercicio = ?)
            AND (AJU_sociedad = ?)
            ORDER BY AJU_tabla_linea", [$ejercicio, $soc->SOC_Nombre]);
            
        }else{//ES ITEKNIA
            //En consulta siguiente ct.RGC_hoja = '3'
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
                        AND (ct.RGC_sociedad = '0' OR ct.RGC_sociedad = ?)
                        ORDER BY RGC_tabla_linea", [$periodo, $ejercicio, $soc->SOC_Id]);
                

                $data_inventarios_acum = DB::select("SELECT COALESCE([IC_Ejercicio], 0) AS IC_Ejercicio
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
                        where  IC_periodo in ($criterioperiodo) AND 
                        (IC_Ejercicio = ? OR IC_Ejercicio IS NULL) 
                        and ct.RGC_hoja = '3' and RGC_tipo_renglon ='LOCALIDAD'
                        AND (ct.RGC_sociedad = '0' OR ct.RGC_sociedad = ?)
                        ORDER BY RGC_tabla_linea", [$ejercicio, $soc->SOC_Id]);
        
        }
        
        $data_formulas_33 = DB::select("select * from RPT_RG_ConfiguracionTabla 
                                where RGC_hoja = '33' and RGC_tipo_renglon IN('FORMULA', 'INPUT') 
                                AND (RGC_sociedad = '0' OR RGC_sociedad = ?)
                                order by RGC_tabla_linea", [$soc->SOC_Id]);
        $data_formulas_34 = DB::select("select * from RPT_RG_ConfiguracionTabla 
                                where RGC_hoja = '34' and RGC_tipo_renglon IN('FORMULA', 'INPUT') 
                                AND (RGC_sociedad = '0' OR RGC_sociedad = ?)
                                order by RGC_tabla_linea", [$soc->SOC_Id]);

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
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
               if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }                            
               $acumuladosxcta_hoja1[$value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2] = $sum * $value->RGC_multiplica;
            }        
        }
        // INICIA ER - Hoja2
        $grupos_hoja2 = array_unique(array_pluck($hoja2, 'RGC_tabla_titulo'));      
        $totales_hoja2 = [];
        $acumulados_hoja2 = [];
        $acumuladosxcta = [];
        $utilidadEjercicio = 0; 
        $ue_ingresos = 0; $ue_gastos_costos = 0;
        $totalesIngresosGastos = [];
        $anteriorIngresos = 0;
        $cantPeriodoIngresos = 0;
        $acumuladoIngresos = 0;
        $anteriorGastos = 0;
        $cantPeriodoGastos = 0;
        $acumuladoGastos = 0;
        foreach ($grupos_hoja2 as $key => $val) {
            $items = array_where($hoja2, function ($key, $value) use ($val){
                return $value->RGC_tabla_titulo == $val;
            });
            $totales_hoja2 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {                
               $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);   
               if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
               }
                      $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta[$value->BC_Cuenta_Id.$value->RGC_BC_Cuenta_Id2] = $sum * $value->RGC_multiplica;                                                       
            }

            $acumulados_hoja2 [$val] = $sum_acumulado;
            
            if (strpos($val, 'INGRESO') === false) {
                $anteriorGastos += $acumulados_hoja2[$val] - $totales_hoja2[$val];
                $cantPeriodoGastos += $totales_hoja2[$val];
                $acumuladoGastos += $acumulados_hoja2[$val];
                $totalesIngresosGastos[1] = [
                    'titulo' => 'TOTAL GASTOS:',
                    'anterior' => $anteriorGastos,
                    'periodo' => $cantPeriodoGastos,
                    'acumulado' => $acumuladoGastos
                ];
                    $ue_gastos_costos += $sum_acumulado;
                } else {
                    
                $anteriorIngresos += $acumulados_hoja2[$val] - $totales_hoja2[$val];
                $cantPeriodoIngresos += $totales_hoja2[$val];
                $acumuladoIngresos += $acumulados_hoja2[$val];
                $totalesIngresosGastos[0] = [
                    'titulo' => 'TOTAL INGRESOS:',
                    'anterior' => $anteriorIngresos,
                    'periodo' => $cantPeriodoIngresos,
                    'acumulado' => $acumuladoIngresos
                ];
                    $ue_ingresos += $sum_acumulado;
                }            
            }
            if (count($totalesIngresosGastos) == 2) {
               $totalesIngresosGastos[2] = [
                    'titulo'=> 'TOTAL ESTADO DE RESULTADOS:',
                    'anterior' => $totalesIngresosGastos[0]['anterior'] - $totalesIngresosGastos[1]['anterior'],
                    'periodo' => $totalesIngresosGastos[0]['periodo'] - $totalesIngresosGastos[1]['periodo'],
                    'acumulado' => $totalesIngresosGastos[0]['acumulado'] - $totalesIngresosGastos[1]['acumulado']
                ];      
            }

        ksort($totalesIngresosGastos);

        

         $utilidadEjercicio = $ue_ingresos - $ue_gastos_costos;
        // INICIA EC - Hoja3 
        //ponemos las variables del usuario e la caja             
        $box['input_mo'] = (is_null(Input::get('mo')) || Input::get('mo') == '') ? 0 : Input::get('mo');
        $box['input_indirectos'] = (is_null(Input::get('indirectos')) || Input::get('indirectos') == '') ? 0 : Input::get('indirectos');
        $box['mp_ot'] = (is_null(Input::get('mp_ot')) || Input::get('mp_ot') == '') ? 0 : Input::get('mp_ot');

    if ($tableName != 'RPT_BalanzaComprobacion') {
            // DB::table('RPT_RG_Ajustes')
            // ->updateOrInsert(
            //     [
            //         'AJU_Id' => 'mp', 'AJU_ejercicio' => $ejercicio,
            //         'AJU_sociedad' => $sociedad, 'AJU_periodo' => $periodo
            //     ],
            //     [
            //         'AJU_valor' => Input::get('mp'),
            //         'AJU_fecha_actualizado' => date('Ymd h:m:s'),
            //         'AJU_tabla_linea' => '1',
            //         'AJU_tabla_titulo' => 'INV FINAL M.P. ALMACEN MATERIAS PRIMAS',
            //         'AJU_descripcion' => 'MATERIA PRIMA'
            //     ]
            // );
            $mp_update = DB::table('RPT_RG_Ajustes') // Guardamos los valores
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->update([
                    'AJU_valor' => Input::get('mp'),
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]);
            if ($mp_update == 0) {
                DB::table('RPT_RG_Ajustes')->insert(
                    [
                        'AJU_tabla_linea' => '1',
                        'AJU_tabla_titulo' => 'INV FINAL M.P. ALMACEN MATERIAS PRIMAS',
                        'AJU_Id' => 'mp',
                        'AJU_ejercicio' => $ejercicio,
                        'AJU_sociedad' => $sociedad,
                        'AJU_periodo' => $periodo,
                        'AJU_valor' => Input::get('mp'),
                        'AJU_descripcion' => 'MATERIA PRIMA',
                        'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
            }
            $pp_update = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pp')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->update([
                    'AJU_valor' => Input::get('pp'),
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]);
            if ($pp_update == 0) {
                DB::table('RPT_RG_Ajustes')->insert(
                    [
                        'AJU_tabla_linea' => '2',
                        'AJU_tabla_titulo' => 'INV FINAL P.P. MATERIALES EN PROCESO',
                        'AJU_Id' => 'pp',
                        'AJU_ejercicio' => $ejercicio,
                        'AJU_sociedad' => $sociedad,
                        'AJU_periodo' => $periodo,
                        'AJU_valor' => Input::get('pp'),
                        'AJU_descripcion' => 'MP EN PROCESO ',
                        'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
            }

            $pt_update = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->update([
                    'AJU_valor' => Input::get('pt'),
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]);
            if ($pt_update == 0) {
                DB::table('RPT_RG_Ajustes')->insert(
                    [
                        'AJU_tabla_linea' => '3',
                        'AJU_tabla_titulo' => 'INV. FINAL P.T. PRODUCTO TEMINADO',
                        'AJU_Id' => 'pt',
                        'AJU_ejercicio' => $ejercicio,
                        'AJU_sociedad' => $sociedad,
                        'AJU_periodo' => $periodo,
                        'AJU_valor' => Input::get('pp'),
                        'AJU_descripcion' => 'PRODUCTO TEMINADO',
                        'AJU_fecha_actualizado' => date('Ymd h:m:s')
                    ]
                );
            }
        } //END $tableName != 'RPT_BalanzaComprobacion'


        $mo = DB::table('RPT_RG_Ajustes') // Guardamos los valores
        ->where('AJU_Id', 'mo')
        ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->update([
                'AJU_valor' => $box['input_mo'],
                'AJU_fecha_actualizado' => date('Ymd h:m:s')
            ]);
        if ($mo == 0) {
            DB::table('RPT_RG_Ajustes')->insert(
                [
                    'AJU_Id' => 'mo',
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_sociedad' => $sociedad,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['input_mo'],
                    'AJU_descripcion' => 'valor sumado a mano obra',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]
            );
        }
        $box['input_mo_acumulado'] = self::getAcumulado_RG_Ajustes('mo', $ejercicio, $sociedad, $periodo);

        $indirectos = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'ind')
        ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->update([
                'AJU_valor' => $box['input_indirectos'],
                'AJU_fecha_actualizado' => date('Ymd h:m:s')
            ]);
        if ($indirectos == 0) {
            DB::table('RPT_RG_Ajustes')->insert(
                [
                    'AJU_Id' => 'ind',
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_sociedad' => $sociedad,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['input_indirectos'],
                    'AJU_descripcion' => 'valor restado a indirectos',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]
            );
        }
        $box['input_indirectos_acumulado'] = self::getAcumulado_RG_Ajustes('ind', $ejercicio, $sociedad, $periodo);
        $mp_ot = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->update([
                'AJU_valor' => $box['mp_ot'],
                'AJU_fecha_actualizado' => date('Ymd h:m:s')
            ]);
        if ($mp_ot == 0) {
            DB::table('RPT_RG_Ajustes')->insert(
                [
                    'AJU_Id' => 'mp_ot',
                    'AJU_ejercicio' => $ejercicio,
                    'AJU_sociedad' => $sociedad,
                    'AJU_periodo' => $periodo,
                    'AJU_valor' => $box['mp_ot'],
                    'AJU_descripcion' => 'valor sumado a PP',
                    'AJU_fecha_actualizado' => date('Ymd h:m:s')
                ]
            );
        }
        $box['mp_ot_acumulado'] = self::getAcumulado_RG_Ajustes('mp_ot', $ejercicio, $sociedad, $periodo);          
        
        $datosEstadoCostos = self::reporteEstadoCostosProcedimiento($sociedad, $ejercicio, $periodo);
        // return ($data);
        //return view('Contabilidad.EstadoCostosIndex', compact('datos', 'sociedad', 'actividades', 'ultimo', 'ejercicio', 'periodo'));
       
        //INICIA Hoja 4 usa $data_inventarios_4
        if ($tableName != 'RPT_BalanzaComprobacion') {
            //en caso de no ser de Iteknia.
           
            $data_inventarios_4 = DB::select("SELECT	
                COALESCE(AJU_ejercicio, 0) AS IC_Ejercicio
                ,COALESCE(AJU_periodo, 0) AS IC_periodo
                ,COALESCE(AJU_descripcion, 'SIN NOMBRE') AS IC_LOC_Nombre
                ,COALESCE(AJU_valor, 0) AS IC_COSTO_TOTAL
                ,AJU_tabla_titulo AS RGC_tabla_titulo
                ,'' AS LOC_CodigoLocalidad
                ,'' AS RGC_estilo
            FROM RPT_RG_Ajustes 
            WHERE 
            AJU_Id in ('mp', 'pp', 'pt') 
            AND (AJU_periodo = ?)
            AND (AJU_ejercicio = ?)
            AND (AJU_sociedad = ?)
            ORDER BY AJU_tabla_linea", [$periodo, $ejercicio, $soc->SOC_Nombre]);
            $data_inventarios = $data_inventarios_4;
        } else { //ES ITEKNIA
            //En consulta siguiente ct.RGC_hoja = '4
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
                            AND (ct.RGC_sociedad = '0' OR ct.RGC_sociedad = ?)
                            ORDER BY RGC_tabla_linea", [$periodo, $ejercicio, $soc->SOC_Id]);
        }
        //clock($data_inventarios_4);
        $total_inventarios = array_sum(array_pluck($data_inventarios, 'IC_COSTO_TOTAL'));
        $total_inventarios_acum = array_sum(array_pluck($data_inventarios_acum, 'IC_COSTO_TOTAL'));
        $total_inventarios_4 = array_sum(array_pluck($data_inventarios_4, 'IC_COSTO_TOTAL'));

        $titulos_inventarios= array_pluck($data_inventarios, 'RGC_tabla_titulo');                 
        //suma las variablesReportes de las Localidades, en este caso solo habia una que es la de mp_ot, y la suma a el total de inventarios
        foreach ($data_formulas_33 as $value) {
            if(in_array($value->RGC_tabla_titulo, $titulos_inventarios)){
                eval('$total_inventarios += (('.$value->RGC_valor_default. ') *'.$value->RGC_multiplica.');');

            }            
        }
        foreach ($data_formulas_34 as $value) {
            if(in_array($value->RGC_tabla_titulo, $titulos_inventarios)){
                eval('$total_inventarios_acum += (('.$value->RGC_valor_default. ') *'.$value->RGC_multiplica.');');

            }            
        }
              
        //INICIA Gastos Fab - Hoja 5
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
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);   
                if (is_null($sum)) {
                    Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                    $sum = 0;
                    unset($hoja5[$key]);
                    unset($items[$key]);
                }else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO EN CERO
                    unset($hoja5[$key]);
                    unset($items[$key]);
                }else {
                    //SE GUARDA ACUMULADO DE ESA CUENTA           
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja5[trim($value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2)] = $sum * $value->RGC_multiplica;                    
                } 
            }
            $totales_hoja5 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $acumulados_hoja5 [$val] = $sum_acumulado;
        }
       //INICIA Gastos Admon - Hoja 6
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
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
                if (is_null($sum)) {
                  Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:'.$value->BC_Cuenta_Id);
                  $sum = 0;
                    unset($hoja6[$key]);
                    unset($items[$key]);
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja6[$key]);
                    unset($items[$key]);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                   /* clock([ //VER DETALLE DE CTAS HOJA6 
                        'CtaId'=> $value->BC_Cuenta_Id,
                        'complementoKey'=>$value->RGC_BC_Cuenta_Id2, 
                        'movimiento'=>$value->movimiento, 
                        'acumuladoCta'=>$sum, 
                        'Multiplicador'=>$value->RGC_multiplica, 
                        'acumulado*Multipli'=>($sum * $value->RGC_multiplica)]);
                        */
                    $acumuladosxcta_hoja6[trim($value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2)] = $sum * $value->RGC_multiplica;
                }   
            }            
            $totales_hoja6 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $acumulados_hoja6 [$val] = $sum_acumulado;
        }
        //INICIA Gastos Ventas - Hoja 7
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
                
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
                //clock($value->BC_Cuenta_Id, $sum, $ejercicio, $periodo, $tableName);
                if (is_null($sum)) {
                    Session::flash('error', 'Cta no existe. #cta:'.$value->BC_Cuenta_Id);
                    
                    unset($hoja7[$key]);
                    unset($items[$key]);
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja7[$key]);
                    unset($items[$key]);
                    //clock($hoja7);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA               
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja7[trim($value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2)] = $sum * $value->RGC_multiplica;
                }
            }
            $totales_hoja7 [$val] = array_sum(array_pluck($items, 'movimiento'));            
            $acumulados_hoja7 [$val] = $sum_acumulado;
        }
        //INICIA Gastos Financieros - Hoja 8
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
                $sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
                if (is_null($sum)) {
                    Session::flash('error', 'Cta no existe. #cta:'.$value->BC_Cuenta_Id);
                    $sum = 0;
                    unset($hoja8[$key]);
                    unset($items[$key]);
                } else if($sum == 0){
                    //ELIMINAR DE LA LISTA DE DESPLIEGUE CUENTAS CON ACUMULADO CERO
                    unset($hoja8[$key]);
                    unset($items[$key]);
                } else{
                    //SE GUARDA ACUMULADO DE ESA CUENTA               
                    $sum_acumulado += $sum * $value->RGC_multiplica;
                    $acumuladosxcta_hoja8[trim($value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2)] = $sum * $value->RGC_multiplica;
                }
            }
            $totales_hoja8 [$val] = array_sum(array_pluck($items, 'movimiento'));
            $acumulados_hoja8 [$val] = $sum_acumulado;
        }
        //inicia reportes adicionales
        $docs = DB::select("SELECT * FROM RPT_RG_Documentos 
                            WHERE DOC_ejercicio = ? AND DOC_periodo = ? AND DOC_sociedad = ?",
                            [$ejercicio, $periodo, $sociedad]);
  
       
       //obtener fecha de actualizacion 
        $fechaA = DB::table('RPT_RG_FechasActualizadoBalanza')
            ->where('RGF_EjercicioPeriodo', Input::get('cbo_periodo'))
            ->value('RGF_FechaActualizado');
        $fecha_actualizado = Carbon::parse($fechaA);
        $ultimoDiaMes = Carbon::parse($ejercicio . '/' . $periodo . '/01')->endOfMonth();
        $fecha_corte = ($fecha_actualizado->lt($ultimoDiaMes))? $fecha_actualizado : $ultimoDiaMes;
        
        $fechaA = (is_null($fechaA)) ? '' : 'Actualizado: '. $helper->getHumanDate($fechaA);
        $leyenda_fecha_corte_estados = $helper->getHumanDate2($fecha_corte->toDateString());
        $leyenda_fecha_corte = $helper->getHumanDate2($ultimoDiaMes->toDateString());
     
        //GUARDAR $box del periodo
        if($periodo == '12'){
            foreach ($box as $key => $value) {//PARA ESTADO DE COSTOS
                $con = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                ->where('VFP_Ejercicio_periodo', Input::get('cbo_periodo'))
                ->where('VFP_SOC_sociedad_id', $soc->SOC_Id)
                ->where('VFP_Box_key', $key)->update(['VFP_Box_Monto' => $value]);
                if ($con == 0) {
                    DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                    ->insert([
                        'VFP_Ejercicio_periodo' => Input::get('cbo_periodo')
                        , 'VFP_SOC_sociedad_id' => $soc->SOC_Id
                        , 'VFP_Box_Monto' => $value
                        , 'VFP_Box_key' => $key
                    ]);
                }
                
            }

            /*
            foreach ($hoja2 as $value) { //PARA ESTADO DE RESULTADOS
                $percent = ($totales_hoja2[$value->RGC_tabla_titulo] == 0) ? '0' : ($value->movimiento / $totales_hoja2[$value->RGC_tabla_titulo]) * 100;
                
                $con = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                ->where('VFP_Ejercicio_periodo', Input::get('cbo_periodo'))
                ->where('VFP_SOC_sociedad_id', $soc->SOC_Id)
                ->where('VFP_Box_key', $value->BC_Cuenta_Id.$value->RGC_BC_Cuenta_Id2)
                ->update(['VFP_Box_Monto' => $value->movimiento]);
                if ($con == 0) {
                    DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                    ->insert([
                        'VFP_Ejercicio_periodo' => Input::get('cbo_periodo'), 
                        'VFP_SOC_sociedad_id' => $soc->SOC_Id, 
                        'VFP_Box_Monto' => $value->movimiento, 
                        'VFP_Box_key' => $value->BC_Cuenta_Id.$value->RGC_BC_Cuenta_Id2
                    ]);
                }
                $con2 = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                ->where('VFP_Ejercicio_periodo', Input::get('cbo_periodo'))
                ->where('VFP_SOC_sociedad_id', $soc->SOC_Id)
                ->where('VFP_Box_key', $value->BC_Cuenta_Id.$value->RGC_BC_Cuenta_Id2.'%')
                ->update(['VFP_Box_Monto' => $percent]);
                if ($con2 == 0) {
                    DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                    ->insert([
                        'VFP_Ejercicio_periodo' => Input::get('cbo_periodo'), 
                        'VFP_SOC_sociedad_id' => $soc->SOC_Id, 
                        'VFP_Box_Monto' => $percent, 
                        'VFP_Box_key' => $value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2 . '%'
                    ]);
                }
            } 
            foreach ($totalesIngresosGastos as $value) { //PARA ESTADO DE RESULTADOS TOTALES
                $con = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                ->where('VFP_Ejercicio_periodo', Input::get('cbo_periodo'))
                ->where('VFP_SOC_sociedad_id', $soc->SOC_Id)
                ->where('VFP_Box_key', $value['titulo'])
                ->update(['VFP_Box_Monto' => $value['periodo']]);
                if ($con == 0) {
                    DB::table('RPT_RG_ValoresFormulasPorPeriodo')
                    ->insert([
                        'VFP_Ejercicio_periodo' => Input::get('cbo_periodo'), 
                        'VFP_SOC_sociedad_id' => $soc->SOC_Id, 
                        'VFP_Box_Monto' => $value['periodo'], 
                        'VFP_Box_key' => $value['titulo']
                    ]);
                }
            }
            */
        }
        $box_anterior = [];
        if($periodo == '01'){
            $con = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
            ->where('VFP_Ejercicio_periodo', ((int)$ejercicio - 1).'-12')
            ->where('VFP_SOC_sociedad_id', $soc->SOC_Id)->get();
            foreach ($con as $value) {
                $box_anterior[$value->VFP_Box_key] = $value->VFP_Box_Monto;
            }
        }
        $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
        $nombrePeriodo = $helper->getNombrePeriodo($periodo);

        
        $params = compact('leyenda_fecha_corte_estados','leyenda_fecha_corte','box_anterior', 'sociedad','fechaA','personalizacion', 'actividades', 'ultimo', 'ejercicio', 
        'utilidadEjercicio',/* 'ue_ingresos', 'ue_gastos_costos',*/ 
        'nombrePeriodo', 'periodo',
        'acumuladosxcta_hoja1', 'hoja1',
        'acumulados_hoja2', 'totales_hoja2', 'acumuladosxcta', 'hoja2', 
        'total_inventarios', 'data_formulas_33',
        'box', 'total_inventarios_acum',
        'data_inventarios_4', 'total_inventarios_4',
        'acumulados_hoja5', 'totales_hoja5', 'acumuladosxcta_hoja5', 'hoja5',
        'acumulados_hoja6', 'totales_hoja6', 'acumuladosxcta_hoja6', 'hoja6',
        'acumulados_hoja7', 'totales_hoja7', 'acumuladosxcta_hoja7', 'hoja7',
        'acumulados_hoja8', 'totales_hoja8', 'acumuladosxcta_hoja8', 'hoja8',
        'docs','totalesIngresosGastos',
        'datosEstadoCostos');
        Session::put('data_rg', $params);
        return view('Mod_RG.RG03_reporte', $params);
    }
    public function ajustesfill(){
        $mo = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'mo')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $indirectos = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'ind')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $mp_ot = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
         $mp = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $pp = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'pp')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');
        $pt = DB::table('RPT_RG_Ajustes') 
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', Input::get('ejercicio'))
            ->where('AJU_sociedad', Input::get('sociedad'))
            ->where('AJU_periodo', Input::get('periodo'))
            ->value('AJU_valor');   
        $mo = (is_null($mo))?'0.00':$mo;
        $indirectos = (is_null($indirectos))?'0.00':$indirectos;
        $mp_ot = (is_null($mp_ot))?'0.00':$mp_ot;
        $mp = (is_null($mp))?'0.00':$mp;
        $pp = (is_null($pp))?'0.00':$pp;
        $pt = (is_null($pt))?'0.00':$pt;
        return compact('mo', 'indirectos', 'mp_ot', 'mp', 'pp', 'pt');
    }
    public function RGPDF($opcion){         
        $data = Session::get('data_rg');                   
        $sociedad = Session::get('sociedad_rg');
        $letra_reporte = ['', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];                   
        $fecha_corte = ucfirst($data["leyenda_fecha_corte"]);
            switch ($opcion) {
                case '0':
                    $vista = 'Mod_RG.';
                    $file_name = "-";
                    break;
                case '1':
                    $vista = 'Mod_RG.RG03_reporte_BG';
                    $file_name = " Balance General";
                   
                    $hoja1_activos = array_where($data['hoja1'], function ($key, $value) {
                        return is_numeric(strpos($value->RGC_tabla_titulo, 'ACTIVO'));
                    });
                    $hoja1_pasivos = array_where($data['hoja1'], function ($key, $value) {
                        return !is_numeric(strpos($value->RGC_tabla_titulo, 'ACTIVO'));
                    });                    
                   //dd($hoja1_pasivos);
                    //dd($data['acumuladosxcta_hoja1']);
                    $data["hoja1_activos"] = $hoja1_activos;
                    $data["hoja1_pasivos"] = $hoja1_pasivos;
                    break;
                case '2':
                    $vista = 'Mod_RG.RG03_reporte_ER';
                    $file_name = " Estado de Resultados";
                    $fecha_corte = 'Del 01 de Enero ' . $data["leyenda_fecha_corte_estados"];
                    break;
                case '3':
                    //este caso no es alcanzado ya que se utilizo otro boton para generar el pdf
                    $vista = 'Mod_RG.RG03_reporte_EC';
                    $file_name = " Estado de Costos";
                    $fecha_corte = 'Del 01 de Enero ' . $data["leyenda_fecha_corte_estados"];
                    break;
                case '4':
                    $vista = 'Mod_RG.RG03_reporte_Inv';
                    $file_name = " Inventario";
                    break;
                case '5':
                    $vista = 'Mod_RG.RG03_reporte_GtosFab';
                    $file_name = " Gastos de Fabricacion";
                    break;
                case '6':
                    $vista = 'Mod_RG.RG03_reporte_GtosAdmon';
                    $file_name = " Gastos de Administración";
                    break;
                case '7':
                    $vista = 'Mod_RG.RG03_reporte_GtosVentas';
                    $file_name = " Gastos de Ventas";
                    break;
                case '8':
                    $vista = 'Mod_RG.RG03_reporte_GtosFinanzas';
                    $file_name = " Gastos Financieros";
                    break;                
            }
            $data["vista"] = $vista;                      
            $data["pie_nombre"] = $file_name;                      
            $data["letra_reporte"] = $letra_reporte[$opcion];                      
            $data["fecha_corte"] = $fecha_corte;
            // $fecha_corte = 'Del 01 de Enero ' . $data["leyenda_fecha_corte_estados"];
            $data["sociedad"] = $sociedad;                      
            $data["fecha_actualizado"]=false;
            //return view('Mod_RG.RG03PDF', $data);
            $pdf = PDF::loadView('Mod_RG.RG03PDF', $data);
            //$pdf = new FPDF('L', 'mm', 'A4');
            $pdf->setPaper('Letter', 'portrait')->setOptions(['isPhpEnabled' => true]);                        
            //$pdf->setOptions(['isPhpEnabled' => true]);             
            
            return $pdf->stream($data["ejercicio"]."_".$data["periodo"].$file_name[1].'.pdf');      
    }
    public function getAcumulado_RG_Ajustes($id, $ejercicio, $sociedad, $periodo){
        $suma = 0;
        for ($i=1; $i <=(int) $periodo; $i++) {
            $peryodo[] = ($i < 10) ? '0' . $i : '' . $i;
           } 
           $sql = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', $id)
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->whereIn('AJU_periodo', $peryodo)
            ->lists('AJU_valor');
            $val = array_sum($sql);
            $val = (is_null($val)) ? 0 : $val * 1;
            $suma += $val;
        
        return $suma;
    }

    public function getAcumulado_muliix($sociedad_id, $periodo_hasta, $ejercicio, $suffixes_keys, $box_config){
        
        $helper = AppHelper::instance();
        $acumulados_mullix['mp'] = 0;
        $acumulados_mullix['pp'] = 0;
        $acumulados_mullix['pt'] = 0;
        for ($i = 1; $i <= (int) $periodo_hasta; $i++) {
            $peryodo = ($i < 10) ? '0' . $i : '' . $i;
            //clock('Acumulado_muliix - '.$peryodo.'-'.$ejercicio);
            $inv = $helper->getInv($peryodo, $ejercicio, $suffixes_keys, $box_config);
            //clock($inv);
            if (count($inv) != 1) {
                if ($suffixes_keys) {
                    $acumulados_mullix['mp'] += (array_key_exists('mp_ini', $inv)) ? $inv['mp_ini'] : 0;
                    $acumulados_mullix['pp'] += (array_key_exists('pp_ini', $inv)) ? $inv['pp_ini'] : 0;
                    $acumulados_mullix['pt'] += (array_key_exists('pt_ini', $inv)) ? $inv['pt_ini'] : 0;
                    
                }else{
                    $acumulados_mullix['mp'] += (array_key_exists('mp_fin', $inv)) ? $inv['mp_fin'] : 0;
                    $acumulados_mullix['pp'] += (array_key_exists('pp_fin', $inv)) ? $inv['pp_fin'] : 0;
                    $acumulados_mullix['pt'] += (array_key_exists('pt_fin', $inv)) ? $inv['pt_fin'] : 0;
                }
            }
        }
        /*
        //Guardar acumulado del ejercicio 
        if($periodo_hasta == 12){
            $tag = '_fin';
            if ($suffixes_keys) {
                $tag = '_ini';
            }
            foreach ($acumulados_mullix as $key => $value) {
               // DB::where
               $con = DB::table('RPT_RG_AcumuladosPorEjercicio')->where('APE_ejercicio', $ejercicio)
                ->where('APE_SOC_sociedad_id', $sociedad_id)
                ->where('APE_RGC_Cuenta_Id', $key.$tag)->update(['APE_Monto' => $value]);
                if ($con == 0) {
                    DB::table('RPT_RG_AcumuladosPorEjercicio')
                    ->insert([
                        'APE_ejercicio' => $ejercicio
                        , 'APE_SOC_sociedad_id' => $sociedad_id
                        , 'APE_Monto' => $value
                        , 'APE_RGC_Cuenta_Id' => $key.$tag
                    ]);
                }
               
            }
        }
        //añadir acumulado del periodo anterior
        $inv_anterior = DB::table('RPT_RG_AcumuladosPorEjercicio')
        ->where('APE_ejercicio', ((int)$ejercicio - 1))
        ->where('APE_SOC_sociedad_id', $sociedad_id)->get();
        $inventarios = array();
        foreach ($inv_anterior as $value) {
            $inventarios[$value->APE_RGC_Cuenta_Id] = $value->APE_Monto;
        }
        if (count($inventarios) > 0) {
            if ($suffixes_keys) {
                $acumulados_mullix['mp'] += (array_key_exists('mp_ini', $inventarios)) ? $inventarios['mp_ini'] : 0;
                $acumulados_mullix['pp'] += (array_key_exists('pp_ini', $inventarios)) ? $inventarios['pp_ini'] : 0;
                $acumulados_mullix['pt'] += (array_key_exists('pt_ini', $inventarios)) ? $inventarios['pt_ini'] : 0;
            } else {
                $acumulados_mullix['mp'] += (array_key_exists('mp_fin', $inventarios)) ? $inventarios['mp_fin'] : 0;
                $acumulados_mullix['pp'] += (array_key_exists('pp_fin', $inventarios)) ? $inventarios['pp_fin'] : 0;
                $acumulados_mullix['pt'] += (array_key_exists('pt_fin', $inventarios)) ? $inventarios['pt_fin'] : 0;
            }
        }
        */
       // clock('TOTAL ------------- $acumulados_mullix', $acumulados_mullix);
        return $acumulados_mullix;
    }
    public function reporteEstadoCostosProcedimiento($input_sociedad, $ejercicio, $periodo)
    {
        // $input_sociedad = "ITEKNIA EQUIPAMIENTO, S.A. DE C.V.";
        // $ejercicio = '2021';
        // $periodo = '12';
        $soc = DB::table('RPT_Sociedades')
        ->where('SOC_Nombre', $input_sociedad)
        ->where('SOC_Reporte', 'ReporteGerencial')
        ->first();
        $helper = AppHelper::instance();
        $matrix = [];
        //inicial
        $matrix[] = [
            "INVENTARIO INICIAL MP",
            "COMPRAS",
            "MERMAS",
            "INVENTARIO MP",
            "INVETARIO FINAL DE MP",
            "MP UTILIZADA",
            "",
            "MANO DE OBRA",
           // "MANO DE OBRA PLANIMENTRIA",
            "MAQUILAS",
            "GASTOS INDIRECTOS",
            "TOTAL DE GASTOS DE FABRICACION ",
            "",
            "PROD EN PROCESO",
            "INV. INCI DE PP",
            "TOTAL INVENTARIO PP",
            "INVENTARIO FINAL P P ",
            "PRODUCTO TERMINADO ",
            "",
            "INV INICIAL DE P. TERMINADO",
            "TOTAL DE INV DE  P. TERMINADO",
            "INV FINAL DE P. TERMINADO ",
            "COSTO"
        ];
        //dd($titulos);
        
        $matrix[] = self::estadoCostoPorPeriodo('Inicial', '01', $ejercicio, $soc, $helper, []);
        $aux[] = [
            str_replace( ',', '', $matrix[1][0]) //INVENTARIO INICIAL  MP	"A"
        , str_replace( ',', '', $matrix[1][13])  //INV. INCI DE PP	"K"
        , str_replace( ',', '', $matrix[1][18])]; //INV INICIAL DE P. TERMINADO    "O"
        //return self::estadoCostoPorPeriodo('Mes', '01', $ejercicio, $soc, $helper,[]);
        for ($i = 1; $i <= (int) $periodo; $i++) {
            $peryodo = ($i < 10) ? '0' . $i : '' . $i;
            $mes = self::estadoCostoPorPeriodo('Mes', $peryodo, $ejercicio, $soc, $helper,[]);
            $matrix[] = $mes;
            if ($i === (int) $periodo) {
              $aux[] = [str_replace( ',', '', $mes[4])   //INVETARIO FINAL DE MP "E"
              , str_replace( ',', '', $mes[15] ) //INVENTARIO FINAL P P 	"M"
              , str_replace( ',', '', $mes[20])];   //INV FINAL DE P. TERMINADO 	"Q"
            }
        }
        //return $aux;
         $matrix[] = self::estadoCostoPorPeriodo('Acumulado', $periodo, $ejercicio, $soc, $helper, $aux);

        return ($matrix);

    }   
    public  function estadoCostoPorPeriodo($tipo, $periodo, $ejercicio, $soc, $helper, $aux){
        $tableName = $soc->SOC_AUX_DB;
        $sociedad = $soc->SOC_Nombre;
        
        if ($tipo == 'Inicial') {
            $fecha = $ejercicio . '/' . $periodo . '/01';
            $fecha = Carbon::parse($fecha);
            $fecha = $fecha->subMonth();
            $periodo = $fecha->format('m');
            $ejercicio = $fecha->format('Y');
            $periodo_ant = null;
            $ejercicio_ant = null;
        } else {
            $fecha = $ejercicio . '/' . $periodo . '/01';
            $fecha = Carbon::parse($fecha);
            $fecha = $fecha->subMonth();
            $periodo_ant = $fecha->format('m');
            $ejercicio_ant = $fecha->format('Y');
        }
        
        // INICIA EC 
        $box_config = DB::select("select RGV_alias,
        RGV_valor_default, RGV_tabla_titulo from [dbo].[RPT_RG_VariablesReporte]");
        
        $box = array();
        foreach ($box_config as $value) {
            $box[$value->RGV_alias] = $value->RGV_valor_default;
            $box[$value->RGV_alias . '_acumulado'] = $value->RGV_valor_default;
        }
      
        //Obtenemos Inv. del periodo
        $inv_Final = $helper->getInv($periodo, $ejercicio, false, $box_config);
        
        
        foreach ($box_config as $value) {
            if (key_exists($value->RGV_alias, $inv_Final)) {
                $box[$value->RGV_alias] = $inv_Final[$value->RGV_alias];
            }
        }
        
        $data_formulas_33 = DB::select("select RGC_BC_Cuenta_Id, 
                                RGC_valor_default, RGC_multiplica from RPT_RG_ConfiguracionTabla 
                                where RGC_hoja = '33' and RGC_tipo_renglon IN('FORMULA', 'INPUT') 
                                AND (RGC_sociedad = '0' OR RGC_sociedad = ?)
                                order by RGC_tabla_linea", [$soc->SOC_Id]);
      
        //ponemos las variables del usuario e la caja             
        $box['mp_ot']  = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mp_ot')
        ->where('AJU_ejercicio', $ejercicio)
        ->where('AJU_sociedad', $sociedad)
        ->where('AJU_periodo', $periodo)
        ->value('AJU_valor');
        if ($tipo == 'Inicial') {
            foreach ($data_formulas_33 as $value) {
            eval("\$box['" . $value->RGC_BC_Cuenta_Id . "'] = (" . $value->RGC_valor_default . ")*" . $value->RGC_multiplica . ";");
            //Ejemplo con primer fila.
            //$box['A'] = ($box['mp_ini']) * 1;
            }
            return [
                    number_format($box['E'],'2', '.',',') // "INVENTARIO INICIAL MP",
                    , number_format(0, '2', '.', ',') // "COMPRAS",
                    , number_format(0, '2', '.', ',') //MERMAS
                    , number_format($box['E'],'2', '.',',') // "INVENTARIO MP",
                    , number_format($box['E'],'2', '.',',') // "INVETARIO FINAL DE MP",
                    , number_format(0, '2', '.', ',') // "MP UTILIZADA",
                    , ''
                    , number_format(0, '2', '.', ',') // "MANO DE OBRA",
                    //,$box[''] // "MANO DE OBRA PLANIMENTRIA",
                    , number_format(0, '2', '.', ',') // "MAQUILAS",
                    , number_format(0, '2', '.', ',') // "GASTOS INDIRECTOS",
                    , number_format(0, '2', '.', ',') // "TOTAL DE GASTOS DE FABRICACION ",
                    , ''
                    , number_format(0, '2', '.', ',') // "PROD EN PROCESO",
                    , number_format($box['M'],'2', '.',',') // "INV. INCI DE PP",
                    , number_format($box['M'],'2', '.',',') // "TOTAL INVENTARIO PP",
                    , number_format($box['M'],'2', '.',',') // "INVENTARIO FINAL P P ",
                    , number_format(0, '2', '.', ',') // "PRODUCTO TERMINADO ",
                    , ''
                    , number_format($box['Q'],'2', '.',',') // "INV INICIAL DE P. TERMINADO",
                    , number_format($box['Q'],'2', '.',',') // "TOTAL DE INV DE  P. TERMINADO",
                    , number_format($box['Q'],'2', '.',',') // "INV FINAL DE P. TERMINADO ",
                    , number_format(0, '2', '.', ',') // "COSTO"
                ];
        }
        $box['input_mo'] = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mo')
        ->where('AJU_ejercicio', $ejercicio)
        ->where('AJU_sociedad', $sociedad)
        ->where('AJU_periodo', $periodo)
        ->value('AJU_valor');
      
        $box['input_indirectos'] = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'ind')
        ->where('AJU_ejercicio', $ejercicio)
        ->where('AJU_sociedad', $sociedad)
        ->where('AJU_periodo', $periodo)
        ->value('AJU_valor');
      
        
        $box['input_mo_acumulado'] = self::getAcumulado_RG_Ajustes('mo', $ejercicio, $sociedad, $periodo);
       
       
        $box['input_indirectos_acumulado'] = self::getAcumulado_RG_Ajustes('ind', $ejercicio, $sociedad, $periodo);
        
        $box['mp_ot_acumulado'] = self::getAcumulado_RG_Ajustes('mp_ot', $ejercicio, $sociedad, $periodo);
        
            $version = DB::table('RPT_RG_CatalogoVersionCuentas')
            ->where('CAT_periodo', $periodo)
                ->value('CAT_version');
            $version = (is_null($version)) ? 0 : $version;
            $hoja3 = DB::select("SELECT 
                                [BC_Ejercicio]
                                ,[BC_Cuenta_Id]
                                ,[BC_Cuenta_Nombre]
                                ,[BC_Saldo_Inicial]
                                ,[BC_Saldo_Final]
                                ,[BC_Movimiento_" . $periodo . "] * conf.RGC_multiplica as movimiento   
                                ,[RGC_hoja]
                                ,[RGC_tabla_titulo]
                                ,[RGC_tabla_linea]
                                ,[RGC_multiplica]
                                ,[RGC_estilo]
                                ,[RGC_BC_Cuenta_Id2]
                            FROM " . $tableName . " bg
                            LEFT join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
                            WHERE [BC_Ejercicio] = ?
                            AND conf.RGC_hoja = '3' AND conf.RGC_tipo_renglon = 'CUENTA'
                            AND [BC_Movimiento_" . $periodo . "] IS NOT NULL
                            AND (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
                            AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)
                            order by RGC_hoja, RGC_tabla_linea
                                    ", [$ejercicio, $version, $soc->SOC_Id]);
            
        $titulos_hoja3 =
            array_map(
                'trim',
                array_pluck($hoja3, 'RGC_tabla_titulo')
            );
        
        $acumulados_hoja3 = [];
        $grupos_hoja3 = array_unique($titulos_hoja3); //COMPRAS NETAS, MO, GASTOS IND
        $ctas_hoja3 = [];
           
        foreach ($grupos_hoja3 as $key => $val) {
            $items = array_where($hoja3, function ($key, $value) use ($val) {
                return $value->RGC_tabla_titulo == $val;
            });
             //clock($items);
            $ctas_hoja3[$val] = array_sum(array_pluck($items, 'movimiento'));
            $sum_acumulado = 0;
            foreach ($items as $key => $value) {
                $sum = $helper->Rg_GetSaldoFinalSinSaldoInicial($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
                //clock(["sum" => $sum, "cta" => $value->BC_Cuenta_Id, "ejercicio" =>$ejercicio.'/'.$periodo, "table" => $tableName]);
                if (is_null($sum)) {
                    Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:' . $value->BC_Cuenta_Id);
                    $sum = 0;
                }
                $sum_acumulado += $sum * $value->RGC_multiplica;
                $acumuladosxcta_3[$value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2] = $sum * $value->RGC_multiplica;
            }
            //clock([$val =>$sum_acumulado]);
            $acumulados_hoja3[$val] = $sum_acumulado;
        }
        foreach ($box_config as $value) {
            //ponemos las variables de las CUENTAS en la caja
            if (key_exists($value->RGV_tabla_titulo, $ctas_hoja3)) {
                $box[$value->RGV_alias] = $ctas_hoja3[$value->RGV_tabla_titulo];
                $box[$value->RGV_alias . '_acumulado'] = $acumulados_hoja3[$value->RGV_tabla_titulo];
            }
        }
       
        if ($tableName != 'RPT_BalanzaComprobacion') { 
            // si no es ITEKNIA SA de CV
            //obtener inventario de la tabla de RPT_RG_Ajustes           
            //asignar MP PP PT a box

            $mp_perido_anterior = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', $ejercicio_ant)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo_ant)
                ->value('AJU_valor');
            $mp_perido_anterior = (is_null($mp_perido_anterior)) ? 0 : $mp_perido_anterior;
            $box['mp_ini'] = $mp_perido_anterior;
            //$box['mp_ini_acumulado'] = self::getAcumulado_RG_Ajustes('mp', $ejercicio_ant, $sociedad, $periodo_ant);
            
            //clock('mp_ini '.$box['mp_ini']);
            $pp_perido_anterior = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pp')
            ->where('AJU_ejercicio', $ejercicio_ant)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo_ant)
                ->value('AJU_valor');
            $pp_perido_anterior = (is_null($pp_perido_anterior)) ? 0 : $pp_perido_anterior;
            //clock('pp_ini '.$pp_perido_anterior);
            $box['pp_ini'] = $pp_perido_anterior;
            //$box['pp_ini_acumulado'] = self::getAcumulado_RG_Ajustes('pp', $ejercicio_ant, $sociedad, $periodo_ant);

            $pt_perido_anterior = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', $ejercicio_ant)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo_ant)
                ->value('AJU_valor');
            $pt_perido_anterior = (is_null($pt_perido_anterior)) ? 0 : $pt_perido_anterior;
            $box['pt_ini'] = $pt_perido_anterior;
            //$box['pt_ini_acumulado'] = self::getAcumulado_RG_Ajustes('pt', $ejercicio_ant, $sociedad, $periodo_ant);
            // }

        } 
        else {

            $inv_Inicial = $helper->getInv($periodo_ant, $ejercicio_ant, true, $box_config);
           
            //mov del periodo
            foreach ($box_config as $value) {
                //ponemos las variables de LOCALIDADES en la caja
                //dd($value->RGV_alias, $inv_Inicial, key_exists($value->RGV_alias, $inv_Inicial));
                if (key_exists($value->RGV_alias, $inv_Inicial)) {
                    $box[$value->RGV_alias] = $inv_Inicial[$value->RGV_alias];
                }
            }
            // $box['mp_ini_acumulado'] = $inv_Inicial['mp_ini'];
            // $box['pp_ini_acumulado'] = $inv_Inicial['pp_ini'];
            // $box['pt_ini_acumulado'] = $inv_Inicial['pt_ini'];
        }
        $acumulado_muliix['mp'] = 0;
        $acumulado_muliix['pp'] = 0;
        $acumulado_muliix['pt'] = 0;
        
        //solo obtenemos acumulados del ejercicio actual
        // if ($ejercicio === $ejercicio_ant) { //sobreescribimos acumulados iniciales
        //     $acumulado_muliix = self::getAcumulado_muliix($soc->SOC_Id, $periodo_ant, $ejercicio_ant, true, $box_config);
            
        //     $box['mp_ini_acumulado'] = $acumulado_muliix['mp'];
        //     $box['pp_ini_acumulado'] = $acumulado_muliix['pp'];
        //     $box['pt_ini_acumulado'] = $acumulado_muliix['pt'];
        // }
        
        $mp_ot_perido_anterior = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mp_ot')
        ->where('AJU_ejercicio', $ejercicio_ant)
        ->where('AJU_sociedad', $sociedad)
        ->where('AJU_periodo', $periodo_ant)
        ->value('AJU_valor');
        
        $mp_ot_perido_anterior = (is_null($mp_ot_perido_anterior)) ? 0 : $mp_ot_perido_anterior;
        
        $box['pp_ini'] += $mp_ot_perido_anterior;
        //obtenemos acumulado de mp_ot y lo sumamos a pp_ini
        // $box['pp_ini_acumulado'] += self::getAcumulado_RG_Ajustes('mp_ot', $ejercicio_ant, $sociedad, $periodo_ant);
        ////////////////////////////////////////////////////////
        

        // $box['mp_fin_acumulado'] = $acumulado_muliix['mp'] + $inv_Final['mp_fin'];
        // $box['pp_fin_acumulado'] = $acumulado_muliix['pp'] + $inv_Final['pp_fin'];
        // $box['pt_fin_acumulado'] = $acumulado_muliix['pt'] + $inv_Final['pt_fin'];

        if ($tableName != 'RPT_BalanzaComprobacion') {
            //NO ES ITEKNIA
            //sobreescribir valores de mp pp y pt
            $mp_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->value('AJU_valor');
            $mp_perido_actual = (is_null($mp_perido_actual)) ? 0 : $mp_perido_actual;
            $box['mp_fin'] = $mp_perido_actual;
            
            $pp_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pp')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->value('AJU_valor');
            $pp_perido_actual = (is_null($pp_perido_actual)) ? 0 : $pp_perido_actual;
            $box['pp_fin'] = $pp_perido_actual;
            
            $pt_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->value('AJU_valor');
            $pt_perido_actual = (is_null($pt_perido_actual)) ? 0 : $pt_perido_actual;
            $box['pt_fin'] = $pt_perido_actual;
            
            $box['mp_fin_acumulado'] = self::getAcumulado_RG_Ajustes('mp', $ejercicio, $sociedad, $periodo);
            $box['pp_fin_acumulado'] = self::getAcumulado_RG_Ajustes('pp', $ejercicio, $sociedad, $periodo);
            $box['pt_fin_acumulado'] = self::getAcumulado_RG_Ajustes('pt', $ejercicio, $sociedad, $periodo);
        } //end otras sociedades
        //  clock( $box);

        
        $data_formulas_34 = DB::select("select RGC_BC_Cuenta_Id, 
        RGC_valor_default, RGC_multiplica from RPT_RG_ConfiguracionTabla 
        where RGC_hoja = '34' and RGC_tipo_renglon IN('FORMULA', 'INPUT') 
        AND (RGC_sociedad = '0' OR RGC_sociedad = ?)
        order by RGC_tabla_linea", [$soc->SOC_Id]);

        foreach ($data_formulas_34 as $value) {
            eval("\$box['" . $value->RGC_BC_Cuenta_Id . "'] = (" . $value->RGC_valor_default . ")*" . $value->RGC_multiplica . ";");
            //Ejemplo con primer fila 34.
            //$box['A'] = ($box['mp_ini']) * 1;
        }
        foreach ($data_formulas_33 as $value) {
            eval("\$box['" . $value->RGC_BC_Cuenta_Id . "'] = (" . $value->RGC_valor_default . ")*" . $value->RGC_multiplica . ";");
            //Ejemplo con primer fila.
            //$box['A'] = ($box['mp_ini']) * 1;
        }
        
        switch ($tipo) {
            case 'Mes':
                $columna_reporte = [
                    number_format($box['A'],'2', '.',',')// "INVENTARIO INICIAL MP",
                    ,number_format($box['B'],'2', '.',',')// "COMPRAS",
                    ,number_format($box['C'],'2', '.',',') //MERMAS
                    ,number_format($box['D'],'2', '.',',')// "INVENTARIO MP",
                    ,number_format($box['E'],'2', '.',',')// "INVETARIO FINAL DE MP",
                    ,number_format($box['F'],'2', '.',',')// "MP UTILIZADA",
                    ,''
                    ,number_format($box['G'],'2', '.',',') // "MANO DE OBRA",
                    //,$box[''] // "MANO DE OBRA PLANIMENTRIA",
                    ,number_format($box['H'],'2', '.',',') // "MAQUILAS",
                    ,number_format($box['I'],'2', '.',',') // "GASTOS INDIRECTOS",
                    ,number_format($box['S'],'2', '.',',') // "TOTAL DE GASTOS DE FABRICACION ",
                    ,''
                    ,number_format($box['J'],'2', '.',',') // "PROD EN PROCESO",
                    ,number_format($box['K'],'2', '.',',') // "INV. INCI DE PP",
                    ,number_format($box['L'],'2', '.',',') // "TOTAL INVENTARIO PP",
                    ,number_format($box['M'],'2', '.',',') // "INVENTARIO FINAL P P ",
                    ,number_format($box['N'],'2', '.',',') // "PRODUCTO TERMINADO ",
                    ,''
                    ,number_format($box['O'],'2', '.',',') // "INV INICIAL DE P. TERMINADO",
                    ,number_format($box['P'],'2', '.',',') // "TOTAL DE INV DE  P. TERMINADO",
                    ,number_format($box['Q'],'2', '.',',') // "INV FINAL DE P. TERMINADO ",
                    ,number_format($box['R'],'2', '.',',') // "COSTO"
                ];
                break;
            case 'Acumulado':
                $columna_reporte = [
                    number_format($aux[0][0],'2', '.',',')// "INVENTARIO INICIAL MP",
                    ,number_format($box['BB'],'2', '.',',')// "COMPRAS",
                    ,number_format($box['CC'],'2', '.',',') //MERMAS
                    ,number_format($aux[0][0]+ $box['BB']+ $box['CC'],'2', '.',',')// "INVENTARIO MP",
                    ,number_format($aux[1][0],'2', '.',',')// "INVETARIO FINAL DE MP",
                    ,number_format(($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0],'2', '.',',')// "MP UTILIZADA",
                    ,''
                    ,number_format($box['GG'],'2', '.',',') // "MANO DE OBRA",
                    //,$box[''] // "MANO DE OBRA PLANIMENTRIA",
                    ,number_format($box['HH'],'2', '.',',') // "MAQUILAS",
                    ,number_format($box['II'],'2', '.',',') // "GASTOS INDIRECTOS",
                    ,number_format($box['SS'],'2', '.',',') // "TOTAL DE GASTOS DE FABRICACION ",
                    ,''
                    ,number_format((($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0]) + $box['SS'],'2', '.',',') // "PROD EN PROCESO",
                    ,number_format($aux[0][1],'2', '.',',') // "INV. INCI DE PP",
                    ,number_format((($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0]) + $box['SS'] + $aux[0][1],'2', '.',',') // "TOTAL INVENTARIO PP",
                    ,number_format($aux[1][1],'2', '.',',') // "INVENTARIO FINAL P P ",
                    ,number_format(((($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0]) + $box['SS'] + $aux[0][1]) - $aux[1][1] ,'2', '.',',') // "PRODUCTO TERMINADO ",
                    ,''
                    ,number_format($aux[0][2],'2', '.',',') // "INV INICIAL DE P. TERMINADO",
                    ,number_format((((($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0]) + $box['SS'] + $aux[0][1]) - $aux[1][1]) + $aux[0][2],'2', '.',',') // "TOTAL DE INV DE  P. TERMINADO",
                    ,number_format($aux[1][2],'2', '.',',') // "INV FINAL DE P. TERMINADO ",
                    ,number_format(((((($aux[0][0] + $box['BB'] + $box['CC']) - $aux[1][0]) + $box['SS'] + $aux[0][1]) - $aux[1][1]) + $aux[0][2]) - $aux[1][2],'2', '.',',') // "COSTO"
                ];
                break;
        }

        return $columna_reporte;
        
    }
   
}
