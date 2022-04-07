<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\LOGOT;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Auth;
use DB;
use App\OP;
use App\User;
use Mail;
use Session;
use Response;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       // return view('welcome');
        $user = Auth::user();
        $actividades = $user->getTareas();
        $cxc_provisiones = DB::select("SELECT RPT_Alertas.*, RPT_ProvisionCXC.PCXC_ID FROM RPT_Alertas
                    INNER JOIN RPT_ProvisionCXC ON RPT_Alertas.ALERT_Clave = RPT_ProvisionCXC.PCXC_ID                        
                     WHERE ALERT_Modulo = 'RPTFinanzasController' AND ALERT_FechaAlerta <= GETDATE() AND ALERT_Eliminado = 0 
                     AND ALERT_Usuarios like '%" . Auth::user()->nomina."%'");
        
        if (count($cxc_provisiones) > 0) {
            $ruta = 'PROVISION CXC';
            $result = DB::table('RPT_routes_log')
                ->where('Usuario', Auth::user()->nomina)
                ->where('route', $ruta)
                ->update(['ultimaFecha' => (new \DateTime('now'))->format('Y-m-d H:i:s')]);
            if ($result > 1) { // borrar si hay mas de 2 registros iguales
                DB::table('RPT_routes_log')
                    ->where('Usuario', Auth::user()->nomina)
                    ->where('route', $ruta)->delete();
                $result = 0;
            }

            if ($result == 0) { //insertar si no hay algun registro
                DB::table('RPT_routes_log')->insert(
                    ['route' => $ruta, 'Usuario' => Auth::user()->nomina, 'ultimaFecha' => (new \DateTime('now'))->format('Y-m-d H:i:s')]
                );
            }
        }

        // Finaliza notificacion de Mod4 Traslado Recepcion

        $links = DB::select('Select top 6 l.route, tm.Descripcion as tarea, mg.Nombre as modulo from RPT_routes_log l
                inner join RPT_Reportes tm on tm.Descripcion = l.route
				left join RPT_Departamentos mg on mg.Id = tm.REP_DEP_Id
                where l.Usuario = ?
                and tm.Descripcion is not null 
                group by tm.Descripcion, mg.Nombre, l.route
                order by tm.Descripcion, mg.Nombre', [Auth::user()->nomina]);
        
        return view('homeIndex',   ['links' => $links, 'cxc_count_provisiones' => count($cxc_provisiones) , 'actividades' => $actividades, 'ultimo' => count($actividades), 'isAdmin'=> User::isAdmin()]);
    }

    public function UPT_Noticias($id){
     
       DB::table('Siz_Noticias')
        ->where('Id', $id)
        ->update(['Leido' => 'Si']);
        $user = Auth::user();
                
       return redirect()->back();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
*/
    public function create(Request $request)
    { 
        $user = Auth::user();
        $actividades = $user->getTareas();
        $id_noticia=$request->input("id");
      //dd($id_noticia);

        $id_user=Auth::user()->U_EmpGiro;
    
        $noticias=DB::select(DB::raw("SELECT * FROM Siz_Noticias WHERE Destinatario='$id_user'and Leido='N'"));
        //dd
      return view('Mod01_Produccion/Noticias', ['actividades' => $actividades,'noticias' => $noticias,'id_user' => $id_user, 'ultimo' => count($actividades)]);
    }

    public function showPdf($PdfName){
        $filename = "assets\\ayudas_pdf\\".$PdfName;
        $path = public_path($filename);
         return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
             'Content-Disposition' => 'inline; filename="'.$filename.'"'
         ]);
    }

    public function showModal(Request $request)
    {
        
        $arrayurl = explode('/', $request->path());
        $nombre = str_replace('%20', ' ', $arrayurl[count($arrayurl) - 1]);
        $nombre = str_replace('%C3%B7', '&#247;', $nombre);
        $target = '_blank';
        $fechas = false;
        $unafecha = false;
        $fieldOtroNumber = '';
        $Text = '';
        $fieldText = '';
        $text_selUno = '';
        $data_selUno = [];
        $text_selDos = '';
        $data_selDos = [];
        $text_selTres = '';
        $data_selTres = [];
        $text_selCuatro = '';
        $data_selCuatro = [];
        $text_selCinco = '';
        $data_selCinco = [];
        $sizeModal = 'modal-sm';
        $data_table = '';
        $btn3 = '';
        $btnSubmitText = 'Generar';
        $disabled = '';
        switch ($nombre) {
            
            case "01 CAPTURA DE HISTORICO":
                $Text = 'Seleccione una Sociedad.';
                $text_selUno = 'Sociedad';
                $sociedades = DB::table('RPT_Sociedades')
                                    ->where('SOC_Reporte', 'ReporteGerencial')
                                    ->lists('SOC_Nombre');                
                $data_selUno = $sociedades;
                $target = '_self';//ejecutar en la misma pagina
                break;
            case "02 RELACIONAR PDF":
                $Text = 'Seleccione una Sociedad.';
                $text_selUno = 'Sociedad';
                $sociedades = DB::table('RPT_Sociedades')
                                    ->where('SOC_Reporte', 'ReporteGerencial')
                                    ->lists('SOC_Nombre');                
                $data_selUno = $sociedades;

                $target = '_self';//ejecutar en la misma pagina
                break;
            case "03 REPORTE GERENCIAL":
                $Text = 'Seleccione una Sociedad.';
                $text_selUno = 'Sociedad';
                $sociedades = DB::table('RPT_Sociedades')
                                    ->where('SOC_Reporte', 'ReporteGerencial')
                                    ->lists('SOC_Nombre');                
                $data_selUno = $sociedades;

                $target = '_self';//ejecutar en la misma pagina
                break;
            case "05 PRESUPUESTOS":
                $Text = 'Seleccione una Sociedad.';
                $text_selUno = 'Sociedad';
                $sociedades = DB::table('RPT_Sociedades')
                                    ->where('SOC_Reporte', 'ReporteGerencial')
                                    ->lists('SOC_Nombre');                
                $data_selUno = $sociedades;
                
                $target = '_self';//ejecutar en la misma pagina
                break;
            case "06 REPORTE PRESUPUESTOS":
                $Text = 'Seleccione una Sociedad.';
                $text_selUno = 'Sociedad';
                $sociedades = DB::table('RPT_Sociedades')
                                    ->where('SOC_Reporte', 'ReporteGerencial')
                                    ->lists('SOC_Nombre');                
                $data_selUno = $sociedades;
                
                $target = '_self';//ejecutar en la misma pagina
                break;
            case "03 KARDEX POR OV":
                //$Text = 'Seleccione una Orden de Venta.';
                //$fieldText = 'Código';
                $sizeModal = 'modal-lg';
                $data_table = 'OrdenesVenta.all';
                //$target = '_self';//ejecutar en la misma pagina
                $disabled = 'disabled';
                break;
            case "013 ENTRADAS EXTERNAS":
                $fechas = true;
                $text_selUno = 'Sociedad';
                $data_selUno = ['ITEKNIA', 'COMERCIALIZADORA', 'MULIIX'];
                break;
            case "003-A REPORTE PRECIOS MATERIAS PRIMAS":
                $text_selUno = 'Sociedad';
                $data_selUno = ['ITEKNIA', 'COMERCIALIZADORA'];
                $text_selDos = 'Tipo';
                $data_selDos = ['COMPLETO', 'SOLO ESTANDAR EN CERO'];
                break;
            
        }


        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            return view('modalParametros',
                [
                    'disabled' => $disabled,
                    'target' => $target,
                    'actividades' => $actividades,
                    'ultimo' => count($actividades),
                    'nombre' => $nombre,
                    'fieldOtroNumber' => $fieldOtroNumber,
                    'text' => $Text,
                    'fieldText' => $fieldText,
                    'fechas' => $fechas,
                    'text_selUno' => $text_selUno,
                    'data_selUno' => $data_selUno,
                    'text_selDos' => $text_selDos,
                    'data_selDos' => $data_selDos,
                    'text_selTres' => $text_selTres,
                    'data_selTres' => $data_selTres,
                    'text_selCuatro' => $text_selCuatro,
                    'data_selCuatro' => $data_selCuatro,
                    'text_selCinco' => $text_selCinco,
                    'data_selCinco' => $data_selCinco,
                    'sizeModal' => $sizeModal,
                    'data_table' => $data_table,
                    'btn3' => $btn3,
                    'btnSubmitText' => $btnSubmitText,
                    'unafecha' => $unafecha
            ]);
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function AjaxToSession($id)
    {
        Session::put($id, Input::get('arr'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
