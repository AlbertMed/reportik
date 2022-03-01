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
			$datosTablaCtas = json_decode($request->input('datosTablaCtas'), true);
			$periodo = explode('-', $request->input('periodo'));
			$ejercicio = $periodo[0];
			$periodo = $periodo[1];
			//GUARDA DETALLE 
			$tamanio_tabla = count($datosTablaCtas);
			for ($x = 0; $x < $tamanio_tabla; $x++) {
				$fila = [
					"RGC_BC_Cuenta_Id" => $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'],
					"RGC_descripcion_cuenta" => $datosTablaCtas[$x]['RGC_descripcion_cuenta']
				];
				switch ($sociedad) {
					case 'ITEKNIA EQUIPAMIENTO, S.A. DE C.V.':
						$fila_presupuesto = RTP_BALANZAPRESUPUESTO_ITEKNIA::firstOrNew($fila);
						break;
					case 'AZARET CORTINAS, S.A DE C.V.':
						$fila_presupuesto = RTP_BALANZAPRESUPUESTO_AZARET::firstOrNew($fila);
						break;
					case 'COMERCIALIZADORA ITEKNIA, S.A. DE C.V.':
						$fila_presupuesto = RTP_BALANZAPRESUPUESTO_COMER::firstOrNew($fila);
						break;
				}
				$fila_update = [
					"BC_Movimiento_".$periodo => $datosTablaCtas[$x]['presupuesto'],
					"BC_Eliminado" => $datosTablaCtas[$x]['CHECKBOX'],
					"BC_Ejercicio" => $ejercicio
				];
				if (!is_null($fila_presupuesto->BC_Eliminado)) {
					$fila_presupuesto->update($fila_update);
					$fila_presupuesto->save();
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

		$cbo_periodos = self::cbo_periodos(Session::get('sociedad_presupuesto'));    
		$data = array(
			'actividades' => $actividades,
			'cbo_bc_titulos' => [],            
			'ultimo' => count($actividades),
			'cbo_periodos' => $cbo_periodos
		);
		return view('Contabilidad.presupuesto_agregar_cta', $data);
	}
	public function index($sociedad = null)
	{
		if (Auth::check()) { 
			$user = Auth::user();
			$actividades = $user->getTareas();
			$ultimo = count($actividades);
			$sociedad = Input::get('text_selUno');
			Session::put('sociedad_presupuesto', $sociedad);
			$cbo_periodos = self::cbo_periodos($sociedad); 
			return view('Contabilidad.PresupuestoIndex', compact('cbo_periodos', 'sociedad','actividades', 'ultimo'));
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
	public function datatables_ctas_presupuesto(Request $request){
		if (!Session::has('sociedad_presupuesto') || $request->input('periodo') == '') {
		   return compact([]);
		}
		$sociedad = Session::get('sociedad_presupuesto');
		$periodo = explode('-', $request->input('periodo'));
		$ejercicio = $periodo[0];
		$periodo = $periodo[1];
		$soc = DB::table('RPT_Sociedades')
		->where('SOC_Nombre', $sociedad)
		->where('SOC_Reporte', 'ReporteGerencial')
		->first();

		$tableName = $soc->SOC_AUX_DB;
		$tablePresupuesto = $soc->SOC_TABLE_PRESUPUESTO;
		$version = DB::table('RPT_RG_CatalogoVersionCuentas')
		->where('CAT_periodo', $periodo)
			->value('CAT_version');
		$version = (is_null($version)) ? 0 : $version;

		$ctas_titulo = DB::select("SELECT CASE WHEN bp.BC_Cuenta_Id IS NOT NULL THEN 1 ELSE 0 END AS CHECKBOX
            ,conf.RGC_BC_Cuenta_Id
            ,conf.RGC_descripcion_cuenta
            ,CASE WHEN bp.BC_Cuenta_Id IS NOT NULL THEN bp.[BC_Movimiento_".$periodo. "] * COALESCE(conf.RGC_multiplica, 0) ELSE 0 END AS presupuesto
            ,[RGC_tabla_titulo] +' - '+ RGC_hojaDescripcion AS titulo    
            FROM RPT_RG_ConfiguracionTabla conf
            LEFT JOIN ". $tablePresupuesto ." bp ON bp.BC_Cuenta_Id = conf.RGC_BC_Cuenta_Id
			WHERE
			 (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
			AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)
			AND [RGC_tabla_titulo] +' - '+ RGC_hojaDescripcion = ?
			order by RGC_hoja, RGC_tabla_titulo, RGC_tabla_linea
			", [$version, $soc->SOC_Id, $request->input('titulo')]);
	   
		
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
   
