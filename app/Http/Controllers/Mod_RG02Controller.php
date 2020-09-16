<?php
namespace App\Http\Controllers;

use App;
use App\Helpers\BalanzaImport;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_RG02Controller extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $periodos = DB::select("select 
                                    BC_Ejercicio,	
                                    sum(BC_Movimiento_01) m_01,	
                                    sum(BC_Movimiento_02)m_02,	
                                    sum(BC_Movimiento_03)m_03,	
                                    sum(BC_Movimiento_04)m_04, 
                                    sum(BC_Movimiento_05)m_05,	
                                    sum(BC_Movimiento_06)m_06,	
                                    sum(BC_Movimiento_07)m_07,	
                                    sum(BC_Movimiento_08)m_08,	
                                    sum(BC_Movimiento_09)m_09, 
                                    sum(BC_Movimiento_10)m_10,	
                                    sum(BC_Movimiento_11)m_11,	
                                    sum(BC_Movimiento_12)m_12 from RPT_BalanzaComprobacion 
                                    group by BC_Ejercicio");
            
                                    $reportes = ['NOTAS', 'OBSERVACIONES', 'VENTAS', 'COMPRAS', 'ALMACEN'];
            
            $cbo_periodos = [];
            foreach ($periodos as  $value) {
                $value = collect($value);

                for ($i=1; $i <= 12; $i++) { 
                    if ($i < 10) {
                        $index = '0'.$i;
                    }else {
                        $index = $i;
                    }
                    if (!is_null($value["m_".$index])) {
                       $cbo_periodos[] = $value["BC_Ejercicio"].'-'.$index;
                    }
                }
            }
            $cbo_periodos = array_reverse($cbo_periodos);
            return view('Mod_RG.RG02', compact('actividades', 'ultimo', 'cbo_periodos', 'reportes'));
        }else{
            return redirect()->route('auth/login');
        }
    }
    public function store(Request $request)
    {
        
     //  dd($request->all());
        $validator = Validator::make($request->all(), [
         'archivo' => 'max:5000',
        ]);       
            $validator->after(function ($validator) use ($request){
        if($this->checkExcelFile($request->file('archivo')->getClientOriginalExtension()) == false) {
            //return validator with error by file input name
            $validator->errors()->add('archivo', 'El archivo debe ser de tipo:  pdf');
        }
    });
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }
        $periodo = explode('-', Input::get('cbo_periodo'));
       // dd(Input::all() );
        $reporte =  Input::get('cbo_reporte');
        $ejercicio = $periodo[0];
        $periodo = $periodo[1];
        $nombre = Input::get('cbo_periodo').'_'.$reporte.'.pdf';
        $fileupdate = false;
        if(\Storage::disk('pdf_reporte_gerencial')->has($nombre)){
            \Storage::disk('pdf_reporte_gerencial')->delete($nombre);
            $fileupdate = true;
        }
        \Storage::disk('pdf_reporte_gerencial')->put($nombre,  \File::get($request->file('archivo')));
        
        $exists = \Storage::disk('pdf_reporte_gerencial')->exists($nombre);
        if($exists && $fileupdate == false){
            DB::insert('insert into RPT_RG_Documentos (DOC_ejercicio, DOC_periodo, 
            DOC_nombre, DOC_tipo) values (?, ?, ?, ?)', 
            [$ejercicio, $periodo, $nombre, $reporte]);
        
            Session::flash('mensaje','Archivo guardado!!.');
        }else if($exists && $fileupdate == true){
            Session::flash('mensaje','Archivo actualizado!!.');
        }else{
            Session::flash('error','Archivo no se puede guardar!!.');
        }

           return redirect()->back();
    }
     

function checkExcelFile($file_ext){
    $valid=array(
        'pdf' // add your extensions here.
    );        
    return in_array($file_ext,$valid) ? true : false;
}   
}
