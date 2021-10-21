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
                [

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

            ]);
        });
        //dd($emps);
        //$emps = RPTMONGO::where('reportDate', '>',  new DateTime('-2 days'))->get();
        //dd($emps);
        //new \MongoDB\BSON\UTCDateTime(new DateTime($daterange[0]))
        $names = [];
        //$sum = 0;
        foreach ($emps as $emp) {
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
            //array_push($names, [$emp->employee['number'], $emp->employee['name'], $emp->hours]);
            array_push($names, [
                'nomina' => $emp->_id['emp_numero'], 
                'nombre' => $emp->_id['emp_nombre'],
                'departamento' => $emp->_id['emp_departamento'],
                'horas' => $emp->total, 
                'fecha_hora' => self::decimal_to_time((string)$emp->total)
            ]);
            //   */
        }
      
    //75.8
          
            $consulta = collect($names);
            //dd($consulta);
            //Definimos las columnas 
            $columns = array(
                ["data" => "nomina", "name" => "# Nómina"], //ID OV
                ["data" => "nombre", "name" => "Nombre"],
                ["data" => "departamento", "name" => "Departamento"],
                ["data" => "horas", "name" => "Horas Decimal"],
                ["data" => "fecha_hora", "name" => "Horas"],               
            );
          
            return response()->json(array('data' => $consulta,'columns' => $columns));
      
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
