<?php

namespace App\RPT_models;

use Illuminate\Database\Eloquent\Model;
use DB;
class RPT_PAGO extends Model
{
    protected $table = 'RPT_PagosConsideradosCXC';
    protected $primaryKey = 'PAGOC_CXCPagoId';
    public $timestamps = false;
    public $consulta = "SELECT ov_codigoov, Pagos.cxcp_fechapago, Pagos.cantidadpagofactura, cxcp_cxcpagoid, CXCP_IdentificacionPago FROM   ordenesventa LEFT JOIN (SELECT ovd_detalleid, ovd_ov_ordenventaid, ovd_cantidadrequerida * ovd_preciounitario AS SUBTOTAL, ovd_cantidadrequerida * ovd_preciounitario * Isnull(ovd_porcentajedescuento, 0.0) AS DESCUENTO, ( ( ovd_cantidadrequerida * ovd_preciounitario ) - ( ovd_cantidadrequerida * ovd_preciounitario * Isnull( ovd_porcentajedescuento, 0.0) ) ) * Isnull(ovd_cmiva_porcentaje, 0.0) AS IVA, ovd_cantidadrequerida FROM   ordenesventadetalle LEFT JOIN articulosespecificaciones ON ovd_art_articuloid = aet_art_articuloid AND aet_cmm_articuloespecificaciones = 'DF85FC23-720F-4E99-A794-FCE3F8D3B66F') AS OrdenesVentaDetalle ON ov_ordenventaid = ovd_ov_ordenventaid LEFT JOIN (SELECT ftr_facturaid, ftr_mon_monedaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida * ftrd_preciounitario AS FTR_SUBTOTAL, ftrd_cantidadrequerida * ftrd_preciounitario * Isnull(ftrd_porcentajedescuento, 0.0) AS FTR_DESCUENTO, ( ( ftrd_cantidadrequerida * ftrd_preciounitario ) - ( ftrd_cantidadrequerida * ftrd_preciounitario * Isnull( ftrd_porcentajedescuento, 0.0) ) ) * Isnull(ftrd_cmiva_porcentaje, 0.0) AS FTR_IVA, ftrd_referenciaid, ftrd_cantidadrequerida FROM   facturas INNER JOIN facturasdetalle fd ON fd.ftrd_ftr_facturaid = facturas.ftr_facturaid WHERE  ftr_eliminado = 0 GROUP  BY ftr_mon_monedaid, ftr_facturaid, ftrd_referenciaid, ftr_ov_ordenventaid, ftrd_cantidadrequerida, ftrd_preciounitario, ftrd_porcentajedescuento, ftrd_cmiva_porcentaje) AS Facturas ON ovd_detalleid = ftrd_referenciaid LEFT JOIN (SELECT cxcpd_ftr_facturaid, Round(Isnull(Sum(Abs(cxcpd_montoaplicado)), 0.0), 2) AS cantidadPagoFactura, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago  FROM   cxcpagos INNER JOIN cxcpagosdetalle ON cxcp_cxcpagoid = cxcpd_cxcp_cxcpagoid WHERE  cxcp_eliminado = 0 AND cxcpd_ftr_facturaid IS NOT NULL GROUP  BY cxcpd_ftr_facturaid, cxcp_mon_monedaid, cxcp_cli_clienteid, cxcp_fechapago, cxcp_cxcpagoid, CXCP_IdentificacionPago) AS Pagos ON ftr_facturaid = cxcpd_ftr_facturaid AND ftr_mon_monedaid = cxcp_mon_monedaid LEFT JOIN (SELECT * FROM   rpt_pagosconsideradoscxc WHERE  pagoc_eliminado = 0) AS PAGOC ON PAGOC.pagoc_cxcpagoid = cxcp_cxcpagoid 
                    WHERE  OV_CMM_EstadoOVId = '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' 
                    AND pagoc_cxcpagoid IS NULL 
                    AND cantidadpagofactura IS NOT NULL ";
    public $criterio = " AND ov_codigoov = ? ";
    public $consulta2 ="GROUP BY
                    ov_codigoov,
                    cxcp_fechapago, 
                    cantidadpagofactura, 
                    cxcp_cxcpagoid, 
                    CXCP_IdentificacionPago 
                    ORDER BY cxcp_fechapago ASC";              
                    
    public function scopeActiveCount($query, $ov)
    {
        $sql = $this->consulta. $this->criterio. $this->consulta2;
        return count(DB::select($sql, [$ov]));
    }
    public function scopeActive($query, $ov)
    {
        $param = [];
        $sql = $this->consulta;
        if ($ov !== 'all') {
            $param = [$ov];
            $sql = $sql . $this->criterio;
        }
        $sql = $sql . $this->consulta2;
       
        return DB::select($sql, $param);
    }
    public function scopePrimero($query, $ov)
    {
        $sql = $this->consulta . $this->criterio . $this->consulta2;  
       // return $sql;     
        $uno = DB::select($sql, [$ov]);
        if (count($uno) > 0) {
            return $uno[0];            
        }else {
            return null;
        }
    }
   
}
