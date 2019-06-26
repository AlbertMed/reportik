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
class Mod_ComprasController extends Controller
{
    public function R003A()
    {
        if (Auth::check()) {
            $sociedad = Input::get('text_selUno');
            $tipo = Input::get('text_selDos');
            
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
          
            return view('Mod_Compras.003A', compact('actividades', 'ultimo', 'sociedad', 'tipo'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function Data_R003A(Request $request)
    {
        $request->session()->put('sessionR003A', array(
            'tipo' => $request->get('tipo'),
            'sociedad' => $request->get('sociedad')
        ));        
        $sociedad = ($request->get('sociedad') == 'COMERCIALIZADORA') ? 'comercializadora' : 'sqlsrv' ;
        switch ($request->get('tipo')) {
            case 'COMPLETO':
                $data = DB::connection($sociedad)->select( "
                            Select	ART_CodigoArticulo as CODIGO, 
                            ART_Nombre as NOMBRE, 
                            AFAM_Nombre as FAMILIA, 
                            CMM_Valor as SUB_CAT, 
                            CMUM_Nombre as UDM, 
                            ART_CantidadAMano as EXISTENCIA, 
                            Convert(Decimal(28,10), ART_CostoMaterialEstandar) as ESTANDAR, 
                            ART_UltimoCostoPromedio as PROMEDIO, 
                            ART_UltimoCostoUltimo as U_COMPRA 
                    from Articulos
                    left join ArticulosFamilias on ART_AFAM_FamiliaId = AFAM_FamiliaId 
                    left join ControlesMaestrosUM on ART_CMUM_UMInventarioId = CMUM_UnidadMedidaId 
                    left join ControlesMaestrosMultiples on ART_SubCategoriaId = CMM_ControllId 
                    where AFAM_Nombre NOT like 'PT%' and  ART_Activo  <> 0 
                    order by  ART_Nombre
                ");
                break;
            
            default:
                $data = DB::connection('sqlsrv')->select( "
                            Select	ART_CodigoArticulo as CODIGO, 
                            ART_Nombre as NOMBRE, 
                            AFAM_Nombre as FAMILIA, 
                            CMM_Valor as SUB_CAT, 
                            CMUM_Nombre as UDM, 
                            ART_CantidadAMano as EXISTENCIA, 
                            Convert(Decimal(28,10), ART_CostoMaterialEstandar) as ESTANDAR, 
                            ART_UltimoCostoPromedio as PROMEDIO, 
                            ART_UltimoCostoUltimo as U_COMPRA 
                    from Articulos
                    left join ArticulosFamilias on ART_AFAM_FamiliaId = AFAM_FamiliaId 
                    left join ControlesMaestrosUM on ART_CMUM_UMInventarioId = CMUM_UnidadMedidaId 
                    left join ControlesMaestrosMultiples on ART_SubCategoriaId = CMM_ControllId 
                    where AFAM_Nombre NOT like 'PT%' and  ART_Activo  <> 0 
                    and ART_CostoMaterialEstandar = 0
                    order by  ART_Nombre
                ");
                break;
        }
        return Datatables::of(collect($data))           
        ->make(true);
    }
    public function R003AXLS()
    {
        if (Auth::check()) {
            $data = json_decode(Session::get('DATA_R003A'));
            $datasession = Session::get('sessionR003A');
            $path = public_path() . '/assets/plantillas_excel/Reporte_003A.xlsx';
            Excel::load($path, function ($excel) use ($data, $datasession) {
                $excel->sheet('Hoja 1', function ($sheet) use ($data, $datasession) {
                    //$sheet->margeCells('A1:F5');      
                   
                    $sociedad = ( array_get($datasession, 'sociedad') == 'COMERCIALIZADORA') ? 'COMERCIALIZADORA ITEKNIA' : 'ITEKNIA EQUIPAMIENTO';
                    $sheet->row(2, [
                        $sociedad.', S.A DE C.V.'
                    ]);
                    
                    $sheet->row(4, [
                        'REPORTE: '.array_get($datasession, 'tipo')
                    ]);
                    $sheet->row(5, [
                        'FECHA DE IMPRESION: ' . \AppHelper::instance()->getHumanDate_format(date( "Y-m-d h:i:s"), 'h:i A'),
                    ]);
                    
                    //Datos    
                    $fila = 7;
                    foreach ($data as $rep) {
                        $sheet->row(
                            $fila,
                            [
                                $rep->CODIGO, $rep->NOMBRE, $rep->FAMILIA, $rep->SUB_CAT, $rep->UDM, $rep->EXISTENCIA, $rep->ESTANDAR, $rep->PROMEDIO, $rep->U_COMPRA
                  
                            ]
                        );
                        $fila++;
                    }
                });
            })
            ->setFilename('Reporte_003_A' . ' - ' . date("d_m_Y"))
            ->export('xlsx');
        } else {
            return redirect()->route('auth/login');
        }
    
    }
    public function R003APDF()
    {
        
        if (Auth::check()) {
            $data = json_decode(Session::get('DATA_R003A'));
            $datasession = Session::get('sessionR003A');
            $tipo = array_get($datasession, 'tipo');
            $s = (array_get($datasession, 'sociedad') == 'COMERCIALIZADORA') ? 'COMERCIALIZADORA ITEKNIA' : 'ITEKNIA EQUIPAMIENTO';
            $sociedad = $s.', S.A DE C.V.';
            $pdf = \PDF::loadView('Mod_Compras.003APDF', compact('data', 'tipo', 'sociedad'));
            //$pdf = new FPDF('L', 'mm', 'A4');
           // $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);             
            $pdf->setPaper('Letter', 'landscape')->setOptions(['isPhpEnabled' => true]);             
            
            return $pdf->stream('Reporte_003_A' . ' - ' . date("d_m_Y") . '.Pdf');
        } else {
            return redirect()->route('auth/login');
        }
    }

  
}
