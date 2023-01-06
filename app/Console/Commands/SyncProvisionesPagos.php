<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
//use App\Http\Controllers\Mod_RPT_SACController;
use Illuminate\Support\Facades\Log;
use App\RPT_models\RPT_PROV;
use App\RPT_models\RPT_PAGO;
use DB;

class SyncProvisionesPagos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:SyncProvisionesPagos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SyncProvisionesPagos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $objeto = new Mod_RPT_SACController();
        // $myVariable = $objeto->recibir_pagos();

        Log::info("Inicio recibiendo pagos...");
        $pagos_no_considerados = RPT_PAGO::active('all');
            //dd($pagos_no_considerados);
            $OVs_conPagos = array_unique(array_pluck($pagos_no_considerados, 'OV_CodigoOV'));           
            //clock('OVS pagos no considerados');
            //clock($OVs_conPagos);
            
            foreach ($OVs_conPagos as  $ov) {
                
                //vamos a ver cuantas provisiones activas hay, es decir que no esten cubiertas con pago
                $countProv = DB::table('RPT_ProvisionCXC')->where('PCXC_OV_Id', $ov)->where('PCXC_Activo', 1)
                ->where('PCXC_Eliminado', 0)->count();
                //vamos a ver cuantos pagos hay en muliix y que no hemos considerado
                $PAS = RPT_PAGO::active($ov);
                $countPago = count($PAS);

                //clock($ov);
               // dd(['countProv/countPagos',$countProv, $countPago]);
                
                if ($countPago > 0 && $countProv == 0) {
                    //Guardando pagos, no hay provisiones
                    clock('Guardando pagos, No hay provisiones'.$ov);
                    foreach ($PAS as $PA) {
                        
                        Self::StorePago($PA);
                    }
                }else {
                while ($countPago > 0 && $countProv > 0) {                      
                    
                    $PR = RPT_PROV::primero($ov);
                    clock('----->>>primerProv');    
                    //dd($PR);    
                    $PA = DB::select("select OV_CodigoOV,
                        FTR_OV_OrdenVentaId,
                        cxcp_fechapago,
                        cxcp_cxcpagoid,
                        CXCP_IdentificacionPago,
                        cantidadPagoFactura from (
                        Select 
                        OV_CodigoOV,
                        FTR_OV_OrdenVentaId,
                        cxcp_fechapago,
                        cxcp_cxcpagoid,
                        CXCP_IdentificacionPago ,
                        COALESCE(cxcpd_montoaplicado, 0.0) AS cantidadPagoFactura
                        From CXCPagos   
                        left Join CXCPagosDetalle on CXCP_CXCPagoId = CXCPD_CXCP_CXCPagoId   
                        left Join Facturas on FTR_FacturaId = CXCPD_FTR_FacturaId 
                        left join OrdenesVenta on FTR_OV_OrdenVentaId = OV_OrdenVentaId
                        Where 
                        CXCP_Eliminado = 0 
                        and CXCP_CMM_FormaPagoId <> 'F86EC67D-79BD-4E1A-A48C-08830D72DA6F'
                        AND OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5'

                        --AND pagoc_cxcpagoid IS NULL
                        AND ov_codigoov = ?
                        GROUP BY
                        OV_CodigoOV,
                        FTR_OV_OrdenVentaId,
                        cxcp_fechapago,
                        cxcp_cxcpagoid,
                        CXCP_IdentificacionPago,
                        CXCPD_MontoAplicado
                        ) t 
                        LEFT JOIN (SELECT PAGOC_CXCPagoId, PAGOC_OV_CodigoOV FROM   rpt_pagosconsideradoscxc WHERE  pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid
                        AND PAGOC_OV_CodigoOV = OV_CodigoOV
                        Where 
                        pagoc_cxcpagoid IS NULL
                        order by OV_CodigoOV, CXCP_FechaPago", [$ov]);
                   if (count($PA) > 0) {
                       
                    $PA = $PA[0];
                    clock('procesando pago...');
                    //dd($PA, $PR);
                    $cantidadPagoFactura = $PA->cantidadPagoFactura * 1;
                    clock('PAGO: '.$cantidadPagoFactura);
                    
                    $valore = floatval($cantidadPagoFactura);
                    //dd(($PR->PCXC_Cantidad_provision * 1) >= ($valore));
                        $identificadorPagoExiste = count(explode(':', $PA->CXCP_IdentificacionPago));
                        $identificadorPago = ($identificadorPagoExiste >= 2)? trim(explode(':', $PA->CXCP_IdentificacionPago)[1]) : trim(explode(':', $PA->CXCP_IdentificacionPago)[0]);
                        if (floatval($PR->PCXC_Cantidad_provision * 1) >= ($valore)) {
                            clock('PROvision es mayor al pago..');
                            Self::StorePago($PA);
                            //dd('guardamos pago..');
                            
                            $nuevaCantidadProv = floatval($PR->PCXC_Cantidad_provision) - floatval($valore);
                            clock('nuevaCantidadProv..'.$nuevaCantidadProv);
                            $PR->PCXC_Cantidad_provision = $nuevaCantidadProv;
                            if (is_null($PR->PCXC_pagos) || strlen($PR->PCXC_pagos) == 0) {
                                $PR->PCXC_pagos = $identificadorPago;
                            }else {                                
                                $PR->PCXC_pagos = $PR->PCXC_pagos . ',' . $identificadorPago;
                            }
                            
                                                                           
                            if ($nuevaCantidadProv == 0) {
                                //pago cubre toda la provision
                                clock('pago cubre toda la provision');
                                $PR->PCXC_Activo ='0'; //desactivar provision  
                                Self::RemoveAlertProvision($PR->PCXC_ID);                         
                            }
                            $PR->save();
                            //dd($PA, $PR);
                        }
                        else if ($valore > floatval($PR->PCXC_Cantidad_provision)) {
                            clock('PAGO es mayor a la provision..');
                            $cantidadPagada = $valore;
                            $provisionesOV = RPT_PROV::activeOV($PA->OV_CodigoOV);
                            clock('provisionesOV', $provisionesOV);
                            foreach ($provisionesOV as $key => $provId) {
                                $prov = RPT_PROV::find($provId);
                                clock('------>>>procesando prov', $prov);
                                if ($cantidadPagada > 0) {                                              
                                    $nuevaCant = number_format( floatval($prov->PCXC_Cantidad_provision) - $cantidadPagada, 2);
                                    $nuevaCant = str_replace(',', '', $nuevaCant);
                                    clock('nuevaCant(PROV - PAGO): ' .$nuevaCant);

                                    if ($nuevaCant <= 0) {
                                        $cantidadPagada = floatval($nuevaCant * -1);
                                        clock('saldo de Pago: ' . $cantidadPagada); //esto queda de tu pago
                                        $prov->PCXC_Cantidad_provision = 0;
                                        if (is_null($prov->PCXC_pagos) || strlen($prov->PCXC_pagos) == 0) {
                                            
                                            $prov->PCXC_pagos = $identificadorPago;
                                        } else {
                                            $prov->PCXC_pagos = $prov->PCXC_pagos . ',' . $identificadorPago;
                                        }                                   
                                        $prov->PCXC_Activo = '0'; //desactivar provision                              
                                        $prov->save();
                                        Self::RemoveAlertProvision($provId);
                                      
                                    } else if ($nuevaCant > 0) {
                                        $cantidadPagada = 0;
                                        $prov->PCXC_Cantidad_provision = $nuevaCant;
                                        if (is_null($prov->PCXC_pagos)|| strlen($prov->PCXC_pagos) == 0) {
                                            $prov->PCXC_pagos = $identificadorPago;
                                        } else {
                                            $prov->PCXC_pagos = $prov->PCXC_pagos . ',' . $identificadorPago;
                                        }                                                                                      
                                        clock('pago saldado: actualizamos provision ',$prov);
                                        $prov->save();
                                       // clock('prov menor a pago: CantPagada==nuevaCant', $cantidadPagada, $identificadorPago, $prov, $PA);
                                    } 
                                }
                                
                            }
                            if ($cantidadPagada > 0){
                                    //Self::StoreSaldoPago($PA);
                            }
                            //Guardando pago en CONSIDERADOS
                            Self::StorePago($PA);

                        } //end PAGO es mayor a la provision..'
                   }  
                    $countProv = DB::table('RPT_ProvisionCXC')->where('PCXC_OV_Id', $ov)->where('PCXC_Activo', 1)
                    ->where('PCXC_Eliminado', 0)->count();

                    $countPago = count(RPT_PAGO::active($ov));
                    clock(['countProv/countPagos',$countProv, $countPago]);
                }//end WHILE
            }
            } //END FOREACH
            self::ajusteProvisiones();
		Log::info("Fin recibiendo pagos...");
    }
}
