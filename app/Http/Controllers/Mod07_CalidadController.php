<?php
namespace App\Http\Controllers;
use App\Grupo;
use App\Modelos\MOD01\MENU_ITEM;
use App\Modelos\MOD01\MODULOS_SIZ;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\User;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Auth;
use Lava;
//excel
use Maatwebsite\Excel\Facades\Excel;
//DOMPDF
use Dompdf\Dompdf;
use App;
//use Pdf;
//Fin DOMPDF
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Datatables;
class Mod07_CalidadController extends Controller
{
    public function Rechazo()
   {
    //$var =  DB::table('OCRD')->where('CardType', 'S')->whereNotNull('CardName')->lists('CardName','CardCode');
    //list($CodeP, $NameP) = array_divide($var);
    $resultProveedores =  DB::select('SELECT CardName, CardCode FROM OCRD WHERE CardType = \'S\' AND CardName IS NOT NULL');
    $CardNames = array_pluck($resultProveedores, 'CardName');
    $CardCodes = array_pluck($resultProveedores, 'CardCode');
    //dd($var);
    //DB::table('OITM')->lists('ItemName','ItemCode');
    //list($CodeMat,$NameM)=array_divide($Material);
    // $CodeMat2=array_map('strval', $CodeMat);
    $resultMateriales = DB::select('SELECT ItemCode, ItemName, InvntryUom AS UM FROM OITM WHERE PrchseItem = \'Y\' AND InvntItem = \'Y\'');
    $ItemNames = array_pluck($resultMateriales, 'ItemName');
    $ItemCodes = array_pluck($resultMateriales, 'ItemCode');
    
    $user = Auth::user();
    $actividades = $user->getTareas();
    //dd($actividades );
    return view('Mod07_Calidad.rechazosNuevo',['Material'=>$resultMateriales,'CodeMat'=>$ItemCodes,'NameM'=>$ItemNames,'CodeP'=>$CardCodes,'NameP'=>$CardNames,'var'=>$resultProveedores,'actividades' => $actividades, 'ultimo' => count($actividades)]);
   }

public function RechazoIn(Request $request)   
    {
    if($request->input('Fech_Recp')<=($request->input('Fech_Rev')))
    {
 
      //  dd($request->input('Inspector'));
        DB::table('Siz_Calidad_Rechazos')->insert(
            [
                'fechaRecepcion'     =>$request->input('Fech_Recp'),
                'fechaRevision'      =>$request->input('Fech_Rev'),
                'proveedorId'        =>$request->input('Id_prov'),
                'proveedorNombre'    =>$request->input('Proveedor'),
                'materialCodigo'     =>$request->input('Codigo'),
                'materialUM'         =>$request->input('Um'),
                'materialDescripcion'=>$request->input('Material'),
                'cantidadRecibida'   =>$request->input('C_Recibida'),
                'cantidadRevisada'   =>$request->input('C_Revisada'),
                'cantidadAceptada'   =>$request->input('C_Aceptada'),
                'cantidadRechazada'  =>$request->input('C_Rechazada'),
                'DescripcionRechazo' =>$request->input('D_Rechazo'),
                'DocumentoNumero'    =>$request->input('N_Doc'),
                'InspectorNombre'    =>$request->input('Inspector'),
                'Observaciones'      =>$request->input('Observaciones'),
                'Borrado'            =>'N'
                    
        
            ]
        );
            Session::flash('mensaje', 'Registro Guardado');
          return response()->redirectTo('home/NUEVO RECHAZO');
    } 
else{
    return Redirect::back()->with('message' , 'Error,La fecha Revisión es menor a la fecha de Recepción');
}
}
    public function autocomplete(Request $request)
    {
      
       // dd($data);
       return response()->json(DB::table('OCRD')->where('CardType', 'S')->value('CardName'));
    }

   public function Reporte(){
    if(Auth::check()){
        $user = Auth::user();
        $actividades = $user->getTareas();
        //dd($actividades );
        //aqui va tu qwery
        $Proveedores=  DB::select('SELECT proveedorId, proveedorNombre FROM Siz_Calidad_Rechazos group by proveedorId, proveedorNombre');
    
        $Articulos=  DB::select('SELECT materialCodigo, materialDescripcion FROM Siz_Calidad_Rechazos group by materialCodigo, materialDescripcion');

        return view('Mod07_Calidad.Reporte_Rechazos',['Articulos' => $Articulos,'Proveedores' => $Proveedores,'actividades' => $actividades, 'ultimo' => count($actividades)]);
    }else {
        return  redirect()->route('auth/login');
    }
    
   }
   
