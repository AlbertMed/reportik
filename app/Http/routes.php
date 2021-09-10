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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Grupo;
use App\Modelos\MOD01\LOGOF;
use App\Modelos\MOD01\MODULOS_GRUPO_SIZ;
use App\Modelos\MOD01\TAREA_MENU;
use App\OP;
use Illuminate\Support\Facades\DB;
use App\User;
use App\SAP;

Route::get('/', 'HomeController@index');
Route::get(
    '/home',
    [
        'as' => 'home',
        'uses' => 'HomeController@index',
    ]
);

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

Route::get('movimientos', function () {
    for ($k = 1; $k <= 12; $k++) { // los 12 periodos
        $peryodo = ($k < 10) ? '0' . $k : '' . $k; // los periodos tienen un formato a 2 numeros, asi que a los menores a 10 se les antepone un 0                                           
        $llave_p = 'BC_Movimiento_' . $peryodo;
        $fil = DB::table('RPT_BalanzaComprobacion')
            ->whereNull($llave_p)->count();
        if ($fil > 0) {

            DB::table('RPT_BalanzaComprobacion')
                ->whereNull($llave_p)
                ->update([$llave_p => 0]);
        }
    }
});
route::get('set-admin-password', function () {
    try {
        $password = Hash::make('sapo133x10');

        DB::table('dbo.RPT_Usuarios')
            ->where('nomina', '002')
            ->update(['password' => $password]);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    echo 'hecho';
});
Route::get('test', 'Mod_FinanzasController@registraPrograma');
route::get('prueba', function () {

     $helper = App\Helpers\AppHelper::instance();
    $sum = $helper->Rg_GetSaldoFinal('601-000-000', '2021', '01', 'RPT_BalanzaComprobacionAzaret');
    dd($sum);
    $periodo = '04';
     for ($i=1; $i <=(int) $periodo; $i++) {
            $peryodo[] = ($i < 10) ? '0' . $i : '' . $i;
           } 
           $sql = DB::table('RPT_RG_Ajustes')
            ->where('AAJU_Id', 'mo')
            ->where('AJU_ejercicio', '2021')
            ->where('AJU_sociedad', 'ITEKNIA EQUIPAMIENTO, S.A. DE C.V.')
            ->whereIn('AJU_periodo', $peryodo)
            ;
            $val = array_sum($sql->lists('AJU_valor'));
            dd($val,  $sql->toSql());
    try {
        $fila = [];
        $provs = DB::table('RPT_ProvisionCXC')
            ->whereNull('PCXC_Semana_fecha')->get();
        //dd($provs);
        foreach ($provs as $prov) {

            $rs = DB::select("select (SUBSTRING( CAST(year('" . $prov->PCXC_Fecha . "') as nvarchar(5)), 3, 2) * 100 + DATEPART(ISO_WEEK, '" . $prov->PCXC_Fecha . "')) as semana");
            $semanaFecha = null;
            if (count($rs) == 1) {
                $semanaFecha = $rs[0]->semana;
            }
            $fila['PCXC_Semana_fecha'] = $semanaFecha;
            DB::table('RPT_ProvisionCXC')
                ->where('PCXC_ID', $prov->PCXC_ID)
                ->update($fila);
            // dd('1');
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    echo 'hecho';
});
route::get('set-users-passwords', function () {
    $users =  null;
    try {

        $users = DB::select('select RPT_Usuarios.nomina, Usuarios.USU_Nombre, USU_Contrasenia,  Empleados.EMP_CodigoEmpleado, Empleados.EMP_Nombre, Empleados.EMP_PrimerApellido from Usuarios
            INNER JOIN RPT_Usuarios on nomina = Usuarios.USU_Nombre
            LEFT JOIN Empleados on Empleados.EMP_EmpleadoId = USU_EMP_EmpleadoId
            WHERE EMP_Activo = 1 and status = 1 ');

        $users = collect($users);

        foreach ($users as $key => $user) {

            DB::table('dbo.RPT_Usuarios')
                ->where('nomina', $user->USU_Nombre)
                ->update(['password' => Hash::make($user->USU_Contrasenia)]);
        }
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    return $users;
});
route::get('print', function () {
    try {

        $zpl_code = "^XA
                    ^FO100, 200
                    ^AD,50,25
                    ^FH_^FD Impresion desde php _7E ^FS
                    ^XZ";
        $zpl2_code = "^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR2,2~SD15^JUS^LRN^CI0^XZ
~DG000.GRF,03584,028,
,:::::::::::::::::::::::::::::::::::::::::::P0540Q0H5R014H4,P02A0Q0HAR02A,O015D5010L0H1HD10O015D5515,P0HAR0HAR02A8A,P0H74040M01750P01774544,P0HA8220N0HAR0HA82,O01DD55D0M01DD0P01DHDI5,P0HA8AA80M0HAR0HA8A,P0H75770M01750P01774544,P02A0AA0N0HAR02A02,P0H51DD010K01DD0010N0151510H010,S0HA80M0HA,Q0417740H054001750H040400140H040H0I454,S0HA80M0HA,P0151DHD4115H501DD015515515H50150115H510,P02A0AHA80AHA800AA02AA0AA8AA802A002AHA,P0H757H7417I7417517740775775077017I740,P02A0AHA82AIA80AA0AA002AJA02A00AJA0,O015F5DID5DFDFD1DD1FD417DFDFD55D15DFDFD0,P0HA8AHA8AKA0AJAH0LA82A82AJA0,P0H75775577557517I75007H757757745755770,P02A0AA80AA00AA0AIA800AHA0AA82A0AA80AA0,O015D5DD01DD115D1DID4015DD15DD5D5DD015D1,P0HA8AA80AA00AA0AIA800AA80AA82A8AA00AA8,P0H75770177557757I74007740575775H5H0574,P02A0AA00AKA0AIAI02A802A82A0A8002A8,O015D5FD01FDFDFD5DJD017DC15DD5D5FD015DC,P0HA8AA80AKA0AJAH0HA80AA82A8A8002A8,P0H757541775J5757740077407757757400574,P02A0AA80AA0J0HA2AA00AA802A82A0A8002A8,O015D5DD01DD00141DD1DD015DC15DD5D5DD015DC,P0HA8AA80AA80280AA0AA80AA80AA82A8AA80AA8,P0H757755775H541751774077405757745755774,P02A0AHA0AHAH2A0AA02A002A802A82A02AA2AA8,O015F5DDFD5DFFDD1DD17DD17DC15DD5D15DFHFDC,P0HA8AHA82AJA0AA02AA0AA80AA82A80AJA8,P0H747H7417I741750577077407757741775774,P02A02AA802AHAH0HAH0HA0AA802A82A002AA2A8,O015D45DDC15DHD41FD01FD15DC15FD5D015DD5D5,P02800AA800AA800A800A8H8A800A828800880A0,P0I40440H0540H0H4H0H404400440440H040040,,g010H010L010L0101010,,::O0101H15151515101L1051151H101451J10,X080P080N080,Q040454554H4104001404I40404404H4504040,S020gH020H0H20,O0H15115H515515H5I1515I51511515H5H15110,,S040J040J040T04,,::::::::::::::::::::::::::::::^XA
^MMT
^PW711
^LL0711
^LS0
^FT480,160^XG000.GRF,1,1^FS
^FT45,266^A0N,19,19^FH\^FDCliente: C00015 - C. VIDANTA VALLARTA^FS
^FT45,311^A0N,19,19^FH\^FDRaz\A2n Social: CONSTRUCTORA Y DESARROLLADORA DE INMUEBLES SA DE CV^FS
^FT45,357^A0N,19,19^FH\^FDContacto: MARIA FERNANDA ROBLES^FS
^FT45,403^A0N,19,19^FH\^FDProyecto: 6134.5 - COJINERIA TORRE CELEBRATE JUNIOR NUEVO VALLARTA^FS
^FT45,449^A0N,19,19^FH\^FDOC: H5O - 000008^FS
^FT45,495^A0N,19,19^FH\^FDOT: OT00065, OT00067^FS
^FT45,541^A0N,19,19^FH\^FDInsumo: 1312453, 1312457^FS
^FT45,587^A0N,19,19^FH\^FDArticulo: ^FS
^FT45,610^A0N,19,19^FH\^FDMM-05-D COJIN DECORATIVO 3.60 X 35^FS
^FT45,633^A0N,19,19^FH\^FDMM-21-A COJIN DECORATIVO 1.65 X 50^FS
^FT39,184^BQN,2,6
^FDMA,BULTO 0104^FS
^FT267,221^A0N,54,76^FH\^FD0104^FS
^FT184,82^A0N,20,21^FB315,1,0,C^FH\^FDIteknia Equipamiento, S.A. de C.V.^FS
^FT184,106^A0N,20,21^FB315,1,0,C^FH\^FDCalle 2 No. 2391, Z. Industrial 44940^FS
^FT184,130^A0N,20,21^FB315,1,0,C^FH\^FDGuadalajara, Jal. Mex.^FS
^FT184,154^A0N,20,21^FB315,1,0,C^FH\^FDTel. 33 3812 3200^FS
^PQ1,0,1,Y^XZ
^XA^ID000.GRF^FS^XZ";
        $fileName = public_path() . "/assets/print.zpl";
        //cambiar formato de file
        //cambiar comando de impresion lpr
        //ingresar una nueva cola de impresion.

        file_put_contents($fileName, $zpl_code);
        //lpr -P 'Zebra_Technologies_ZTC_GC420t_EPL_itk_gna_ubt_200' -o raw ''
        echo "lp -d 'Zebra_Technologies_ZTC_GC420t_EPL_itk_gna_ubt_200' -o raw '" . $fileName . "'";
        exec("lp -d 'Zebra_Technologies_ZTC_GC420t_' -o raw '" . $fileName . "'");
        //la siguiente linea funciono el 13 11 2020
        //lp -d 'Zebra_Technologies_ZTC_GC420t_' -o raw '/opt/lampp/htdocs/reportik/public/assets/print.zpl'

        //lpr -P 'Zebra_Technologies_ZTC_GC420t_' -o raw media=Custom.4x6in -o page-left=0 -o page-right=0 -o page-top=0 -o page-bottom=0 '' 
        //Zebra-Technologies-ZTC-GC420t-(EPL)
    } catch (\Exception $e) {
        echo $e->getMessage();
    }

    echo '      terminado...';
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
Route::get('admin/users/modificar/accesos/borrar/{id}/{nomina}/{reporte}', 'Mod00_AdministradorController@borrarUserReporte');
Route::post('admin/modificar/usuario', 'Mod00_AdministradorController@UpdateUser');
Route::post('admin/addReporte/usuario', 'Mod00_AdministradorController@AutorizarReporte');
Route::get('home/RECURSOS HUMANOS/CATALOGO DE RECURSOS HUMANOS', 'Mod_RHController@R009A');
Route::get('home/R009APDF', 'Mod_RHController@R009APDF');
Route::get('home/R009AXLS', 'Mod_RHController@R009AXLS');

//Ruta generica para guardar ajaxtoSession
Route::post('home/reporte/ajaxtosession/{id}', 'HomeController@AjaxToSession');

//rutas del reporte de materias primas de reportik
Route::get('home/ALMACEN GENERAL/013 ENTRADAS EXTERNAS', 'HomeController@showModal')->middleware('routelog');
Route::post('home/reporte/013 ENTRADAS EXTERNAS', 'Mod_AlmacenGralController@R013');
Route::get('datatables.showentradas', 'Mod_AlmacenGralController@DataShowEntradas')->name('datatables.showentradas');
Route::get('home/reporte/R013PDF', 'Mod_AlmacenGralController@R013PDF');
Route::get('home/reporte/R013XLS', 'Mod_AlmacenGralController@R013XLS');

//rutas reporte 014 articulos reportik
Route::get('home/ALMACEN GENERAL/014-A INVENTARIO GRAL', 'Mod_AlmacenGralController@R014A')->middleware('routelog');
//Route::post('home/reporte/014-A INVENTARIO GRAL', 'Mod_AlmacenGralController@R014A');
Route::get('datatables.show014', 'Mod_AlmacenGralController@Data_R014A')->name('datatables.show014');
Route::get('home/ALMACEN GENERAL/R014APDF', 'Mod_AlmacenGralController@R014APDF');
Route::get('home/ALMACEN GENERAL/R014AXLS', 'Mod_AlmacenGralController@R014AXLS');
// reporteador / public / home / ALMACEN GENERAL / R014XLS

//Rutas reporte 003-A auditoria costos compras
Route::get('home/COMPRAS/003-A REPORTE PRECIOS MATERIAS PRIMAS', 'HomeController@showModal')->middleware('routelog');
Route::post('home/reporte/003-A REPORTE PRECIOS MATERIAS PRIMAS', 'Mod_ComprasController@R003A');
Route::get('datatables.show003a', 'Mod_ComprasController@Data_R003A')->name('datatables.show003a');
Route::get('home/reporte/R003AXLS', 'Mod_ComprasController@R003AXLS');
Route::get('home/reporte/R003APDF', 'Mod_ComprasController@R003APDF');
//Reporte Proyeccion CXC
Route::get('home/SAC/02 PROYECCION CXC', 'Mod_RPT_SACController@index_proyeccion')->middleware('routelog');
Route::get('datatables.cxc_proyeccion', 'Mod_RPT_SACController@data_cxc_proyeccion')->name('datatables.cxc_proyeccion');
//Reporte Provision CXC
Route::get('home/SAC/01 PROVISION CXC', 'Mod_RPT_SACController@index_provision')->middleware('routelog');
Route::any('home/SAC/cxc_guardar_estado_ov', 'Mod_RPT_SACController@guardarEstadoOV');
Route::post('home/SAC/cxc_combobox', 'Mod_RPT_SACController@combobox');
Route::post('home/SAC/cxc_combobox2', 'Mod_RPT_SACController@combobox2');
Route::any('datatables.cxc', 'Mod_RPT_SACController@registros')->name('datatables.cxc');
//Route::any('datatables.resumen_cxc', 'Mod_RPT_SACController@data_resumen_cxc_cliente')->name('datatables.resumen_cxc');
Route::any('datatables.cxc_alertadas', 'Mod_RPT_SACController@registrosOValertadas')->name('datatables.cxc_alertadas');
Route::any('cxc_store_provision', 'Mod_RPT_SACController@guardaProvision')->name('cxc_store_provision');
Route::any('cxc_store_alerta', 'Mod_RPT_SACController@guardaAlerta')->name('cxc_store_alerta');
Route::any('cxc_guarda_edit_alerta', 'Mod_RPT_SACController@guardaEditAlerta')->name('cxc_guarda_edit_alerta');
Route::any('getcantprovision', 'Mod_RPT_SACController@cantprovision')->name('getcantprovision');
Route::any('datatables.cxc_provisiones', 'Mod_RPT_SACController@registros_provisiones')->name('datatables.cxc_provisiones');
Route::any('borra-alerta', 'Mod_RPT_SACController@borraAlerta')->name('borra-alerta');
Route::any('home/SAC/OrdenVenta-registros', 'Mod_RPT_SACController@registros');

Route::any('getcantalertas_cxc', 'Mod_RPT_SACController@getcantalertas')->name('getcantalertas_cxc');
Route::any('borra-prov', 'Mod_RPT_SACController@borraProvision')->name('borra-prov');
Route::any('getconcepto_prov_cxc', 'Mod_RPT_SACController@getconcepto_prov_cxc')->name('getconcepto_prov_cxc');
Route::any('cxc_update_provision', 'Mod_RPT_SACController@actualizaProvision')->name('cxc_update_provision');

//Reporte Kardex por OV 
//SAC/KARDEX%20POR%20OV
Route::get('home/SAC/03 KARDEX POR OV', 'HomeController@showModal')->middleware('routelog');
Route::get('OrdenesVenta.all', 'Mod_RPT_SACController@allOvs')->name('OrdenesVenta.all');
Route::post('home/reporte/03 KARDEX POR OV', 'Mod_RPT_SACController@KardexOV');
//Reporte Gerencial
//Route::get('home/CONTABILIDAD/01 CAPTURA DE HISTORICO', 'Mod_RG01Controller@index')->middleware('routelog');
Route::get('home/CONTABILIDAD/01 CAPTURA DE HISTORICO', 'HomeController@showModal')->middleware('routelog');
Route::any('home/reporte/01 CAPTURA DE HISTORICO/{sociedad?}', 'Mod_RG01Controller@index');
Route::post('home/RG01-guardar', 'Mod_RG01Controller@store');
Route::post('home/reporte/checkctas', 'Mod_RG01Controller@checkctas');

//Route::get('home/CONTABILIDAD/02 RELACIONAR PDF', 'Mod_RG02Controller@index')->middleware('routelog');
Route::get('home/CONTABILIDAD/02 RELACIONAR PDF', 'HomeController@showModal')->middleware('routelog');
Route::any('home/reporte/02 RELACIONAR PDF/{sociedad?}', 'Mod_RG02Controller@index');
Route::post('home/RG02-guardar', 'Mod_RG02Controller@store');

//Route::get('home/CONTABILIDAD/03 REPORTE GERENCIAL', 'Mod_RG03Controller@index')->middleware('routelog');
Route::get('home/CONTABILIDAD/03 REPORTE GERENCIAL', 'HomeController@showModal')->middleware('routelog');
Route::any('home/reporte/03 REPORTE GERENCIAL/{sociedad?}', 'Mod_RG03Controller@index');
Route::post('home/RG03-reporte', 'Mod_RG03Controller@reporte');

Route::post('home/reporte/ajustesfill', 'Mod_RG03Controller@ajustesfill');
Route::get('home/ReporteGerencial/{opcion}', 'Mod_RG03Controller@RGPDF');
//Mod Finanzas
Route::group(['prefix' => 'home/FINANZAS'], function () {

    Route::get('01 FLUJO EFECTIVO', 'Mod_FinanzasController@index_flujoEfectivoResumen')->middleware('routelog');
    Route::get('flujoefectivo-programas', 'Mod_FinanzasController@index_flujoEfectivoShowProgramas');
    Route::get('flujoefectivo-resumen-cliente-proveedor', 'Mod_FinanzasController@flujoEfectivoResumenCXCCXP');
    Route::get('datatables.FTPDCXPPesos', 'Mod_FinanzasController@DataFTPDCXPPesos')->name('datatables.FTPDCXPPesos');
    Route::any('registraPrograma', 'Mod_FinanzasController@registraPrograma');
    Route::any('consultaDatosInicio', 'Mod_FinanzasController@consultaDatosInicio');
    Route::any('programas-registros', 'Mod_FinanzasController@programas_registros');
    Route::any('consultaProgramaPorId/{id_programa}', 'Mod_FinanzasController@consultaProgramaPorId');
    Route::get('nuevoPrograma', 'Mod_FinanzasController@index_flujoEfectivoProgramarPagos');
    Route::any('cancelarPorgramaCXP', 'Mod_FinanzasController@cancelarPorgramaCXP');
    Route::any('autorizaProgramaPorId', 'Mod_FinanzasController@autorizaProgramaPorId');
    Route::any('datatables.resumen_cxc', 'Mod_FinanzasController@data_resumen_cxc_cliente')->name('datatables.resumen_cxc');
    Route::any('datatables.resumen_cxp', 'Mod_FinanzasController@data_resumen_cxp_proveedor')->name('datatables.resumen_cxp');
    /*Route::any('consultaDatosPorFiltro', 'FlujoEfectivoController@consultaDatosPorFiltro');
    Route::get('consultaDatosCalendarios', 'FlujoEfectivoController@consultaDatosCalendarios');
    Route::get('consultaDatosCalendarios2', 'FlujoEfectivoController@consultaDatosCalendarios2');
    Route::get('consultaDatosCalendariosCXCResumen', 'FlujoEfectivoController@consultaDatosCalendariosCXCResumen');
    Route::get('consultaDatosCalendariosCXCResumen2', 'FlujoEfectivoController@consultaDatosCalendariosCXCResumen2');
    Route::get('consultaDatosCalendariosCXPResumen', 'FlujoEfectivoController@consultaDatosCalendariosCXPResumen');
    Route::get('consultaDatosCalendariosCXPResumen2', 'FlujoEfectivoController@consultaDatosCalendariosCXPResumen2');    
*/
});

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
    'as' => 'traslado', 'uses' => 'Mod01_ProduccionController@traslados'
]);
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
Route::get('home/CALIDAD POR DEPTO', 'Mod07_CalidadController@repCalidad');
Route::post('home/CALIDAD POR DEPTO', 'Mod07_CalidadController@repCalidad2');

//RUTAS 112-CORTE PIEL///
Route::get('home/112 CORTE DE PIEL', 'Mod01_ProduccionController@repCortePiel');
Route::post('home/112 CORTE DE PIEL', 'Mod01_ProduccionController@repCortePiel');
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
Route::get('home/BONOS CORTE', 'Mod10_RhController@bonosCorte');
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

//ALMACEN DIGITAL ROUT PATH
Route::get('home/AlmacenDigital', "DigitalStorage@index");
Route::get('home/ALMACENDIGITAL/AlmacenDigital', "DigitalStorage@index");
Route::get('home/ALMACENDIGITAL/', "DigitalStorage@index");
Route::get('home/AlmacenDigital/edit/{id}/{moduleType}', "DigitalStorage@edit");
Route::get('home/ALMACENDIGITAL/edit/{id}/{moduleType}', "DigitalStorage@edit");
Route::get('home/AlmacenDigital/find', 'DigitalStorage@find');
Route::post('home/AlmacenDigital/update/{id}/{moduleType}', 'DigitalStorage@update');
Route::post('home/AlmacenDigital/crear/{moduleType}', 'DigitalStorage@create');
Route::get('home/AlmacenDigital/crear/{moduleType}', 'DigitalStorage@create');
Route::post('home/AlmacenDigital/store', 'DigitalStorage@store');
Route::post('home/AlmacenDigital/syncOrdersWithDigitalStorage', 'DigitalStorage@syncOrdersWithDigitalStorage');

Route::get('home/ALMACENDIGITAL/01 DOCUMENTOS SAC', "DigitalStorage@SACIndex");
Route::get('home/ALMACENDIGITAL/02 DOCUMENTOS COMPRAS', "DigitalStorage@COMIndex");
Route::get('home/ALMACENDIGITAL/03 DOCUMENTOS SID', "DigitalStorage@SIDIndex");
Route::get('home/ALMACENDIGITAL/04 VER SAC', "DigitalStorage@SACView");
Route::get('home/ALMACENDIGITAL/05 VER COMPRAS', "DigitalStorage@COMView");
Route::get('home/ALMACENDIGITAL/06 VER SID', "DigitalStorage@SIDView");
Route::get('home/ALMACENDIGITAL/CONFIG', "DigitalStorage@ConfigView");
Route::post('home/ALMACENDIGITAL/config/new', "DigitalStorage@newConfigView");
Route::get('home/ALMACENDIGITAL/config/edit/{id}', "DigitalStorage@editConfigView");
Route::post('home/ALMACENDIGITAL/config/insertConfig', "DigitalStorage@insertConfigView");
Route::post('home/ALMACENDIGITAL/config/updateConfig', "DigitalStorage@updateConfigView");
//TODO
Route::get('home/ALMACENDIGITAL/07_VALIDAR_POLIZA_INGR', "DigitalStorage@notFound");
Route::get('home/ALMACENDIGITAL/08_VALIDAR_POLIZA_EGRE', "DigitalStorage@notFound");
Route::get('home/ALMACENDIGITAL/09_VALIDAR_CONTADOR', "DigitalStorage@notFound");
Route::get('home/ALMACENDIGITAL/10_CONFIG_INDICES', "DigitalStorage@notFound");

//CONFIGURACION BASADA EN DOC_ID
Route::get('home/ALMACENDIGITAL/11_CONFIG_INDICES', "DigitalStorage@notFound");
