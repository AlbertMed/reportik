<?php
namespace App\Http\Controllers;

use App;
use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Datatables;
use App\RTP_BALANZAPRESUPUESTO_ITEKNIA;
use App\RTP_BALANZAPRESUPUESTO_AZARET;
use App\RTP_BALANZAPRESUPUESTO_COMER;
use App\RTP_BALANZA_CONFIG;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
error_reporting(E_ALL);
class Mod_05PresupuestosController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	public function guardar_presupuesto(Request $request)
	{
		DB::beginTransaction();

		try {

			date_default_timezone_set('America/Mexico_City');
		  
			$hoy = date('d-m-Y H:i:s');
			
			$sociedad = Session::get('sociedad_presupuesto');
			$soc = DB::table('RPT_Sociedades')
			->where('SOC_Nombre', $sociedad)
			->where('SOC_Reporte', 'ReporteGerencial')
			->first();
			//$tableName = $soc->SOC_AUX_DB;
			$tablePresupuesto = $soc->SOC_TABLE_PRESUPUESTO;

			$datosTablaCtas = json_decode($request->input('datosTablaCtas'), true);
			$ejercicio = $request->input('periodo');			
			
			//GUARDA DETALLE 
			$tamanio_tabla = count($datosTablaCtas);
			//dd($tamanio_tabla, $datosTablaCtas);
			for ($x = 0; $x < $tamanio_tabla; $x++) {
				//dd($datosTablaCtas[$x]['BC_Saldo_Inicial'], (is_null($datosTablaCtas[$x]['BC_Saldo_Inicial'])));
				$fila_update = [
					"BC_Saldo_Inicial" => (is_null($datosTablaCtas[$x]['BC_Saldo_Inicial']))? 0 : $datosTablaCtas[$x]['BC_Saldo_Inicial'],
					"BC_Movimiento_01" => (is_null($datosTablaCtas[$x]['BC_Movimiento_01']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_01'],							
					"BC_Movimiento_02" => (is_null($datosTablaCtas[$x]['BC_Movimiento_02']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_02'],						
					"BC_Movimiento_03" => (is_null($datosTablaCtas[$x]['BC_Movimiento_03']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_03'],						
					"BC_Movimiento_04" => (is_null($datosTablaCtas[$x]['BC_Movimiento_04']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_04'],						
					"BC_Movimiento_05" => (is_null($datosTablaCtas[$x]['BC_Movimiento_05']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_05'],						
					"BC_Movimiento_06" => (is_null($datosTablaCtas[$x]['BC_Movimiento_06']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_06'],						
					"BC_Movimiento_07" => (is_null($datosTablaCtas[$x]['BC_Movimiento_07']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_07'],						
					"BC_Movimiento_08" => (is_null($datosTablaCtas[$x]['BC_Movimiento_08']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_08'],						
					"BC_Movimiento_09" => (is_null($datosTablaCtas[$x]['BC_Movimiento_09']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_09'],						
					"BC_Movimiento_10" => (is_null($datosTablaCtas[$x]['BC_Movimiento_10']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_10'],						
					"BC_Movimiento_11" => (is_null($datosTablaCtas[$x]['BC_Movimiento_11']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_11'],						
					"BC_Movimiento_12" => (is_null($datosTablaCtas[$x]['BC_Movimiento_12']))? 0 : $datosTablaCtas[$x]['BC_Movimiento_12']
				];
				
					DB::table($tablePresupuesto)
					->where('BC_Cuenta_Id', $datosTablaCtas[$x]['BC_Cuenta_Id'])
					->where('BC_Cuenta_Nombre', $datosTablaCtas[$x]['BC_Cuenta_Nombre'])
					->update($fila_update);
								
			}
			
			$response = array("action" => "success");

			DB::commit();

			return ['Status' => 'Valido', 'respuesta' => $response];
		} catch (\Exception $e) {

			DB::rollback();
			return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' . $e->getMessage()];
		}
	}
	public function guardar_ctas_ejercicio(Request $request)
	{
		DB::beginTransaction();

		try {

			date_default_timezone_set('America/Mexico_City');
		  
			$hoy = date('d-m-Y H:i:s');
			
			$sociedad = Session::get('sociedad_presupuesto');
			$soc = DB::table('RPT_Sociedades')
			->where('SOC_Nombre', $sociedad)
			->where('SOC_Reporte', 'ReporteGerencial')
			->first();
			//$tableName = $soc->SOC_AUX_DB;
			$tablePresupuesto = $soc->SOC_TABLE_PRESUPUESTO;

			$datosTablaCtas = json_decode($request->input('datosTablaCtas'), true);
			 
			$ejercicio = $request->input('ejercicio');
			
			//GUARDA DETALLE 
			$tamanio_tabla = count($datosTablaCtas);
			//dd($tamanio_tabla, $datosTablaCtas);
			for ($x = 0; $x < $tamanio_tabla; $x++) {
				$fila_update = [
					"BC_Eliminado" => ($datosTablaCtas[$x]['CHECKBOX'] === 1)? 0 : 1,
					"BC_Ejercicio" => $ejercicio
				];
				$fila_db = DB::table($tablePresupuesto)
					->where('BC_Cuenta_Id', $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'])
					->where('BC_Cuenta_Nombre', $datosTablaCtas[$x]['RGC_descripcion_cuenta'])->get();
					//clock($datosTablaCtas[$x]['CHECKBOX']);
					clock($fila_db);
				if (count($fila_db) > 0) {
					clock('UPDATE');
					DB::table($tablePresupuesto)
					->where('BC_Cuenta_Id', $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'])
					->where('BC_Cuenta_Nombre', $datosTablaCtas[$x]['RGC_descripcion_cuenta'])
					->update($fila_update);
				} else if($datosTablaCtas[$x]['CHECKBOX'] === 1){
					clock('INSERT');
					$fila_update += [
						"BC_Cuenta_Id" => $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'], 
						"BC_Cuenta_Nombre" => $datosTablaCtas[$x]['RGC_descripcion_cuenta']
					];
					DB::table($tablePresupuesto)->insert($fila_update);
				}
				
			}
			
			$response = array("action" => "success");

			DB::commit();

			return ['Status' => 'Valido', 'respuesta' => $response];
		} catch (\Exception $e) {

			DB::rollback();
			return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' . $e->getMessage()];
		}
	}
	public function presupuesto_agregar_cta(){
		$user = Auth::user();
		$actividades = $user->getTareas();

		//$cbo_periodos = self::cbo_periodos(Session::get('sociedad_presupuesto'));    
		$sociedad = Session::get('sociedad_presupuesto');
		$data = array(
			'actividades' => $actividades,
			'cbo_bc_titulos' => [],            
			'ultimo' => count($actividades),
			'sociedad' => $sociedad
		);
		return view('Contabilidad.presupuesto_agregar_cta', $data);
	}
	public function index($sociedad = null)
	{
		if (Auth::check()) { 
			$user = Auth::user();
			$actividades = $user->getTareas();
			$ultimo = count($actividades); 
			clock($sociedad);
			if ($sociedad != null) {
				$sociedad = Session::get('sociedad_presupuesto');
			} else {
				$sociedad = Input::get('text_selUno');
			}
			$hojas_reporte = DB::select('SELECT conf.RGC_hoja k, conf.RGC_hojaDescripcion d FROM RPT_RG_ConfiguracionTabla conf
				WHERE conf.RGC_hojaDescripcion IS NOT NULL
				GROUP BY conf.RGC_hoja, conf.RGC_hojaDescripcion');
			Session::put('sociedad_presupuesto', $sociedad);
			//$cbo_periodos = self::cbo_periodos($sociedad); 
			return view('Contabilidad.PresupuestoIndex', compact('hojas_reporte', 'sociedad','actividades', 'ultimo'));
		}else{
			return redirect()->route('auth/login');
		}
	}
	public function reload_cbo_titulos(Request $request)
	{
		$sociedad = Session::get('sociedad_presupuesto');
		$periodo = explode('-', $request->input('periodo'));
		//dd($periodo);
		$ejercicio = $periodo[0];
		$periodo = $periodo[1];
		$soc = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
		->where('SOC_Reporte', 'ReporteGerencial')
		->first();

		$tableName = $soc->SOC_AUX_DB;
		$version = DB::table('RPT_RG_CatalogoVersionCuentas')
		->where('CAT_periodo', $periodo)
			->value('CAT_version');
		$version = (is_null($version)) ? 0 : $version;

		$data_presupuesto = DB::select("SELECT 
			[RGC_tabla_titulo] +' - '+ RGC_hojaDescripcion as titulo
			FROM " . $tableName . " bg
			LEFT join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
			WHERE [BC_Ejercicio] = ?
			AND RGC_hojaDescripcion IS NOT NULL 
			AND (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
			AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)
			GROUP BY [RGC_tabla_titulo] , RGC_hojaDescripcion
			order by  RGC_hojaDescripcion, [RGC_tabla_titulo]
			", [$ejercicio, $version, $soc->SOC_Id]);

		$cbo_titulos = array_unique(array_pluck($data_presupuesto, 'titulo'));
		return compact('cbo_titulos');
	}
	public function reload_cbo_titulos_xreporte(Request $request){
		$sociedad = Session::get('sociedad_presupuesto');
		
		
		$soc = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
		->where('SOC_Reporte', 'ReporteGerencial')
		->first();

		$tableName = $soc->SOC_AUX_DB;
	
		$cbo = DB::select("SELECT conf.RGC_tabla_titulo k
			FROM RPT_RG_ConfiguracionTabla conf
			WHERE conf.RGC_hojaDescripcion IS NOT NULL
			AND conf.RGC_hoja = ?
			GROUP BY conf.RGC_tabla_titulo", [ $request->input('hoja')]);

		return compact('cbo');
	}
	public function alta_cta(Request $request){
		try {
			//revisar si existe
			$countCta = RTP_BALANZA_CONFIG::where('RGC_BC_Cuenta_Id', $request->input("cuenta_codigo"))
			->where('RGC_descripcion_cuenta', $request->input("cuenta_descripcion"))->count();
			//si existe mandar error
			if ($countCta > 0) {
				throw new \Exception("Error: " . 'La Cuenta ya existe!.', 304);
			}
			//obtener posicion entera siguiente
			$max_posicion = RTP_BALANZA_CONFIG::where('RGC_hoja', $request->input("cuenta_hoja"))
			->where('RGC_tabla_titulo', $request->input("cuenta_titulo"))->max('RGC_tabla_linea');
			$posicion = (is_null($max_posicion) || $max_posicion == 0 || $max_posicion = '')? 1 : ceil($max_posicion) + 1;
			
			$sociedad = Session::get('sociedad_presupuesto');
			$soc = DB::table('RPT_Sociedades')
				->where('SOC_Nombre', $sociedad)
				->where('SOC_Reporte', 'ReporteGerencial')
				->first();
			$data = [
				'RGC_BC_Cuenta_Id' => $request->input("cuenta_codigo")
				, 'RGC_tipo_renglon' => 'CUENTA'
				, 'RGC_descripcion_cuenta' => $request->input("cuenta_descripcion")
				, 'RGC_hoja' => $request->input("cuenta_hoja")
				, 'RGC_tabla_titulo' => $request->input("cuenta_titulo")
				, 'RGC_tabla_linea' => $posicion
				, 'RGC_valor_default' => NULL
				, 'RGC_fecha_alta' => date("Ymd")
				, 'RGC_mostrar' => $request->input("cuenta_catalogo")
				, 'RGC_estilo' => NULL
				, 'RGC_sociedad' =>  $soc->SOC_Id
				, 'RGC_BC_Cuenta_Id2' => '' //es un diferienciador para cuentas iguales, ejemplo '-1'
				, 'RGC_multiplica' => $request->input("cuenta_multiplicador")
				, 'RGC_hojaDescripcion' => $request->input("cuenta_hoja_descripcion")
			];
			RTP_BALANZA_CONFIG::create($data);
			$response = array("action" => "success");
			return ['Status' => 'Valido', 'respuesta' => $response];
		} catch (\Exception $e) {
			return ['Status' => 'Error', 'Mensaje' => 'Ocurrió un error al realizar el proceso. Error: ' . $e->getMessage()];
		}
	}
	public function datatables_ctas_presupuesto(Request $request){
		if (!Session::has('sociedad_presupuesto') || $request->input('periodo') == '') {
		   $data = [];
			return compact('data');
		}
		$sociedad = Session::get('sociedad_presupuesto');	
		$ejercicio = $request->input('periodo');
		$soc = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
		->where('SOC_Reporte', 'ReporteGerencial')
		->first();
		$tablePresupuesto = $soc->SOC_TABLE_PRESUPUESTO;
		
		$ctas_titulo = DB::select("SELECT *    
            FROM ". $tablePresupuesto ." 
			WHERE
			 (BC_Ejercicio = ? )
			AND (BC_Eliminado = '0')
			ORDER BY BC_Cuenta_Nombre, BC_Cuenta_Id
			", [$ejercicio]);
		return Datatables::of(collect($ctas_titulo))           
		->make(true);
	}
	public function datatables_ctas_conf(Request $request){
		if (!Session::has('sociedad_presupuesto') || $request->input('ejercicio') == '') {
			$data = [];
			return compact('data');
		}
		$sociedad = Session::get('sociedad_presupuesto');
		
		$soc = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
		->where('SOC_Reporte', 'ReporteGerencial')
		->first();

		$tableName = $soc->SOC_AUX_DB;
		$tablePresupuesto = $soc->SOC_TABLE_PRESUPUESTO;
		$ejercicio = $request->input('ejercicio');

		$version = DB::table('RPT_RG_CatalogoVersionCuentas')
		->where('CAT_periodo', 'like', '%' . $ejercicio . '%')
		->whereNotNull('CAT_version')
		->groupBy('CAT_version')
		->lists('CAT_version');
		$versiones = implode("','", $version);
		$criterio = '';
		if (count($version) > 0) {
			$criterio = "OR conf.RGC_mostrar in ('" . $versiones . "') ";
		}
		$ctas_titulo = DB::select("SELECT CASE WHEN bp.BC_Eliminado = 0  THEN 1 ELSE 0 END AS CHECKBOX
			,bp.BC_Eliminado
            ,conf.RGC_BC_Cuenta_Id
            ,conf.RGC_descripcion_cuenta
            ,RGC_hojaDescripcion AS Reporte   
            FROM RPT_RG_ConfiguracionTabla conf
            LEFT JOIN (SELECT * FROM ". $tablePresupuesto . " WHERE BC_Ejercicio = ?)
			bp ON bp.BC_Cuenta_Id = conf.RGC_BC_Cuenta_Id AND COALESCE(bp.BC_Cuenta_Nombre, '') = COALESCE(conf.RGC_descripcion_cuenta, '')
			WHERE
			 (conf.RGC_mostrar = '0' ".$criterio. ")
			 AND conf.RGC_tipo_renglon = 'CUENTA'
			AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)			
			ORDER BY CHECKBOX desc, bp.BC_Eliminado desc, RGC_hoja, RGC_tabla_titulo, RGC_tabla_linea
			", [$ejercicio, $soc->SOC_Id]);
	   
		
		return Datatables::of(collect($ctas_titulo))           
		->make(true);
	}

	public function cbo_periodos($sociedad){
		$tableName = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
			->where('SOC_Reporte', 'ReporteGerencial')
			->value('SOC_AUX_DB');

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
									sum(BC_Movimiento_12)m_12 from " . $tableName . " 
									group by BC_Ejercicio");

		$cbo_periodos = [];
		foreach ($periodos as  $value) {
			$value = collect($value);

			for ($i = 1; $i <= 12; $i++) {
				if ($i < 10) {
					$index = '0' . $i;
				} else {
					$index = $i;
				}
				if (!is_null($value["m_" . $index])) {
					if ($value["m_" . $index] != 0) {
						$cbo_periodos[] = $value["BC_Ejercicio"] . '-' . $index;
					}
				}
			}
		}
		return $cbo_periodos = array_reverse($cbo_periodos);
	}
}
   
