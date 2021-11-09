<?php
namespace App\Http\Controllers;

use App;
use App\LOG;
use App\RPT_models\RPT_PROV;
use App\RPT_models\RPT_PAGO;
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
class Mod_RPT_SACController extends Controller
{
    
    public function data_cxc_proyeccion(){
        //SP SQL obtiene la proyeccion de CXC a 8 semanas
        $consulta = DB::select('exec RPT_SP_CXC_PROYECCION'); 
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
            //obtenemos las primeras 10 columnas, esas no cambian
            $columns_init = array_slice($cols, 0, 10);
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
    public function index_proyeccion()
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

            return view('Finanzas.ProyeccionCXC', compact('cbousuarios', 'estado', 'estado_save', 'cliente', 'comprador', 'actividades', 'ultimo', 'provdescripciones', 'provalertas', 'cbonumpago'));
        } else {
            return redirect()->route('auth/login');
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
                        $valore = floatval($PA->cantidadpagofactura * 1);
                        $identificadorPagoExiste = count(explode(':', $PA->CXCP_IdentificacionPago));
                        $identificadorPago = ($identificadorPagoExiste >= 2)? trim(explode(':', $PA->CXCP_IdentificacionPago)[1]) : trim(explode(':', $PA->CXCP_IdentificacionPago)[0]);
                        if (floatval($PR->PCXC_Cantidad_provision * 1) >= ($valore)) {
                            Self::StorePago($PA);
                            $nuevaCantidadProv = floatval($PR->PCXC_Cantidad_provision) - floatval($valore);
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
                        else if ($valore > floatval($PR->PCXC_Cantidad_provision)) {
                            $cantidadPagada = $valore;
                            $provisionesOV = RPT_PROV::activeOV($PA->ov_codigoov);
                            //dd('provisionesOV'. $provisionesOV);
                            foreach ($provisionesOV as $key => $provId) {
                                $prov = RPT_PROV::find($provId);
                                if ($cantidadPagada > 0) {                                              
                                    $nuevaCant = number_format( floatval($prov->PCXC_Cantidad_provision) - $cantidadPagada, 2);
                                    $nuevaCant = str_replace(',', '', $nuevaCant);
                                    if ($nuevaCant <= 0) {
                                        $cantidadPagada = floatval($nuevaCant * -1);
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
        OV_MON_MonedaId AS MON_ID, 
        OV_CodigoOV AS CODIGO_OV,        
        MON_Nombre AS MONEDA,
		COALESCE (OV_MONP_Paridad, 1) as PARIDAD,
        PRY_CodigoEvento + ' - ' + PRY_NombreProyecto AS PROYECTO,
		CCON_Nombre as COMPRADOR
		FROM OrdenesVenta                                
    INNER JOIN Clientes ON OV_CLI_ClienteId = CLI_ClienteId
    LEFT  JOIN Proyectos ON OV_PRO_ProyectoId = PRY_ProyectoId AND PRY_Activo = 1 AND PRY_Borrado = 0
	LEFT JOIN ClientesContactos ON OV_CCON_ContactoId = CCON_ContactoId AND CCON_Eliminado = 0
    LEFT JOIN  Monedas ON OV_MON_MonedaId = Monedas.MON_MonedaId        
    where CONVERT(varchar(MAX), OV_OrdenVentaId) = ?",[$Id_OV]);
    //dd($info);
        if (Auth::check()) {
            $sql = "exec SP_RPT_KARDEX_OV ?, ?, ?";
//dd($sql);
            $ovs = DB::select($sql, [$Id_OV, $info[0]->PARIDAD, $info[0]->MON_ID]);
            $sumOV = array_sum(array_pluck($ovs, 'IMP_OV'));
            $sumFAC = array_sum(array_pluck($ovs, 'IMP_FAC'));
            $sumEMB = array_sum(array_pluck($ovs, 'IMP_EMB'));
            $sumPAG = array_sum(array_pluck($ovs, 'IMP_PAG'));
           // $ovs = collect($ovs);
            $pdf = \PDF::loadView('Finanzas.kardexOV_PDF', 
            compact('ovs', 'info', 'sumOV', 'sumFAC', 'sumEMB', 'sumPAG'));
           // $pdf = \PDF::loadView('welcome', compact('data'));
            $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]); 
            return $pdf->stream(date("Y/m/d") . ' Kardex '.$info[0]->CODIGO_OV. '.Pdf');
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
        $provdescripciones = \DB::select("SELECT 
                          CMM_ControlId
                        , CMM_Valor
                    FROM ControlesMaestrosMultiples
                    WHERE CMM_Control = 'CMM_ProvisionDescripcionesCXC' AND CMM_Eliminado = 0
                    ORDER BY CMM_Valor");

        $provalertas = \DB::select("SELECT 
                          CMM_ControlId
                        , CMM_Valor
                    FROM ControlesMaestrosMultiples
                    WHERE CMM_Control = 'CMM_ProvisionAlertasCXC' AND CMM_Eliminado = 0
                    ORDER BY CMM_Valor");
        $cbousuarios = \DB::select("SELECT nomina AS llave , name AS valor FROM RPT_Usuarios 
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
					    Select
                        
                        OVD_OV_OrdenVentaId AS OVD_id,	
                        SUM((BULD_Cantidad * OVD_PrecioUnitario) - 
                        ( BULD_Cantidad * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) ) + 
                        ( ((BULD_Cantidad * OVD_PrecioUnitario) - (BULD_Cantidad * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) * 
                        ISNULL(OVD_CMIVA_Porcentaje, 0.0) )) AS EMB_TOTAL
                        from EmbarquesBultosDetalle
                        Inner Join PreembarqueBultoDetalle on EMBBD_PREBD_PreembarqueBultoDetalleId = PREBD_PreembarqueBultoDetalleId and PREBD_Eliminado = 0
                        Inner Join BultosDetalle on PREBD_BULD_BultoDetalleId = BULD_BultoDetalleId and BULD_Eliminado = 0
                        Inner Join OrdenesTrabajoReferencia on BULD_OT_OrdenTrabajoId = OTRE_OT_OrdenTrabajoId
                        Inner Join OrdenesVentaDetalle on OVD_OV_OrdenVentaId = OTRE_OV_OrdenVentaId and OVD_ART_ArticuloId = BULD_ART_ArticuloId
                        
                        Inner Join EmbarquesBultos on EMBB_EmbarqueBultoId = EMBBD_EMBB_EmbarqueBultoId
                        Where EMBBD_Eliminado = 0 
                       
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
        //dd($sel);
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
                        Select
                        
                        OVD_OV_OrdenVentaId AS OVD_id,	
                        SUM((BULD_Cantidad * OVD_PrecioUnitario) - 
                        ( BULD_Cantidad * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0) ) + 
                        ( ((BULD_Cantidad * OVD_PrecioUnitario) - (BULD_Cantidad * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) * 
                        ISNULL(OVD_CMIVA_Porcentaje, 0.0) )) AS EMB_TOTAL
                        from EmbarquesBultosDetalle
                        Inner Join PreembarqueBultoDetalle on EMBBD_PREBD_PreembarqueBultoDetalleId = PREBD_PreembarqueBultoDetalleId and PREBD_Eliminado = 0
                        Inner Join BultosDetalle on PREBD_BULD_BultoDetalleId = BULD_BultoDetalleId and BULD_Eliminado = 0
                        Inner Join OrdenesTrabajoReferencia on BULD_OT_OrdenTrabajoId = OTRE_OT_OrdenTrabajoId
                        Inner Join OrdenesVentaDetalle on OVD_OV_OrdenVentaId = OTRE_OV_OrdenVentaId and OVD_ART_ArticuloId = BULD_ART_ArticuloId
                        
                        Inner Join EmbarquesBultos on EMBB_EmbarqueBultoId = EMBBD_EMBB_EmbarqueBultoId
                        Where EMBBD_Eliminado = 0 
                        
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
