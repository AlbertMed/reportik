<?php
namespace App\Http\Controllers;

use App;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Dompdf\Dompdf;
//excel
use Illuminate\Http\Request;
//DOMPDF
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;
//Fin DOMPDF
use Illuminate\Support\Facades\Validator;
use Datatables;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_AlmacenGralController extends Controller
{
    public function R013()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $fi = Input::get('FechIn');
            $ff = Input::get('FechaFa');
            return view('Mod_Almacen.013', compact('actividades', 'ultimo', 'fi', 'ff'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function DataShowEntradas(Request $request)
    {        
            $data = DB::select( "
            Select OC_CodigoOC as ORDEN, OCRC_FechaRecibo AS F_RECIBO, PRO_CodigoProveedor AS CLIENTE, OC_PDOC_Nombre AS RAZON_SOC, OCRC_Comentarios AS NOTAS, EV_CodigoEvento AS C_PROY, EV_Descripcion AS PROYECTO, ART_CodigoArticulo AS CODE_ART, OCD_DescripcionArticulo AS ARTICULO, OCD_CMUM_UMCompras AS UMC, OCD_AFC_FactorConversion AS FACT, (Select CMUM_Nombre from ControlesMaestrosUM Where CMUM_UnidadMedidaId = ISNULL(ART_CMUM_UMInventarioId,10)) AS UMI, CANTIDAD_RECIBIDA AS CANTIDAD, OCFR_PrecioUnitario AS COSTO, (Select MON_Nombre from Monedas Where MON_MonedaId = OC_MON_MonedaId) AS MONEDA, (OCFR_PrecioUnitario * (1-OCFR_PorcentajeDescuento) / ISNULL(OCD_AFC_FactorConversion, 1)) AS COSTO_OC, EMP_CodigoEmpleado AS C_EMPL, RTRIM(EMP_Nombre)+' '+RTRIM(EMP_PrimerApellido) AS NOM_EMPL from OrdenesCompra inner join OrdenesCompraDetalle on OC_OrdenCompraId = OCD_OC_OrdenCompraId left JOIN Articulos ON OCD_ART_ArticuloId = ART_ArticuloId inner join OrdenesCompraFechasRequeridas on OC_OrdenCompraId = OCFR_OC_OrdenCompraId AND OCD_PartidaId = OCFR_OCD_PartidaId LEFT JOIN (SELECT SUM(OCRC_CantidadRecibo) AS CANTIDAD_RECIBIDA,OCRC_OC_OrdenCompraId, OCRC_OCR_OCRequeridaId, OCRC_EMP_ModificadoPor, OCRC_FechaRecibo, OCRC_Comentarios, OCRC_PrecioOrdenCompraAlRecibir FROM OrdenesCompraRecibos GROUP BY OCRC_OC_OrdenCompraId, OCRC_OCR_OCRequeridaId, OCRC_FechaRecibo, OCRC_Comentarios, OCRC_PrecioOrdenCompraAlRecibir, OCRC_EMP_ModificadoPor) AS OrdenesCompraRecibos ON OC_OrdenCompraId = OCRC_OC_OrdenCompraId AND OCFR_FechaRequeridaId = OCRC_OCR_OCRequeridaId inner join Proveedores PRO on OC_PRO_ProveedorId = PRO_ProveedorId left join Eventos PRY on OC_EV_ProyectoId = EV_EventoId inner join Empleados on OCRC_EMP_ModificadoPor = EMP_EmpleadoId where OC_Borrado = 0 and Cast(OCRC_FechaRecibo As Date) 
            BETWEEN '" . date('Y-m-d', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('Y-m-d', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            and OC_MON_MonedaId <> '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1' 
            UNION ALL
            Select OC_CodigoOC as ORDEN, OCRC_FechaRecibo AS F_RECIBO, PRO_CodigoProveedor AS CLIENTE, OC_PDOC_Nombre AS RAZON_SOC, OCRC_Comentarios AS NOTAS, EV_CodigoEvento AS C_PROY, EV_Descripcion AS PROYECTO, ART_CodigoArticulo AS CODE_ART, OCD_DescripcionArticulo AS ARTICULO, OCD_CMUM_UMCompras AS UMC, OCD_AFC_FactorConversion AS FACT, (Select CMUM_Nombre from ControlesMaestrosUM Where CMUM_UnidadMedidaId = ISNULL(ART_CMUM_UMInventarioId,10)) AS UMI, CANTIDAD_RECIBIDA AS CANTIDAD, OCFR_PrecioUnitario AS COSTO, (Select MON_Nombre from Monedas Where MON_MonedaId = OC_MON_MonedaId) AS MONEDA, (OCFR_PrecioUnitario * (1-OCFR_PorcentajeDescuento) / ISNULL(OCD_AFC_FactorConversion, 1)) AS COSTO_OC, EMP_CodigoEmpleado AS C_EMPL, RTRIM(EMP_Nombre)+' '+RTRIM(EMP_PrimerApellido) AS NOM_EMPL from OrdenesCompra inner join OrdenesCompraDetalle on OC_OrdenCompraId = OCD_OC_OrdenCompraId left JOIN Articulos ON OCD_ART_ArticuloId = ART_ArticuloId inner join OrdenesCompraFechasRequeridas on OC_OrdenCompraId = OCFR_OC_OrdenCompraId AND OCD_PartidaId = OCFR_OCD_PartidaId LEFT JOIN (SELECT SUM(OCRC_CantidadRecibo) AS CANTIDAD_RECIBIDA,OCRC_OC_OrdenCompraId, OCRC_OCR_OCRequeridaId, OCRC_EMP_ModificadoPor, OCRC_FechaRecibo, OCRC_Comentarios, OCRC_PrecioOrdenCompraAlRecibir FROM OrdenesCompraRecibos GROUP BY OCRC_OC_OrdenCompraId, OCRC_OCR_OCRequeridaId, OCRC_FechaRecibo, OCRC_Comentarios, OCRC_PrecioOrdenCompraAlRecibir, OCRC_EMP_ModificadoPor) AS OrdenesCompraRecibos ON OC_OrdenCompraId = OCRC_OC_OrdenCompraId AND OCFR_FechaRequeridaId = OCRC_OCR_OCRequeridaId inner join Proveedores PRO on OC_PRO_ProveedorId = PRO_ProveedorId left join Eventos PRY on OC_EV_ProyectoId = EV_EventoId inner join Empleados on OCRC_EMP_ModificadoPor = EMP_EmpleadoId where OC_Borrado = 0 and Cast(OCRC_FechaRecibo As Date) 
            BETWEEN '" . date('Y-m-d', strtotime($request->get('fi'))) . ' 00:00' . "' and '" . date('Y-m-d', strtotime($request->get('ff'))) . ' 23:59:59' . "'
            and OC_MON_MonedaId = '748BE9C9-B56D-4FD2-A77F-EE4C6CD226A1' 
            order by MONEDA desc, F_RECIBO, OC_CodigoOC
            ");
            $request->session()->put('fechas_entradas', array(
                'fi' => $request->get('fi'),
                'ff' => $request->get('ff')
            ));
            return Datatables::of(collect($data))
            ->addColumn('IMPORTE', function ($consulta) {
                return ($consulta->COSTO_OC * $consulta->CANTIDAD);
            })
            ->make(true);
    }
    public function R013XLS()
    {
        if (Auth::check()) {
            $data = json_decode(Session::get('DATA_R013'));
            $fechas_entradas = Session::get('fechas_entradas');
            $fecha = 'Del: ' . \AppHelper::instance()->getHumanDate(array_get($fechas_entradas, 'fi')) . ' al: ' .
                \AppHelper::instance()->getHumanDate(array_get($fechas_entradas, 'ff'));

            Excel::create('Reporte_013' . ' - ' . date("d/m/Y") . '', function ($excel) use ($data, $fecha) {
                $excel->sheet('Hoja 1', function ($sheet) use ($data, $fecha) {
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, [
                        'No. 013'
                    ]);
                    $sheet->row(2, [
                        'ITEKNIA EQUIPAMIENTO, S.A DE C.V.'
                    ]);
                    $sheet->row(3, [
                        'Reporte de Entradas a Almacén Artículos y Miceláneas (COMPRAS)'
                    ]);
                    $sheet->row(4, [
                        $fecha
                    ]);
                    $sheet->row(5, [
                        'FECHA DE IMPRESION: ' . \AppHelper::instance()->getHumanDate(date("Y-m-d")),
                    ]);
                    $sheet->row(6, [
                        'ORDEN', 'F_RECIBO', 'CLIENTE', 'RAZON_SOC', 'NOTAS', 'C_PROY', 'PROYECTO', 'CODE_ART', 'ARTICULO', 'UMC', 'FACT', 'UMI', 'CANTIDAD', 'COSTO_OC', 'IMPORTE', 'MONEDA', 'C_EMPL', 'NOM_EMPL'
                    ]);
                    //Datos    
                    $fila = 7;
                    foreach ($data as $rep) {
                        $sheet->row(
                            $fila,
                            [
                                $rep->ORDEN, $rep->F_RECIBO, $rep->CLIENTE, $rep->RAZON_SOC, $rep->NOTAS, $rep->C_PROY, $rep->PROYECTO, $rep->CODE_ART, $rep->ARTICULO, $rep->UMC, $rep->FACT, $rep->UMI, $rep->CANTIDAD, $rep->COSTO_OC, $rep->IMPORTE, $rep->MONEDA, $rep->C_EMPL, $rep->NOM_EMPL
                  
                            ]
                        );
                        $fila++;
                    }
                });
            })->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    
    }
    public function R013PDF()
    {
        
       // dd( Session::get('DATA_R013'));
        
        if (Auth::check()) {
        $a = json_decode(Session::get('DATA_R013'));
            
          //  dd($a);
            
        $entradasMXP = array_filter($a, function ($value) {
            return $value->MONEDA == 'Pesos';
        });
        $entradasUSD = array_filter($a, function ($value) {
            return $value->MONEDA == 'Dolar';
        });
          
            $data = array('entradasMXP' => $entradasMXP, 'entradasUSD' => $entradasUSD, 'fechas_entradas' => Session::get('fechas_entradas'));
            $pdf = \PDF::loadView('Mod_Almacen.013PDF', $data);
            //$pdf = new FPDF('L', 'mm', 'A4');
            $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);             
            
            return $pdf->stream('Reporte_013 ' . ' - ' . date("d/m/Y") . '.Pdf');
        } else {
            return redirect()->route('auth/login');
        }
    }

    public function R014A()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            return view('Mod_Almacen.014', compact('actividades', 'ultimo'));
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function Data_R014A()
    {
        $data = DB::select( "
           Select {fn CONCAT (AL.ALM_CodigoAlmacen,'$', LO.LOC_CodigoLocalidad)} AS LLAVE,AL.ALM_CodigoAlmacen as ALMACEN, LO.LOC_CodigoLocalidad as LOCALIDAD, LO.LOC_Nombre as NOM_LOCAL, A1.ART_CodigoArticulo as CODIGO, A1.ART_Nombre as NOMBRE, UM.CMUM_Nombre as UM_Inv, EX.LOCA_Cantidad as EXISTE, CM.CMM_Valor as TIPO_COS, Convert(Decimal(28,10), A1.ART_CostoMaterialEstandar) as COS_EST, A1.ART_UltimoCostoPromedio as COS_PRO, A1.ART_UltimoCostoUltimo as COS_ULT, AF.AFAM_Nombre as FAMILIA, AC.ACAT_Nombre as CATEGORIA, AT.ATP_Descripcion as TIPO from LocalidadesArticulo EX inner join Articulos A1 on A1.ART_ArticuloId = EX.LOCA_ART_ArticuloId inner join ControlesMaestrosUM UM on A1.ART_CMUM_UMInventarioId = UM.CMUM_UnidadMedidaId Inner join Localidades LO on EX.LOCA_LOC_LocalidadId = LO.LOC_LocalidadId Inner join Almacenes AL on LO.LOC_ALM_AlmacenId = AL.ALM_AlmacenId left join ArticulosFamilias AF on A1.ART_AFAM_FamiliaId = AF.AFAM_FamiliaId left join ArticulosCategorias AC on A1.ART_ACAT_CategoriaId= AC.ACAT_CategoriaId
            left join ArticulosTipos AT on A1.ART_ATP_TipoId = AT.ATP_TipoId left join ControlesMaestrosMultiples CM on A1.ART_CMM_TipoCostoId = CM.CMM_ControllId and CM.CMM_Control = 'CMM_CDA_TiposCosto' Where
            EX.LOCA_Cantidad <> 0 Order By Al.ALM_CodigoAlmacen, LO.LOC_CodigoLocalidad, A1.ART_Nombre
            ");
        
        return Datatables::of(collect($data))->make(true);
    }
    public function R014AXLS()
    {
        if (Auth::check()) {
            $data = json_decode(Session::get('DATA_R014'));
            
            
            Excel::create('Reporte_014A' . ' - ' . date("d/m/Y") . '', function ($excel) use ($data) {
                $excel->sheet('Hoja 1', function ($sheet) use ($data) {
                    //$sheet->margeCells('A1:F5');     
                    $sheet->row(1, [
                        'No. 014 A'
                    ]);
                    $sheet->row(2, [
                        'ITEKNIA EQUIPAMIENTO, S.A DE C.V.'
                    ]);
                    $sheet->row(3, [
                        'Reporte de Inventario General'
                    ]);
                    $sheet->row(4, [
                        'FECHA DE IMPRESION: ' . \AppHelper::instance()->getHumanDate(date("Y-m-d")),
                    ]);
                    $sheet->row(5, [
                        'ALMACEN', 'LOCALIDAD', 'NOM_LOCAL', 'CODIGO', 'NOMBRE', 'UM_Inv', 'EXISTE', 'COS_EST', 'FAMILIA', 'CATEGORIA', 'TIPO'
                    ]);
                    //Datos    
                    $fila = 6;
                    foreach ($data as $rep) {
                        $sheet->row(
                            $fila,
                            [
                                $rep->ALMACEN, $rep->LOCALIDAD, $rep->NOM_LOCAL, $rep->CODIGO, $rep->NOMBRE, $rep->UM_Inv, $rep->EXISTE, $rep->COS_EST, $rep->FAMILIA, $rep->CATEGORIA, $rep->TIPO
                            ]
                        );
                        $fila++;
                    }
                });
            })->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function R014APDF()
    {
        // dd( Session::get('DATA_R014'));
        if (Auth::check()) {
            $data = Session::get( 'DATA_R014');
            //json_decode($data)
            $data = collect(json_decode($data))->sortBy('LLAVE')->toArray();
            //dd($data);
            
            $pdf = \PDF::loadView('Mod_Almacen.014PDF', compact('data'));
            //$pdf = new FPDF('L', 'mm', 'A4');
            $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);
            return $pdf->stream('Reporte_014A ' . ' - ' . date("d/m/Y") . '.Pdf');
        } else {
            return redirect()->route('auth/login');
        }
    }
}
