<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Maatwebsite\Excel\Facades\Excel;

ini_set('max_execution_time', 90);
class Reportes_ProduccionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('Mod00_Administrador.admin');
    }

    public function Produccion1(Request $request)
    {

        $enviado = $request->input('send');
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
    
                  if ($enviado == 'send') {
                $departamento = $request->input('dep');
                //  $fecha = explode(" - ",$request->input('date_range'));
                //$dt = date('d-m-Y H:i:s');
                $fechaI = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechIn'))));
                $fechaF = Date('d-m-y', strtotime(str_replace('-', '/', $request->input('FechaFa'))));  
                $fecha_desde = strtotime($request->input('FechIn'));
                $fecha_hasta = strtotime($request->input('FechaFa'));
if($fecha_hasta>=$fecha_desde){  
$clientes = DB::select('SELECT CardName from  "CP_ProdTerminada" WHERE  (fecha>=\'' . $fechaI . '\' AND
  fecha<=\'' . $fechaF . '\') AND
 (Name= (\'' . $departamento . '\')  OR Name= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 GROUP BY CardName, fecha, Name');
$produccion = DB::select('SELECT "CP_ProdTerminada"."orden", "CP_ProdTerminada"."Pedido", "CP_ProdTerminada"."Codigo",
 "CP_ProdTerminada"."modelo", "CP_ProdTerminada"."VS", "CP_ProdTerminada"."fecha",
 "CP_ProdTerminada"."CardName", 
 "CP_ProdTerminada"."Cantidad", "CP_ProdTerminada"."TVS"
 FROM   "CP_ProdTerminada" "CP_ProdTerminada"
 WHERE  ("CP_ProdTerminada"."fecha">=\'' . $fechaI . '\' AND
 "CP_ProdTerminada"."fecha"<=\'' . $fechaF . '\') AND
 ("CP_ProdTerminada"."Name"= (\'' . $departamento . '\')  OR "CP_ProdTerminada"."Name"= (CASE
 WHEN  \'' . $departamento . '\' like \'112%\' THEN N\'01 Corte de Piel\'
 WHEN  \'' . $departamento . '\' like \'115%\' THEN N\'02 Inspeccionar Piel\'
 WHEN  \'' . $departamento . '\' like \'118%\' THEN N\'02 Pegar.\'
 WHEN  \'' . $departamento . '\' like \'121%\' THEN N\'03 Anaquel Costura.\'
 WHEN  \'' . $departamento . '\' like \'133%\' THEN N\'03 Costura completa.\'
 WHEN  \'' . $departamento . '\' like \'136%\' THEN N\'04 Inspeccionar Costura\'
 WHEN  \'' . $departamento . '\' like \'139%\' THEN N\'139 Series Incompletas Costura\'
 WHEN  \'' . $departamento . '\' like \'145%\' THEN N\'05 Cojineria\'
 WHEN  \'' . $departamento . '\' like \'148%\' THEN N\'06 Funda Terminada\'
 WHEN  \'' . $departamento . '\' like \'151%\' THEN N\'07 Kitting\'
 WHEN  \'' . $departamento . '\' like \'157%\' THEN N\'07 Tapizar y Empaque\'
 WHEN  \'' . $departamento . '\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 ORDER BY "CP_ProdTerminada"."CardName", "CP_ProdTerminada"."orden"');
}else {
    return redirect()->back()->withErrors(array('message' => 'de rango de Fechas'));
  }
                $result = json_decode(json_encode($produccion), true);
                $finalarray = [];
                foreach ($clientes as $client) {
                    $miarray = array_filter($result, function ($item) use ($client) {
                        return $item['CardName'] == $client->CardName;
                    });
                    $finalarray[$client->CardName] = $miarray;
                }
                //dd(($finalarray['CASTRO HERRERA ALEJANDRO ISAAC'][0]['orden']));
                $values = ['produccion'=>$produccion,'actividades' => $actividades, 'ultimo' => count($actividades), 'ofs' => $finalarray, 'departamento' => $departamento, 'fechaI' => $fechaI, 'fechaF' => $fechaF, 'tvs' => 0, 'cant' => 0];
                Session::flash('Ocultamodal', 1);
                //dd($produccion);
                $pdf_array=[
                     $produccion,
                     'del día '.$fechaI.' al '.$fechaF,
                     $departamento
                ];      
                Session::put('repP', $values);      
                Session::put('pdf_array', $pdf_array);
                return view('Mod01_Produccion.produccionGeneral', $values);
                $compiled = view('Mod01_Produccion.produccionGeneral', $values)->render();
            } else {
                Session::flash('Ocultamodal', false);
                return view('Mod01_Produccion.produccionGeneral', ['actividades' => $actividades, 'ultimo' => count($actividades)]);
            }

        } else {
            return redirect()->route('auth/login');
        }
    }
    public function ReporteProduccionPDF()
    {
        $pdf_array = Session::get('pdf_array');
        $valores = $pdf_array[0];
        $fecha = $pdf_array[1];
        $depto = $pdf_array[2];
        $pdf = \PDF::loadView('Mod01_Produccion.produccionGeneralPDF', compact('valores', 'fecha', 'depto'));
        //$pdf = new FPDF('L', 'mm', 'A4');
        $pdf->setOptions(['isPhpEnabled' => true]);
//        Session::forget('values');
        return $pdf->stream('Siz_Reporte_Produccion ' . ' - ' . $hoy = date("d/m/Y") . '.Pdf');
    }
    public function ReporteProduccionEXL()
    {
        if(Session::has ('repP')){          
            $values=Session::get('repP');
            Excel::create('Siz_Reporte_Produccion_General' . ' - ' . $hoy = date("d/m/Y").'', function($excel)use($values) {
             $excel->sheet('Hoja 1', function($sheet) use($values){
                //$sheet->margeCells('A1:F5');     
                $sheet->row(1, [
                   'Cliente','Fecha','Orden','Pedido','Código','Modelo','VS','Cantidad','Total VS'
                ]);
               //Datos    
               $fila = 2;     
            foreach ( $values['produccion'] as $produccion){
              //  $tvs= $tvs + $produccion->TVS;
                //$cant = $cant + $produccion->Cantidad;
                $sheet->row($fila, 
                [
                  $produccion->CardName,    
                   substr($produccion->fecha,0,10),
                   $produccion->orden,
                   $produccion->Pedido,
                   $produccion->Codigo,
                   $produccion->modelo,
                   $produccion->VS,
                   $produccion->Cantidad,
                   $produccion->TVS,
                 //  $produccion->cant,
                   //$produccion->tvs,
                    ]);	
                    $fila ++;
                }
    });         
    })->export('xlsx');
           }else {
            return redirect()->route('auth/login');
    }
    }
}
