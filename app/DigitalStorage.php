<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DigitalStorage extends Model
{
   /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = 'RPT_AlmacenDigitalIndice';

   /**
    * The storage format of the model's date columns.
    *
    * @var string
    */
   protected $dateFormat = 'U';


   /**
    * GET THE WHOLE LIST BASED ON THE DATE;
    * @param App\Http\Request $request
    * @param bool $ignoreRequest
    * @return Object
    */
   public function getList(Request $request, $useRequest = true)
   {
      $result = DB::table("RPT_AlmacenDigitalIndice");
      if ($useRequest) {
         if ($request->input("GROUP_ID") != "") {
            $result->Where('GRUPO_ID', 'like', "%" . $request->input("GROUP_ID") . "%");
         }
         if ($request->input("DOC_ID") != "") {
            $result->Where('DOC_ID', 'like', "%" . $request->input("DOC_ID") . "%");
         }
         if ($request->input("moduleType") != "") {
            $result->Where('LLAVE_ID', 'like', "" . $request->input("moduleType") . "%");
         }
      }
      $result->orderBy("GRUPO_ID");
      $result->orderBy("LLAVE_ID");
      // var_dump($result->toSql());
      // die;
      return $result->get();
   }
   public function getDigitalStorageJson()
   {
      $result = DB::table("RPT_AlmacenDigitalIndice");
      $result->orderBy("GRUPO_ID");
      $result->orderBy("LLAVE_ID");
      return $result->get();
   }

   public function getSalesList($ventas = null)
   {
      $saleList = DB::table("OrdenesVenta")
         ->select('OrdenesVenta.OV_CodigoOV', 'Clientes.CLI_CodigoCliente ', 'Clientes.CLI_RazonSocial', 'ControlesMaestrosMultiples.CMM_Valor')
         ->join("Clientes", "OrdenesVenta.OV_CLI_ClienteId", "=", "Clientes.CLI_ClienteId")
         ->join("ControlesMaestrosMultiples", "OrdenesVenta.OV_CMM_EstadoOVId", "=", "ControlesMaestrosMultiples.CMM_ControlId");
      //  $saleList->where("OrdenesVenta.OV_Eliminado", "=" , "0");

      if ($ventas) {
         $saleList->where(function ($query) use ($ventas) {
            $query->orWhere("OrdenesVenta.OV_CodigoOV", "like", "%" . $ventas . "%")
               ->orWhere('Clientes.CLI_CodigoCliente', "like", "%" . $ventas . "%")
               ->orWhere('Clientes.CLI_RazonSocial', "like", "%" . $ventas . "%")
               ->orWhere("ControlesMaestrosMultiples.CMM_Valor", "like", "%" . $ventas . "%");
         });
      }

      $saleList->orderBy('OV_CodigoOV', 'asc');
      return $saleList->get();
   }

   public function getInvoiceCollection()
   {

      $configRow = $this->getConfigRow('FAC');
      $configRowxml = $this->getConfigRow('XML');
      $rawQuery = "'SAC' + ov.OV_CodigoOV  + 'FAC' + f.FTR_NumeroFactura AS LLAVE_ID";
      // $rawQuery .= ", 'SAC' + ov.OV_CodigoOV  AS GRUPO_ID ";
      $rawQuery .= ", ov.OV_CodigoOV  AS GRUPO_ID ";
      $rawQuery .= ", 'FAC' + f.FTR_NumeroFactura AS DOC_ID ";
      $rawQuery .= ", '' + f.FTR_NumeroFactura + '-' + c.CLI_RFC + '.pdf' AS ARCHIVO_1 ";
      $rawQuery .= ", '' + f.FTR_NumeroFactura + '-' + c.CLI_RFC + '.xml' AS ARCHIVO_XML ";
      $rawQuery .= ", SUM(fd.FTRD_CantidadRequerida * fd.FTRD_PrecioUnitario *(1 + fd.FTRD_CMIVA_Porcentaje)) AS IMPORTE";
      $collection = DB::table('Facturas as f')
         ->select(DB::raw($rawQuery))
         ->join("FacturasDetalle as fd", "fd.FTRD_FTR_FacturaId", "=", "f.FTR_FacturaId")
         ->join("OrdenesVenta as ov", "f.FTR_OV_OrdenVentaId", "=", "ov.OV_OrdenVentaId")
         ->join("Clientes as c", "ov.OV_CLI_ClienteId", "=", "c.CLI_ClienteId")
         ->groupBy("ov.OV_CodigoOV")
         ->groupBy("f.FTR_NumeroFactura")
         ->groupBy("c.CLI_RFC");
      // $collection->where('ov.OV_CodigoOV', '=', 'OV00586');
      // find file in path reportik 
      // find /opt/lampp/htdocs/ -name "*00418-DKD170417RF9.pdf"
      return $collection->get();
   }
   public function getRequisitionCollection(Request $request)
   {
      $configRow = $this->getConfigRow('COM');
      $rawQuery = " DISTINCT 'COM' + oc.OC_CodigoOC + r.REQ_CodigoRequisicion AS LLAVE_ID,";
      $rawQuery .= "'COM' + oc.OC_CodigoOC AS GRUPO_ID ,";
      $rawQuery .= "r.REQ_CodigoRequisicion AS DOC_ID ,";
      $rawQuery .= "'' + r.REQ_ArchivoCotizacion1 AS ARCHIVO_1 ,";
      $rawQuery .= "'' + r.REQ_ArchivoCotizacion2 AS ARCHIVO_2,";
      $rawQuery .= "'' + r.REQ_ArchivoCotizacion3 AS ARCHIVO_3";
      $collection = DB::table('Requisiciones as r')
         ->select(DB::raw($rawQuery))
         ->join("RequisicionesDetalle as rd", "rd.REQD_REQ_RequisicionId", "=", "r.REQ_RequisicionId")
         ->join("OrdenesCompra as oc", "rd.REQD_OC_OrdenCompraId", "=", "oc.OC_OrdenCompraId")
         ->leftJoin("OrdenesTrabajo as ot", "ot.OT_OrdenTrabajoId", "=", "r.REQ_OT_OrdenTrabajoId")
         ->leftJoin("Clientes as c", "ot.OT_CLI_ClienteId", "=", "c.CLI_ClienteId")
         ->where("r.REQ_Eliminado", "=", "0");
      return $collection->get();
   }
   public function getCreditNoteCollection()
   {
      $configRow = $this->getConfigRow('SAC');
      $configRowxml = $this->getConfigRow('XML');
      $rawQuery = "'SAC' + OV_CodigoOV  + 'FAC' + nc.NC_Codigo AS LLAVE_ID";
      // $rawQuery .= ", 'SAC' + OV_CodigoOV  AS GRUPO_ID";
      $rawQuery .= ", '' + OV_CodigoOV  AS GRUPO_ID";
      $rawQuery .= ", nc.NC_Codigo AS DOC_ID";
      $rawQuery .= ", '' + nc.NC_Codigo + '-' + CLI_RFC + '.pdf' AS ARCHIVO_1";
      $rawQuery .= ", '' + nc.NC_Codigo + '-' + CLI_RFC + '.xml' AS ARCHIVO_XML";
      $rawQuery .= ", SUM(ncd.NCD_Cantidad * nc.NC_MONP_Paridad * ncd.NCD_PrecioUnitario * (1 + ncd.NCD_CMIVA_Porcentaje)) AS IMPORTE";

      $collection = DB::table('NotasCredito as nc')
         ->select(DB::raw($rawQuery))
         ->join("NotasCreditoDetalle as ncd", "nc.NC_NotaCreditoId", "=", "ncd.NCD_NC_NotaCreditoId")
         ->join("Facturas as f", "f.FTR_FacturaId", "=", "nc.NC_FTR_FacturaId")
         ->join("OrdenesVenta as ov", "f.FTR_OV_OrdenVentaId", "=", "ov.OV_OrdenVentaId")
         ->join("Clientes as c", "ov.OV_CLI_ClienteId", "=", "c.CLI_ClienteId")
         ->groupBy("ov.OV_CodigoOV")
         ->groupBy("nc.NC_Codigo")
         ->groupBy("c.CLI_RFC");
      // $collection->where('ov.OV_CodigoOV', '=', 'OV00586');
      // find file in path reportik 
      // find /opt/lampp/htdocs/ -name "*00418-DKD170417RF9.pdf"
      return $collection->get();
   }

   public function getSalesOrderCollection()
   {
      // $configRow = $this->getConfigRow('SAC');

      // $rawQuery = "'SAC' + ov.OV_CodigoOV + ov.OV_CodigoOV as LLAVE_ID,";
      // // $rawQuery .= "'SAC' + ov.OV_CodigoOV as GRUPO_ID, ";
      // $rawQuery .= "'' + ov.OV_CodigoOV as GRUPO_ID, ";
      // $rawQuery .= "ov.OV_CodigoOV as DOC_ID,";
      // $rawQuery .= "'' + ov.OV_CodigoOV + '.pdf' as ARCHIVO_1, ";
      // $rawQuery .= "'' + ov.OV_Archivo1 as ARCHIVO_2,";
      // $rawQuery .= "'' + ov.OV_Archivo2 as ARCHIVO_3, ";
      // $rawQuery .= "'' + ov.OV_Archivo3 as ARCHIVO_4, ";
      // $rawQuery .= "SUM(Cast((ovd.OVD_CantidadRequerida * ovd.OVD_PrecioUnitario) -
      // ((ovd.OVD_CantidadRequerida * ovd.OVD_PrecioUnitario) *
      // ovd.OVD_PorcentajeDescuento) +
      // ((ovd.OVD_CantidadRequerida * ovd.OVD_PrecioUnitario) -
      // ((ovd.OVD_CantidadRequerida * ovd.OVD_PrecioUnitario) *
      // ovd.OVD_PorcentajeDescuento)) * ovd.OVD_CMIVA_Porcentaje as decimal(16, 2))) as IMPORTE";
      // $collection = DB::table('OrdenesVenta as ov')
      //    ->select(DB::raw($rawQuery))
      //    ->join('OrdenesVentaDetalle as ovd', 'ov.OV_OrdenVentaId', '=', 'ovd.OVD_OV_OrdenVentaId')
      //    ->groupBy('ov.OV_CodigoOV')
      //    ->groupBy('ov.OV_Archivo1')
      //    ->groupBy('ov.OV_Archivo2')
      //    ->groupBy('ov.OV_Archivo3');
      $rawQuery = "Select 'SAC'+ OV_CodigoOV + OV_CodigoOV AS LLAVE_ID
 , 'SAC'+ OV_CodigoOV AS GRUPO_ID
, OV_CodigoOV AS DOC_ID
, OV_CodigoOV+'.pdf' AS ARCHIVO_1
, OV_Archivo1 AS ARCHIVO_2
, OV_Archivo2 AS ARCHIVO_3
, OV_Archivo3 AS ARCHIVO_4
 , SUM(Cast((OVD_CantidadRequerida * OVD_PrecioUnitario) -
((OVD_CantidadRequerida * OVD_PrecioUnitario) *
OVD_PorcentajeDescuento) +
 ((OVD_CantidadRequerida * OVD_PrecioUnitario) -
((OVD_CantidadRequerida * OVD_PrecioUnitario) *
OVD_PorcentajeDescuento)) * OVD_CMIVA_Porcentaje as decimal(16,2)))
AS IMPORTE
From OrdenesVenta
Inner Join OrdenesVentaDetalle on OV_OrdenVentaId =
OVD_OV_OrdenVentaId
Group By OV_CodigoOV, OV_Archivo1, OV_Archivo2, OV_Archivo3";
      $collection = DB::select(DB::raw($rawQuery));
      // $collection->where('ov.OV_CodigoOV', '=', 'OV00586');
      // find file in path reportik 
      // find /opt/lampp/htdocs/ -name "*00418-DKD170417RF9.pdf"
      // var_dump($collection->toSql());
      // die;

      return $collection;
   }

   public function getWorkOrders(Request $request)
   {
      $configRow = "";
      try {
         $configRow = $this->getConfigRow('ORDEN_TRABAJO');
      } catch (\Throwable $th) {
         //throw $th;
      }
      $rawQuery = "Select 'SAC' + OV_CodigoOV + OT_Codigo AS LLAVE_ID ,
         'SAC' + OV_CodigoOV AS GRUPO_ID ,
         OT_Codigo AS DOC_ID ,
         '' + OT_Codigo + '.pdf' AS ARCHIVO_1 ,
         Cast(OTDA_Cantidad * (
         Select
            top(1) ((1 * OVD_PrecioUnitario) - ((1 * OVD_PrecioUnitario) * OVD_PorcentajeDescuento) +
               (((1 * OVD_PrecioUnitario) - ((1 * OVD_PrecioUnitario) * OVD_PorcentajeDescuento)) * OVD_CMIVA_Porcentaje))
         from
            OrdenesVentaDetalle
         Where
            OVD_OV_OrdenVentaId = OTRE_OV_OrdenVentaId
            and OVD_ART_ArticuloId = OTDA_ART_ArticuloId) as decimal(16,
         2)) AS IMPORTE
         from
         OrdenesTrabajo
         inner join OrdenesTrabajoReferencia on
         OT_OrdenTrabajoId = OTRE_OT_OrdenTrabajoId
         inner join OrdenesTrabajoDetalleArticulos on
         OT_OrdenTrabajoId = OTDA_OT_OrdenTrabajoId
         inner join OrdenesVenta on
         OV_OrdenVentaId = OTRE_OV_OrdenVentaId
         Order By
         OV_CodigoOV,
         OT_Codigo";
      $collection = DB::select($rawQuery);

      return $collection;
   }

   public function getDepartments()
   {
      $collection = DB::table("departamentos as d")
         ->where("d.DEP_Eliminado", "=", "0")
         ->where(DB::raw("CONVERT(NUMERIC(10,2),d.DEP_Codigo)"), ">", "100")
         ->orderBy("d.DEP_Codigo");
      return $collection->get();
   }

   public function getRowData($id)
   {
      return DB::table("RPT_AlmacenDigitalIndice")->where('id', $id)->first();
   }
   public function getSchema()
   {
      return  \Schema::getColumnListing("RPT_AlmacenDigitalIndice"); //DB::table("RPT_AlmacenDigitalIndice")->first();
   }

   public function updateData($params, $id)
   {
      return DB::table("RPT_AlmacenDigitalIndice")
         ->where('id', $id)->update(
            $params
         );
   }
   public function updateSyncData($params, $LLAVE_ID)
   {
      return DB::table("RPT_AlmacenDigitalIndice")
         ->where('LLAVE_ID', $LLAVE_ID)->update(
            $params
         );
   }
   public function newRow($params)
   {

      return DB::table("RPT_AlmacenDigitalIndice")->insertGetId(
         $params
      );
   }

   /**
    * Get Schema Configuration from table
    * @return Object
    */
   public function getConfiguration()
   {
      return  \Schema::getColumnListing("RPT_AlmacenDigitalConfiguration");
   }

   /**
    * Ger RPT_AlmacenDigitalConfiguration rows
    * @return Object
    */
   public function getConfigValues(Request $request, $id = false)
   {
      $collection =  DB::table('RPT_AlmacenDigitalConfiguration');
      if ($id) {
         return $collection->where("id", "=", $id)->first();
      }
      return $collection->get();
   }

   public function updateConfigRow($params, $id)
   {
      return DB::table("RPT_AlmacenDigitalConfiguration")
         ->where('ID', $id)->update(
            $params
         );
   }
   public function newConfigRow($params)
   {
      return DB::table("RPT_AlmacenDigitalConfiguration")->insertGetId(
         $params
      );
   }
   public function getConfigRow($moduleType)
   {
      return  DB::table('RPT_AlmacenDigitalConfiguration')
         ->where('GROUP_NAME', "=", $moduleType)
         ->where('ENABLED', "=", "1")->first();
   }
   public function getConfigRowsAll()
   {
      $collection = DB::table('RPT_AlmacenDigitalConfiguration')
         ->where('ENABLED', "=", "1");
      return  $collection->get();
   }
}
