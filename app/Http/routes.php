<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */
use App\Grupo;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\OP;
use Illuminate\Support\Facades\DB;
use App\User;
use App\SAP;
Route::get('/', 'HomeController@index');
Route::get('/home',
    [
        'as' => 'home',
        'uses' => 'HomeController@index',
    ]);

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
 */
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
 */
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' => 'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);
Route::post('passwordUpdate', ['as' => 'passwordUpdate', 'uses' => 'Auth\FunctionsController@cambioPasswordUsers']);
Route::get('viewpassword', ['as' => 'viewpassword', 'uses' => 'Auth\FunctionsController@viewpassword']);
/*
|--------------------------------------------------------------------------
| MOD00-ADMINISTRADOR Routes
|--------------------------------------------------------------------------
 */
Route::get('MOD00-ADMINISTRADOR', 'Mod00_AdministradorController@index');

route::get('setpassword', function () {
    try {
        $password = Hash::make('1234');
        DB::table('dbo.RPT_Usuarios')
            ->where('nomina', '1')
            ->update(['password' => $password]);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    echo 'hecho';
});

Route::post('cambio.password', 'Mod00_AdministradorController@cambiopassword');


Route::get('admin/users', 'Mod00_AdministradorController@showUsers');
Route::get('users/edit/{empid}', 'Mod00_AdministradorController@editUser');
Route::get('admin/detalle-depto/{depto}', 'Mod00_AdministradorController@showUsers');
Route::get('datatables.showusers', 'Mod00_AdministradorController@DataShowUsers')->name('datatables.showusers');
//--nuevas rutas 27/08/2018');
Route::get('admin/plantilla/{depto}', 'Mod00_AdministradorController@PlantillaExcel');
Route::get('admin/Plantilla_PDF/{depto}', 'Mod00_AdministradorController@Plantilla_PDF');

//Rutas Reportik
Route::post('cambio.depto', 'Mod00_AdministradorController@cambiodepto');
Route::post('cambio.reporte', 'Mod00_AdministradorController@cambioreporte');
Route::get('admin/departamentos', 'Mod00_AdministradorController@showdepartamentos');
Route::get('admin/reportes', 'Mod00_AdministradorController@showreportes');
Route::get('admin/reportes/borrar/{id}', 'Mod00_AdministradorController@borrarReporte');
Route::post('admin/addUser', 'Mod00_AdministradorController@addUser');
Route::post('admin/addReporte', 'Mod00_AdministradorController@addReporte');
Route::post('admin/addDepto', 'Mod00_AdministradorController@addDepto');
Route::get('admin/departamentos/borrar/{id}', 'Mod00_AdministradorController@borrarDepto');
Route::get('admin/departamentos/modificar/{id}', 'Mod00_AdministradorController@modificarDepto');
Route::get('admin/users/modificar/{id}', 'Mod00_AdministradorController@modificarUser');
Route::get('admin/users/modificar/accesos/borrar/{id}', 'Mod00_AdministradorController@borrarUserReporte');
Route::post('admin/modificar/usuario', 'Mod00_AdministradorController@UpdateUser');
Route::post('admin/addReporte/usuario', 'Mod00_AdministradorController@AutorizarReporte');
Route::get('home/RECURSOS HUMANOS/CATALOGO DE RECURSOS HUMANOS', 'Mod_RHController@R009A');
Route::get('home/R009APDF', 'Mod_RHController@R009APDF');
Route::get('home/R009AXLS', 'Mod_RHController@R009AXLS');

//Ruta generica para guardar ajaxtoSession
Route::post('home/reporte/ajaxtosession/{id}', 'HomeController@AjaxToSession');

//rutas del reporte de materias primas de reportik
Route::get('home/ALMACEN GENERAL/013 ENTRADAS EXTERNAS', 'HomeController@showModal');
Route::post('home/reporte/013 ENTRADAS EXTERNAS', 'Mod_AlmacenGralController@R013');
Route::get('datatables.showentradas', 'Mod_AlmacenGralController@DataShowEntradas')->name('datatables.showentradas');
Route::get('home/reporte/R013PDF', 'Mod_AlmacenGralController@R013PDF');
Route::get('home/reporte/R013XLS', 'Mod_AlmacenGralController@R013XLS');

//rutas reporte 014 articulos reportik
Route::get('home/ALMACEN GENERAL/014-A INVENTARIO GRAL', 'Mod_AlmacenGralController@R014A');
//Route::post('home/reporte/014-A INVENTARIO GRAL', 'Mod_AlmacenGralController@R014A');
Route::get('datatables.show014', 'Mod_AlmacenGralController@Data_R014A')->name('datatables.show014');
Route::get('home/ALMACEN GENERAL/R014APDF', 'Mod_AlmacenGralController@R014APDF');
Route::get('home/ALMACEN GENERAL/R014AXLS', 'Mod_AlmacenGralController@R014AXLS');
// reporteador / public / home / ALMACEN GENERAL / R014XLS

//Rutas reporte 003-A auditoria costos compras
Route::get('home/COMPRAS/003-A REPORTE PRECIOS MATERIAS PRIMAS', 'HomeController@showModal');
Route::post('home/reporte/003-A REPORTE PRECIOS MATERIAS PRIMAS', 'Mod_ComprasController@R003A');
Route::get('datatables.show003a', 'Mod_ComprasController@Data_R003A')->name( 'datatables.show003a');
Route::get('home/reporte/R003AXLS', 'Mod_ComprasController@R003AXLS');
Route::get('home/reporte/R003APDF', 'Mod_ComprasController@R003APDF');

//Reporte CXC
Route::get('home/FINANZAS/PROVISION CXC', 'Mod_RPTFinanzasController@index');

//Reporte Gerencial
Route::get('home/CONTABILIDAD/01 CAPTURA DE HISTORICO', 'Mod_RG01Controller@index');
Route::post('home/RG01-guardar', 'Mod_RG01Controller@store');
Route::post('home/CONTABILIDAD/checkctas', 'Mod_RG01Controller@checkctas');

Route::get('home/CONTABILIDAD/02 RELACIONAR PDF', 'Mod_RG02Controller@index');
Route::post('home/RG02-guardar', 'Mod_RG02Controller@store');

Route::get('home/CONTABILIDAD/03 REPORTE GERENCIAL', 'Mod_RG03Controller@index');
Route::post('home/RG03-reporte', 'Mod_RG03Controller@reporte');

Route::post('home/CONTABILIDAD/ajustesfill', 'Mod_RG03Controller@ajustesfill');
Route::get('home/ReporteGerencial/{opcion}', 'Mod_RG03Controller@RGPDF');

//Rutas del Módulo de inventarios
Route::get('admin/altaInventario', 'Mod00_AdministradorController@altaInventario');
Route::post('admin/altaInventario', 'Mod00_AdministradorController@altaInventario2');
Route::post('admin/ModInventario', 'Mod00_AdministradorController@ModInventario');
Route::get('/admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor');
Route::post('admin/altaMonitor', 'Mod00_AdministradorController@altaMonitor2');
Route::get('admin/inventario', 'Mod00_AdministradorController@inventario');
Route::get('admin/inventarioObsoleto', 'Mod00_AdministradorController@inventarioObsoleto');
Route::get('admin/monitores', 'Mod00_AdministradorController@monitores');
Route::get('admin/mark_obs/{id}', 'Mod00_AdministradorController@mark_obs');
Route::get('admin/mark_rest/{id}', 'Mod00_AdministradorController@mark_rest');
Route::get('admin/delete_inv/{id}', 'Mod00_AdministradorController@delete_inv');
Route::get('admin/mod_inv/{id}/{mensaje}', 'Mod00_AdministradorController@mod_inv');
Route::get('admin/mod_mon/{id}/{mensaje}', 'Mod00_AdministradorController@mod_mon');
Route::post('admin/mod_mon2', 'Mod00_AdministradorController@mod_mon2');
Route::post('admin/mod_inv2', 'Mod00_AdministradorController@mod_inv2');
Route::get('admin/generarPdf/{id}', 'Mod00_AdministradorController@generarPdf');

Route::get('controlPiso', 'Mod01_ProduccionController@estacionSiguiente');
Route::get('grupo/{id}', function ($id) {
    Grupo::getInfo($id);
});
Route::get('admin/grupos/{id}', 'Mod00_AdministradorController@editgrupos');
Route::post('admin/createModulo/{id}', 'Mod00_AdministradorController@createModulo');
Route::post('admin/createMenu/{id}', 'Mod00_AdministradorController@createMenu');
Route::post('admin/createTarea/{id_grupo}', 'Mod00_AdministradorController@createTarea'); //si se usa
Route::get('admin/grupos/delete_modulo/{grupo}/{id}', 'Mod00_AdministradorController@deleteModulo');
Route::get('admin/grupos/conf_modulo/{grupo}/{id}', 'Mod00_AdministradorController@confModulo');
Route::get('admin/grupos/conf_modulo/{grupo}/quitar-tarea/{id}', 'Mod00_AdministradorController@deleteTarea');
Route::get('help', function () {

    $produccion = DB::select('SELECT "CP_ProdTerminada"."orden", "CP_ProdTerminada"."Pedido", "CP_ProdTerminada"."Codigo",
 "CP_ProdTerminada"."modelo", "CP_ProdTerminada"."VS", "CP_ProdTerminada"."fecha",
 "CP_ProdTerminada"."Name", "CP_ProdTerminada"."CardName", "CP_ProdTerminada"."Semana",
 "CP_ProdTerminada"."U_Tiempo", "CP_ProdTerminada"."Cantidad", "CP_ProdTerminada"."TVS",
 "CP_ProdTerminada"."TTiempo"
 FROM   "FUSIONL"."dbo"."CP_ProdTerminada" "CP_ProdTerminada"
 WHERE  ("CP_ProdTerminada"."fecha">=\'12/12/2017\' AND
 "CP_ProdTerminada"."fecha"<=\'12/12/2017\') AND
 ("CP_ProdTerminada"."Name"= (\'175 Inspeccion Final\')  OR "CP_ProdTerminada"."Name"= (CASE
 WHEN  \'175 Inspeccion Final\' like \'175%\' THEN N\'08 Inspeccionar Empaque\'
 END))
 ');

    print_r($produccion);

    dd(date('Y-m-d H:i:s'));
    $index = 1;
    $log = LOGOF::where('id', 1000)->first();
    // dd($log);
    //    $newCode = new OP();
    //    $newCode->Code =12121212;
    //    $newCode->save();
    //    $varOP = OP::find(12121212);
    $consecutivo = DB::select('SELECT TOP 1 Code FROM  [FUSIONL2].[dbo].[@CP_LOGOT] ORDER BY  U_FechaHora DESC');
    //$consecutivo = ((int)$users->Code);
    echo $consecutivo[0]->Code;
    // echo $log->U_CT;

});
Route::get('datatable/{idGrup}/{idMod}', 'Mod00_AdministradorController@confModulo');
Route::get('datatables.data', 'Mod00_AdministradorController@anyData')->name('datatables.data');
Route::get('getAutocomplete', function () {
    return view('Mod07_Calidad.RechazoFrame');
})->name('getAutocomplete');

Route::get('search', array('as' => 'search', 'uses' => 'Mod07_CalidadController@search'));
Route::get('autocomplete', array('as' => 'autocomplete', 'uses' => 'Mod07_CalidadController@autocomplete'));
/*
|--------------------------------------------------------------------------
|NOTICIAS Y NOTIFICACIONES 'BRAYAN'
|--------------------------------------------------------------------------
 */

Route::post('admin/Nueva', 'Mod00_AdministradorController@Noticia2');
Route::get('admin/Notificaciones', 'Mod00_AdministradorController@Notificacion');
Route::post('admin/Notificaciones', 'Mod00_AdministradorController@Notificacion2');
Route::get('admin/Mod_Noti/{id}/{mensaje}', 'Mod00_AdministradorController@Mod_Noti');
Route::post('admin/Mod_Noti2/', 'Mod00_AdministradorController@Mod_Noti2');
Route::get('admin/delete_Noti/{id}', 'Mod00_AdministradorController@delete_Noti');
Route::post('admin/delete_Noti/', 'Mod00_AdministradorController@delete_Noti');
//Route::get('admin/Nueva', 'Mod00_AdministradorController@Show');
/*
|--------------------------------------------------------------------------
| Finaliza Rutas Noticias y Notificaciones
|--------------------------------------------------------------------------
 */

Route::get('updateprivilegio', 'Mod00_AdministradorController@updateprivilegio');
Route::get('dropdown', function () {
    return TAREA_MENU::where('id_menu_item', Input::get('option'))
        ->lists('name', 'id');
});
Route::get('home/pdf', 
function(){
    error_reporting(E_ALL);
    $pdf = PDF::loadHTML('<h1>Styde.net</h1>');
  
    return $pdf->download('mi-archivo.pdf');
}
);

Route::get('switch', function () {
    $vava = MODULOS_GRUPO_SIZ::find(2);
    $vava->id_menu = null;
    $vava->save();
    var_dump(count(MODULOS_GRUPO_SIZ::find(1)));
});
Route::post('nuevatarea', 'Mod00_AdministradorController@nuevatarea');

/*
|--------------------------------------------------------------------------
| MOD01-PRODUCCION Routes
|--------------------------------------------------------------------------
 */
Route::get('home/R. PROD. GRAL.', 'Reportes_ProduccionController@produccion1');
Route::post('home/R. PROD. GRAL.', 'Reportes_ProduccionController@produccion1');
Route::get('home/TRASLADO ÷ AREAS', [
    'as' => 'traslado', 'uses' =>'Mod01_ProduccionController@traslados']);
Route::post('home/TRASLADO ÷ AREAS', 'Mod01_ProduccionController@traslados');
Route::get('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
Route::post('home/TRASLADO ÷ AREAS/{id}', 'Mod01_ProduccionController@getOP');
//la siguiente ruta avanza la orden //
Route::post('home/traslados/avanzar', 'Mod01_ProduccionController@avanzarOP');
Route::post('home/traslados/Reprocesos', 'Mod01_ProduccionController@Retroceso');
//Route::get('home/traslados/Reprocesos', 'Mod01_ProduccionController@getOP');
Route::post('/', 'HomeController@index');
Route::get('Mod01_Produccion/Noticias', 'HomeController@create');
Route::get('leido/{id}', 'HomeController@UPT_Noticias');
Route::post('/leido', 'HomeController@UPT_Noticias');

// PDF de Historial por OP
Route::get('home/ReporteOpPDF/{op}', 'Mod01_ProduccionController@ReporteOpPDF');
Route::get('home/ReporteMaterialesPDF/{op}', 'Mod01_ProduccionController@ReporteMaterialesPDF');
Route::get('home/ReporteProduccionPDF', 'Reportes_ProduccionController@ReporteProduccionPDF');
Route::get('home/ReporteProduccionEXL', 'Reportes_ProduccionController@ReporteProduccionEXL');

Route::get('admin/aux', function () {
   dd(User::isProductionUser());
});

Route::get('home/NUEVO RECHAZO', 'Mod07_CalidadController@Rechazo');
Route::post('RechazosNuevo', 'Mod07_CalidadController@RechazoIn');
Route::get('Mod07_Calidad/Mod_Rechazo/{id}/{mensaje}', 'Mod07_CalidadController@Mod_Rechazo');
Route::post('Mod07_Calidad/Mod_RechazoUPDT', 'Mod07_CalidadController@Mod_RechazoUPDT');
Route::get('admin/Delete_Rechazo/{id}', 'Mod07_CalidadController@Delete_Rechazo');
Route::post('admin/Delete_Rechazo/', 'Mod07_CalidadController@Delete_Rechazo');
Route::get('search/autocomplete', 'Mod07_CalidadController@autocomplete');
Route::post('/pdfRechazo', 'Mod07_CalidadController@Pdf_Rechazo');
Route::get('home/REPORTE DE RECHAZOS', 'Mod07_CalidadController@Reporte');
Route::get('home/CANCELACIONES', 'Mod07_CalidadController@Cancelado');
Route::get('borrado/{id}', 'Mod07_CalidadController@UPT_Cancelado');
Route::post('/borrado', 'Mod07_CalidadController@UPT_Cancelado');
Route::get('home/HISTORIAL', 'Mod07_CalidadController@Historial');
Route::post('/excel', 'Mod07_CalidadController@excel');
////reporte calidad
Route::get('home/CALIDAD POR DEPTO','Mod07_CalidadController@repCalidad' );
Route::post('home/CALIDAD POR DEPTO','Mod07_CalidadController@repCalidad2' );

//RUTAS 112-CORTE PIEL///
Route::get('home/112 CORTE DE PIEL','Mod01_ProduccionController@repCortePiel' );
Route::post('home/112 CORTE DE PIEL','Mod01_ProduccionController@repCortePiel' );
Route::post('home/reporte/DetinsPiel', 'Mod01_ProduccionController@repCortePiel');
Route::get('home/repCortePielExl', 'Mod01_ProduccionController@repCortePielExl');
//
//-------------------------//
//RUTAS DE RECURSOS HUMANOS//---------------------------------------------------------
//-------------------------//
//
Route::get('home/CALCULO DE BONOS', 'Mod10_RhController@parametrosmodal');
//Route::get('home/rh/reportes/bonos','Mod10_RhController@calculoBonos');
Route::post('home/rh/reportes/bonos', 'Mod10_RhController@calculoBonos');
Route::get('home/PARAMETROS BONOS', 'Mod10_RhController@setParametrosBonos');
Route::post('home/PARAMETROS BONOS', 'Mod10_RhController@setParametrosBonos2');
Route::get('home/rh/reportes/bonosPdf', 'Mod10_RhController@bonosPdf');
Route::get('home/BONOS CORTE','Mod10_RhController@bonosCorte' );
Route::post('home/rh/reportes/bonosCorte', 'Mod10_RhController@calculoBonosCorte');
Route::get('home/rh/reportes/bonoscortePdf', 'Mod10_RhController@bonoscortePdf');
Route::get('home/rh/reportes/bonoscorteEXL', 'Mod10_RhController@bonoscorteEXL');
Route::get('home/mod_parametro/{id}', 'Mod10_RhController@mod_parametro');
Route::post('home/mod_parametro2/{id}', 'Mod10_RhController@mod_parametro2');
Route::get('home/delete_parametro/{id}', 'Mod10_RhController@delete_parametro');
//
//-------------------------//
//RUTAS DE COMPRAS//---------------------------------------------------------
//-------------------------//
//
Route::get('home/CONSULTA OC', 'Mod03_ComprasController@pedidosCsv');
Route::post('home/CONSULTA OC', 'Mod03_ComprasController@postPedidosCsv');
Route::get('home/desPedidosCsv', 'Mod03_ComprasController@desPedidosCsv');
Route::get('home/PedidosCsvPDF', 'Mod03_ComprasController@PedidosCsvPDF');
///Ruta Ayudas
Route::get('home/ayudas_pdf/{PdfName}', 'HomeController@showPdf');