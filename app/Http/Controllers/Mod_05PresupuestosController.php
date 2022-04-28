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
	public function ReportePresupuestoXLS(){
		$data = Session::get('data_rg_presupuesto');
		$sociedad = Session::get('sociedad_presupuesto');
		Excel::create('Siz_Reporte_Presupuesto' . ' - ' . $hoy = date("d/m/Y").'', function($excel)use($data, $sociedad) {
			$excel->sheet('Hoja 1', function($sheet) use($data, $sociedad){
			$periodo = $data['periodo'];
			$box_anterior = $data['box_anterior'];
			$sociedad = $data['sociedad'];
			
			$actividades = $data['actividades'];
			$ultimo = $data['ultimo'];
			$ejercicio = $data['ejercicio'];
			$nombrePeriodo = $data['nombrePeriodo'];
			$acumulados_hoja2 = $data['acumulados_hoja2'];
			$totales_hoja2 = $data['totales_hoja2'];
			$acumuladosxcta = $data['acumuladosxcta'];
			$totalesIngresosGastos = $data['totalesIngresosGastos'];
			$box_anterior_p = $data['box_anterior_p'];
			$acumulados_hoja2_p = $data['acumulados_hoja2_p'];
			$totales_hoja2_p = $data['totales_hoja2_p'];
			$acumuladosxcta_p = $data['acumuladosxcta_p'];

			$index = 1;
			$count_tabla = 1;
			$totalEntrada = 0; 
			$totalAnterior = 0;   
			$totalAcumulado = 0;    

			$totalEntrada_p = 0; 
			$totalAnterior_p = 0;   
			$totalAcumulado_p = 0;
			
			$totalGeneralEntrada = 0; 
			$totalGeneralAnterior = 0;   
			$totalGeneralAcumulado = 0;

			$totalGeneralEntrada_p = 0; 
			$totalGeneralAnterior_p = 0;   
			$totalGeneralAcumulado_p = 0;

			$sheet->row(1, [
				'ITEKNIA EQUIPAMIENTO, S.A DE C.V.'
			]);
			$sheet->row(2, [
				'REPORTE PRESUPUESTO (ESTADO DE RESULTADOS)'
			]);
			$sheet->row(3, [
				'Periodo:' .$nombrePeriodo.'/'. $ejercicio
			]);
			$sheet->row(4, [
				'FECHA DE IMPRESION: '.\AppHelper::instance()->getHumanDate(date("Y-m-d")),
			]);
			$fila = 4; 
			$hoja2 = $data['hoja2'];
			foreach ($hoja2 as $rep) {
				$fila++;
				if($index == 1){
					$llave = $rep->RGC_tabla_titulo;
					$totalEntrada = abs($rep->movimiento);
					$totalEntrada_p = abs($rep->movimiento_p);

					if ($periodo == '01') {
						$totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] : 0;
						$totalAnterior_p = (array_key_exists(($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] : 0;
					} else {
						$totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)] - abs($rep->movimiento);
						$totalAnterior_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)] - abs($rep->movimiento_p);
					}

					$totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)];
					$totalAcumulado_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)];
					$totalAcumulado = abs($totalAcumulado);
					$totalAcumulado_p = abs($totalAcumulado_p);
					$totalAnterior = abs($totalAnterior);
					$totalAnterior_p = abs($totalAnterior_p);

					$sheet->row($fila, [
						'Cuenta', $rep->RGC_tabla_titulo, 'Anterior', 'Mes', 'Acumulado', 'Presupuesto Anterior', 'Presupuesto Mes', 'Presupuesto Acumulado', 'Diferiencia Anterior', 'Diferiencia Mes', 'Diferiencia Acumulado'
					]);

					$fila++; //añadimos una nueva fila
					$sheet->row($fila, [
						$rep->BC_Cuenta_Id, $rep->BC_Cuenta_Nombre 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',') 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] -
								$rep->movimiento) - abs(($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
								$rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento) - abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
							abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
					]);
					//fin nueva fila
				}//index==1
				else if($llave == $rep->RGC_tabla_titulo){
					$totalEntrada += abs($rep->movimiento);
					$totalEntrada_p += abs($rep->movimiento_p);
				
					$totalAcumulado += abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);
					$totalAcumulado_p += abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);
				
					if ($periodo == '01'){
						$totalAnterior += abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
						$totalAnterior_p += abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
					}else {            
						$totalAnterior += abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento);
						$totalAnterior_p += abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento_p);
					}
					//añadimos una nueva fila
					$sheet->row($fila, [
						$rep->BC_Cuenta_Id, $rep->BC_Cuenta_Nombre 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',') 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] -
								$rep->movimiento) - abs(($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
								$rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento) - abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
							abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
					]);
					//fin nueva fila
				}//if $llave == $rep->RGC_tabla_titulo
				else{ //ES OTRO, SE CAMBIA LA LLAVE 
						//fila de totales
					$sheet->row($fila, [
						'', 'TOTAL ' .$llave
						, number_format($totalAnterior, '2', '.', ',')
						, number_format($totalEntrada,'2', '.',',')
						, number_format($totalAcumulado,'2', '.',',')
						, number_format($totalAnterior_p,'2', '.',',')
						, number_format($totalEntrada_p,'2', '.',',')
						, number_format($totalAcumulado_p,'2', '.',',')
						, number_format($totalAnterior - $totalAnterior_p,'2', '.',',')
						, number_format($totalEntrada - $totalEntrada_p,'2', '.',',')
						, number_format($totalAcumulado - $totalAcumulado_p,'2', '.',',')
					]);
					$fila = $fila+2;
					$sheet->row($fila, [
						'Cuenta', $rep->RGC_tabla_titulo, 'Anterior', 'Mes', 'Acumulado', 'Presupuesto Anterior', 'Presupuesto Mes', 'Presupuesto Acumulado', 'Diferiencia Anterior', 'Diferiencia Mes', 'Diferiencia Acumulado'
					]);

					$totalGeneralEntrada += $totalEntrada;
					$totalGeneralAnterior += $totalAnterior;
					$totalGeneralAcumulado += $totalAcumulado;
					$totalGeneralEntrada_p += $totalEntrada_p;
					$totalGeneralAnterior_p += $totalAnterior_p;
					$totalGeneralAcumulado_p += $totalAcumulado_p;

					$llave = $rep->RGC_tabla_titulo;

					$totalEntrada = abs($rep->movimiento);
					$totalEntrada_p = abs($rep->movimiento_p);

					$totalAcumulado = abs($acumuladosxcta[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)]);
					$totalAcumulado_p = abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)]);

					if ($periodo == '01') {
						$totalAnterior = abs((array_key_exists(($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] : 0);
						$totalAnterior_p = abs((array_key_exists(($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] : 0);
					} else {
						$totalAnterior = abs($acumuladosxcta[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento);
						$totalAnterior_p = abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento_p);
					}

					$fila++;
					//añadimos una nueva fila
					$sheet->row($fila, [
						$rep->BC_Cuenta_Id, $rep->BC_Cuenta_Nombre 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($rep->movimiento), '2', '.', ',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) - abs($rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',') 
						, ($periodo == '01') ? number_format(0, '2', '.', ','):
							number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2] -
								$rep->movimiento) - abs(($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
								$rep->movimiento_p), '2', '.', ',')
						, number_format(abs($rep->movimiento) - abs($rep->movimiento_p),'2', '.',',')
						, number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]) -
							abs($acumuladosxcta_p[$rep->BC_Cuenta_Id . $rep->RGC_BC_Cuenta_Id2]), '2', '.', ',')
					]);
					//fin nueva fila
				}//ELSE OTRA LLAVE //end IF
				if($index == count($hoja2)){
					$totalGeneralEntrada += $totalEntrada;
					$totalGeneralAnterior += $totalAnterior;
					$totalGeneralAcumulado += $totalAcumulado;
					$totalGeneralEntrada_p += $totalEntrada_p;
					$totalGeneralAnterior_p += $totalAnterior_p;
					$totalGeneralAcumulado_p += $totalAcumulado_p;
					//fila de totales
					$fila++;
					$sheet->row($fila, [
						'', 'TOTAL ' .$llave
						, number_format($totalAnterior, '2', '.', ',')
						, number_format($totalEntrada,'2', '.',',')
						, number_format($totalAcumulado,'2', '.',',')
						, number_format($totalAnterior_p,'2', '.',',')
						, number_format($totalEntrada_p,'2', '.',',')
						, number_format($totalAcumulado_p,'2', '.',',')
						, number_format($totalAnterior - $totalAnterior_p,'2', '.',',')
						, number_format($totalEntrada - $totalEntrada_p,'2', '.',',')
						, number_format($totalAcumulado - $totalAcumulado_p,'2', '.',',')
					]);
						$fila = $fila + 2;
						$sheet->row($fila, [
							'', '', 'Anterior', 'Mes', 'Acumulado', 'Presupuesto Anterior', 'Presupuesto Mes', 'Presupuesto Acumulado', 'Diferiencia Anterior', 'Diferiencia Mes', 'Diferiencia Acumulado'
						]);
					
						$fila++;
						$sheet->row($fila, [
							'', 'TOTAL GENERAL'
							, number_format($totalGeneralAnterior, '2', '.', ',')
							, number_format($totalGeneralEntrada, '2', '.', ',')
							, number_format($totalGeneralAcumulado, '2', '.', ',')
							, number_format($totalGeneralAnterior_p, '2', '.', ',')
							, number_format($totalGeneralEntrada_p,'2', '.',',')
							, number_format($totalGeneralAcumulado_p, '2', '.', ',')
							, number_format($totalGeneralAnterior - $totalGeneralAnterior_p, '2', '.', ',')
							, number_format($totalGeneralEntrada - $totalGeneralEntrada_p, '2', '.', ',')
							, number_format($totalGeneralAcumulado - $totalGeneralAcumulado_p,'2', '.',',')
						]);

					
				}
					$index++;
			}//foreach hoja2
			
		});         
		})->export('xlsx');
	}
	public function ReportePresupuestoPDF(){
		$data = Session::get('data_rg_presupuesto');
		$sociedad = Session::get('sociedad_presupuesto');
		
		$vista = 'Contabilidad.RG03_reporte_ER';
		$file_name = "_EstadoResultados";
			
		$data["vista"] = $vista;
		$data["sociedad"] = $sociedad;
		$data["fecha_actualizado"] = false;
		$pdf = PDF::loadView('Contabilidad.RG03PDF', $data);
		$pdf->setPaper('Letter', 'Landscape')->setOptions(['isPhpEnabled' => true, 'isHtml5ParserEnabled'=> true]);		           

		return $pdf->stream($data["ejercicio"] . "_" . $data["periodo"] . $file_name[1] . '.pdf');  
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
					->where('BC_Ejercicio', $ejercicio)
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
				
				//clock);
				$fila_update = [
					"BC_Eliminado" => ($datosTablaCtas[$x]['CHECKBOX'] == 1)? 0 : 1,
					"BC_Ejercicio" => $ejercicio,
					"BC_Fecha_Actualizado" => date("Ymd")
				];
				$fila_db = DB::table($tablePresupuesto)
				->where('BC_Cuenta_Id', $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'])
				->where('BC_Ejercicio', $ejercicio)
				->where('BC_Cuenta_Nombre', $datosTablaCtas[$x]['RGC_descripcion_cuenta'])->get();

				/* if ($datosTablaCtas[$x]['CHECKBOX'] == 1) {
					# code...
					clock($fila_db);
				} */
					//clock($datosTablaCtas[$x]['CHECKBOX']);
				if (count($fila_db) > 0) {
					clock('UPDATE');
					DB::table($tablePresupuesto)
					->where('BC_Cuenta_Id', $datosTablaCtas[$x]['RGC_BC_Cuenta_Id'])
					->where('BC_Ejercicio', $ejercicio)
					->where('BC_Cuenta_Nombre', $datosTablaCtas[$x]['RGC_descripcion_cuenta'])
					->update($fila_update);
				} else if($datosTablaCtas[$x]['CHECKBOX'] == 1){
					//clock('INSERT');
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
			$sociedad = Session::get('sociedad_presupuesto');
			
			$hojas_reporte = DB::select('SELECT conf.RGC_hoja k, conf.RGC_hojaDescripcion d FROM RPT_RG_ConfiguracionTabla conf
				WHERE conf.RGC_hojaDescripcion IS NOT NULL
				GROUP BY conf.RGC_hoja, conf.RGC_hojaDescripcion');
			
			//$cbo_periodos = self::cbo_periodos($sociedad); 
			return view('Contabilidad.PresupuestoIndex', compact('hojas_reporte', 'sociedad','actividades', 'ultimo'));
		}else{
			return redirect()->route('auth/login');
		}
	}
	public function index_rp($periodo = null,$sociedad = null)
	{
		if (Auth::check()) { 
			$user = Auth::user();
			$actividades = $user->getTareas();
			$ultimo = count($actividades); 			

			if(is_null($sociedad)){				
				if (Input::has('text_selUno')){
					$sociedad = Input::get('text_selUno');
				}else{
					if (Session::has('sociedad_presupuesto')) {
						$sociedad = Session::get('sociedad_presupuesto');
					}
				}
			} 
			
			Session::put('sociedad_presupuesto', $sociedad);
			if(is_null($periodo)){
				return view('Contabilidad.PresupuestoIndex_rp', compact('sociedad', 'actividades', 'ultimo', 'periodo'));
			}
			//dd($periodo);
			/* */
			$periodo = explode('-', $periodo);
			$soc = DB::table('RPT_Sociedades')
			->where('SOC_Nombre', $sociedad)
				->where('SOC_Reporte', 'ReporteGerencial')
				->first();
			$tableName = $soc->SOC_AUX_DB;
			$tableName_p = $soc->SOC_TABLE_PRESUPUESTO;
			$version = DB::table('RPT_RG_CatalogoVersionCuentas')
			->where('CAT_periodo', $periodo)
				->value('CAT_version');
			$version = (is_null($version)) ? 0 : $version;
			$ejercicio = $periodo[0];
			$periodo = $periodo[1];

			$fecha = $ejercicio . '/' . $periodo . '/01';
			$fecha = Carbon::parse($fecha);
			$fecha = $fecha->subMonth();
			$periodo_ant = $fecha->format('m');
			$ejercicio_ant = $fecha->format('Y');
			//clock($fecha);

			$hoja2 = DB::select("SELECT 
					bg.[BC_Ejercicio]
					,bg.[BC_Cuenta_Id]
					,bg.[BC_Cuenta_Nombre]

					,bg.[BC_Saldo_Inicial]
					,bg.[BC_Saldo_Final]
					,bg.[BC_Movimiento_" . $periodo . "]  as movimiento
											
					,COALESCE(p.[BC_Saldo_Inicial], 0) AS BC_Saldo_Inicial_p
					,COALESCE(p.[BC_Saldo_Final], 0) AS BC_Saldo_Final_p
					,COALESCE(p.[BC_Movimiento_" . $periodo . "] , 0) AS movimiento_p 
						
					,[RGC_hoja]
					,[RGC_tabla_titulo]
					,[RGC_tabla_linea]
					,[RGC_multiplica]
					,[RGC_estilo]
					,[RGC_BC_Cuenta_Id2]
				FROM " . $tableName . " bg
				LEFT join RPT_RG_ConfiguracionTabla conf on conf.RGC_BC_Cuenta_Id = bg.BC_Cuenta_Id
				INNER join " . $tableName_p . " p on conf.RGC_BC_Cuenta_Id = p.BC_Cuenta_Id
				AND p.[BC_Ejercicio] = ? AND BC_Eliminado = 0
				WHERE bg.[BC_Ejercicio] = ?
				AND bg.[BC_Movimiento_" . $periodo . "] IS NOT NULL
                            AND (conf.RGC_mostrar = '0' OR conf.RGC_mostrar = ?)
                            AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)
                            order by RGC_hoja, RGC_tabla_linea
                                    ", [$ejercicio, $ejercicio, $version, $soc->SOC_Id]);

			// $hoja2 = array_where($data, function ($key, $value) {
			// 	return $value->RGC_hoja == 2;
			// });
			
			//clock($hoja2);
			 $helper = AppHelper::instance();
			// INICIA ER - Hoja2
			$grupos_hoja2 = array_unique(array_pluck($hoja2, 'RGC_tabla_titulo'));
			$totales_hoja2 = [];
			$acumulados_hoja2 = [];
			$acumuladosxcta = [];
			$utilidadEjercicio = 0;
			$ue_ingresos = 0;
			$ue_gastos_costos = 0;
			$totalesIngresosGastos = [];
			$anteriorIngresos = 0;
			$cantPeriodoIngresos = 0;
			$acumuladoIngresos = 0;
			$anteriorGastos = 0;
			$cantPeriodoGastos = 0;
			$acumuladoGastos = 0;
			
			$totales_hoja2_p = [];
			$acumulados_hoja2_p = [];
			$acumuladosxcta_p = [];
			$utilidadEjercicio_p = 0;
			$ue_ingresos_p = 0;
			$ue_gastos_costos_p = 0;
			//$totalesIngresosGastos_p = [];
			$anteriorIngresos_p = 0;
			$cantPeriodoIngresos_p = 0;
			$acumuladoIngresos_p = 0;
			$anteriorGastos_p = 0;
			$cantPeriodoGastos_p = 0;
			$acumuladoGastos_p = 0;

			foreach ($grupos_hoja2 as $key => $val) {
				$items = array_where($hoja2, function ($key, $value) use ($val) {
					return $value->RGC_tabla_titulo == $val;
				});
				
				$totales_hoja2[$val] = array_sum(array_pluck($items, 'movimiento'));
					$totales_hoja2_p[$val] = array_sum(array_pluck($items, 'movimiento_p'));
				$sum_acumulado = 0;					
				foreach ($items as $key => $value) {
					$sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName);
					if (is_null($sum)) {
						//Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:' . $value->BC_Cuenta_Id);
						$sum = 0;
					}
					$sum_acumulado += $sum;// * $value->RGC_multipli;
					$acumuladosxcta[$value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2] = $sum; //* $value->RGC_multipli;
				}

					$sum_acumulado_p = 0;
					foreach ($items as $key => $value) {
						$sum = $helper->Rg_GetSaldoFinal($value->BC_Cuenta_Id, $ejercicio, $periodo, $tableName_p);
						if (is_null($sum)) {
							//Session::flash('error', 'El saldo Inicial o algun periodo no esta capturado. #cta:' . $value->BC_Cuenta_Id);
							$sum = 0;
						}
						$sum_acumulado_p += $sum;// * $value->RGC_multipli;
						$acumuladosxcta_p[$value->BC_Cuenta_Id . $value->RGC_BC_Cuenta_Id2] = $sum;// * $value->RGC_multipli;
					}

				$acumulados_hoja2[$val] = $sum_acumulado;
					$acumulados_hoja2_p[$val] = $sum_acumulado_p;

				
			}
			
			$box_anterior = [];
			$box_anterior_p = [];
			if ($periodo == '01') {
				$con = DB::table('RPT_RG_ValoresFormulasPorPeriodo')
				->where('VFP_Ejercicio_periodo', ((int)$ejercicio - 1) . '-12')
					->where('VFP_SOC_sociedad_id', $soc->SOC_Id)->get();
				foreach ($con as $value) {
					$box_anterior[$value->VFP_Box_key] = 0;
				}
					foreach ($con as $value) {
						$box_anterior_p[$value->VFP_Box_key] = 0;
					}
			}
			$nombrePeriodo = $helper->getNombrePeriodo($periodo);
			$params = compact(
				'box_anterior',
				'sociedad',
				'fechaA',
				'actividades',
				'ultimo',
				'ejercicio',
				'nombrePeriodo',
				'periodo',
				'acumulados_hoja2',
				'totales_hoja2',
				'acumuladosxcta',
				'hoja2',
				'totalesIngresosGastos',

				'box_anterior_p',
				'acumulados_hoja2_p',
				'totales_hoja2_p',
				'acumuladosxcta_p'
			);
			Session::put('data_rg_presupuesto', $params);
			//return view('Mod_RG.RG03_reporte', $params);
    
			/* */
			
			return view('Contabilidad.PresupuestoIndex_rp', $params);
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
				, 'RGC_mostrar' => 0
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
		$ctas_titulo = DB::select("SELECT 
			CASE WHEN bp.BC_Eliminado = 0  THEN 1 ELSE 0 END AS CHECKBOX
			,bp.BC_Eliminado
            ,conf.RGC_BC_Cuenta_Id
            ,conf.RGC_descripcion_cuenta
            ,RGC_hojaDescripcion AS Reporte
			,conf.RGC_sociedad
            FROM RPT_RG_ConfiguracionTabla conf
            LEFT JOIN (SELECT * FROM ". $tablePresupuesto . " WHERE BC_Ejercicio = ?)
			bp ON bp.BC_Cuenta_Id = conf.RGC_BC_Cuenta_Id AND COALESCE(bp.BC_Cuenta_Nombre, '') = COALESCE(conf.RGC_descripcion_cuenta, '')
			WHERE
			(conf.RGC_mostrar = '0' ".$criterio. ")
			AND conf.RGC_tipo_renglon = 'CUENTA'
			AND (conf.RGC_sociedad = '0' OR conf.RGC_sociedad = ?)			
			ORDER BY CHECKBOX desc, bp.BC_Eliminado desc, RGC_hoja, RGC_tabla_titulo, RGC_tabla_linea
			", [$ejercicio, $soc->SOC_Id]);
		
		$registros = array_where($ctas_titulo, function ($key, $value) use ($soc){
			return $value->RGC_sociedad == $soc->SOC_Id;
		});
		$registros0 = array_where($ctas_titulo, function ($key, $value){
			return $value->RGC_sociedad == 0;
		});
		$pajar = array_pluck($registros, 'RGC_descripcion_cuenta');

		foreach ($registros0 as $key => $v) {
			if(!in_array($v->RGC_descripcion_cuenta, $pajar)){
				array_push($registros, $v);
			}
		}
		
		return Datatables::of(collect($registros))           
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
   