    public function Pdf_Rechazo(Request $request)
    {
      // $rechazo=DB::select('SELECT* FROM Siz_Calidad_Rechazos');
       //$pdf = App::make('dompdf');
       $fechaIni = $request->input('FechIn');
       $fechaFin = $request->input('FechaFa');
       $sociedad=DB::table('OADM')->value('CompnyName');
       
    
         $prov1= $request->input('prov');
       if($prov1==null){
        $prov1='';
    }
       $btnradio=$request->input('registro');
       if($btnradio==null){
        $btnradio='0';
    }
       $artic1=$request->input('arti');
       if($artic1==null){
        $artic1='';
    }
    $rechazo=null;
    switch ($btnradio) {
        case 0:
        $rechazo=DB::table('Siz_Calidad_Rechazos')
        ->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->get();
            break;
        case 1:
        $rechazo=DB::table('Siz_Calidad_Rechazos')->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->where('cantidadRechazada',">",0)
        ->get();

            break;
        case 2:
        $rechazo=DB::table('Siz_Calidad_Rechazos')
        ->whereBetween('fechaRevision',[$fechaIni ,$fechaFin])
        ->where('proveedorId','LIKE','%'.$prov1.'%')
        ->where('materialCodigo','LIKE','%'.$artic1.'%')
        ->where('cantidadRechazada',0)
        ->get();

            break;
    }
    $Opc_Document = $request->input('expor');

    if($Opc_Document==1){
            $pdf = \PDF::loadView('Mod07_Calidad.RechazoPDF',compact('sociedad','rechazo','fechaIni','fechaFin'));
            $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true]);
            return $pdf->stream('Siz_Calidad_Reporte_Rechazo.Pdf');

    //dd($rechazo);
            $pdf = \PDF::loadView('Mod07_Calidad.RechazoPDF',['sociedad'=>$sociedad,'rechazo'=>$rechazo,'fechaIni'=>$fechaIni,'fechaFin'=>$fechaFin]);
            return $pdf->setPaper('Letter','landscape')->setOptions(['isPhpEnabled'=>true])->stream('Siz_Calidad_Reporte_Rechazo.Pdf');

           // return $pdf->download('ReporteOP.pdf');
        }
        else
        {
            Excel::load('Siz_Calidad_Reporte_Rechazos.xlsx' ,function($excel)use($rechazo) {
               //Header
                $excel->sheet('Hoja1', function($sheet) use($rechazo){

        foreach($rechazo as $R => $Rec) {
            $sheet->row($R+11, [
                "        ",
            date('d/m/Y',strtotime($Rec->fechaRevision)),
             $Rec->proveedorNombre, 
             $Rec->materialCodigo, 
             $Rec->materialDescripcion, 
             $Rec->cantidadRecibida,
             $Rec->cantidadAceptada,
             $Rec->cantidadRechazada,
             $Rec->cantidadRevisada,
             $Rec->cantidadAceptada /$Rec->cantidadRecibida * 100,
             $Rec->InspectorNombre,
             $Rec->DocumentoNumero 
             
    ]);	
                }         
            });
             
            })->export('xlsx');
            
        }
    }



    
    public function Cancelado()
    {
        if(Auth::check()){
            $user = Auth::user();
            $actividades = $user->getTareas();
            $DelRechazo= DB::select("SELECT * FROM Siz_Calidad_Rechazos where Borrado='N'");
            //$Delfechas=DB::select('SELECT fechaRevision FROM Siz_Calidad_Rechazos');
         //dd($DelRechazo);
           return view('Mod07_Calidad.Cancelaciones',['DelRechazo'=>$DelRechazo,'actividades' => $actividades, 'ultimo' => count($actividades)]);
        }else {
            return  redirect()->route('/auth/login');
        }
    
       
    }
    
    public function UPT_Cancelado($id){
     
        DB::table('Siz_Calidad_Rechazos')
         ->where('id', $id)
         ->update(['Borrado' => 'S']);
        
                 
         return redirect()->back();
     }
     public function Historial()
     {
         $user = Auth::user();
         $actividades = $user->getTareas();
         $VerHistorial= DB::select("SELECT * FROM Siz_Calidad_Rechazos where Borrado='S'");
         //$Delfechas=DB::select('SELECT fechaRevision FROM Siz_Calidad_Rechazos');
      //dd($DelRechazo);
      return view('Mod07_Calidad.Historial',['VerHistorial'=>$VerHistorial,'actividades' => $actividades, 'ultimo' => count($actividades)]);
     }
 public function repCalidad2(){
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();      
        //anio_in
       // dd(Input::all());
        $valid = Validator::make(Input::all(), [
            'anio_in' => 'required' ,
            'semana_in' => 'required|unique:Siz_Calidad_Depto,Semana',
            'cor_in'=> 'required',
            'cos_in'=> 'required',
            'coj_in'=> 'required',
            'tap_in'=> 'required',
            'car_in'=> 'required',
        ]);

        if ($valid->fails()) {
            return redirect()->back()
                        ->withErrors($valid)
                        ->withInput();
        }
        DB::table('Siz_Calidad_Depto')
        ->insert(
            [
                'anio'   => Input::get('anio_in'),
                'Semana' => Input::get('semana_in'),
                'CorteIn'=> Input::get('cor_in'),
                'CostIn' => Input::get('cos_in'),
                'CojiIn' => Input::get('coj_in'),
                'TapIn'  => Input::get('tap_in'),
                'CarpIn' => Input::get('car_in'),           
            ]
            );
     
            return redirect()->back();
    } else {
        return redirect()->route('auth/login');
    }
}
public function repCalidad(){
    if (Auth::check()) {
        $user = Auth::user();
        $actividades = $user->getTareas();      
$Indatos = DB::table('Siz_Calidad_Depto')->orderBy('Semana','asc')->get();

         return view('Mod07_Calidad.CalidadDepto',
             ['actividades' => $actividades,
                 'ultimo' => count($actividades),
                 'enviado' => false,
                 'semana_in' => '',
                 'Indatos' => $Indatos
                ]
             );
    } else {
        return redirect()->route('auth/login');
    }
}
}