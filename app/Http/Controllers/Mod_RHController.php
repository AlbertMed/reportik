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

class Mod_RhController extends Controller
{
   public function R009A(){
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);

        $data = DB::select("Select  EMP_CodigoEmpleado, 
                            EMP_Nombre + ' ' + EMP_PrimerApellido + ' ' + EMP_SegundoApellido AS Nombre, 
                            ISNULL (DEP_Nombre, 'SIN DEPTO...') AS Departamento, 
                            ISNULL (PUE_Nombre, 'SIN PUESTO...') AS Puesto, 
                            EMP_FechaEgreso As Estatus 
                            from Empleados 
                            left join Departamentos on DEP_DeptoId = EMP_DEP_DeptoId 
                            left join Puestos on PUE_PuestoId = EMP_PUE_PuestoId 
                            Where EMP_FechaEgreso Is Null 
                            Order by Departamento, Puesto, Nombre");
        Session::put('DATA_R009A', $data);      
        return view('Mod_RH.009-A', compact('actividades', 'ultimo', 'data'));
    }else{
        return redirect()->route('auth/login');
    }
   }
   public function R009APDF(){
    if (Auth::check()) {          
        $data = Session::get('DATA_R009A');    
        $pdf = \PDF::loadView('Mod_RH.009APDF', compact('data'));
        //$pdf = new FPDF('L', 'mm', 'A4');
        $pdf->setOptions(['isPhpEnabled' => true]);
        return $pdf->stream('Reporte_009A ' . ' - ' . date("d/m/Y") . '.Pdf');       
    }else{
        return redirect()->route('auth/login');
    }
   }
   public function R009AXLS(){
    if (Auth::check()) {    
        $data = Session::get('DATA_R009A');    
        Excel::create('Reporte_009-A' . ' - ' . $hoy = date("d/m/Y").'', function($excel)use($data) {
            $excel->sheet('Hoja 1', function($sheet) use($data){
               //$sheet->margeCells('A1:F5');     
               $sheet->row(1, [
                  'No. 009-A'
               ]);
               $sheet->row(2, [
                  'ITEKNIA EQUIPAMIENTO, S.A DE C.V.'
               ]);
               $sheet->row(3, [
                  'CATALOGO DE RECURSOS HUMANOS'
               ]);
               $sheet->row(4, [
                  'FECHA DE IMPRESION: '.\AppHelper::instance()->getHumanDate(date("Y-m-d")),
               ]);
               $sheet->row(5, [
                  'CODIGO','NOMBRE','DEPARTAMENTO','PUESTO'
               ]);
              //Datos    
              $fila = 6;     
           foreach ( $data as $rep){          
               $sheet->row($fila, 
               [
                    $rep->EMP_CodigoEmpleado,    
                    $rep->Nombre,
                    $rep->Departamento,
                    $rep->Puesto 
                   ]);	
                   $fila ++;
               }
   });         
   })->export('xlsx');
    }else{
        return redirect()->route('auth/login');
    }
   }

}
