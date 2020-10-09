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
class Mod_RPTFinanzasController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $estado = [];
            $cliente = [];
            $comprador = [];

            return view('Finanzas.ProvisionCXC', compact('estado', 'cliente', 'comprador', 'actividades', 'ultimo'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function combobox(Request $request){  
            if (!is_null($request->input('solocompradores'))) {
                $comboclientes = "'".$request->input('solocompradores'). "'";
                $compradores = DB::select("SELECT CCON_ContactoId as llave, CCON_Nombre + ' - ' + CCON_Puesto AS valor
                FROM ClientesContactos
                INNER JOIN OrdenesVenta ON OV_CCON_ContactoId = CCON_ContactoId 
                LEFT JOIN  CLientes ON OV_CLI_ClienteId = CLI_ClienteId
                WHERE CCON_Eliminado = 0 AND  OV_Eliminado = ".$request->input('estado')."
                AND CLI_CodigoCliente in (".$comboclientes.")
                GROUP BY CCON_ContactoId, CCON_Nombre, CCON_Puesto
                ORDER BY CCON_Nombre");
                $clientes = '';
            } else {
                $clientes = DB::select("SELECT CLI_CodigoCliente as llave, CLI_CodigoCliente +' - '+CLI_RazonSocial AS valor
                FROM Clientes
                LEFT JOIN OrdenesVenta ON OV_CLI_ClienteId = CLI_ClienteId 
                WHERE CLI_Activo = 1 AND CLI_Eliminado = 0 AND  OV_Eliminado = ".$request->input('estado')."
                GROUP BY CLI_CodigoCliente, CLI_CodigoCliente, CLI_RazonSocial
                ORDER BY CLI_RazonSocial");
                $compradores = DB::select("SELECT CCON_ContactoId as llave, CCON_Nombre + ' - ' + CCON_Puesto AS valor
                FROM ClientesContactos
                INNER JOIN OrdenesVenta ON OV_CCON_ContactoId = CCON_ContactoId 
                WHERE CCON_Eliminado = 0 AND  OV_Eliminado = ".$request->input('estado')."
                GROUP BY CCON_ContactoId, CCON_Nombre, CCON_Puesto
                ORDER BY CCON_Nombre");
            }
        return compact('clientes', 'compradores');
    }
    public function registros(Request $request){
        try{
            ini_set('memory_limit', '-1');
            set_time_limit(0);

            $criterio = '';
            $clientes = "'".$request->input('clientes'). "'";
            $clientes = str_replace("'',", "", $clientes);
            $compradores = "'".$request->input('compradores'). "'";
            $compradores = str_replace("'',", "", $compradores);
            $estado = $request->input('estado');
            if (strlen($clientes) > 3 && $clientes != '') {
                $criterio = " AND (CLI_CodigoCliente in(".$clientes.") OR CLI_CodigoCliente is null) ";
            }
            if (strlen($compradores) > 3 && $compradores != '') {
                $criterio = $criterio." AND ( CCON_ContactoId in(".$compradores.") ) ";
            }
            $criterio = $criterio." AND OV_Eliminado =".$estado." ";
          //  $polizas_decimales = $dao->getEjecutaConsulta
          //  ("SELECT CMA_Valor FROM ControlesMaestros 
           // WHERE CMA_Control = 'CMA_CCNF_DecimalesPolizas'")[0]->CMA_Valor;
            $sel = "SELECT
        OV_OrdenVentaId  AS DT_ID,
        OV_CodigoOV AS CODIGO,
        CASE WHEN OV_Eliminado = 0 THEN 'Activo' ELSE 'Cancelado' END ESTATUS_OV,
        CLI_CodigoCliente + ' - ' + CLI_RazonSocial AS CLIENTE,                                  
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR,
        CONVERT(varchar, OV_FechaOV,103) AS FECHA_OV,
        OV_ReferenciaOC,      
        (SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) AS TOTAL       		 
		,(ISNULL(SUM(ROUND(FTR_SUBTOTAL,2)), 0.0) - ISNULL(SUM(ROUND(FTR_DESCUENTO, 2)), 0.0) ) + ISNULL(SUM(ROUND(FTR_IVA, 2)), 0.0) AS FTR_TOTAL
		,((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2))) - ((ISNULL(SUM(ROUND(FTR_SUBTOTAL,2)), 0.0) - ISNULL(SUM(ROUND(FTR_DESCUENTO, 2)), 0.0) ) + ISNULL(SUM(ROUND(FTR_IVA, 2)), 0.0)) AS IMPORTE_XFACTURAR
		,SUM(OrdenesVentaDetalle.OVD_CantidadRequerida) - ISNULL(SUM(FTRD_CantidadRequerida), 0.0) AS CANTIDAD_PENDIENTE	
		,COALESCE(SUM(NotaCredito.TotalNC), 0) TotalNC,
        ((ISNULL(SUM(ROUND(FTR_SUBTOTAL,2)), 0.0) - ISNULL(SUM(ROUND(FTR_DESCUENTO, 2)), 0.0) ) + ISNULL(SUM(ROUND(FTR_IVA, 2)), 0.0)) - COALESCE(SUM(NotaCredito.TotalNC), 0) AS IMPORTE_FACTURADO,							
		COALESCE(SUM(Pagos.cantidadPagoFactura), 0) PAGOS_FACTURAS,
        (SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE(SUM(Pagos.cantidadPagoFactura), 0) AS X_PAGAR,
        CASE 
            WHEN (SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE(SUM(Pagos.cantidadPagoFactura), 0) > 0 AND COALESCE(SUM(RPT_ProvisionCXC.PCXC_Cantidad_provision), 0) = 0 THEN 'NO PROVISIONADO'
            WHEN ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2)) - COALESCE(SUM(Pagos.cantidadPagoFactura), 0)) > COALESCE(SUM(RPT_ProvisionCXC.PCXC_Cantidad_provision), 0) THEN 'FALTA PROVISIONAR'
            ELSE 'PROVISIONADO' END AS PROVISION,
		COALESCE(SUM(Embarque.EMB_TOTAL), 0 ) AS EMBARCADO,
        ((SUM(ROUND(SUBTOTAL,2)) - SUM(ROUND(DESCUENTO, 2))) + SUM(ROUND(IVA, 2))) - COALESCE(SUM(Embarque.EMB_TOTAL), 0 ) AS IMPORTE_XEMBARCAR
    FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	INNER JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0
											LEFT JOIN (SELECT
											OVD_DetalleId,
                                                OVD_OV_OrdenVentaId,
                                                OVD_CantidadRequerida * OVD_PrecioUnitario AS SUBTOTAL,
                                                OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) AS DESCUENTO,
                                                ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0) AS IVA
                                               ,OVD_CantidadRequerida 
											FROM OrdenesVentaDetalle
                                            LEFT  JOIN ArticulosEspecificaciones ON OVD_ART_ArticuloId = AET_ART_ArticuloId AND AET_CMM_ArticuloEspecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F'                                           
                                            ) AS OrdenesVentaDetalle ON OV_OrdenVentaId = OVD_OV_OrdenVentaId
											LEFT JOIN (
                                                      	SELECT
														FTR_FacturaId,
														FTR_MON_MonedaId,
														FTR_OV_OrdenVentaId,
														FTRD_CantidadRequerida * FTRD_PrecioUnitario AS FTR_SUBTOTAL,
														FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0) AS FTR_DESCUENTO,
														((FTRD_CantidadRequerida * FTRD_PrecioUnitario) - (FTRD_CantidadRequerida * FTRD_PrecioUnitario * ISNULL(FTRD_PorcentajeDescuento, 0.0))) *
														ISNULL(FTRD_CMIVA_Porcentaje, 0.0) AS FTR_IVA,
														FTRD_ReferenciaId
														, FTRD_CantidadRequerida
														FROM Facturas
														inner join FacturasDetalle fd on fd.FTRD_FTR_FacturaId = Facturas.FTR_FacturaId													
														WHERE FTR_Eliminado = 0 
														GROUP BY FTR_MON_MonedaId, FTR_FacturaId, FTRD_ReferenciaId
														,FTR_OV_OrdenVentaId
														, FTRD_CantidadRequerida 
														,FTRD_PrecioUnitario
														,FTRD_PorcentajeDescuento
														,FTRD_CMIVA_Porcentaje
                                                        ) AS Facturas ON OVD_DetalleId = FTRD_ReferenciaId
					LEFT  JOIN (
                       SELECT
                              NC_FTR_FacturaId ,
                               (SUM(ISNULL( NCD_Cantidad*NCD_PrecioUnitario ,  0.0))  + (SUM(ISNULL( NCD_Cantidad*NCD_PrecioUnitario ,  0.0)) * (ISNULL( NCD_CMIVA_Porcentaje ,  0.0) ))) AS TotalNC,
                              NC_MON_MonedaId
                       FROM NotasCredito
                       INNER JOIN NotasCreditoDetalle ON NC_NotaCreditoId = NCD_NC_NotaCreditoId
                       INNER JOIN Clientes ON NC_CLI_ClienteId = CLI_ClienteId
                       INNER JOIN Monedas ON NC_MON_MonedaId = MON_MonedaId
                       WHERE NC_FTR_FacturaId IS NOT NULL AND
                             NC_Eliminado = 0
                       GROUP BY
					   NCD_CMIVA_Porcentaje,
                                NC_MON_MonedaId ,
                                NC_FTR_FacturaId
                    ) AS NotaCredito ON Facturas.FTR_FacturaId = NC_FTR_FacturaId AND FTR_MON_MonedaId = NC_MON_MonedaId				
					LEFT JOIN (
					SELECT (OVD_CantidadRequerida * OVD_PrecioUnitario) - ( OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) )
							+ ( ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
                                                ISNULL(OVD_CMIVA_Porcentaje, 0.0) )
												AS EMB_TOTAL
							,EMBD_OVD_DetalleId							
                FROM OrdenesVentaDetalle
                INNER JOIN EmbarquesDetalle ON OVD_DetalleId = EMBD_OVD_DetalleId
                INNER JOIN Embarques ON EMB_EmbarqueId = EMBD_EMB_EmbarqueId 
				WHERE EMBD_OVD_DetalleId IS NOT NULL
					) AS Embarque ON EMBD_OVD_DetalleId = OVD_DetalleId
				LEFT  JOIN (
                       SELECT
                              CXCPD_FTR_FacturaId ,
                               ROUND( ISNULL( SUM( ABS(CXCPD_MontoAplicado )), 0.0), 2) AS cantidadPagoFactura,
                              CXCP_MON_MonedaId ,
                              CXCP_CLI_ClienteId
                       FROM CXCPagos
                       INNER JOIN CXCPagosDetalle ON CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId
                       WHERE CXCP_Eliminado = 0
                       GROUP BY
                                CXCPD_FTR_FacturaId ,
                                CXCP_MON_MonedaId ,
                                CXCP_CLI_ClienteId
                    ) AS Pagos ON FTR_FacturaId = CXCPD_FTR_FacturaId AND
                                  FTR_MON_MonedaId = CXCP_MON_MonedaId 
            LEFT JOIN RPT_ProvisionCXC on PCXC_OV_Id = ov_ordenventaid AND PCXC_Activo = 1
    WHERE OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' 
    ".$criterio. "
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
        OV_Eliminado,
		CCON_Nombre
    ORDER BY
        OV_CodigoOV";    
        $sel =  preg_replace('/[ ]{2,}|[\t]|[\n]|[\r]/', ' ', ($sel));
            $consulta = DB::select($sel);

            //$resultSet = $dao->getArrayAsociativo($consulta);

           // $registros = count($resultSet);
          //  for($i= 0; $i < $registros; $i++){
          //      $resultSet[$i]['TOTAL'] = '$' . number_format($resultSet[$i]['TOTAL'], $polizas_decimales, '.', ',');
          //  }

          
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
       /*
        if(\Storage::disk('balanzas')->has(Input::get('date').'.xls')){
             \Storage::disk('balanzas')->delete(Input::get('date').'.xls');
        }
         \Storage::disk('balanzas')->put(Input::get('date').'.xls',\File::get($request->file('archivo')));       
         */
        config(['excel.import.startRow' => $filaInicio ]);
        config(['excel.import.heading' => false ]);
         if($request->hasFile('archivo')){
             $path = $request->file('archivo')->getRealPath();
             
             //$path = public_path('balanzas/').Input::get('date').'.xls';
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
                     $fila = [   //hay 12 movimientos en la tabla correspondientes a los 12 periodos                      
                        'BC_Movimiento_'.$periodo => null                 
                        ];
                       if ($periodo == '01') {                                                            
                            $fila['BC_Saldo_Inicial'] = null;
                        }    
                    DB::table('RPT_BalanzaComprobacion')
                        ->where("BC_Ejercicio", $ejercicio)
                        ->update($fila);                    

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
                            $getCtas = array_map('trim', $getCtas);
                            $v = trim($value[0]);       
                            $conta = array_where($getCtas, function ($key, $value) use ($v){                                
                                return trim($value) == $v;
                            });
                            $buscaCta = (count($conta) > 0)?true:false;                           
                        }
                        //la info de excel se limita a 2 decimales para evitar errores en operaciones
                        $val2 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[2]) ,'2')));
                        $val3 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[3]) ,'2')));
                        $val4 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[4]) ,'2')));
                        $val5 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[5]) ,'2')));
                        $val6 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[6]) ,'2')));
                        $val7 = floatval(preg_replace("/[^-0-9\.]/","",number_format(floatval($value[7]) ,'2')));
                        
                        $saldoIni = $val2 - $val3; //deudor - acreedor                        
                        $saldoFin = $val6 - $val7; // saldo final del periodo segun la balanzaCom:
                        $cargosAbonos = $val4 - $val5; //+cargos -abonos                           
                        $movIni = ($saldoIni * 1) + ($cargosAbonos * 1);                   
                        $movText = $val4.'-'.$val5.'='.$cargosAbonos;
                        
                        if(false){//if ($saldoFin != $movIni) {                             
                          // $cargosAbonos = ($value[4] * -1) + ($value[5] * 1);// -cargos +abonos
                          // $movText = ($value[4] * -1).'+'.($value[5] * 1).'='.$cargosAbonos;
                        }                          
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
                            $exist = DB::table('RPT_BalanzaComprobacion')
                                ->where('BC_Cuenta_Id', $value[0])->count();
                            if ($exist == 0) {
                                DB::table('RPT_BalanzaComprobacion')->insert($fila);
                                $exist = 1;
                            }                                                                         
                        }
                        if ($exist > 0) {//si existe la cuenta se actuliza
                             DB::table('RPT_BalanzaComprobacion')
                                ->where('BC_Cuenta_Id', $value[0])
                                ->where('BC_Ejercicio', $ejercicio)
                                ->update($fila);
                        }    
                        $cont++;

                        if (false){//if ($saldoIni == 0 && $periodo <> '01') { //todos los periodos menos el primero
                           $cta = DB::table('RPT_BalanzaComprobacion')
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

public function checkctas(Request $request){
    $periodo = explode('-', Input::get('date'));
    $ejercicio = $periodo[0];
    $periodo = $periodo[1];
     $buscaejercicio = DB::table('RPT_BalanzaComprobacion')
     ->where("BC_Ejercicio", $ejercicio)
     ->whereNotNull('BC_Movimiento_'.$periodo)     
     ->count();                
                 $respuesta = false;
     if ($buscaejercicio > 0) {
                    $respuesta = true;
                }
                return compact('respuesta');

}
}
