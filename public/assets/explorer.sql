SELECT (Select Cast(OV_FechaOV as Date) from OrdenesVenta Where OV_OrdenVentaId = '". $Id_OV ."') as FECHA,
        'VENTAS' as IDENTIF,
       (Select OV_CodigoOV from OrdenesVenta Where OV_OrdenVentaId =  '". $Id_OV ."' ) as DOCUMENT, 
       (Select OV_ReferenciaOC from OrdenesVenta Where OV_OrdenVentaId = '". $Id_OV ."') as REFERENCIA,       
       SUM(OVD_CantidadRequerida * OVD_PrecioUnitario - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0)) +
       ((OVD_CantidadRequerida * OVD_PrecioUnitario) - (OVD_CantidadRequerida * OVD_PrecioUnitario * ISNULL(OVD_PorcentajeDescuento, 0.0))) *
       ISNULL(OVD_CMIVA_Porcentaje, 0.0)) as IMP_OV,
         0 as IMP_FAC,
         0 as IMP_EMB,
         0 as IMP_PAG
From OrdenesVentaDetalle
Where OVD_OV_OrdenVentaId = '". $Id_OV ."'
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
Where FTR_Eliminado = 0 and FTR_OV_OrdenVentaId = '". $Id_OV ."' 
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
Inner Join Facturas on FTR_FacturaId = CXCPD_FTR_FacturaId and FTR_OV_OrdenVentaId = '". $Id_OV ."'
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
inner join Facturas on NC_FTR_FacturaId = FTR_FacturaId and FTR_OV_OrdenVentaId = '". $Id_OV ."'
Where CXCP_Eliminado = 0
Order by FECHA, IDENTIF DESC