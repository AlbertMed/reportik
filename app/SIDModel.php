<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SIDModel extends Model
{
    public function getList()
    {
        $result = DB::table("RPT_AlmacenDigitalIndice");
        $result->Where('LLAVE_ID', 'like', "SID%");
        // var_dump($result->toSql());
        // die;
        return $result->get();
    }
    public function getConfigRow($moduleType)
    {
        return  DB::table('RPT_AlmacenDigitalConfiguration')
            ->where('GROUP_NAME', "=", $moduleType)
            ->where('ENABLED', "=", "1")->first();
    }

    /**
     * Get Schema Configuration from table
     * @return Object
     */
    public function getColumns()
    {
        return  \Schema::getColumnListing("RPT_AlmacenDigitalIndice");
    }

    public function getArea()
    {
        $collection = DB::table("departamentos as d")
            ->where("d.DEP_Eliminado", "=", "0")
            ->where(DB::raw("CONVERT(NUMERIC(10,2),d.DEP_Codigo)"), ">", "100")
            ->orderBy("d.DEP_Codigo");
        return $collection->get();
    }

    /**
     * @param string $workOrder
     * return 
     */
    public function getOT($workOrder)
    {
        $rawQuery = "Select OT_Codigo AS OT 
        , ART_CodigoArticulo AS COD_ARTICULO
        , ART_Nombre AS NOB_ARTICULO
        , OV_CodigoOV AS OV
        , PRY_CodigoEvento AS COD_PROY
        , PRY_NombreProyecto AS PROYECTO
        from OrdenesTrabajo
        inner Join OrdenesTrabajoDetalleArticulos on OT_OrdenTrabajoId =
        OTDA_OT_OrdenTrabajoId
        inner Join OrdenesTrabajoReferencia on OT_OrdenTrabajoId =
        OTRE_OT_OrdenTrabajoId
        inner Join Articulos on OTDA_ART_ArticuloId = ART_ArticuloId
        inner Join OrdenesVenta on OTRE_OV_OrdenVentaId = OV_OrdenVentaId
        inner Join Proyectos on OV_PRO_ProyectoId = PRY_ProyectoId
        Where OT_Eliminado = 0 
        and OT_CMM_Estatus <> '3887AF19-EA11-4464-A514-8FA6030E5E93'
        and OT_CMM_Estatus <> '46B96B9F-3A45-4CF9-9775-175C845B6198'
        and OT_CMM_Estatus <> '7246798D-137A-4E94-9404-1D80B777EE09'
        and OT_CMM_Estatus <> '3E35C727-DAEE-47FE-AA07-C50EFD93B25F'
        and OT_CODIGO = '{$workOrder}'";
        return DB::select($rawQuery);
    }

    public function getOTDetailData($workOrder)
    {
        $rawQuery = "Select OT_Codigo AS OT 
        , ART_CodigoArticulo AS COD_ARTICULO
        , ART_Nombre AS NOB_ARTICULO
        , OV_CodigoOV AS OV
        , PRY_CodigoEvento AS COD_PROY
        , PRY_NombreProyecto AS PROYECTO
        from OrdenesTrabajo
        inner Join OrdenesTrabajoDetalleArticulos on OT_OrdenTrabajoId =
        OTDA_OT_OrdenTrabajoId
        inner Join OrdenesTrabajoReferencia on OT_OrdenTrabajoId =
        OTRE_OT_OrdenTrabajoId
        inner Join Articulos on OTDA_ART_ArticuloId = ART_ArticuloId
        inner Join OrdenesVenta on OTRE_OV_OrdenVentaId = OV_OrdenVentaId
        inner Join Proyectos on OV_PRO_ProyectoId = PRY_ProyectoId
        Where OT_Eliminado = 0 
        and OT_Codigo ='{$workOrder}'";
        return DB::select($rawQuery);
    }

    public function newRow($params)
    {
        return DB::table("RPT_AlmacenDigitalIndice")->insertGetId(
            $params
        );
    }

    public function updateData($params, $id)
    {
        return DB::table("RPT_AlmacenDigitalIndice")
            ->where('id', $id)->update(
                $params
            );
    }
}
