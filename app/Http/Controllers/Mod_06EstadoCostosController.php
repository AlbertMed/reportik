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
class Mod_06EstadoCostosController extends Controller
{
  
    public function index(Request $request)
    {
        if (Auth::check()) { 
            
            $validator = Validator::make($request->all(), [
                'text_selUno' => 'required', 
                'fecha_datepicker' => 'required',
                         
            ]);
            if ($validator->fails()) { 
                return redirect()
                            ->back()
                            ->withErrors($validator);
            }

            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            
            $sociedad = Input::get('text_selUno');
            $periodo = explode('-', Input::get('fecha_datepicker'));        
            $ejercicio = $periodo[0];
            $periodo = $periodo[1]; 
            $data = self::reporteProcedimiento($sociedad, $ejercicio, $periodo);
            //dd($data);
            return view('Contabilidad.EstadoCostosIndex', compact('data', 'sociedad','actividades', 'ultimo', 'ejercicio', 'periodo'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function reporteProcedimiento($input_sociedad, $ejercicio, $periodo)
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
        
        $matrix[] = self::estadoCostoPorPeriodo('Inicial', '01', $ejercicio, $soc, $helper);
        
        for ($i = 1; $i <= (int) $periodo; $i++) {
            $peryodo = ($i < 10) ? '0' . $i : '' . $i;
            $matrix[] = self::estadoCostoPorPeriodo('Mes', $peryodo, $ejercicio, $soc, $helper);
        }

        $matrix[] = self::estadoCostoPorPeriodo('Acumulado', $periodo, $ejercicio, $soc, $helper);

        return ($matrix);

    }
    
    
    public  function estadoCostoPorPeriodo($tipo, $periodo, $ejercicio, $soc, $helper){
        $tableName = $soc->SOC_AUX_DB;
        $version = DB::table('RPT_RG_CatalogoVersionCuentas')
            ->where('CAT_periodo', $periodo)
            ->value('CAT_version');
        $version = (is_null($version)) ? 0 : $version;
        $sociedad = $soc->SOC_Nombre;
        
        if ($tipo == 'Inicial') {
            $fecha = $ejercicio . '/' . $periodo . '/01';
            $fecha = Carbon::parse($fecha);
            $fecha = $fecha->subMonth();
            $periodo = $fecha->format('m');
            $ejercicio = $fecha->format('Y');
           
            $fecha = $fecha->subMonth();
            $periodo_ant = $fecha->format('m');
            $ejercicio_ant = $fecha->format('Y');
        } else {
            $fecha = $ejercicio . '/' . $periodo . '/01';
            $fecha = Carbon::parse($fecha);
            $fecha = $fecha->subMonth();
            $periodo_ant = $fecha->format('m');
            $ejercicio_ant = $fecha->format('Y');
        }
        
        
        //clock($fecha);

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
        } else { //ES ITEKNIA
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
        
        
        
                                // INICIA EC - Hoja3 
       $box_config = DB::select("select * from [dbo].[RPT_RG_VariablesReporte]");



        $box = array();
        foreach ($box_config as $value) {
            $box[$value->RGV_alias] = $value->RGV_valor_default;
            $box[$value->RGV_alias . '_acumulado'] = $value->RGV_valor_default;
        }

        //ponemos las variables del usuario e la caja             
       // $box['input_mo'] = (is_null(Input::get('mo')) || Input::get('mo') == '') ? 0 : Input::get('mo');
        //$box['input_indirectos'] = (is_null(Input::get('indirectos')) || Input::get('indirectos') == '') ? 0 : Input::get('indirectos');
        //$box['mp_ot'] = (is_null(Input::get('mp_ot')) || Input::get('mp_ot') == '') ? 0 : Input::get('mp_ot');
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
        $box['mp_ot']  = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', $ejercicio)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo)
            ->value('AJU_valor');
      

      
        $box['input_mo_acumulado'] = self::getAcumulado_RG_Ajustes('mo', $ejercicio, $sociedad, $periodo);

       
        $box['input_indirectos_acumulado'] = self::getAcumulado_RG_Ajustes('ind', $ejercicio, $sociedad, $periodo);
        
        $box['mp_ot_acumulado'] = self::getAcumulado_RG_Ajustes('mp_ot', $ejercicio, $sociedad, $periodo);

        $titulos_hoja3 =
            array_map(
                'trim',
                array_pluck($hoja3, 'RGC_tabla_titulo')
            );
        $acumulados_hoja3 = [];
        $grupos_hoja3 = array_unique($titulos_hoja3); //COMPRAS NETAS, MO, GASTOS IND
        $ctas_hoja3 = [];
        //$acumuladosxcta_h3 = [];
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
        //clock($box, $acumulados_hoja3);
        foreach ($box_config as $value) {
            //ponemos las variables de las CUENTAS en la caja
            if (key_exists($value->RGV_tabla_titulo, $ctas_hoja3)) {
                $box[$value->RGV_alias] = $ctas_hoja3[$value->RGV_tabla_titulo];
                $box[$value->RGV_alias . '_acumulado'] = $acumulados_hoja3[$value->RGV_tabla_titulo];
            }
        }
        //dd($box, $acumulados_hoja3);
        //clock($box);

        if ($tableName != 'RPT_BalanzaComprobacion') { // si no es ITEKNIA SA de CV
            //obtener inventario de la tabla de RPT_RG_Ajustes           
            //asignar MP PP PT a box
            //clock('NO ES ITEKNIA');
            //  if ( (int)$periodo > 1 ) {
            ///


            $mp_perido_anterior = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', $ejercicio_ant)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo_ant)
                ->value('AJU_valor');
            $mp_perido_anterior = (is_null($mp_perido_anterior)) ? 0 : $mp_perido_anterior;
            $box['mp_ini'] = $mp_perido_anterior;
            $box['mp_ini_acumulado'] = self::getAcumulado_RG_Ajustes('mp', $ejercicio_ant, $sociedad, $periodo_ant);
            //clock('mp_ini_acumulado '.$box['mp_ini_acumulado']);
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
            $box['pp_ini_acumulado'] = self::getAcumulado_RG_Ajustes('pp', $ejercicio_ant, $sociedad, $periodo_ant);

            $pt_perido_anterior = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', $ejercicio_ant)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo_ant)
                ->value('AJU_valor');
            $pt_perido_anterior = (is_null($pt_perido_anterior)) ? 0 : $pt_perido_anterior;
            $box['pt_ini'] = $pt_perido_anterior;
            $box['pp_ini_acumulado'] = self::getAcumulado_RG_Ajustes('pt', $ejercicio_ant, $sociedad, $periodo_ant);
            // }

        } else {


            $inv_Inicial = $helper->getInv($periodo_ant, $ejercicio_ant, true, $box_config);
            //clock($inv_Inicial);
            //mov del periodo
            foreach ($box_config as $value) {
                //ponemos las variables de LOCALIDADES en la caja
                //dd($value->RGV_alias, $inv_Inicial, key_exists($value->RGV_alias, $inv_Inicial));
                if (key_exists($value->RGV_alias, $inv_Inicial)) {
                    $box[$value->RGV_alias] = $inv_Inicial[$value->RGV_alias];
                }
            }
            $box['mp_ini_acumulado'] = $inv_Inicial['mp_ini'];
            $box['pp_ini_acumulado'] = $inv_Inicial['pp_ini'];
            $box['pt_ini_acumulado'] = $inv_Inicial['pt_ini'];
        }
        //Obtenemos Inv. del periodo
        $inv_Final = $helper->getInv($periodo, $ejercicio, false, $box_config);
        $acumulado_muliix['mp'] = 0;
        $acumulado_muliix['pp'] = 0;
        $acumulado_muliix['pt'] = 0;

        //solo obtenemos acumulados del ejercicio actual
        if ($ejercicio === $ejercicio_ant) { //sobreescribimos acumulados iniciales
            $acumulado_muliix = self::getAcumulado_muliix($soc->SOC_Id, $periodo_ant, $ejercicio_ant, true, $box_config);

            $box['mp_ini_acumulado'] = $acumulado_muliix['mp'];
            $box['pp_ini_acumulado'] = $acumulado_muliix['pp'];
            $box['pt_ini_acumulado'] = $acumulado_muliix['pt'];
        }

        $mp_ot_perido_anterior = DB::table('RPT_RG_Ajustes')
        ->where('AJU_Id', 'mp_ot')
            ->where('AJU_ejercicio', $ejercicio_ant)
            ->where('AJU_sociedad', $sociedad)
            ->where('AJU_periodo', $periodo_ant)
            ->value('AJU_valor');

        $mp_ot_perido_anterior = (is_null($mp_ot_perido_anterior)) ? 0 : $mp_ot_perido_anterior;

        $box['pp_ini'] += $mp_ot_perido_anterior;
        //obtenemos acumulado de mp_ot y lo sumamos a pp_ini
        $box['pp_ini_acumulado'] += self::getAcumulado_RG_Ajustes('mp_ot', $ejercicio_ant, $sociedad, $periodo_ant);
        ////////////////////////////////////////////////////////

        foreach ($box_config as $value) {
            if (key_exists($value->RGV_alias, $inv_Final)) {
                $box[$value->RGV_alias] = $inv_Final[$value->RGV_alias];
            }
        }

        $box['mp_fin_acumulado'] = $acumulado_muliix['mp'] + $inv_Final['mp_fin'];
        $box['pp_fin_acumulado'] = $acumulado_muliix['pp'] + $inv_Final['pp_fin'];
        $box['pt_fin_acumulado'] = $acumulado_muliix['pt'] + $inv_Final['pt_fin'];

        /*      
                $acumulado_muliix = self::getAcumulado_muliix($soc->SOC_Id, $periodo, $ejercicio, false, $box_config);
                $box['mp_fin_acumulado'] = $acumulado_muliix['mp'];
                $box['pp_fin_acumulado'] = $acumulado_muliix['pp'];
                $box['pt_fin_acumulado'] = $acumulado_muliix['pt'];
        */
        if ($tableName != 'RPT_BalanzaComprobacion') {
            //sobreescribir valores de mp pp y pt
            $mp_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'mp')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->value('AJU_valor');
            $mp_perido_actual = (is_null($mp_perido_actual)) ? 0 : $mp_perido_actual;
            $box['mp_fin'] = $mp_perido_actual;
            $box['mp_fin_acumulado'] = self::getAcumulado_RG_Ajustes('mp', $ejercicio, $sociedad, $periodo);

            $pp_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pp')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->value('AJU_valor');
            $pp_perido_actual = (is_null($pp_perido_actual)) ? 0 : $pp_perido_actual;
            $box['pp_fin'] = $pp_perido_actual;
            $box['pp_fin_acumulado'] = self::getAcumulado_RG_Ajustes('pp', $ejercicio, $sociedad, $periodo);

            $pt_perido_actual = DB::table('RPT_RG_Ajustes')
            ->where('AJU_Id', 'pt')
            ->where('AJU_ejercicio', $ejercicio)
                ->where('AJU_sociedad', $sociedad)
                ->where('AJU_periodo', $periodo)
                ->value('AJU_valor');
            $pt_perido_actual = (is_null($pt_perido_actual)) ? 0 : $pt_perido_actual;
            $box['pt_fin'] = $pt_perido_actual;
            $box['pt_fin_acumulado'] = self::getAcumulado_RG_Ajustes('pt', $ejercicio, $sociedad, $periodo);
        } //end otras sociedades

        unset(
            $inv_Final['mp_fin'],
            $inv_Final['pp_fin'],
            $inv_Final['pt_fin']
        );
        $llaves_invFinal = array_keys($inv_Final);

        //  clock( $box);

        foreach ($data_formulas_33 as $value) {
            eval("\$box['" . $value->RGC_BC_Cuenta_Id . "'] = (" . $value->RGC_valor_default . ")*" . $value->RGC_multiplica . ";");
            //Ejemplo con primer fila.
            //$box['A'] = ($box['mp_ini']) * 1;
        }
        foreach ($data_formulas_34 as $value) {
            eval("\$box['" . $value->RGC_BC_Cuenta_Id . "'] = (" . $value->RGC_valor_default . ")*" . $value->RGC_multiplica . ";");
            //Ejemplo con primer fila 34.
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
            case 'Inicial':
                //dd($box, $ejercicio, $periodo, $periodo_ant, $ejercicio_ant);
                $columna_reporte = [
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
                break;
            case 'Acumulado':
                $columna_reporte = [
                    number_format($box['AA'],'2', '.',',')// "INVENTARIO INICIAL MP",
                    ,number_format($box['BB'],'2', '.',',')// "COMPRAS",
                    ,number_format($box['CC'],'2', '.',',') //MERMAS
                    ,number_format($box['DD'],'2', '.',',')// "INVENTARIO MP",
                    ,number_format($box['EE'],'2', '.',',')// "INVETARIO FINAL DE MP",
                    ,number_format($box['FF'],'2', '.',',')// "MP UTILIZADA",
                    ,''
                    ,number_format($box['GG'],'2', '.',',') // "MANO DE OBRA",
                    //,$box[''] // "MANO DE OBRA PLANIMENTRIA",
                    ,number_format($box['HH'],'2', '.',',') // "MAQUILAS",
                    ,number_format($box['II'],'2', '.',',') // "GASTOS INDIRECTOS",
                    ,number_format($box['SS'],'2', '.',',') // "TOTAL DE GASTOS DE FABRICACION ",
                    ,''
                    ,number_format($box['JJ'],'2', '.',',') // "PROD EN PROCESO",
                    ,number_format($box['KK'],'2', '.',',') // "INV. INCI DE PP",
                    ,number_format($box['LL'],'2', '.',',') // "TOTAL INVENTARIO PP",
                    ,number_format($box['MM'],'2', '.',',') // "INVENTARIO FINAL P P ",
                    ,number_format($box['NN'],'2', '.',',') // "PRODUCTO TERMINADO ",
                    ,''
                    ,number_format($box['OO'],'2', '.',',') // "INV INICIAL DE P. TERMINADO",
                    ,number_format($box['PP'],'2', '.',',') // "TOTAL DE INV DE  P. TERMINADO",
                    ,number_format($box['QQ'],'2', '.',',') // "INV FINAL DE P. TERMINADO ",
                    ,number_format($box['RR'],'2', '.',',') // "COSTO"
                ];
                break;
        }

        return $columna_reporte;
        
    }


    public function RGPDF($opcion){         
            $data = Session::get('data_rg');                   
            $sociedad = Session::get('sociedad_rg');                   
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
                        return !is_numeric(strpos($value->RGC_tabla_titulo, 'ACTIVO'));
                    });                    
                   //dd($hoja1_pasivos);
                    //dd($data['acumuladosxcta_hoja1']);
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
            $data["sociedad"] = $sociedad;                      
            $data["fecha_actualizado"]=false;
            //return view('Mod_RG.RG03PDF', $data);
            $pdf = PDF::loadView('Mod_RG.RG03PDF', $data);
            //$pdf = new FPDF('L', 'mm', 'A4');
            $pdf->setPaper('Letter', 'portrait')->setOptions(['isPhpEnabled' => true]);                        
            //$pdf->setOptions(['isPhpEnabled' => true]);             
            
            return $pdf->stream($data["ejercicio"]."_".$data["periodo"].$file_name[1].'.pdf');      
    }
    public   function getAcumulado_RG_Ajustes($id, $ejercicio, $sociedad, $periodo){
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

    public  function getAcumulado_muliix($sociedad_id, $periodo_hasta, $ejercicio, $suffixes_keys, $box_config){
        
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

   
}
