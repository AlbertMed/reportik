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
    */
   public function getList(Request $request)
   {
      $result = DB::table("RPT_AlmacenDigitalIndice");
      if ($request->input("group_id") != "") {
         $result->orWhere('GRUPO_ID', 'like', "%" . $request->input("group_id") . "%");
      }
      if ($request->input("document_id") != "") {
         $result->orWhere('DOC_ID', 'like', "%" . $request->input("document_id") . "%");
      }
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
   public function newRow($params)
   {

      return DB::table("RPT_AlmacenDigitalIndice")->insertGetId(
         $params
      );
   }
}
