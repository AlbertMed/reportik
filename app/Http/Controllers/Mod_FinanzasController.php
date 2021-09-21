<?php
namespace App\Http\Controllers;

use App;
use App\LOG;
use App\RPT_models\RPT_PROV;
use App\RPT_models\RPT_PAGO;
use App\Modelos\FacturasProveedores;
use App\Modelos\ProgramasPagosCXP;
use App\Modelos\ProgramasPagosCXPDetalle;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Sistema\AutonumericoController;
use App\Http\Controllers\Sistema\DAOGeneralController;
use Datatables;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_FinanzasController extends Controller
{
    public function flujoEfectivoDetalleCXCCXP()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $estado = [];
            $estado_save = [];
            $cliente = [];
            $comprador = [];

            $provdescripciones = [];
            $provalertas = [];
            $cbonumpago = [];
            $cbousuarios = [];

            $sem = DB::select('select SUBSTRING( CAST(year(GETDATE()) as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, GETDATE()) as sem_actual');
            $sem = $sem[0]->sem_actual;

            ini_set('memory_limit', '-1');
            set_time_limit(0);

            date_default_timezone_set('America/Mexico_City');


            return view(
                'Finanzas.Flujo_Efectivo_Detalle',
                compact(
                    'actividades',
                    'ultimo'
                    ,'provdescripciones'
                    ,'provalertas'
                    ,'cbonumpago'
                    ,'cbousuarios'
                    ,'sem'

                    ,'estado'
                    ,'estado_save'
                    ,'cliente'
                    ,'comprador'
                )
            );
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function data_resumen_cxp_proveedor()
    {
        $tipoCambio = DB::select("SELECT COALESCE(MONP_TipoCambioOficial, 0) MONP_TipoCambioOficial
                FROM MonedasParidad
                WHERE CAST(MONP_FechaInicio AS DATE) = convert(varchar,DATEADD(d,-1,GETDATE()), 23)
                AND MONP_MON_MonedaId ='1EA50C6D-AD92-4DE6-A562-F155D0D516D3'
                ");

        if (count($tipoCambio) != 1) {
            $tipoCambio = 0;
        } else {
            $tipoCambio = $tipoCambio[0]->MONP_TipoCambioOficial;
        }
        $consulta = DB::select('exec SP_RPT_Flujo_Efectivo_XPROVEEDOR_CXP '. $tipoCambio);
        $columns = array();
        if (count($consulta) > 0) {
            //queremos obtener las columnas dinamicas de la tabla
            $cols = array_keys((array)$consulta[0]);
            $sem = DB::select('select SUBSTRING( CAST(year(GETDATE()) as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, GETDATE()) as sem_actual');
            $sem = $sem[0]->sem_actual;

            foreach ($cols as $key => $value) {
                $nombre = $value;
                if (substr($value, 0, 1) === 's') {
                    $num = (int) substr($value, 1, 1);
                    $nombre = 'SEM_'.($sem + $num);
                }
                array_push($columns, ["data" => $value, "name" => $nombre]);
            }
           
        }
        return response()->json(array('data' => $consulta, 'columns' => $columns));
    }
    public function data_resumen_cxc_cliente()
    {
        //SP SQL obtiene la proyeccion de CXC a 8 semanas
        $consulta = DB::select('exec RPT_SP_RESUMEN_CXC_CLIENTE');
        $columns = array();
        if (count($consulta) > 0) {
            //queremos obtener las columnas dinamicas de la tabla
            $cols = array_keys((array)$consulta[0]);
            //obtenemos las columnas de las semanas dinamicas, estas tienen un _
            /******
             * NO AGREGAR _ EN LOS NOMBRES DE LA CONSULTA SQL
             * ***/
            $numerickeys = array_where($cols, function ($key, $value) {
                return is_numeric(strpos($value, '_'));
            });
            //las ordenamos
            sort($numerickeys);
            //dd($cols);
            //obtenemos las primeras 3 columnas, esas no cambian
            $columns_init = array_slice($cols, 0, 3);
            //agregamos las columnas dinamicas ordenadas
            //dd($columns_init);
            $columns_init = array_merge($columns_init, $numerickeys);
            //preparamos el array final para el datatable
            foreach ($columns_init as $key => $value) {
                array_push($columns, ["data" => $value, "name" => $value]);
            }
            array_push($columns, ["data" => "RESTO", "name" => "RESTO"]);
        }
        return response()->json(array('data' => $consulta, 'columns' => $columns));
    }
    public function autorizaProgramaPorId(Request $request)
    {

        \DB::beginTransaction();

        try {

            date_default_timezone_set('America/Mexico_City');
            $dia = date('d-m-Y');
            $hoy = date('d-m-Y H:i:s');

            $programaId = $request->input('programaId');
            $empleadoId = DB::table('Empleados')
            ->where('EMP_CodigoEmpleado', Auth::user()->nomina)
            ->value('EMP_EmpleadoId');

            $programa = ProgramasPagosCXP::find($programaId);
            $programa->PPCXP_CMM_EstatusId = 'DEF5E8FB-C70D-443E-86BF-29DD08492E57'; //Autorizado
            $programa->PPCXP_EMP_ModificadoPorId = $empleadoId;
            $programa->PPCXP_FechaUltimaModificacion = $hoy;
            $programa->save();

            $response = array("action" => "success");

            \DB::commit();

            return ['Status' => 'Valido', 'respuesta' => $response];
        } catch (\Exception $e) {

            \DB::rollback();
            return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' . $e->getMessage()];
        }
    }
    public function cancelarPorgramaCXP(Request $request)
    {

        ini_set('memory_limit', '-1');
        set_time_limit(0);

        \DB::beginTransaction();

        try {

            date_default_timezone_set('America/Mexico_City');
            $hoy = date('d-m-Y H:i:s');
            $idEmpleado =
            DB::table('Empleados')
            ->where('EMP_CodigoEmpleado', Auth::user()->nomina)
            ->value('EMP_EmpleadoId');
            $programaId = $request->input('programaId');//programaId

            //CONSULTA FACTURAS PROVEEDORES DEL PROGRAMA
            $consultaFP = \DB::select(
                \DB::raw(
                    "SELECT FP_FacturaProveedorId FROM FacturasProveedores WHERE FP_DefinidoPorUsuario1 = '" . $programaId . "'"
                )
            );

            $cuentaConsultaFP = count($consultaFP);
            for ($x = 0; $x < $cuentaConsultaFP; $x++) {

                $facturaProveedor = FacturasProveedores::find($consultaFP[$x]->FP_FacturaProveedorId);
                $facturaProveedor->FP_DefinidoPorUsuario1 = null;
                $facturaProveedor->FP_FechaUltimaModificacion = $hoy;
                $facturaProveedor->FP_EMP_ModificadoPor = $idEmpleado;
                $facturaProveedor->save();
            }

            //CONSULTA DETALLE DEL PROGRAMA
            $consultaDetallePrograma = \DB::select(
                \DB::raw(
                    "SELECT PPCXPD_DetalleId FROM ProgramasPagosCXPDetalle WHERE PPCXPD_PPCXP_ProgramaId = '" . $programaId . "' AND PPCXPD_Eliminado = 0"
                )
            );

            $cuentaConsultaDetallePrograma = count($consultaDetallePrograma);
            for ($x = 0; $x < $cuentaConsultaDetallePrograma; $x++) {


                $programaDetalle = ProgramasPagosCXPDetalle::find($consultaDetallePrograma[$x]->PPCXPD_DetalleId);
                $programaDetalle->PPCXPD_Eliminado = 1;
                $programaDetalle->PPCXPD_FechaUltimaModificacion = $hoy;
                $programaDetalle->PPCXPD_EMP_ModificadoPorId = $idEmpleado;
                $programaDetalle->save();
            }

            $programa = ProgramasPagosCXP::find($programaId);
            $programa->PPCXP_Eliminado = 1;
            $programa->PPCXP_FechaUltimaModificacion = $hoy;
            $programa->PPCXP_EMP_ModificadoPorId = $idEmpleado;
            $programa->save();

            \DB::commit();

            $ajaxData = array();
            $ajaxData['codigo'] = 200;

            echo json_encode($ajaxData);
        } catch (\Exception $e) {

            \DB::rollback();

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }
    public function consultaProgramaPorId($programaId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);

            $estado = [];
            $estado_save = [];
            $cliente = [];
            $comprador = [];
            $provdescripciones = [];
            $provalertas = [];
            $cbonumpago = [];
            $cbousuarios = [];

            $sem = DB::select('select SUBSTRING( CAST(year(GETDATE()) as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, GETDATE()) as sem_actual');
            $sem = $sem[0]->sem_actual;

            $programa = ProgramasPagosCXP::find($programaId);
            $facturas = DB::select(
                    "SELECT
                        FP_CodigoFactura
                        ,PRO_CodigoProveedor
	                    ,PRO_NombreComercial
                        ,PPCXPD_Monto
                    FROM ProgramasPagosCXPDetalle
                    INNER JOIN FacturasProveedores ON FP_FacturaProveedorId = PPCXPD_FP_FacturaProveedorId
                    INNER JOIN Proveedores ON PRO_ProveedorId = FP_PRO_ProveedorId
                    WHERE PPCXPD_Eliminado = 0
                    AND PPCXPD_PPCXP_ProgramaId = '" . $programaId . "'"
            );
            return view('Finanzas.Edita_Flujo_Efectivo_facturasCXP_Proveedores', 
            compact('programa', 'facturas','sem','cbousuarios', 'estado', 'estado_save',
             'cliente', 'comprador', 'actividades', 'ultimo', 'provdescripciones', 
             'provalertas', 'cbonumpago'));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function programas_registros(){

        try{

            ini_set('memory_limit', '-1');
            set_time_limit(0);

           // $FechaInicio = NewRequest::input('fechaDesde');
           // $FechaFinal = NewRequest::input('fechaHasta');

            $consulta = \DB::select(
                \DB::raw(
                    "SELECT
                        PPCXP_ProgramaId AS DT_RowId
                        ,PPCXP_Codigo
                        ,PPCXP_Nombre
                        ,CMM_Valor AS PPCXP_Estado
                        ,PPCXP_Monto
                        ,BAN_NombreBanco
                        ,BCS_Cuenta
                        ,MON_Nombre
                        ,CAST(PPCXP_FechaCreacion AS DATE) AS PPCXP_FechaPrograma
						,CAST(PPCXP_FechaPago AS DATE) AS PPCXP_FechaPago
                        ,EMP_Nombre AS PPCXP_CreadoPor
                    FROM ProgramasPagosCXP
                    INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = PPCXP_CMM_EstatusId
                    INNER JOIN BancosCuentasSimples ON BCS_BancoCuentaId = PPCXP_BCS_BancoCuentaId
                    INNER JOIN Bancos ON BAN_BancoId = BCS_BAN_BancoId
                    INNER JOIN Monedas ON MON_MonedaId = BCS_MON_MonedaId
                    INNER JOIN Empleados ON EMP_EmpleadoId = PPCXP_EMP_CreadoPorId
                    WHERE PPCXP_Eliminado = 0
                    AND (PPCXP_CMM_EstatusId = 'DEF5E8FB-C70D-443E-86BF-29DD08492E57'--Autorizado
                    OR PPCXP_CMM_EstatusId = '0723339B-8F13-4109-8810-B720593CDF40')--Abierto
                    "
                )
            );
            //AND CAST(PPCXP_FechaCreacion AS DATE) BETWEEN '".$FechaInicio."' AND '".$FechaFinal."'
            $ajaxData = array();
            $ajaxData['consulta'] = $consulta;
            return (json_encode($ajaxData));
            
            /*$ajaxData = array();
            $ajaxData['data'] = $consulta;
            $ajaxData['options'] = array();
            return (json_encode($ajaxData));*/
        } catch (\Exception $e){

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array("mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine())));

        }

    }
    public function index_flujoEfectivoResumen()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            ini_set('memory_limit', '-1');
            set_time_limit(0);

            date_default_timezone_set('America/Mexico_City');
            $fechaDia = date('d-m-Y');
            $nuevaFechaPago = date("d-m-Y", strtotime($fechaDia . "- 1 days"));

            ////////////////////////
            $total_bancos = DB::select("SELECT
                   SUM( CASE
                            WHEN MON_Abreviacion = 'MN'
                                THEN
                                    CASE
                                    WHEN CON.CON_SaldoFinal IS NULL
                                        THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    ELSE
                                        IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    END
                            ELSE
                                CASE
                                    WHEN CON.CON_SaldoFinal IS NULL
                                        THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    ELSE
                                        IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                END * TC.MONP_TipoCambioOficial
                        END ) AS SaldoDisponibleMN
                    FROM BancosCuentasSimples
                    INNER JOIN Bancos ON BAN_BancoId = BCS_BAN_BancoId
                    INNER JOIN Monedas ON MON_MonedaId = BCS_MON_MonedaId
                    LEFT JOIN (
                        SELECT
                            MONP_TipoCambioOficial
                            ,MONP_MON_MonedaId
                        FROM MonedasParidad
                        WHERE CAST(MONP_FechaInicio AS DATE) = '" . $nuevaFechaPago . "'
                    ) AS TC ON TC.MONP_MON_MonedaId = MON_MonedaId
                    LEFT JOIN (
                        SELECT TOP 1
                            CON_FechaFinal
                            ,CON_SaldoFinal
                            ,CON_BCS_BancoCuentaId
                        FROM Conciliaciones
                        INNER JOIN BancosCuentasSimples ON CON_BCS_BancoCuentaId = BCS_BancoCuentaId
                        WHERE CON_Eliminado = 0
                        ORDER BY
                            CON_FechaCreacion
                        DESC
                    ) AS CON ON CON.CON_BCS_BancoCuentaId = BCS_BancoCuentaId
                    LEFT JOIN(
                        SELECT
                            --SUM(CXCP_MontoPago) AS TotalDepositos_G
                            IsNull(SUM(CASE
                            WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Pesos'
                                THEN (CXCP_MontoPago / CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Pesos'
                                THEN (CXCP_MontoPago / CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Dolar'
                                THEN (CXCP_MontoPago * CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Euro'
                                THEN (CXCP_MontoPago * CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Pesos'
                                THEN CXCP_MontoPago
                            WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Dolar'
                                THEN CXCP_MontoPago
                            WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Euro'
                                THEN CXCP_MontoPago
                            --ELSE 'The quantity is under 30'
                        END),0) AS TotalDepositos_G
                            ,CXCP_BCS_BancoCuentaId
                        FROM CXCPagos
                        LEFT JOIN Clientes ON CLI_ClienteId = CXCP_CLI_ClienteId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXCP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXCP_MON_MonedaId
                        WHERE CAST(CXCP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        AND CXCP_Eliminado = 0
                        AND CXCP_Conciliado = 0
                        AND CXCP_CandidatoConciliacion = 1
                        GROUP BY
                            CXCP_BCS_BancoCuentaId
                    ) AS DEP_GUARDADOS ON DEP_GUARDADOS.CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    
                    LEFT JOIN(
                        SELECT
                            --SUM(CXPP_MontoPago) AS TotalRetiros
                            IsNull(SUM(CASE
                                WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Pesos'
                                    THEN (CXPP_MontoPago / CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Pesos'
                                    THEN (CXPP_MontoPago / CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Dolar'
                                    THEN (CXPP_MontoPago * CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Euro'
                                    THEN (CXPP_MontoPago * CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Pesos'
                                    THEN CXPP_MontoPago
                                WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Dolar'
                                    THEN CXPP_MontoPago
                                WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Euro'
                                    THEN CXPP_MontoPago
                                --ELSE 'The quantity is under 30'
                            END),0) AS TotalRetiros_G
                            ,CXPP_BCS_BancoCuentaId
                        FROM CXPPagos
                        LEFT JOIN Proveedores ON PRO_ProveedorId = CXPP_PRO_ProveedorId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXPP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXPP_MON_MonedaId
                        WHERE CAST(CXPP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        AND CXPP_Eliminado = 0
                        AND CXPP_CandidatoConciliacion = 1
                        AND CXPP_Conciliado = 0
                        AND CXPP_CMM_FormaPagoId <> '29C26EAC-AE9F-4A2B-B03A-7983B13C656B'
                        GROUP BY
                            CXPP_BCS_BancoCuentaId
                    ) AS RET_GUARDADOS ON RET_GUARDADOS.CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    WHERE BCS_Eliminado = 0
                    AND BAN_Activo = 1
                    AND BAN_DefinidoPorUsuario1 = 'S'");
            
            $tipoCambio = DB::select("SELECT COALESCE(MONP_TipoCambioOficial, 0) MONP_TipoCambioOficial
                FROM MonedasParidad
                WHERE CAST(MONP_FechaInicio AS DATE) = convert(varchar,DATEADD(d,-1,GETDATE()), 23)
                AND MONP_MON_MonedaId ='1EA50C6D-AD92-4DE6-A562-F155D0D516D3'
                ");

            if (count($tipoCambio) != 1) {
                $tipoCambio = null;
            } else {
                $tipoCambio = $tipoCambio[0]->MONP_TipoCambioOficial;
            }

            $numsemanas = 5;
            $totales_cxp = DB::select("exec SP_RPT_Flujo_Efectivo_Monto_facturasCXP ?", [$tipoCambio]);
            $cxp_xsemana = [];
            $sem = DB::select('select SUBSTRING( CAST(year(GETDATE()) as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, GETDATE()) as sem_actual');
            $sem = $sem[0]->sem_actual;
            $cxp_anterior = 0;
            $cxp_resto = 0;

            foreach ($totales_cxp as $cxp) {
               // clock($cxp->SEMANA);
                if ($cxp->SEMANA < $sem) {
                    $cxp_anterior += $cxp->MONTOACTUAL;
                } else if($cxp->SEMANA > ($sem + $numsemanas)){
                    $cxp_resto += $cxp->MONTOACTUAL;
                } else{
                    $cxp_xsemana[ (int) $cxp->SEMANA ] = $cxp->MONTOACTUAL * 1;
                }
            }
            $cxp_xsemana['VENCIDO'] = $cxp_anterior;
            $cxp_xsemana['RESTO'] = $cxp_resto;

            //SP SQL obtiene la proyeccion de CXC a 8 semanas
            $consulta = DB::select('exec RPT_SP_RESUMEN_CXC_ANTERIORRESTO_SEPARADO');
            $cxc_xsemana = array();
            $cxc_anterior = 0;
            $cxc_resto = 0;
            $total_comprometido = 0;
            $total_estimado = 0;
            if (count($consulta) > 0) {
                //queremos obtener las columnas dinamicas de la tabla
                $cols = array_keys((array)$consulta[0]);
                //obtenemos las columnas de las semanas dinamicas, estas tienen un _
                /******
                 * NO AGREGAR _ EN LOS NOMBRES DE LA CONSULTA SQL
                 * ***/
                $numerickeys = array_where($cols, function ($key, $value) {
                    return is_numeric(strpos($value, '_COMPROMETIDO'));
                });
                //las ordenamos
                sort($numerickeys);
               
                foreach ($numerickeys as $value) {
                    $semana_cxc = substr($value, 0, 4);
                    $monto = array_sum(array_pluck($consulta, $value));
                    if ((int)$semana_cxc < $sem) {
                        $cxc_anterior += $monto;
                    } else if ((int)$semana_cxc > ($sem + $numsemanas)) {
                        $cxc_resto += $monto;
                    } else {
                        $cxc_xsemana[(int) $semana_cxc] = $monto;
                    }
                    $total_comprometido += $monto; 

                }
                
                $monto = array_sum(array_pluck($consulta, 'ANTERIORCOM'));
                $total_comprometido += $monto;
                $cxc_xsemana["VENCIDO"] = $monto + $cxc_anterior;
                
                $monto = array_sum(array_pluck($consulta, 'RESTOCOM'));
                $total_comprometido += $monto;
                $cxc_xsemana["RESTO"] = $monto + $cxc_resto;

                //para obtener el estimado total de todas las columnas (semanas)
                $cols_estimado = array_where($cols, function ($key, $value) {
                    return is_numeric(strpos($value, '_ESTIMADO'));
                });
                //las ordenamos
                sort($numerickeys);

                foreach ($cols_estimado as $value) {

                    $total_estimado += $monto;
                }
                $monto = array_sum(array_pluck($consulta, 'ANTERIOREST'));
                $total_estimado += $monto;
                
                $monto = array_sum(array_pluck($consulta, 'RESTOEST'));
                $total_estimado += $monto;

                $total_cobrado = array_sum(array_pluck($consulta, 'COBRADO'));
                $total_ov = array_sum(array_pluck($consulta, 'MONTO'));
                $no_programado = $total_ov - ($total_cobrado + $total_estimado + $total_comprometido);
            }

            for ($i=0; $i <= $numsemanas ; $i++) {            
                if (!array_key_exists($sem + $i, $cxp_xsemana)) {
                    $cxp_xsemana[$sem + $i] = 0;
                }
                if (!array_key_exists($sem + $i, $cxc_xsemana)) {
                    $cxc_xsemana[$sem + $i] = 0;
                }
            }
            $total_cxp = array_sum(array_pluck($totales_cxp, 'MONTOACTUAL'));
            //dd($cxc_xsemana);
            
            return view('Finanzas.Flujo_Efectivo_resumen', 
            compact(
                    'actividades'
                    ,'ultimo'
                    ,'total_cxp'
                    ,'total_bancos'
                    ,'cxp_xsemana'
                    ,'sem'
                    ,'cxc_xsemana'
                    ,'total_ov'
                    ,'total_cobrado'
                    ,'total_comprometido'
                    ,'total_estimado'
                    ,'no_programado'
            ));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function flujoEfectivoResumenCXCCXP()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            ini_set('memory_limit', '-1');
            set_time_limit(0);

            date_default_timezone_set('America/Mexico_City');
         
            
            return view('Finanzas.Flujo_Efectivo_resumen_CXCCXP', 
            compact(
                    'actividades'
                    ,'ultimo'
            ));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function index_flujoEfectivoShowProgramas()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            ini_set('memory_limit', '-1');
            set_time_limit(0);

            date_default_timezone_set('America/Mexico_City');
         
            
            return view('Finanzas.Flujo_Efectivo_programas', 
            compact(
                    'actividades'
                    ,'ultimo'
            ));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function index_flujoEfectivoProgramarPagos()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);

            $estado = [];
            $estado_save = [];
            $cliente = [];
            $comprador = [];
            $provdescripciones = [];
            $provalertas = [];
            $cbonumpago = [];
            $cbousuarios = [];

            $sem = DB::select('select SUBSTRING( CAST(year(GETDATE()) as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, GETDATE()) as sem_actual');
            $sem = $sem[0]->sem_actual;

            return view('Finanzas.Nuevo_Flujo_Efectivo_facturasCXP_Proveedores', compact('sem','cbousuarios', 'estado', 'estado_save', 'cliente', 'comprador', 'actividades', 'ultimo', 'provdescripciones', 'provalertas', 'cbonumpago'));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function DataFTPDCXPPesos(Request $request){
        $programaId = $request->input('programaId');
        $tipoCambio = DB::select("SELECT COALESCE(MONP_TipoCambioOficial, 0) MONP_TipoCambioOficial
        FROM MonedasParidad
        WHERE CAST(MONP_FechaInicio AS DATE) = convert(varchar,DATEADD(d,-1,GETDATE()), 23)
        AND MONP_MON_MonedaId ='1EA50C6D-AD92-4DE6-A562-F155D0D516D3'
        ");
        //dd($tipoCambio);
        if (count($tipoCambio) != 1) {
            return response()->json('tc');
        }
        $tipoCambio = $tipoCambio[0]->MONP_TipoCambioOficial;
        //dd('exec SP_RPT_Flujo_Efectivo_facturasCXP_Proveedores ' . $tipoCambio . ', ' . $programaId);
        $FTPDCXPPesos = DB::select("exec SP_RPT_Flujo_Efectivo_facturasCXP_Proveedores ?,?",[$tipoCambio, $programaId]);
        return response()->json(compact('FTPDCXPPesos'));
    }
    public function establecerAutonumerico($clienteId, $empleadoId)
    {
        try {
            $autonumerico_dao = new AutonumericoController();
            $autonumericoFicha = $autonumerico_dao->getAutonumerico($clienteId, "CM_FIN_SiguientePrograma", $empleadoId);
            return $autonumericoFicha->AUT_AutonumericoId;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public static function getNuevoId()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function consultaCodigoPrograma()
    {

        //////////SACAR AUTONUMERICO///////////////////////////
        $autonumerico_dao = new AutonumericoController();
        $clienteId = null;
        $empleadoId = null;
        $codigo_Programa = null;

        if ($autonumerico_dao->isAutonumericoActivoPorReferenciaId('CM_FIN_SiguientePrograma', null)) {
            $autonumerico_id = self::establecerAutonumerico($clienteId, $empleadoId);
            $codigo_Programa = $autonumerico_dao->getSiguienteAutonumericoPorId($autonumerico_id);
        }

        return $codigo_Programa;
    }
    public function registraPrograma(Request $request)
    {
        DB::beginTransaction();

        try {

            date_default_timezone_set('America/Mexico_City');
           // $dia = date('d-m-Y');
            $hoy = date('d-m-Y H:i:s');
            
            $fechapago = $request->input('fechapago');
            $descripcion = $request->input('descripcion');
            $cuentaId = ($request->input('cuentaId') == '')? null: $request->input('cuentaId');
            $montoTotalPrograma = $request->input('montoTotalPrograma');
            $montoDispersar = $request->input('montoDispersar');
            $TablaCXPPesos = json_decode($request->input('TablaCXPPesos'), true);
            //$TablaCXPDolar = isset($_POST['TablaCXPDolar']) ? json_decode($_POST['TablaCXPDolar'], true) : array();
            $empleadoId = DB::table('Empleados')
            ->where('EMP_CodigoEmpleado', Auth::user()->nomina)
            ->value('EMP_EmpleadoId');
            $programaId_r = $request->input('programaId');
            
            //REGISTRA NUEVO PROGRAMA
            if (isset($programaId_r)) {
                $programaId = $programaId_r;
                $programa = ProgramasPagosCXP::find($programaId);
                //si tenemos que actualizar los pagos detalle, entonces barremos los registros
                ProgramasPagosCXPDetalle::where('PPCXPD_PPCXP_ProgramaId', $programaId)->delete();
                
            } else {
                $programaId = self::getNuevoId();
                $codigoPrograma = self::consultaCodigoPrograma();
                $programa = new ProgramasPagosCXP();
                $programa->PPCXP_ProgramaId = $programaId;
                $programa->PPCXP_Codigo = $codigoPrograma;
                $programa->PPCXP_CMM_EstatusId = '0723339B-8F13-4109-8810-B720593CDF40'; //Abierto
                
            }
            $programa->PPCXP_FechaPago = $fechapago;
            $programa->PPCXP_EMP_CreadoPorId = $empleadoId;
            $programa->PPCXP_BCS_BancoCuentaId = $cuentaId;
            $programa->PPCXP_MontoDispersar = $montoDispersar;
            $programa->PPCXP_Monto = $montoTotalPrograma;
            $programa->PPCXP_Nombre = $descripcion;
            $programa->save();

            //GUARDA DETALLE CXP PESOS
            $cuentaTablaCXPPesos = count($TablaCXPPesos);
            for ($x = 0; $x < $cuentaTablaCXPPesos; $x++) {

                $programaDetalle = new ProgramasPagosCXPDetalle();
                $programaDetalle->PPCXPD_PPCXP_ProgramaId = $programaId;
                $programaDetalle->PPCXPD_FP_FacturaProveedorId = $TablaCXPPesos[$x]['facturaProveedorId'];
                //$programaDetalle->PPCXPD_BCS_BancoCuentaId = NULL;
                $programaDetalle->PPCXPD_Monto = $TablaCXPPesos[$x]['montofactura'];
                $programaDetalle->save();

                $facturaProveedor = FacturasProveedores::find($TablaCXPPesos[$x]['facturaProveedorId']);
                $facturaProveedor->FP_DefinidoPorUsuario1 = $programaId;
                $facturaProveedor->FP_FechaUltimaModificacion = $hoy;
                $facturaProveedor->FP_EMP_ModificadoPor = $empleadoId;
                $facturaProveedor->save();
            }
            /*
            //GUARDA DETALLE CXP DOLAR
            $cuentaTablaCXPDolar = count($TablaCXPDolar);
            for ($x = 0; $x < $cuentaTablaCXPDolar; $x++) {

                $programaDetalle = new ProgramasPagosCXPDetalle();
                $programaDetalle->PPCXPD_PPCXP_ProgramaId = $programaId;
                $programaDetalle->PPCXPD_FP_FacturaProveedorId = $TablaCXPDolar[$x]['facturaProveedorId'];
                //$programaDetalle->PPCXPD_BCS_BancoCuentaId = NULL;
                $programaDetalle->PPCXPD_Monto = $TablaCXPDolar[$x]['montofactura'];
                $programaDetalle->save();

                $facturaProveedor = FacturasProveedores::find($TablaCXPDolar[$x]['facturaProveedorId']);
                $facturaProveedor->FP_DefinidoPorUsuario1 = $programaId;
                $facturaProveedor->FP_FechaUltimaModificacion = $hoy;
                $facturaProveedor->FP_EMP_ModificadoPor = $empleadoId;
                $facturaProveedor->save();
            }
            */
            $response = array("action" => "success");

            DB::commit();

            return ['Status' => 'Valido', 'respuesta' => $response];
        } catch (\Exception $e) {

            DB::rollback();
            return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' . $e->getMessage()];
        }
    }
    public function index_provision()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $estado = [];
            $estado_save = [];
            $cliente = [];
            $comprador = [];
            $provdescripciones = [];
            $provalertas = [];
            $cbonumpago = [];
            $cbousuarios = [];
           
            DB::beginTransaction();
           
            $pagos_no_considerados = RPT_PAGO::active('all');
            $OVs_conPagos = array_unique(array_pluck($pagos_no_considerados, 'ov_codigoov'));           
            //clock($pagos_no_considerados);
            foreach ($OVs_conPagos as  $ov) {
                //vamos a ver cuantas provisiones activas hay, es decir que no esten cubiertas con pago
                $countProv = DB::table('RPT_ProvisionCXC')->where('PCXC_OV_Id', $ov)->where('PCXC_Activo', 1)
                ->where('PCXC_Eliminado', 0)->count();
                //vamos a ver cuantos pagos hay en muliix y que no hemos considerado
                $countPago = count(DB::select("SELECT ov_codigoov, Pagos.cxcp_fechapago, Pagos.cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM ordenesventa LEFT JOIN (SELECT ovd_detalleid, ovd_ov_ordenventaid, ovd_cantidadrequerida * ovd_preciounitario AS SUBTOTAL, ovd_cantidadrequerida * ovd_preciounitario * Isnull(ovd_porcentajedescuento, 0.0) AS DESCUENTO, ( ( ovd_cantidadrequerida * ovd_preciounitario ) - ( ovd_cantidadrequerida * ovd_preciounitario * Isnull( ovd_porcentajedescuento, 0.0) ) ) * Isnull(ovd_cmiva_porcentaje, 0.0) AS IVA, ovd_cantidadrequerida FROM ordenesventadetalle LEFT JOIN articulosespecificaciones ON ovd_art_articuloid = aet_art_articuloid AND aet_cmm_articuloespecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F') AS OrdenesVentaDetalle ON ov_ordenventaid = ovd_ov_ordenventaid LEFT JOIN (SELECT ftr_facturaid, ftr_mon_monedaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida * ftrd_preciounitario AS FTR_SUBTOTAL, ftrd_cantidadrequerida * ftrd_preciounitario * Isnull(ftrd_porcentajedescuento, 0.0) AS FTR_DESCUENTO, ( ( ftrd_cantidadrequerida * ftrd_preciounitario ) - ( ftrd_cantidadrequerida * ftrd_preciounitario * Isnull( ftrd_porcentajedescuento, 0.0) ) ) * Isnull(ftrd_cmiva_porcentaje, 0.0) AS FTR_IVA, ftrd_referenciaid, ftrd_cantidadrequerida FROM facturas INNER JOIN facturasdetalle fd ON fd.ftrd_ftr_facturaid = facturas.ftr_facturaid WHERE ftr_eliminado = 0 GROUP BY ftr_mon_monedaid, ftr_facturaid, ftrd_referenciaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida, ftrd_preciounitario, ftrd_porcentajedescuento, ftrd_cmiva_porcentaje) AS Facturas ON ovd_detalleid = ftrd_referenciaid LEFT JOIN (SELECT cxcpd_ftr_facturaid, Round(Isnull(Sum(Abs(cxcpd_montoaplicado)), 0.0), 2) AS cantidadPagoFactura, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM cxcpagos INNER JOIN cxcpagosdetalle ON cxcp_cxcpagoid = cxcpd_cxcp_cxcpagoid WHERE cxcp_eliminado = 0 AND cxcpd_ftr_facturaid IS NOT NULL GROUP BY cxcpd_ftr_facturaid, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago) AS Pagos ON ftr_facturaid = cxcpd_ftr_facturaid AND ftr_mon_monedaid = cxcp_mon_monedaid LEFT JOIN (SELECT * FROM rpt_pagosconsideradoscxc WHERE pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid AND PAGOC_OV_CodigoOV = ? WHERE OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' AND pagoc_cxcpagoid IS NULL AND cantidadpagofactura IS NOT NULL AND ov_codigoov = ? GROUP BY ov_codigoov, cxcp_fechapago, cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago ORDER BY cxcp_fechapago", [$ov, $ov]));
                //clock($countPago);
                //clock($countProv);
                if ($countPago > 0 && $countProv == 0) {
                    $PAs = DB::select("SELECT ov_codigoov, Pagos.cxcp_fechapago, Pagos.cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM ordenesventa LEFT JOIN (SELECT ovd_detalleid, ovd_ov_ordenventaid, ovd_cantidadrequerida * ovd_preciounitario AS SUBTOTAL, ovd_cantidadrequerida * ovd_preciounitario * Isnull(ovd_porcentajedescuento, 0.0) AS DESCUENTO, ( ( ovd_cantidadrequerida * ovd_preciounitario ) - ( ovd_cantidadrequerida * ovd_preciounitario * Isnull( ovd_porcentajedescuento, 0.0) ) ) * Isnull(ovd_cmiva_porcentaje, 0.0) AS IVA, ovd_cantidadrequerida FROM ordenesventadetalle LEFT JOIN articulosespecificaciones ON ovd_art_articuloid = aet_art_articuloid AND aet_cmm_articuloespecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F') AS OrdenesVentaDetalle ON ov_ordenventaid = ovd_ov_ordenventaid LEFT JOIN (SELECT ftr_facturaid, ftr_mon_monedaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida * ftrd_preciounitario AS FTR_SUBTOTAL, ftrd_cantidadrequerida * ftrd_preciounitario * Isnull(ftrd_porcentajedescuento, 0.0) AS FTR_DESCUENTO, ( ( ftrd_cantidadrequerida * ftrd_preciounitario ) - ( ftrd_cantidadrequerida * ftrd_preciounitario * Isnull( ftrd_porcentajedescuento, 0.0) ) ) * Isnull(ftrd_cmiva_porcentaje, 0.0) AS FTR_IVA, ftrd_referenciaid, ftrd_cantidadrequerida FROM facturas INNER JOIN facturasdetalle fd ON fd.ftrd_ftr_facturaid = facturas.ftr_facturaid WHERE ftr_eliminado = 0 GROUP BY ftr_mon_monedaid, ftr_facturaid, ftrd_referenciaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida, ftrd_preciounitario, ftrd_porcentajedescuento, ftrd_cmiva_porcentaje) AS Facturas ON ovd_detalleid = ftrd_referenciaid LEFT JOIN (SELECT cxcpd_ftr_facturaid, Round(Isnull(Sum(Abs(cxcpd_montoaplicado)), 0.0), 2) AS cantidadPagoFactura, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM cxcpagos INNER JOIN cxcpagosdetalle ON cxcp_cxcpagoid = cxcpd_cxcp_cxcpagoid WHERE cxcp_eliminado = 0 AND cxcpd_ftr_facturaid IS NOT NULL GROUP BY cxcpd_ftr_facturaid, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago) AS Pagos ON ftr_facturaid = cxcpd_ftr_facturaid AND ftr_mon_monedaid = cxcp_mon_monedaid LEFT JOIN (SELECT * FROM rpt_pagosconsideradoscxc WHERE pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid AND PAGOC_OV_CodigoOV = ? WHERE OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' AND pagoc_cxcpagoid IS NULL AND cantidadpagofactura IS NOT NULL AND ov_codigoov = ? GROUP BY ov_codigoov, cxcp_fechapago, cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago ORDER BY cxcp_fechapago", [$ov, $ov]);
                    foreach ($PAs as $PA) {
                        //dd($PA);
                        Self::StorePago($PA);
                    }
                }else {
                while ($countPago > 0 && $countProv > 0) {                      
                    
                    $PR = RPT_PROV::primero($ov);                    
                    $PA = DB::select("SELECT ov_codigoov, Pagos.cxcp_fechapago, Pagos.cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM ordenesventa LEFT JOIN (SELECT ovd_detalleid, ovd_ov_ordenventaid, ovd_cantidadrequerida * ovd_preciounitario AS SUBTOTAL, ovd_cantidadrequerida * ovd_preciounitario * Isnull(ovd_porcentajedescuento, 0.0) AS DESCUENTO, ( ( ovd_cantidadrequerida * ovd_preciounitario ) - ( ovd_cantidadrequerida * ovd_preciounitario * Isnull( ovd_porcentajedescuento, 0.0) ) ) * Isnull(ovd_cmiva_porcentaje, 0.0) AS IVA, ovd_cantidadrequerida FROM ordenesventadetalle LEFT JOIN articulosespecificaciones ON ovd_art_articuloid = aet_art_articuloid AND aet_cmm_articuloespecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F') AS OrdenesVentaDetalle ON ov_ordenventaid = ovd_ov_ordenventaid LEFT JOIN (SELECT ftr_facturaid, ftr_mon_monedaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida * ftrd_preciounitario AS FTR_SUBTOTAL, ftrd_cantidadrequerida * ftrd_preciounitario * Isnull(ftrd_porcentajedescuento, 0.0) AS FTR_DESCUENTO, ( ( ftrd_cantidadrequerida * ftrd_preciounitario ) - ( ftrd_cantidadrequerida * ftrd_preciounitario * Isnull( ftrd_porcentajedescuento, 0.0) ) ) * Isnull(ftrd_cmiva_porcentaje, 0.0) AS FTR_IVA, ftrd_referenciaid, ftrd_cantidadrequerida FROM facturas INNER JOIN facturasdetalle fd ON fd.ftrd_ftr_facturaid = facturas.ftr_facturaid WHERE ftr_eliminado = 0 GROUP BY ftr_mon_monedaid, ftr_facturaid, ftrd_referenciaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida, ftrd_preciounitario, ftrd_porcentajedescuento, ftrd_cmiva_porcentaje) AS Facturas ON ovd_detalleid = ftrd_referenciaid LEFT JOIN (SELECT cxcpd_ftr_facturaid, Round(Isnull(Sum(Abs(cxcpd_montoaplicado)), 0.0), 2) AS cantidadPagoFactura, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM cxcpagos INNER JOIN cxcpagosdetalle ON cxcp_cxcpagoid = cxcpd_cxcp_cxcpagoid WHERE cxcp_eliminado = 0 AND cxcpd_ftr_facturaid IS NOT NULL GROUP BY cxcpd_ftr_facturaid, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago) AS Pagos ON ftr_facturaid = cxcpd_ftr_facturaid AND ftr_mon_monedaid = cxcp_mon_monedaid LEFT JOIN (SELECT * FROM rpt_pagosconsideradoscxc WHERE pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid AND PAGOC_OV_CodigoOV = ? WHERE OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' AND pagoc_cxcpagoid IS NULL AND cantidadpagofactura IS NOT NULL AND ov_codigoov = ? GROUP BY ov_codigoov, cxcp_fechapago, cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago ORDER BY cxcp_fechapago ASC", [$ov, $ov]);
                    //dd($PA);
                                      
                    if (count($PA) > 0){
                        
                        $PA = $PA[0];
                        $valore= $PA->cantidadpagofactura * 1;
                        $identificadorPagoExiste = count(explode(':', $PA->CXCP_IdentificacionPago));
                        $identificadorPago = ($identificadorPagoExiste >= 2)? trim(explode(':', $PA->CXCP_IdentificacionPago)[1]) : trim(explode(':', $PA->CXCP_IdentificacionPago)[0]);
                        if (($PR->PCXC_Cantidad_provision * 1) >= ($valore)) {
                            Self::StorePago($PA);
                            $nuevaCantidadProv = $PR->PCXC_Cantidad_provision - $valore;
                            $PR->PCXC_Cantidad_provision = $nuevaCantidadProv;
                            if (is_null($PR->PCXC_pagos) || strlen($PR->PCXC_pagos) == 0) {
                                $PR->PCXC_pagos = $identificadorPago;
                            }else {                                
                                $PR->PCXC_pagos = $PR->PCXC_pagos . ',' . $identificadorPago;
                            }
                            //clock('prov mayor a pago', $nuevaCantidadProv, $identificadorPago, $PR, $PA);
                                                                           
                            if ($nuevaCantidadProv == 0) {
                                $PR->PCXC_Activo ='0'; //desactivar provision  
                                Self::RemoveAlertProvision($PR->PCXC_ID);                         
                            }
                            $PR->save();
                        }
                        else if ($valore > $PR->PCXC_Cantidad_provision) {
                            $cantidadPagada = $valore;
                            $provisionesOV = RPT_PROV::activeOV($PA->ov_codigoov);
                            //dd('provisionesOV'. $provisionesOV);
                            foreach ($provisionesOV as $key => $provId) {
                                $prov = RPT_PROV::find($provId);
                                if ($cantidadPagada > 0) {                                              
                                    $nuevaCant = $prov->PCXC_Cantidad_provision - $cantidadPagada;
                                    if ($nuevaCant <= 0) {
                                        $cantidadPagada = $nuevaCant * -1;
                                        $prov->PCXC_Cantidad_provision = 0;
                                        if (is_null($prov->PCXC_pagos) || strlen($prov->PCXC_pagos) == 0) {
                                            
                                            $prov->PCXC_pagos = $identificadorPago;
                                        } else {
                                            $prov->PCXC_pagos = $prov->PCXC_pagos . ',' . $identificadorPago;
                                        }                                   
                                        $prov->PCXC_Activo = '0'; //desactivar provision                              
                                        $prov->save();
                                        Self::RemoveAlertProvision($provId);
                                        //clock('prov menor a pago: CantPagada', $cantidadPagada, $identificadorPago, $prov, $PA);
                                    } else if ($nuevaCant > 0) {
                                        $cantidadPagada = 0;
                                        $prov->PCXC_Cantidad_provision = $nuevaCant;
                                        if (is_null($prov->PCXC_pagos)|| strlen($prov->PCXC_pagos) == 0) {
                                            $prov->PCXC_pagos = $identificadorPago;
                                        } else {
                                            $prov->PCXC_pagos = $prov->PCXC_pagos . ',' . $identificadorPago;
                                        }                                                                                      
                                        $prov->save();
                                        //clock('prov menor a pago: CantPagada==nuevaCant', $cantidadPagada, $identificadorPago, $prov, $PA);
                                    } 
                                }
                                
                            }
                            Self::StorePago($PA);

                        }
                }
                    $countProv = DB::table('RPT_ProvisionCXC')->where('PCXC_OV_Id', $ov)->where('PCXC_Activo', 1)
                    ->where('PCXC_Eliminado', 0)->count();

                    $countPago = count(DB::select("SELECT ov_codigoov, Pagos.cxcp_fechapago, Pagos.cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM ordenesventa LEFT JOIN (SELECT ovd_detalleid, ovd_ov_ordenventaid, ovd_cantidadrequerida * ovd_preciounitario AS SUBTOTAL, ovd_cantidadrequerida * ovd_preciounitario * Isnull(ovd_porcentajedescuento, 0.0) AS DESCUENTO, ( ( ovd_cantidadrequerida * ovd_preciounitario ) - ( ovd_cantidadrequerida * ovd_preciounitario * Isnull( ovd_porcentajedescuento, 0.0) ) ) * Isnull(ovd_cmiva_porcentaje, 0.0) AS IVA, ovd_cantidadrequerida FROM ordenesventadetalle LEFT JOIN articulosespecificaciones ON ovd_art_articuloid = aet_art_articuloid AND aet_cmm_articuloespecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F') AS OrdenesVentaDetalle ON ov_ordenventaid = ovd_ov_ordenventaid LEFT JOIN (SELECT ftr_facturaid, ftr_mon_monedaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida * ftrd_preciounitario AS FTR_SUBTOTAL, ftrd_cantidadrequerida * ftrd_preciounitario * Isnull(ftrd_porcentajedescuento, 0.0) AS FTR_DESCUENTO, ( ( ftrd_cantidadrequerida * ftrd_preciounitario ) - ( ftrd_cantidadrequerida * ftrd_preciounitario * Isnull( ftrd_porcentajedescuento, 0.0) ) ) * Isnull(ftrd_cmiva_porcentaje, 0.0) AS FTR_IVA, ftrd_referenciaid, ftrd_cantidadrequerida FROM facturas INNER JOIN facturasdetalle fd ON fd.ftrd_ftr_facturaid = facturas.ftr_facturaid WHERE ftr_eliminado = 0 GROUP BY ftr_mon_monedaid, ftr_facturaid, ftrd_referenciaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida, ftrd_preciounitario, ftrd_porcentajedescuento, ftrd_cmiva_porcentaje) AS Facturas ON ovd_detalleid = ftrd_referenciaid LEFT JOIN (SELECT cxcpd_ftr_facturaid, Round(Isnull(Sum(Abs(cxcpd_montoaplicado)), 0.0), 2) AS cantidadPagoFactura, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM cxcpagos INNER JOIN cxcpagosdetalle ON cxcp_cxcpagoid = cxcpd_cxcp_cxcpagoid WHERE cxcp_eliminado = 0 AND cxcpd_ftr_facturaid IS NOT NULL GROUP BY cxcpd_ftr_facturaid, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago) AS Pagos ON ftr_facturaid = cxcpd_ftr_facturaid AND ftr_mon_monedaid = cxcp_mon_monedaid LEFT JOIN (SELECT * FROM rpt_pagosconsideradoscxc WHERE pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid AND PAGOC_OV_CodigoOV = ? WHERE OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' AND pagoc_cxcpagoid IS NULL AND cantidadpagofactura IS NOT NULL AND ov_codigoov = ? GROUP BY ov_codigoov, cxcp_fechapago, cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago ORDER BY cxcp_fechapago", [$ov, $ov]));
                }//end WHILE
            }
            } //END FOREACH
            DB::commit();

            return view('Finanzas.ProvisionCXC', compact('cbousuarios', 'estado', 'estado_save', 'cliente', 'comprador', 'actividades', 'ultimo', 'provdescripciones', 'provalertas', 'cbonumpago'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function consultaDatosInicio()
    {

        try {

            ini_set('memory_limit', '-1');
            set_time_limit(0);

            date_default_timezone_set('America/Mexico_City');
            $fechaDia = date('d-m-Y');
            $nuevaFechaPago = date("d-m-Y", strtotime($fechaDia . "- 1 days"));

            $consulta = \DB::select(
                \DB::raw(
                    "SELECT
                        BCS_BancoCuentaId AS DT_RowId
                        ,0 AS CHECK_BOX
                        ,BAN_NombreBanco AS Banco
                        ,BCS_Cuenta AS Cuenta
                        ,MON_Abreviacion AS Moneda
                        ,CASE
                            WHEN MON_Abreviacion = 'MN'
                                THEN 1
                            ELSE TC.MONP_TipoCambioOficial
                        END AS TipoCambio
                        ,CASE
                            WHEN MON_Abreviacion = 'MN' THEN
								NULL
							ELSE
								CASE
									WHEN CON.CON_SaldoFinal IS NULL
										THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
									ELSE
										IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
								END 
							END
						AS SaldoDisponible
                        ,CASE
                            WHEN MON_Abreviacion = 'MN'
                                --THEN BCS_MontoInicial
                                THEN
                                    CASE
                                    WHEN CON.CON_SaldoFinal IS NULL
                                        THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    ELSE
                                        IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    END
                            ELSE
                                CASE
                                    WHEN CON.CON_SaldoFinal IS NULL
                                        THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    ELSE
                                        IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                END * TC.MONP_TipoCambioOficial
                                --TC.MONP_TipoCambioOficial * BCS_MontoInicial
                        END AS SaldoDisponibleMN
                        ,CASE
                            WHEN TotalDepositos IS NULL
                                THEN 0
                            ELSE TotalDepositos
                        END AS DepositosTransito
                        ,CASE
                            WHEN MON_Abreviacion = 'MN' AND TotalDepositos IS NOT NULL
                                THEN 1 * TotalDepositos
                            WHEN MON_Abreviacion = 'MN' AND TotalDepositos IS NULL
                                THEN 0
                            WHEN TC.MONP_TipoCambioOficial IS NULL AND TotalDepositos IS NULL
                                THEN 0
                            WHEN TC.MONP_TipoCambioOficial IS NOT NULL AND TotalDepositos IS NULL
                                THEN 0
                            ELSE TC.MONP_TipoCambioOficial * TotalDepositos
                        END AS DepositosTransitoMN
                        ,CASE
                            WHEN TotalRetiros IS NULL
                                THEN 0
                            ELSE TotalRetiros
                        END AS RetirosTransito
                        ,CASE
                            WHEN MON_Abreviacion = 'MN' AND TotalRetiros IS NOT NULL
                                THEN 1 * TotalRetiros
                            WHEN MON_Abreviacion = 'MN' AND TotalRetiros IS NULL
                                THEN 0
                            WHEN TC.MONP_TipoCambioOficial IS NULL AND TotalRetiros IS NULL
                                THEN 0
                            WHEN TC.MONP_TipoCambioOficial IS NOT NULL AND TotalRetiros IS NULL
                                THEN 0
                            ELSE TC.MONP_TipoCambioOficial * TotalRetiros
                        END AS RetirosTransitoMN
                        ,CASE
                            WHEN CON.CON_SaldoFinal IS NULL
                                THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                            ELSE
                                IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                        END + IsNull(TotalDepositos,0) - IsNull(TotalRetiros,0)
                        AS SaldosContable
                        ,CASE
                            WHEN MON_Abreviacion = 'MN'
                                THEN
                                    CASE
                                        WHEN CON.CON_SaldoFinal IS NULL
                                            THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                        ELSE
                                            IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    END + IsNull(TotalDepositos,0) - IsNull(TotalRetiros,0) * 1
                            ELSE
                                CASE
                                        WHEN CON.CON_SaldoFinal IS NULL
                                            THEN IsNull(BCS_MontoInicial,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                        ELSE
                                            IsNull(CON.CON_SaldoFinal,0) + IsNull(TotalDepositos_G,0) - IsNull(TotalRetiros_G,0)
                                    END + IsNull(TotalDepositos,0) - IsNull(TotalRetiros,0) * ISNULL(TC.MONP_TipoCambioOficial,0)
                        END AS SaldosContableMN
                    FROM BancosCuentasSimples
                    INNER JOIN Bancos ON BAN_BancoId = BCS_BAN_BancoId
                    INNER JOIN Monedas ON MON_MonedaId = BCS_MON_MonedaId
                    LEFT JOIN (
                        SELECT
                            MONP_TipoCambioOficial
                            ,MONP_MON_MonedaId
                        FROM MonedasParidad
                        WHERE CAST(MONP_FechaInicio AS DATE) = '" . $nuevaFechaPago . "'
                    ) AS TC ON TC.MONP_MON_MonedaId = MON_MonedaId
                    LEFT JOIN (
                        SELECT TOP 1
                            CON_FechaFinal
                            ,CON_SaldoFinal
                            ,CON_BCS_BancoCuentaId
                        FROM Conciliaciones
                        INNER JOIN BancosCuentasSimples ON CON_BCS_BancoCuentaId = BCS_BancoCuentaId
                        WHERE CON_Eliminado = 0
                        ORDER BY
                            CON_FechaCreacion
                        DESC
                    ) AS CON ON CON.CON_BCS_BancoCuentaId = BCS_BancoCuentaId
                    LEFT JOIN(
                        SELECT
                            --SUM(CXCP_MontoPago) AS TotalDepositos_G
                            IsNull(SUM(CASE
                            WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Pesos'
                                THEN (CXCP_MontoPago / CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Pesos'
                                THEN (CXCP_MontoPago / CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Dolar'
                                THEN (CXCP_MontoPago * CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Euro'
                                THEN (CXCP_MontoPago * CXCP_ParidadUsuario)
                            WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Pesos'
                                THEN CXCP_MontoPago
                            WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Dolar'
                                THEN CXCP_MontoPago
                            WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Euro'
                                THEN CXCP_MontoPago
                            --ELSE 'The quantity is under 30'
                        END),0) AS TotalDepositos_G
                            ,CXCP_BCS_BancoCuentaId
                        FROM CXCPagos
                        LEFT JOIN Clientes ON CLI_ClienteId = CXCP_CLI_ClienteId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXCP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXCP_MON_MonedaId
                        WHERE CAST(CXCP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        AND CXCP_Eliminado = 0
                        AND CXCP_Conciliado = 0
                        AND CXCP_CandidatoConciliacion = 1
                        GROUP BY
                            CXCP_BCS_BancoCuentaId
                    ) AS DEP_GUARDADOS ON DEP_GUARDADOS.CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    LEFT JOIN(
                        SELECT
                            SUM(CXCP_MontoPago) AS TotalDepositos
                            ,CXCP_BCS_BancoCuentaId
                        FROM CXCPagos
                        INNER JOIN Clientes ON CLI_ClienteId = CXCP_CLI_ClienteId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXCP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXCP_MON_MonedaId
                        WHERE CXCP_Eliminado = 0
                        AND CXCP_Conciliado = 0
                        AND CXCP_CandidatoConciliacion = 0
                        AND CAST(CXCP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        GROUP BY
                            CXCP_BCS_BancoCuentaId
                    ) AS DEP ON DEP.CXCP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    LEFT JOIN(
                        SELECT
                            --SUM(CXPP_MontoPago) AS TotalRetiros
                            IsNull(SUM(CASE
                                WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Pesos'
                                    THEN (CXPP_MontoPago / CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Pesos'
                                    THEN (CXPP_MontoPago / CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Dolar'
                                    THEN (CXPP_MontoPago * CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Euro'
                                    THEN (CXPP_MontoPago * CXPP_ParidadUsuario)
                                WHEN Cuenta.MON_Nombre = 'Pesos' AND Pago.MON_Nombre = 'Pesos'
                                    THEN CXPP_MontoPago
                                WHEN Cuenta.MON_Nombre = 'Dolar' AND Pago.MON_Nombre = 'Dolar'
                                    THEN CXPP_MontoPago
                                WHEN Cuenta.MON_Nombre = 'Euro' AND Pago.MON_Nombre = 'Euro'
                                    THEN CXPP_MontoPago
                                --ELSE 'The quantity is under 30'
                            END),0) AS TotalRetiros_G
                            ,CXPP_BCS_BancoCuentaId
                        FROM CXPPagos
                        LEFT JOIN Proveedores ON PRO_ProveedorId = CXPP_PRO_ProveedorId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXPP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXPP_MON_MonedaId
                        WHERE CAST(CXPP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        AND CXPP_Eliminado = 0
                        AND CXPP_CandidatoConciliacion = 1
                        AND CXPP_Conciliado = 0
                        AND CXPP_CMM_FormaPagoId <> '29C26EAC-AE9F-4A2B-B03A-7983B13C656B'
                        GROUP BY
                            CXPP_BCS_BancoCuentaId
                    ) AS RET_GUARDADOS ON RET_GUARDADOS.CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    LEFT JOIN(
                        SELECT
                            SUM(CXPP_MontoPago) AS TotalRetiros
                            ,CXPP_BCS_BancoCuentaId
                        FROM CXPPagos
                        INNER JOIN Proveedores ON PRO_ProveedorId = CXPP_PRO_ProveedorId
                        INNER JOIN ControlesMaestrosMultiples ON CMM_ControlId = CXPP_CMM_FormaPagoId
                        INNER JOIN BancosCuentasSimples ON CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                        INNER JOIN Monedas Cuenta ON Cuenta.MON_MonedaId = BCS_MON_MonedaId
                        INNER JOIN Monedas Pago ON Pago.MON_MonedaId = CXPP_MON_MonedaId
                        WHERE CXPP_Eliminado = 0
                        AND CXPP_CandidatoConciliacion = 0
                        AND CXPP_Conciliado = 0
                        AND CXPP_CMM_FormaPagoId <> '29C26EAC-AE9F-4A2B-B03A-7983B13C656B'
                        AND CAST(CXPP_FechaPago AS DATE) BETWEEN CAST(BCS_FechaInicial AS DATE) AND '" . $fechaDia . "'
                        GROUP BY
                            CXPP_BCS_BancoCuentaId
                    ) AS RET ON RET.CXPP_BCS_BancoCuentaId = BCS_BancoCuentaId
                    WHERE BCS_Eliminado = 0
                    AND BAN_Activo = 1
                    AND BAN_DefinidoPorUsuario1 = 'S'
                    ORDER BY
                        MON_Abreviacion
                        ,BCS_Cuenta
                        ,BAN_NombreBanco
                    ASC"
                )
            );

            $ajaxData = array();
            $ajaxData['consulta'] = $consulta;

            $array = array();
            $array['bancos'] = json_encode($ajaxData);

            return $array;
        } catch (\Exception $e) {

            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }
    public function actualizaProvision(Request $request)
    {
        $fila = [];
        //falta borrar de las tablas de pagos recibidos
        $fila['PCXC_Fecha'] = $request->input('fechaprovision');
        $rs = DB::select("select (SUBSTRING( CAST(year('" . $request->input('fechaprovision') . "') as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, '" . $request->input('fechaprovision') . "')) as semana");
        $semanaFecha = null;
        if (count($rs) == 1) {
            $semanaFecha = $rs[0]->semana;
        }
        $fila['PCXC_Semana_fecha'] = $semanaFecha;
        $fila['PCXC_Cantidad'] = $request->input('cant');
        $fila['PCXC_Cantidad_provision'] = $request->input('cant');
        $fila['PCXC_Concepto'] = $request->input('descripcion');
        $fila['PCXC_Observaciones'] = $request->input('comment');

        DB::table('RPT_ProvisionCXC')
        ->where('PCXC_ID', $request->input('id'))
        ->update($fila);

        $cantxprovisionar = self::cant_restantex_provisionar($request->input('idov'));
        
        return compact('cantxprovisionar');
    }
    public function getconcepto_prov_cxc(Request $request){
       $idconcepto =  DB::table('ControlesMaestrosMultiples')
            ->where("CMM_Control", 'CMM_ProvisionDescripcionesCXC')
            ->where("CMM_Valor", $request->input('textconcepto'))
            ->where("CMM_Eliminado", 0)
            ->value('CMM_ControlId');
        return compact('idconcepto');
    }

    public function borraProvision(Request $request)
    {
        DB::table('RPT_ProvisionCXC')
        ->where("PCXC_ID", $request->input('idprov'))
        ->update(['PCXC_Eliminado' => 1, 'PCXC_Activo' => 0]);
        
        $cantxprovisionar = self::cant_restantex_provisionar($request->input('idov'));
        return compact('cantxprovisionar');
    }  

    public function getcantalertas(Request $request){
        $Id_prov = Input::get('idprov');
        $cant = DB::select("SELECT * FROM RPT_Alertas 
        WHERE ALERT_Modulo = 'RPTFinanzasController' AND ALERT_Eliminado = 0 
        AND ALERT_Clave = ?", [$Id_prov]);
        $cantalertas = count($cant);
        return compact('cantalertas');
    }

    public function KardexOV(Request $request){
        //dd(Input::get('pKey'));
        if (Auth::check()) {
            if ($request->has('pKey') && Input::get('pKey') != '') {
                    $Id_OV = Input::get('pKey');
            }else{
                $Id_OV = '';
                Session::flash('error', 'Ninguna OV seleccionada.');
                return redirect()->back();
            }
            
        
        $info = DB::select("SELECT CLI_CodigoCliente + ' - ' + CLI_RazonSocial AS CLIENTE,                                  
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR
		FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	LEFT JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0

    where CONVERT(varchar(MAX), OV_OrdenVentaId) = ?",[$Id_OV]);
    //dd($info);
        if (Auth::check()) {
            $sql = "SELECT (Select Cast(OV_FechaOV as Date) from OrdenesVenta Where OV_OrdenVentaId = '" . $Id_OV . "') as FECHA,
        'VENTAS' as IDENTIF,
       (Select OV_CodigoOV from OrdenesVenta Where OV_OrdenVentaId =  '" . $Id_OV . "' ) as DOCUMENT, 
       (Select OV_ReferenciaOC from OrdenesVenta Where OV_OrdenVentaId = '" . $Id_OV . "') as REFERENCIA,       
       SUM(OVD_CantidadRequerida * OVD_PrecioUnitario - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0)) +
       ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
       ISNULL(OVD_CMIVA_Porcentaje, 0.0)) as IMP_OV,
         0 as IMP_FAC,
         0 as IMP_EMB,
         0 as IMP_PAG
From OrdenesVentaDetalle
Where OVD_OV_OrdenVentaId = '" . $Id_OV . "'
Union All
Select  Cast(FTR_FechaFactura as Date) as FECHA,
        'FACTURA' as IDENTIF,
        'FT' + FTR_NumeroFactura as DOCUMENT,
        (Select CMM_Valor from ControlesMaestrosMultiples Where CMM_ControlId = FTR_CMM_TipoRegistroId) as REFERENCIA,
        0 as IMP_OV,
        SUM(FTRD_CantidadRequerida * FTRD_PrecioUnitario -                
        FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0) +
        ((FTRD_CantidadRequerida * FTRD_PrecioUnitario) - (FTRD_CantidadRequerida * FTRD_PrecioUnitario * 
        ISNULL(FTRD_PorcentajeDescuento, 0.0))) * ISNULL(FTRD_CMIVA_Porcentaje, 0.0)) as IMP_FAC,
         0 as IMP_EMB,
         0 as IMP_PAG   
From Facturas                
Inner join FacturasDetalle on FTRD_FTR_FacturaId = FTR_FacturaId  
Where FTR_Eliminado = 0 and FTR_OV_OrdenVentaId = '" . $Id_OV . "' 
Group By FTR_FechaFactura, FTR_NumeroFactura, FTR_CMM_TipoRegistroId
Union All
Select  Cast(CXCP_FechaCaptura as Date) as FECHA,
        'PAGO A F- ' + FTR_NumeroFactura  as IDENTIF,
        ISNULL(CXCP_CodigoPago, CXCP_IdentificacionPago) as DOCUMENT,
        (Select CMM_Valor from ControlesMaestrosMultiples Where CMM_ControlId = CXCP_CMM_TipoRegistro) as REFERENCIA,
        0 as IMP_OV,
        0 as IMP_FAC,
        0 as IMP_EMB,
        (CXCPD_MontoAplicado * CXCP_MONP_Paridad) as IMP_PAG
From CXCPagos   
Inner Join CXCPagosDetalle on CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId   
Inner Join Facturas on FTR_FacturaId = CXCPD_FTR_FacturaId and FTR_OV_OrdenVentaId = '" . $Id_OV . "'
Where CXCP_Eliminado = 0 and CXCP_CMM_FormaPagoId <> 'F86EC67D-79BD-4E1A-A48C-08830D72DA6F'
Union All
Select  Cast(NC_FechaPoliza as Date) as FECHA,
        'NC A F- ' + FTR_NumeroFactura as IDENTIF,
        NC_Codigo as DOCUMENT,
        NCD_Descripcion as REFERENCIA,
        0 as IMP_OV,
        (CXCP_MontoPago * CXCP_MONP_Paridad * -1) as IMP_FAC,
        0 as IMP_EMB,
        0 as IMP_PAG
From CXCPagos
Inner Join CXCPagosDetalle on CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId   
inner Join NotasCredito on NC_NotaCreditoId = CXCPD_NC_NotaCreditoId
inner join NotasCreditoDetalle on NCD_NC_NotaCreditoId = NC_NotaCreditoId
inner join Facturas on NC_FTR_FacturaId = FTR_FacturaId and FTR_OV_OrdenVentaId = '" . $Id_OV . "'
Where CXCP_Eliminado = 0
Order by FECHA, IDENTIF DESC";
            $ovs = DB::select($sql);
            $sumOV = array_sum(array_pluck($ovs, 'IMP_OV'));
            $sumFAC = array_sum(array_pluck($ovs, 'IMP_FAC'));
            $sumEMB = array_sum(array_pluck($ovs, 'IMP_EMB'));
            $sumPAG = array_sum(array_pluck($ovs, 'IMP_PAG'));
           // $ovs = collect($ovs);
            $pdf = \PDF::loadView('Finanzas.kardexOV_PDF', 
            compact('ovs', 'info', 'sumOV', 'sumFAC', 'sumEMB', 'sumPAG'));
           // $pdf = \PDF::loadView('welcome', compact('data'));
            $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]); 
            return $pdf->stream('Kardex OV ' . ' - ' . date("d/m/Y") . '.Pdf');
        } else {
            return redirect()->route('auth/login');
        }
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function allOvs()
    {
        if (Auth::check()) {
            $sql = "SELECT
        OV_OrdenVentaId  AS DT_ID,
        OV_CodigoOV AS CODIGO,
        CASE WHEN OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' THEN 'Abierta' 
        WHEN OV_CMM_EstadoOVId = '2209C8BF-8259-4D8C-A0E9-389F52B33B46' THEN 'Cerrada' 
        WHEN OV_CMM_EstadoOVId = 'D528E9EC-83CF-49BE-AEED-C3751A3B0F27' THEN 'Embarque Completo' 
        ELSE 'Cancelado' END ESTATUS_OV,
        CLI_CodigoCliente + ' - ' + CLI_RazonSocial AS CLIENTE,                                  
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR
    FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	LEFT JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0

    GROUP BY
	
        OV_OrdenVentaId,
        OV_CodigoOV,       
       CLI_CodigoCliente,
        CLI_RazonSocial,       
       PRY_CodigoEvento,
		PRY_NombreProyecto,
        OV_FechaOV,
        OV_ReferenciaOC,
        OV_FechaRequerida,
        OV_CMM_EstadoOVId,
       CCON_Nombre
    ORDER BY
        OV_CodigoOV";    
            $consulta = DB::select($sql);

            //Definimos las columnas 
            $columns = array(
                ["data" => "DT_ID", "name" => "ID"], //ID OV
                ["data" => "CODIGO", "name" => "CODIGO"],
                ["data" => "ESTATUS_OV", "name" => "ESTATUS"],
                ["data" => "CLIENTE", "name" => "CLIENTE"],
                ["data" => "PROYECTO", "name" => "PROYECTO"],
                ["data" => "COMPRADOR", "name" => "COMPRADOR"],
            );
           $columndefs = array(
            ["targets"=> [ 0 ],
                "visible"=> false]
            );
            return response()->json(array('data' => $consulta, 'columndefs' => $columndefs, 'columns' => $columns, 'pkey' => 'DT_ID'));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function RemoveAlertProvision($id_prov){
        DB::table('RPT_Alertas')
            ->where("ALERT_Clave", $id_prov)
            ->update(['ALERT_Eliminado' => 1, 'ALERT_AccionTomada' => 'RPT-Completa']);
    }
   
    public function StorePago($pago){
        $p = new RPT_PAGO;
        $p->PAGOC_OV_CodigoOV = $pago->ov_codigoov;
        $p->PAGOC_CXCP_FechaPago = date_create($pago->cxcp_fechapago);
        $p->PAGOC_cantidadPagoFactura = $pago->cantidadpagofactura;
        $p->PAGOC_CXCPagoId = $pago->cxcp_cxcpagoid;
        $p->PAGOC_Eliminado = 0;
        $p->PAGOC_IdentificadorPagoDesc = $pago->CXCP_IdentificacionPago;
        $pos = strpos($pago->CXCP_IdentificacionPago, ':');
        if ($pos === false) {
            $p->PAGOC_IdentificadorPago = $pago->CXCP_IdentificacionPago;
        }else{
            $p->PAGOC_IdentificadorPago = trim(explode(':', $pago->CXCP_IdentificacionPago)[1]);
        }
        $p->save();      
    }
    public function combobox2(){
        $provdescripciones = DB::select("SELECT 
                          CMM_ControlId
                        , CMM_Valor
                    FROM ControlesMaestrosMultiples
                    WHERE CMM_Control = 'CMM_ProvisionDescripcionesCXC' AND CMM_Eliminado = 0
                    ORDER BY CMM_Valor");

        $provalertas = DB::select("SELECT 
                          CMM_ControlId
                        , CMM_Valor
                    FROM ControlesMaestrosMultiples
                    WHERE CMM_Control = 'CMM_ProvisionAlertasCXC' AND CMM_Eliminado = 0
                    ORDER BY CMM_Valor");
        $cbousuarios = DB::select("SELECT nomina AS llave , name AS valor FROM RPT_Usuarios 
INNER JOIN 
(select * from Empleados
WHERE
EMP_CodigoEmpleado > '' 
  AND NOT EMP_CodigoEmpleado like '%[^0-9]%')
Emp on Emp.EMP_CodigoEmpleado = nomina 
WHERE EMP_Activo = 1 ORDER BY name");
        return compact('provdescripciones', 'provalertas', 'cbousuarios');
    }
    public function combobox(Request $request){  
            if (!is_null($request->input('solocompradores'))) {
                $comboclientes = "'".$request->input('solocompradores'). "'";
                $compradores = DB::select("SELECT CCON_Nombre as llave, COALESCE (CCON_Nombre + ' - ' + CCON_Puesto, CCON_Nombre) AS valor
                FROM ClientesContactos
                INNER JOIN OrdenesVenta ON OV_CCON_ContactoId = CCON_ContactoId 
                LEFT JOIN  CLientes ON OV_CLI_ClienteId = CLI_ClienteId
                WHERE CCON_Eliminado = 0 AND  OV_CMM_EstadoOVId = '".$request->input('estado')."'
                AND CLI_CodigoCliente in (".$comboclientes.")
                GROUP BY CCON_Nombre, CCON_Puesto
                ORDER BY CCON_Nombre");
                $clientes = '';
            } else {
                $clientes = DB::select("SELECT CLI_CodigoCliente as llave, CLI_CodigoCliente +' - '+CLI_RazonSocial AS valor
                FROM Clientes
                LEFT JOIN OrdenesVenta ON OV_CLI_ClienteId = CLI_ClienteId 
                WHERE CLI_Activo = 1 AND CLI_Eliminado = 0 AND  OV_CMM_EstadoOVId = '".$request->input('estado')."'
                GROUP BY CLI_CodigoCliente, CLI_CodigoCliente, CLI_RazonSocial
                ORDER BY CLI_RazonSocial");
                $compradores = DB::select("SELECT CCON_Nombre as llave, COALESCE (CCON_Nombre + ' - ' + CCON_Puesto, CCON_Nombre) AS valor
                FROM ClientesContactos
                INNER JOIN OrdenesVenta ON OV_CCON_ContactoId = CCON_ContactoId 
                WHERE CCON_Eliminado = 0 AND  OV_CMM_EstadoOVId = '".$request->input('estado')."'
                GROUP BY CCON_Nombre, CCON_Puesto
                ORDER BY CCON_Nombre");
            }
        return compact('clientes', 'compradores');
    }
    public function registros(Request $request){
        try{
            ini_set('memory_limit', '-1');
            set_time_limit(0);

            $clientes = "'".$request->input('clientes'). "'";
            $clientes = str_replace("'',", "", $clientes);
            $compradores = "'".$request->input('compradores'). "'";
            $compradores = str_replace("'',", "", $compradores);
            $estado = $request->input('estado');
            $criterio = " OV_CMM_EstadoOVId ='" . $estado . "' ";
            if (strlen($clientes) > 3 && $clientes != '') {
                $criterio = $criterio . " AND (CLI_CodigoCliente in(".$clientes.") OR CLI_CodigoCliente is null) ";
            }
            if (strlen($compradores) > 3 && $compradores != '') {
                $criterio = $criterio. " AND ( CCON_Nombre in(".$compradores.") ) ";
            }
         
          //  $polizas_decimales = $dao->getEjecutaConsulta
          //  ("SELECT CMA_Valor FROM ControlesMaestros 
           // WHERE CMA_Control = 'CMA_CCNF_DecimalesPolizas'")[0]->CMA_Valor;
            $sel = "SELECT
        MON_Nombre AS Moneda, 
        OV_OrdenVentaId  AS DT_ID,
        OV_CodigoOV AS CODIGO,
        OV_CMM_EstadoOVId AS EstadoOV,
        CASE WHEN OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' THEN 'Abierta' 
        WHEN OV_CMM_EstadoOVId = '2209C8BF-8259-4D8C-A0E9-389F52B33B46' THEN 'Cerrada' 
        WHEN OV_CMM_EstadoOVId = 'D528E9EC-83CF-49BE-AEED-C3751A3B0F27' THEN 'Embarque Completo' 
        ELSE 'Cancelado' END ESTATUS_OV,
        CLI_CodigoCliente + ' - ' + CLI_RazonSocial AS CLIENTE,                                  
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR,
        CONVERT(varchar, OV_FechaOV,103) AS FECHA_OV,
        OV_ReferenciaOC,      
        ((ROUND(SUBTOTAL,2)) - (ROUND(DESCUENTO, 2))) + (ROUND(IVA, 2)) AS TOTAL,       	
		 
		(ISNULL((ROUND(FTR_TOTAL,2)), 0.0))  AS FTR_TOTAL

		,(((ROUND(SUBTOTAL,2)) - (ROUND(DESCUENTO, 2))) + (ROUND(IVA, 2))) - (ISNULL((ROUND(FTR_TOTAL,2)), 0.0)  + COALESCE(SUM(NotaCredito.TotalNC), 0)) AS IMPORTE_XFACTURAR
	
		,SUM(OrdenesVentaDetalle.OVD_CantidadRequerida) - ISNULL(SUM(FTRD_CantidadRequerida), 0.0) AS CANTIDAD_PENDIENTE,	
		COALESCE(SUM(NotaCredito.TotalNC), 0) TotalNC,
        ISNULL((ROUND(FTR_TOTAL,2)), 0.0)  + COALESCE(SUM(NotaCredito.TotalNC), 0) AS IMPORTE_FACTURADO,							
		
		COALESCE((Pagos.cantidadPagoFactura), 0) PAGOS_FACTURAS,
			
        (SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0) AS X_PAGAR,
        CANTPROVISION,
        CANTPROVISION_PAGADAS,
        CASE 
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) > 0 AND CANTPROVISION IS NULL AND CANTPROVISION_PAGADAS IS NULL THEN 'SIN CAPTURA'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) = 0 THEN 'COMPLETO'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) > CANTPROVISION OR CANTPROVISION_PAGADAS = 0 THEN 'INCOMPLETO'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) = CANTPROVISION THEN 'PROVISIONADO'
            ELSE 'NO ESPECIFICADO' END AS PROVISION,
		COALESCE((Embarque.EMB_TOTAL), 0 ) AS EMBARCADO,
        ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2))) - COALESCE((Embarque.EMB_TOTAL), 0 ) AS IMPORTE_XEMBARCAR
    FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	LEFT JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0
										
											LEFT JOIN (SELECT
											OVD_OV_OrdenVentaId,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario) AS SUBTOTAL,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0)) AS DESCUENTO,
                                                SUM(((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0)) AS IVA
                                               ,SUM(OVD_CantidadRequerida) OVD_CantidadRequerida
											FROM OrdenesVentaDetalle
                                            LEFT  JOIN ArticulosEspecificaciones ON OVD_ART_ArticuloId = AET_ART_ArticuloId AND AET_CMM_ArticuloEspecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F'
											GROUP BY OVD_OV_OrdenVentaId                                           
                                            ) AS OrdenesVentaDetalle ON OV_OrdenVentaId = OVD_OV_OrdenVentaId
											LEFT JOIN (
                                                      	SELECT
														FTR_OV_OrdenVentaId,
														SUM((FTRD_CantidadRequerida * FTRD_PrecioUnitario)- (FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0)) + (((FTRD_CantidadRequerida * FTRD_PrecioUnitario) - (FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0))) *
														ISNULL(FTRD_CMIVA_Porcentaje, 0.0))) AS FTR_TOTAL,
														SUM (FTRD_CantidadRequerida) FTRD_CantidadRequerida												
														FROM Facturas
														inner join FacturasDetalle fd on fd.FTRD_FTR_FacturaId = Facturas.FTR_FacturaId													
														WHERE FTR_Eliminado = 0 
													    GROUP BY FTR_OV_OrdenVentaId
                                                        ) AS Facturas ON FTR_OV_OrdenVentaId = OV_OrdenVentaId
														
					LEFT  JOIN (
					SELECT 
                      FTR_OV_OrdenVentaId,
        SUM (CXCP_MontoPago * CXCP_MONP_Paridad * -1) as TotalNC        
From CXCPagos
Inner Join CXCPagosDetalle on CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId   
inner Join NotasCredito on NC_NotaCreditoId = CXCPD_NC_NotaCreditoId
inner join NotasCreditoDetalle on NCD_NC_NotaCreditoId = NC_NotaCreditoId
inner join Facturas on NC_FTR_FacturaId = FTR_FacturaId 
Where CXCP_Eliminado = 0
GROUP BY FTR_OV_OrdenVentaId
                    ) AS NotaCredito ON  NotaCredito.FTR_OV_OrdenVentaId = OV_OrdenVentaId
					LEFT JOIN (
					SELECT SUM((OVD_CantidadRequerida * OVD_PrecioUnitario) - ( OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) )
							+ ( ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0) )
												)AS EMB_TOTAL
							,OVD_OV_OrdenVentaId AS OVD_id							
                FROM OrdenesVentaDetalle
                INNER JOIN EmbarquesDetalle ON OVD_DetalleId = EMBD_OVD_DetalleId
                INNER JOIN Embarques ON EMB_EmbarqueId = EMBD_EMB_EmbarqueId 
				WHERE EMBD_OVD_DetalleId IS NOT NULL
				GROUP BY OVD_OV_OrdenVentaId
					) AS Embarque ON OVD_id = OV_OrdenVentaId
				LEFT JOIN (
                       select FTR_OV_OrdenVentaId , 
					   SUM(CXCPD_MontoAplicado * CXCP_MONP_Paridad) as cantidadPagoFactura from CXCPagos
						inner join CXCPagosDetalle on CXCP_CXCPagoId  = CXCPD_CXCP_CXCPagoId
						inner join Facturas on CXCPD_FTR_FacturaId = FTR_FacturaId 						
                       WHERE CXCP_Eliminado = 0 and CXCP_CMM_FormaPagoId <> 'F86EC67D-79BD-4E1A-A48C-08830D72DA6F'
                   group by FTR_OV_OrdenVentaId   
                    ) AS Pagos ON Pagos.FTR_OV_OrdenVentaId = OV_OrdenVentaId
			 LEFT JOIN (		 
				SELECT PCXC_OV_Id,
                SUM( CASE WHEN PCXC_Activo = 1 THEN
				COALESCE(PCXC_Cantidad_provision,0) END) AS CANTPROVISION,
				SUM( CASE WHEN PCXC_Activo = 0 THEN
				COALESCE(PCXC_Cantidad,0) END) AS CANTPROVISION_PAGADAS
				FROM RPT_ProvisionCXC
				WHERE PCXC_Eliminado = 0
				GROUP BY PCXC_OV_Id
			) AS PROVISIONES ON PCXC_OV_Id = CONVERT (VARCHAR(100), OV_CodigoOV )
            INNER JOIN Monedas ON OV_MON_MonedaId = Monedas.MON_MonedaId
    WHERE  
    " . $criterio . "
     GROUP BY
	EMB_TOTAL,
        CANTPROVISION,
        CANTPROVISION_PAGADAS,
        OV_OrdenVentaId,
        OV_CodigoOV,       
       CLI_CodigoCliente,
        CLI_RazonSocial,       
       PRY_CodigoEvento,
	 	cantidadPagoFactura,
		 SUBTOTAL, DESCUENTO, IVA,
		PRY_NombreProyecto,
        OV_FechaOV,
        OV_ReferenciaOC,
        OV_FechaRequerida,
        OV_CMM_EstadoOVId,
       CCON_Nombre,
        MON_Nombre,
		FTR_TOTAL
    ORDER BY
        OV_CodigoOV";    
        $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
       // dd($sel);
            $consulta = DB::select($sel);
            $ordenesVenta = collect($consulta);
            return compact('ordenesVenta');
        } catch (\Exception $e){
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array("mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine())));
        }
    }
    public function guardarEstadoOV(Request $request){
        //dd($request);
        //si se va a cerrar o marcar como embarque verificamos si tiene OT pendientes esta OV   
        $ots = DB::select(
            "Select OT_Codigo as Codigo,
                    CONCAT( ART_CodigoArticulo , ' - ',
                    ART_Nombre) as Articulo,
                    OTDA_Cantidad as Cantidad
                    from OrdenesTrabajo
                    inner join OrdenesTrabajoReferencia on OT_OrdenTrabajoId = OTRE_OT_OrdenTrabajoId
                    inner join OrdenesVenta on OV_OrdenVentaId = OTRE_OV_OrdenVentaId
                    inner join OrdenesTrabajoDetalleArticulos on OT_OrdenTrabajoId = OTDA_OT_OrdenTrabajoId
                    inner join Articulos on ART_ArticuloId = OTDA_ART_ArticuloId
                    Where OT_Eliminado = 0 and (OT_CMM_Estatus = ? or OT_CMM_Estatus = ?)
                    and OV_CodigoOV = ?
                    Order By OV_CodigoOV, OT_Codigo",
            [
                '3C843D99-87A6-442C-8B89-1E49322B265A',
                'A488B27B-15CD-47D8-A8F3-E9FB8AC70B9B',
                $request->input('idov')
            ]
        );
        $cont = count($ots);
        $estado_save = trim($request->input('estado_save'));
        switch ($estado_save) {
            case '2209C8BF-8259-4D8C-A0E9-389F52B33B46'://cerrada x usuario
                $eliminarOV = 1;
                
                //DUDAS PENDIENTES AL MOMENTO DE CERRAR OV
                //si la OV tiene provisiones estas quedan eliminadas
                //self::RemoveProvision($request->input('idov'));
                break;            
            case 'D528E9EC-83CF-49BE-AEED-C3751A3B0F27': //embarque completo
                $eliminarOV = 0;
                break;
            default: // para el caso de seleccionar abierta 
                $eliminarOV = 0;
                $cont = 0; //es decir que no importa si hay OrdenesTrabajo pendientes, si se puede actualizar el estado de la OV
                $ots = []; //tambien vaciamos esta variable.
                break;
        }
        if ($cont == 0) {
            $rs = DB::table('OrdenesVenta')
            ->where("OV_CodigoOV", $request->input('idov'))
                ->update([
                    'OV_CMM_EstadoOVId' => $estado_save,
                    'OV_Eliminado' => $eliminarOV
                    ]);
        } //endif
        $ots = collect($ots);
        return compact('ots');
    }
    public function borraAlerta(Request $request){
        DB::table('RPT_Alertas')
            ->where("ALERT_Id", $request->input('idalerta'))
            ->update(['ALERT_Eliminado' => 1, 'ALERT_AccionTomada' => $request->input('evidencia')]);
    }     
    public function registros_provisiones(Request $request){
        try{
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $sel = "SELECT '' as ELIMINAR, RPT_ProvisionCXC.* FROM RPT_ProvisionCXC WHERE PCXC_OV_Id = ? AND PCXC_Eliminado = 0";    
            //$sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel,[$request->input('idov')]);  
            $provisiones = collect($consulta);
            
            $sel = "SELECT '' as ELIMINAR, '' as USUARIOS, RPT_Alertas.*, RPT_ProvisionCXC.PCXC_ID FROM RPT_Alertas
                    INNER JOIN RPT_ProvisionCXC ON RPT_Alertas.ALERT_Clave = RPT_ProvisionCXC.PCXC_ID
                    WHERE PCXC_OV_Id = ? AND PCXC_Eliminado = 0 AND
                    ALERT_Eliminado = 0 AND ALERT_Modulo = ?";
            $consulta = DB::select($sel, [$request->input('idov'), 'RPTFinanzasController']);
            $alertas = collect($consulta);
            return compact('provisiones', 'alertas');
        } catch (\Exception $e){
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array("mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine())));
        }
    }
    public function registrosOValertadas(Request $request)
    {
        try {
            ini_set('memory_limit', '-1');
            set_time_limit(0);
            $estado = $request->input('estado');            
            $criterio = " OV_CMM_EstadoOVId ='" . $estado . "' ";
            $clientes = "'" . $request->input('clientes') . "'";
            $clientes = str_replace("'',", "", $clientes);
            $compradores = "'" . $request->input('compradores') . "'";
            $compradores = str_replace("'',", "", $compradores);
            if (strlen($clientes) > 3 && $clientes != '') {
                $criterio = " AND (CLI_CodigoCliente in(" . $clientes . ") OR CLI_CodigoCliente is null) ";
            }
            if (strlen($compradores) > 3 && $compradores != '') {
                $criterio = $criterio . " AND ( CCON_Nombre in(" . $compradores . ") ) ";
            }
           
            //  $polizas_decimales = $dao->getEjecutaConsulta
            //  ("SELECT CMA_Valor FROM ControlesMaestros 
            // WHERE CMA_Control = 'CMA_CCNF_DecimalesPolizas'")[0]->CMA_Valor;
            $sel = "SELECT
        MON_Nombre AS Moneda, 
        OV_OrdenVentaId  AS DT_ID,
        OV_CodigoOV AS CODIGO,
        OV_CMM_EstadoOVId AS EstadoOV,
        CASE WHEN OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' THEN 'Abierta' 
        WHEN OV_CMM_EstadoOVId = '2209C8BF-8259-4D8C-A0E9-389F52B33B46' THEN 'Cerrada' 
        WHEN OV_CMM_EstadoOVId = 'D528E9EC-83CF-49BE-AEED-C3751A3B0F27' THEN 'Embarque Completo' 
        ELSE 'Cancelado' END ESTATUS_OV,
        CLI_CodigoCliente + ' - ' + CLI_RazonSocial AS CLIENTE,                                  
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR,
        CONVERT(varchar, OV_FechaOV,103) AS FECHA_OV,
        OV_ReferenciaOC,      
        ((ROUND(SUBTOTAL,2)) - (ROUND(DESCUENTO, 2))) + (ROUND(IVA, 2)) AS TOTAL,       	
		 
		(ISNULL((ROUND(FTR_TOTAL,2)), 0.0))  AS FTR_TOTAL

		,(((ROUND(SUBTOTAL,2)) - (ROUND(DESCUENTO, 2))) + (ROUND(IVA, 2))) - (ISNULL((ROUND(FTR_TOTAL,2)), 0.0)  + COALESCE(SUM(NotaCredito.TotalNC), 0)) AS IMPORTE_XFACTURAR
	
		,SUM(OrdenesVentaDetalle.OVD_CantidadRequerida) - ISNULL(SUM(FTRD_CantidadRequerida), 0.0) AS CANTIDAD_PENDIENTE,	
		COALESCE(SUM(NotaCredito.TotalNC), 0) TotalNC,
        ISNULL((ROUND(FTR_TOTAL,2)), 0.0)  + COALESCE(SUM(NotaCredito.TotalNC), 0) AS IMPORTE_FACTURADO,							
		
		COALESCE((Pagos.cantidadPagoFactura), 0) PAGOS_FACTURAS,
			
        (SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0) AS X_PAGAR,
        CANTPROVISION,
        CANTPROVISION_PAGADAS,
        CASE 
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) > 0 AND CANTPROVISION IS NULL AND CANTPROVISION_PAGADAS IS NULL THEN 'SIN CAPTURA'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) = 0 THEN 'COMPLETO'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) > CANTPROVISION OR CANTPROVISION_PAGADAS = 0 THEN 'INCOMPLETO'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0)) = CANTPROVISION THEN 'PROVISIONADO'
            ELSE 'NO ESPECIFICADO' END AS PROVISION,
		COALESCE((Embarque.EMB_TOTAL), 0 ) AS EMBARCADO,
        ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2))) - COALESCE((Embarque.EMB_TOTAL), 0 ) AS IMPORTE_XEMBARCAR
    FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	LEFT JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0
										
											LEFT JOIN (SELECT
											OVD_OV_OrdenVentaId,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario) AS SUBTOTAL,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0)) AS DESCUENTO,
                                                SUM(((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0)) AS IVA
                                               ,SUM(OVD_CantidadRequerida) OVD_CantidadRequerida
											FROM OrdenesVentaDetalle
                                            LEFT  JOIN ArticulosEspecificaciones ON OVD_ART_ArticuloId = AET_ART_ArticuloId AND AET_CMM_ArticuloEspecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F'
											GROUP BY OVD_OV_OrdenVentaId                                           
                                            ) AS OrdenesVentaDetalle ON OV_OrdenVentaId = OVD_OV_OrdenVentaId
											LEFT JOIN (
                                                      	SELECT
														FTR_OV_OrdenVentaId,
														SUM((FTRD_CantidadRequerida * FTRD_PrecioUnitario)- (FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0)) + (((FTRD_CantidadRequerida * FTRD_PrecioUnitario) - (FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0))) *
														ISNULL(FTRD_CMIVA_Porcentaje, 0.0))) AS FTR_TOTAL,
														SUM (FTRD_CantidadRequerida) FTRD_CantidadRequerida												
														FROM Facturas
														inner join FacturasDetalle fd on fd.FTRD_FTR_FacturaId = Facturas.FTR_FacturaId													
														WHERE FTR_Eliminado = 0 
													    GROUP BY FTR_OV_OrdenVentaId
                                                        ) AS Facturas ON FTR_OV_OrdenVentaId = OV_OrdenVentaId
														
					LEFT  JOIN (
					SELECT 
                      FTR_OV_OrdenVentaId,
       SUM (CXCP_MontoPago * CXCP_MONP_Paridad * -1) as TotalNC        
From CXCPagos
Inner Join CXCPagosDetalle on CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId   
inner Join NotasCredito on NC_NotaCreditoId = CXCPD_NC_NotaCreditoId
inner join NotasCreditoDetalle on NCD_NC_NotaCreditoId = NC_NotaCreditoId
inner join Facturas on NC_FTR_FacturaId = FTR_FacturaId 
Where CXCP_Eliminado = 0
GROUP BY FTR_OV_OrdenVentaId
                    ) AS NotaCredito ON  NotaCredito.FTR_OV_OrdenVentaId = OV_OrdenVentaId
					LEFT JOIN (
					SELECT SUM((OVD_CantidadRequerida * OVD_PrecioUnitario) - ( OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) )
							+ ( ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0) )
												)AS EMB_TOTAL
							,OVD_OV_OrdenVentaId AS OVD_id							
                FROM OrdenesVentaDetalle
                INNER JOIN EmbarquesDetalle ON OVD_DetalleId = EMBD_OVD_DetalleId
                INNER JOIN Embarques ON EMB_EmbarqueId = EMBD_EMB_EmbarqueId 
				WHERE EMBD_OVD_DetalleId IS NOT NULL
				GROUP BY OVD_OV_OrdenVentaId
					) AS Embarque ON OVD_id = OV_OrdenVentaId
				LEFT JOIN (
                       select FTR_OV_OrdenVentaId , 
					   SUM(CXCPD_MontoAplicado * CXCP_MONP_Paridad) as cantidadPagoFactura from CXCPagos
						inner join CXCPagosDetalle on CXCP_CXCPagoId  = CXCPD_CXCP_CXCPagoId
						inner join Facturas on CXCPD_FTR_FacturaId = FTR_FacturaId 						
                       WHERE CXCP_Eliminado = 0 and CXCP_CMM_FormaPagoId <> 'F86EC67D-79BD-4E1A-A48C-08830D72DA6F'
                   group by FTR_OV_OrdenVentaId   
                    ) AS Pagos ON Pagos.FTR_OV_OrdenVentaId = OV_OrdenVentaId
			 LEFT JOIN (		 
				SELECT PCXC_OV_Id ,
                SUM( CASE WHEN PCXC_Activo = 1 THEN
				COALESCE(PCXC_Cantidad_provision,0) END) AS CANTPROVISION,
				SUM( CASE WHEN PCXC_Activo = 0 THEN
				COALESCE(PCXC_Cantidad,0) END) AS CANTPROVISION_PAGADAS
				FROM RPT_ProvisionCXC
				WHERE PCXC_Eliminado = 0
				GROUP BY PCXC_OV_Id
			) AS PROVISIONES ON PCXC_OV_Id = CONVERT (VARCHAR(100), OV_CodigoOV )
            INNER JOIN Monedas ON OV_MON_MonedaId = Monedas.MON_MonedaId    
            INNER JOIN (
				SELECT PCXC_OV_Id 
				FROM RPT_ProvisionCXC
				INNER JOIN RPT_Alertas ON RPT_Alertas.ALERT_Clave = RPT_ProvisionCXC.PCXC_ID 
				WHERE PCXC_Activo = 1 AND PCXC_Eliminado = 0 
                AND ALERT_Modulo = 'RPTFinanzasController' 
                AND ALERT_FechaAlerta <= GETDATE() 
                AND ALERT_Eliminado = 0
                AND ALERT_Usuarios like '%" . Auth::user()->nomina . "%' 
				GROUP BY PCXC_OV_Id
			) AS ALERTAS ON ALERTAS.PCXC_OV_Id = CONVERT (VARCHAR(100), OV_CodigoOV )

    WHERE  
    " . $criterio . "   GROUP BY
	EMB_TOTAL,
        CANTPROVISION,
        CANTPROVISION_PAGADAS,
        OV_OrdenVentaId,
        OV_CodigoOV,       
       CLI_CodigoCliente,
        CLI_RazonSocial,       
       PRY_CodigoEvento,
	 	cantidadPagoFactura,
		 SUBTOTAL, DESCUENTO, IVA,
		PRY_NombreProyecto,
        OV_FechaOV,
        OV_ReferenciaOC,
        OV_FechaRequerida,
        OV_CMM_EstadoOVId,
       CCON_Nombre,
        MON_Nombre,
		FTR_TOTAL
    ORDER BY
        OV_CodigoOV";
            $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            //dd($sel);
            $consulta = DB::select($sel);

            //$resultSet = $dao->getArrayAsociativo($consulta);

            // $registros = count($resultSet);
            //  for($i= 0; $i < $registros; $i++){
            //      $resultSet[$i]['TOTAL'] = '$' . number_format($resultSet[$i]['TOTAL'], $polizas_decimales, '.', ',');
            //  }
           

            $ordenesVenta = collect($consulta);

            return compact('ordenesVenta');
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array(
                "mensaje" => $e->getMessage(),
                "codigo" => $e->getCode(),
                "clase" => $e->getFile(),
                "linea" => $e->getLine()
            )));
        }
    }   
     

function checkExcelFile($file_ext){
    $valid=array(
        'xls' // add your extensions here.
    );        
  
    return in_array($file_ext,$valid) ? true : false;
} 

public function guardaProvision(Request $request){
        $fila = [];
      //date('Ymd h:m:s');       
        $fila['PCXC_OV_Id'] = $request->input('inputid');
        $fila['PCXC_Fecha'] = $request->input('fechaprovision');
        //clock($request->input('fechaprovision'));
        //calculo de semana, falta validar en productivo.
        $rs = DB::select("select (SUBSTRING( CAST(year('". $request->input('fechaprovision')."') as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, '". $request->input('fechaprovision')."')) as semana");
        $semanaFecha = null;
        if (count($rs) == 1) {
            $semanaFecha = $rs[0]->semana;
        }
        $fila['PCXC_Semana_fecha'] = $semanaFecha;
        $fila['PCXC_Activo'] = 1;
        $fila['PCXC_Eliminado'] = 0;
        $fila['PCXC_Usuario'] = Auth::user()->nomina;
        $fila['PCXC_Cantidad_provision'] = $request->input('cant');
        $fila['PCXC_Cantidad'] = $request->input('cant');
        $fila['PCXC_Concepto'] = $request->input('descripcion');
        $fila['PCXC_Observaciones'] = $request->input('comment');
        
        // $exist = DB::table('RPT_ProvisionCXC')
        //     ->where('PCXC_Id', $request->input('input-id'))->count();
        // if ($exist == 0) {
        DB::table('RPT_ProvisionCXC')->insert($fila);
        // } else if($exist == 1){
        //     DB::table('RPT_ProvisionCXC')
        //         ->where("BC_Ejercicio", $ejercicio)
        //         ->update($fila);   
        // }
}
    public function guardaAlerta(Request $request)
    {
        $fila = [];      
        $fila['ALERT_Clave'] = $request->input('numpago');
        $fila['ALERT_FechaAlerta'] = $request->input('fechaalerta');
        $fila['ALERT_FechaCreacion'] = date('Ymd h:m:s');
        $fila['ALERT_Eliminado'] = 0;
        $fila['ALERT_Descripcion'] = $request->input('alerta');
        $fila['ALERT_Usuario'] = Auth::user()->nomina;        
        $fila['ALERT_Usuarios'] = Auth::user()->nomina;        
        $fila['ALERT_Modulo'] = 'RPTFinanzasController';

        DB::table('RPT_Alertas')->insert($fila);
        
    }
    public function guardaEditAlerta(Request $request){
        $users = '';
        if ($request->input('cbousuarios') != '') {
           $users = implode(",", $request->input('cbousuarios'));
        }
        DB::table('RPT_Alertas')
            ->where("ALERT_Id", $request->input('idalerta'))
            ->update(['ALERT_Usuarios' => $users]);
    }
public function cantprovision(Request $request){    
    
    $sel = "SELECT PCXC_Cantidad_provision, PCXC_ID AS llave, CONVERT(VARCHAR(max),PCXC_ID)+' - $'+ CONVERT(VARCHAR(max),CONVERT(MONEY,PCXC_Cantidad_provision),1) +' - ' + PCXC_Concepto AS valor FROM RPT_ProvisionCXC WHERE PCXC_Activo = 1 AND PCXC_OV_Id = ? AND PCXC_Eliminado = 0";
    $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
    $consulta = DB::select($sel, [$request->input('idov')]);
    $estado_save = DB::table('OrdenesVenta')->where ('OV_CodigoOV', $request->input('idov'))->value('OV_CMM_EstadoOVId');
    $suma = array_sum(array_pluck($consulta, 'PCXC_Cantidad_provision')); 
    if (is_null($suma)) {
        $suma = 0;
    }
    $cboprovisiones = $consulta;
//dd($cboprovisiones);
    return compact('suma', 'cboprovisiones', 'estado_save');
}
public function cant_restantex_provisionar($id_ov){    
    
    $sel = "select 

(SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE((Pagos.cantidadPagoFactura), 0) AS X_COBRAR
FROM OrdenesVenta
LEFT JOIN (SELECT
											OVD_OV_OrdenVentaId,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario) AS SUBTOTAL,
                                                SUM(OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0)) AS DESCUENTO,
                                                SUM(((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0)) AS IVA
                                               ,SUM(OVD_CantidadRequerida) OVD_CantidadRequerida
											FROM OrdenesVentaDetalle
                                            LEFT  JOIN ArticulosEspecificaciones ON OVD_ART_ArticuloId = AET_ART_ArticuloId AND AET_CMM_ArticuloEspecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F'
											GROUP BY OVD_OV_OrdenVentaId                                           
                                            ) AS OrdenesVentaDetalle ON OV_OrdenVentaId = OVD_OV_OrdenVentaId
left join (
                       select FTR_OV_OrdenVentaId , 
					   SUM(CXCPD_MontoAplicado * CXCP_MONP_Paridad) as cantidadPagoFactura from CXCPagos
						inner join   CXCPagosDetalle on CXCP_CXCPagoId  = CXCPD_CXCP_CXCPagoId
						inner join Facturas on CXCPD_FTR_FacturaId = FTR_FacturaId 						
                       WHERE CXCP_Eliminado = 0 and CXCP_CMM_FormaPagoId <> 'F86EC67D-79BD-4E1A-A48C-08830D72DA6F'
                   group by FTR_OV_OrdenVentaId   
                    ) AS Pagos ON Pagos.FTR_OV_OrdenVentaId = OV_OrdenVentaId
					 WHERE  
   OV_CodigoOV = ? 
     GROUP BY
					OV_OrdenVentaId,
        OV_CodigoOV,       
       
	 	cantidadPagoFactura,
		 SUBTOTAL, DESCUENTO, IVA";
    $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
    $consulta = DB::select($sel, [$id_ov]);
    
    $cantOV_xCobrar = array_sum(array_pluck($consulta, 'X_COBRAR')); 
    if (is_null($cantOV_xCobrar)) {
            $cantOV_xCobrar = 0;
    }

        $sel = "SELECT PCXC_Cantidad_provision, PCXC_ID AS llave, CONVERT(VARCHAR(max),PCXC_ID)+' - $'+ CONVERT(VARCHAR(max),CONVERT(MONEY,PCXC_Cantidad_provision),1) +' - ' + PCXC_Concepto AS valor FROM RPT_ProvisionCXC WHERE PCXC_Activo = 1 AND PCXC_OV_Id = ? AND PCXC_Eliminado = 0";
        $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
        $consulta = DB::select($sel, [$id_ov]);

        $suma_provisionado = array_sum(array_pluck($consulta, 'PCXC_Cantidad_provision'));
        
        if (is_null($suma_provisionado)) {
            $suma_provisionado = 0;
        }
    
    return $cantOV_xCobrar - $suma_provisionado;
}

}
