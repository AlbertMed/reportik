<?php
namespace App\Http\Controllers;

use App;
use App\RPTMONGO;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Datatables;
ini_set("memory_limit", '512M');
ini_set('max_execution_time', 0);
class Mod_ProduccionAppController extends Controller
{
    public function index_produccionApp()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);

            ini_set('memory_limit', '-1');
            set_time_limit(0);

            return view(
                'Mod01_Produccion.RPT_UsuariosHrsApp',
                compact(
                    'actividades',
                    'ultimo'
                  
                )
            );
        } else {
            return redirect()->route('auth/login');
        }
    }
    public function datatables_empleados_app(Request $request)
    {
        //dd($request->get('fi'));
        
        $start = new \MongoDB\BSON\UTCDateTime(strtotime($request->get('fi')." 00:00:00") * 1000);
        $end =   new \MongoDB\BSON\UTCDateTime(strtotime($request->get('ff')." 24:59:59") * 1000);
        //$start = new \MongoDB\BSON\UTCDateTime(strtotime('-2 day') * 1000);
        //$end =   new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000);
        //dd($start);
        $emps = RPTMONGO::raw(function ($collection) use ($start, $end) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'reportDate' => ['$gte' => $start, '$lt' => $end]
                    ]
                ],
               /* [

                    '$group' => [
                        '_id' =>
                        [
                            'emp_nombre' => '$employee.name',
                            'emp_numero' => '$employee.number',
                            'emp_departamento' => '$department.name'
                        ],
                        'total' => [
                            '$sum' => '$hours'
                        ],
                    ],

                ],
                */
            ]);
        });
        
        //obtener lista de empleados sin repetir, para poder sacar las filas
        //correspondientes a cada empleado y poder sumar las horas
        $emp_ids = $emps->unique('employee.id')->pluck('employee.id');
       // dd($emp_ids);
        $k_mongo_emps = [];
        foreach ($emp_ids as $emp_id) {
            //empleados por dia
            $emp_days = $emps->where('employee.id', $emp_id);
            //separar por dias, para ello obtenemos la lista de los dias
            $days = $emp_days->unique('reportDate')->pluck('reportDate');          
            //datos generales para el empleado, independientemente del dia
            $primer_emp = $emp_days->first();
            $nomina = $primer_emp->employee['number'];
            $nombre = $primer_emp->employee['name'];
            $departamento = $primer_emp->department['name'];
            //recorremos por dia, para obtener las horas por dia
            foreach ($days as $day) {
                //dd($emps[0]->reportDate,\DateTime::createFromFormat('d/m/Y',
                //    $day->format('d/m/Y')), $day);
                $emp_day = array_where($emp_days, function ($key, $value) use($day) {                    
                    return ($value->reportDate)->format('d/m/Y') === $day->format('d/m/Y');
                });
                //sacamos la suma por dia, convertimos el string a decimal
                $suma_horas = 0;
                foreach ($emp_day as $ed) {
                    $suma_horas += self::timeString_to_decimal($ed->hours);
                }
                //añadir columna status
                $status = 'SIN ASIGNAR';
                if ($suma_horas < 9.5 && $suma_horas > 0) {
                    $status = 'INCOMPLETO';
                } else if ($suma_horas >= 9.5 && $suma_horas < 10) {
                    $status = 'COMPLETO';
                } else if ($suma_horas == 0) {
                    $status = 'NO REPORTO';
                } else if ($suma_horas > 10) {
                    $status = 'SOBRE CAPTURA';
                }
                //colocamos el empleado con sus horas por dia, en otra vuelta 
                //puede repetirse el empleado, pero tendria que ser otro dia diferente.
                array_push($k_mongo_emps, [
                    'horas_decimal' => $suma_horas,
                    'status' => $status,
                    'nomina' => $nomina, 
                    'nombre' => $nombre,
                    'departamento' => $departamento,
                    'horas' => self::decimal_to_time((string)$suma_horas), 
                    'fecha' => $day->format('d/m/Y')
                ]);
            }
            //dd(($emps[0])->format('d-m-Y'));
            //$empleado = array_where()
        }

        //dd($emps);
        //$emps = RPTMONGO::where('reportDate', '>',  new DateTime('-2 days'))->get();
        //dd($emps);
        //new \MongoDB\BSON\UTCDateTime(new DateTime($daterange[0]))

        //$sum = 0;
        // foreach ($emps as $emp) {
        /*
        $utcdatetime = new MongoDB\BSON\UTCDateTime($emp->reportDate);
        $datetime = $utcdatetime->toDateTime();
        dd($datetime);
        */
        //dd($emp->_id['emp_nombre']);
        //dd($emp->department['name']);
        //dd($emp);
        //$sum += $emp->hours;
        //$sum += $emp->total;
        //array_push($k_mongo_emps, [$emp->employee['number'], $emp->employee['name'], $emp->hours]);

        //   */
        // }

        //75.8  
        $mul_emps = DB::select("SELECT 	EMP_CodigoEmpleado AS CODIGO,
                EMP_Nombre + ' ' + EMP_PrimerApellido + ' ' + 
                EMP_SegundoApellido AS NOMBRE,
                ISNULL (DEP_Nombre, 'SIN DEPTO...') AS DEPARTAMENTO
                from Empleados
                left join Departamentos	on DEP_DeptoId = EMP_DEP_DeptoId
                where EMP_Eliminado = 0 
                and EMP_LIP_LineaProduccionId = '3FFD5508-8EA0-4577-A405-5B2BBE2A449A'");
        //obtener los dias finales.
        $days = array_pluck($k_mongo_emps, 'fecha');
        $faltantes= [];
        foreach ($days as $day) {
            //obtengo empleados del dia (o por dia)
            $emps_day = array_where($k_mongo_emps, function ($key, $value) use ($day) {               
                return $value['fecha'] === $day;
            });
            //obtener los codigos de los empleados para descartarlos de los de muliix
            $en_mongo = array_pluck($emps_day, 'nomina');
            //los faltantes seran los que no esten en nuestra lista de codigos.
            $faltantes = array_where($mul_emps, function ($key, $value) use ($en_mongo) {
                return !in_array( $value->CODIGO, $en_mongo ) ;
            });

            //agregamos empleados faltantes.
            foreach ($faltantes as $emp_faltante) {
                array_push($k_mongo_emps, [
                    'horas_decimal' => 0,
                    'status' => 'NO REPORTO',
                    'nomina' => $emp_faltante->CODIGO,
                    'nombre' => $emp_faltante->NOMBRE,
                    'departamento' => $emp_faltante->DEPARTAMENTO,
                    'horas' => '0:00',
                    'fecha' => $day
                ]);
            }
           
        }

        
            
            
          
            $consulta = collect($k_mongo_emps);
            //dd($consulta);
            //Definimos las columnas 
            $columns = array(
                ["data" => "horas_decimal", "name" => "Horas Decimal"],
                ["data" => "status", "name" => "Estatus"],               
                ["data" => "fecha", "name" => "Fecha"],   
                ["data" => "nomina", "name" => "# Nómina"], //ID OV
                ["data" => "nombre", "name" => "Nombre"],
                ["data" => "departamento", "name" => "Departamento"],
                ["data" => "horas", "name" => "Horas"],
            );
          
            return response()->json(array('data' => $consulta,'columns' => $columns));
      
    }
    public function timeString_to_decimal($timeString){
        $array = explode(":", $timeString);
        return floatval($array[0] + ($array[1] / 60));
    }
    public function decimal_to_time($decimal)
    {
        $parte_entera = floor((int)$decimal);
        if ($decimal - $parte_entera > 0) {
            $val = explode('.', $decimal);
            $hours = floor((int)$val[0]);
            $parte_decimal = floor((int)$val[1]);
        } else {
            $hours = $parte_entera;
            $parte_decimal = 0;
        }
        $minutes = ($parte_decimal / 10) * 60;

        return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT);
    }

}
