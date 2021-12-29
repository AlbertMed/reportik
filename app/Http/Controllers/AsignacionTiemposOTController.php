<?php
/**
 * Created by PhpStorm.
 * User: Muliix-01
 * Date: 07/03/2016
 * Time: 02:06 PM
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Sistema\DAOGeneralController;
use DB;
use Auth;

use App\Modelos\OrdenesTrabajoSeguimientoOperacionDetalle;

use App\Modelos\FabricacionEstructura;
use App\Modelos\OrdenesTrabajo;
use App\Modelos\OrdenesTrabajoSeguimiento;
use App\Modelos\OrdenesTrabajoSeguimientoOperacion;

use App\Http\Controllers\Sistema\EmbarquesController;
use App\RPTMONGO;
 ini_set('memory_limit', '-1');
 set_time_limit(0);
class AsignacionTiemposOTController extends Controller {
    public function __construct()
    {
        // check if session expired for ajax request
        $this->middleware('ajax-session-expired');

        // check if user is autenticated for non-ajax request
        $this->middleware('auth');
    }
    public function storeOT(){
       
        //dd(RPTMONGO::all(), EmbarquesController::getNuevoId());
        
        $start = new \MongoDB\BSON\UTCDateTime(strtotime('-25 day') * 1000);
        $end =   new \MongoDB\BSON\UTCDateTime(strtotime("now") * 1000);
        
        $emps = RPTMONGO::raw(function ($collection) use ($start, $end) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'reportDate' => ['$gte' => $start, '$lt' => $end]
                    ]
                ],
                
            ]);
        });
        $idEmpleado =
            DB::table('Empleados')
            ->where('EMP_CodigoEmpleado', Auth::user()->nomina)
            ->value('EMP_EmpleadoId');
        //dd($idEmpleado);
        if (count($emps) == 0) {
            return //json_encode(
                [
                    "Respuesta" => [
                        [
                            'InformacionError' => [],
                            'Estatus' => 'Procesado',
                            'Mensaje' => "No hay registros capturados en Mongo."
                        ]
                    ]
                ];
        } else {        
        
            foreach ($emps as $emp) {
                //el siguiente if verifica que los campos sean uniqueidentifie
                if (strlen($emp->ot['itemId']) >= 36 && strlen( $emp->department['id']) >= 36) {                                   
                    $operacionId = DB::select('SELECT FAE_EstructuraId
                        from FabricacionEstructura
                        inner join Fabricacion on FAB_FabricacionId = FAE_FAB_FabricacionId
                        inner join Articulos on ART_ArticuloId = FAB_ART_ArticuloId
                        Where ART_ArticuloId = ?
                        and FAE_DEP_DeptoId = ?', [$emp->ot['itemId'], $emp->department['id']]);                         
                    if (count($operacionId) == 1) {                
                        $operacionId = $operacionId[0]->FAE_EstructuraId;
                    } else {
                        $operacionId = null;
                    }
                    
                    $detalleId = EmbarquesController::getNuevoId();
                    $existe = DB::table('RPT_Mongo_OT_Muliix')
                    ->where('MOT_REPMON_Id', $emp->_id)
                    ->count();
                    if ($existe == 0) {
                        \DB::beginTransaction();
                        DB::insert('INSERT into RPT_Mongo_OT_Muliix
                        (MOT_REPMON_Id, MOT_MUL_id, MOT_FechaCreacion) values (?, ?, ?)', [$emp->_id, $detalleId, date('Ymd h:m:s')]);
                    
                        $arraySeguimientoMon = [
                            "IdOT" => $emp->ot['id']
                            ,"EmpleadoId" => $idEmpleado
                            ,"Operaciones" => [[
                                "OperacionId" => $operacionId
                                ,"Detalle" => [[
                                    "ReferenciaId" => $emp->workCenter['id']
                                    ,'TurnoId' => '73C0843B-568F-4C47-A8FD-8F8BE4ED88A7'
                                    ,"CantidadTrabajada" => $emp->ot['amount']
                                    ,"TiempoEfectivo" => strlen( $emp->hours == 8) ? $emp->hours : $emp->hours.':00'
                                    ,"Fecha" => ($emp->reportDate)->format('Y-d-m')
                                    ,"Operadores" => $emp->employee['id']
                                    ,"Calidad" => '00:00:00'
                                    ,"Mantenimiento" => '00:00:00'
                                    ,"Planeacion" => '00:00:00'
                                    ,"Produccion" => '00:00:00'
                                    ,"CalidadComentario" => '00:00:00'
                                    ,"MantenimientoComentario" => ''
                                    ,"PlaneacionComentario" => ''
                                    ,"ProduccionComentario" => ''
                                    ,"Desperdicio" => 0
                                    ,"TiempoExtra" => '00:00:00'
                                ]]
                                ,"DetalleId" => $detalleId
                            ]]  
                        ];
                    
                        date_default_timezone_set('America/Mexico_City');

                        //file_put_contents("logs/TiemposOT.txt", date("Y-m-d | h:i:sa") . " -->  " . \Illuminate\Support\Facades\Request::input('seguimientoOT') . "\r\n", FILE_APPEND);
                    
                        $resultM = AsignacionTiemposOTController::guardaSeguimientoOT($arraySeguimientoMon, 2);
                        //dd($resultM);
                    }
                }
            }
        }
    }
    public function test(){
        $arraySeguimiento = [
            "IdOT" => '777'
            ,"EmpleadoId" => '7'
            ,"Operaciones" => [
                "OperacionId" => 1
                ,"Detalle" => [
                    "ReferenciaId" => 1
                    ,'TurnoId' => 1
                    ,"CantidadTrabajada"
                    ,"TiempoEfectivo"
                    ,"Fecha"
                    ,"Operadores"
                    ,"Calidad"
                    ,"Mantenimiento"
                    ,"Planeacion"
                    ,"Produccion"
                    ,"CalidadComentario"
                    ,"MantenimientoComentario"
                    ,"PlaneacionComentario"
                    ,"ProduccionComentario"
                    ,"Desperdicio"
                    ,"TiempoExtra"
                ]
                ,"DetalleId"
            ]
            
        ];

        //OrdenesTrabajoSeguimientoOperacionDetalle
        
        /*
        OTSOD_DetalleId = $z == 0 ? $arrayOperacion[$x]['DetalleId'] : EmbarquesController::getNuevoId();
        OTSOD_TiempoTotal => $detalles[$z]['TiempoEfectivo'];
        OTSOD_EMP_OperadorId => $detalles[$z]['Operadores']
        OTSOD_FechaRegistroInicio = $detalles[$z]['Fecha']
        OTSOD_FechaRegistroFin = $detalles[$z]['Fecha'];
        OTSOD_EMP_ModificadoPorId = $empleadoId;
        OTSOD_CantidadTrabajada = $detalles[$z]['CantidadTrabajada'];
                            OTSOD_TiempoCalidad = $detalles[$z]['Calidad'] == '' ? null : $detalles[$z]['Calidad'];
                            OTSOD_TiempoMantenimiento = $detalles[$z]['Mantenimiento'] == '' ? null : $detalles[$z]['Mantenimiento'];
                            OTSOD_TiempoPlaneacion = $detalles[$z]['Planeacion'] == '' ? null : $detalles[$z]['Planeacion'];
                            OTSOD_TiempoProduccion = $detalles[$z]['Produccion'] == '' ? null : $detalles[$z]['Produccion'];
                            OTSOD_TiempoCalidadComentario = $detalles[$z]['CalidadComentario'];
                            OTSOD_TiempoMantenimientoComentario = $detalles[$z]['MantenimientoComentario'];
                            OTSOD_TiempoPlaneacionComentario = $detalles[$z]['PlaneacionComentario'];
                            OTSOD_TiempoProduccionComentario = $detalles[$z]['ProduccionComentario'];
                            OTSOD_Desperdicio = $detalles[$z]['Desperdicio'] == '' ? 0 : $detalles[$z]['Desperdicio'];
                            OTSOD_TUR_TurnoId = $detalles[$z]['TurnoId'];
                            OTSOD_TiempoExtra = $detalles[$z]['TiempoExtra'];
                            $seguimientoDetalle->OTSOD_MAQ_MaquinaId = $maquinaId;
                            $seguimientoDetalle->OTSOD_CET_CentroTrabajoId = $centroTrabajoId;
                            $seguimientoDetalle->save();
        
        */
    }
    
    public function procesaTiemposOT(){


        //        $json = json_encode(
        //            [
        //                "SeguimientoOT" => [
        //                        'Operaciones' => [
        //                            ['OperacionId' => 'D5AA1745-7578-4469-B664-8E640E292F8E',
        //                             'Detalle' => [
        //                                 [
        //                                     'CentroTrabajoId' => '',
        //                                     'ReferenciaId'=> 'FB004D7A-EAC1-2C63-4463-6EB6F01E54AA',
        //                                     'TiempoEfectivo' => '05:05:00',
        //                                     'Operadores' => 0,
        //                                     'CantidadTrabajada'=> 2356.8,
        //                                     'Calidad'=> '00:00:00',
        //                                     'Mantenimiento'=> '00:00:00',
        //                                     'Planeacion'=> '00:00:00',
        //                                     'Produccion'=> '00:00:00',
        //                                     'CalidadComentario'=> '',
        //                                     'MantenimientoComentario'=> '',
        //                                     'PlaneacionComentario'=> '',
        //                                     'ProduccionComentario'=> '',
        //                                     'Desperdicio' => 0,
        //                                     'TiempoExtra' => '00:00:00',
        //                                     'Fecha'=>'2020410',
        //                                     'TurnoId'=>'F4CB44EC-73D9-4117-8D75-DB3F4508633D'
        //                                 ]
        //                             ],
        //                             'DetalleId'=>'AAd40b67-d65a-4eee-9ee3-a20886eebd89'
        //                            ]
        //                        ],
        //                        'IdOT' => 'BC4BF27C-13C5-457F-859B-0E7AC96B1F63',
        //                        'EmpleadoId'=> '3A2D4A67-BB29-493B-BFB1-3A1A03310372'
        ////
        //                ]
        //            ]
        //        );


        //        $jsonResponse = json_encode(
        //            [
        //                "Respuesta" => [
        //                    [
        //                        'InformacionError' => [
        //                            ['Lote' => $lote->LOT_CodigoLote,
        //                                'Localidad' => $codigoLocalidad ." - ".$localidad->LOC_Nombre,
        //                                'Articulo'=> $lote->ART_CodigoArticulo
        //                            ]
        //                        ],
        //                        'Estatus' => 'Error',
        //                        'Mensaje'=> "No es posible sacar la cantidad de ". abs($cantidadTraspasar) .", ya que su existencia es de " . $loteLocalidad[0]->LOTL_Cantidad . "."
        //                    ]
        //                ]
        //            ]
        //        );

            $jsonSeguimiento = json_decode(\Illuminate\Support\Facades\Request::input('seguimientoOT'), true);            
    

        //dd($json);
        //$jsonSeguimiento = json_decode($json, true);

        //dd($jsonRecibo['Recibo']);
        // LotesRecibosController::guardaReciboLote($jsonRecibo['Recibo']);
        date_default_timezone_set('America/Mexico_City');

        file_put_contents("logs/TiemposOT.txt", date("Y-m-d | h:i:sa")." -->  ".\Illuminate\Support\Facades\Request::input('seguimientoOT')."\r\n",FILE_APPEND);

        AsignacionTiemposOTController::guardaSeguimientoOT($jsonSeguimiento['SeguimientoOT'],1);

        //return $jsonTraspasos;

    }

    public function guardaSeguimientoOT($arraySeguimiento,$movil){

       
        try {

            

            $idOT = $arraySeguimiento['IdOT'];
            $empleadoId = $arraySeguimiento['EmpleadoId'];
            $OT = OrdenesTrabajo::find($idOT);
//            $idArticuloOT =  \DB::select(\DB::raw("
//                                    SELECT OTDA_ART_ArticuloId FROM OrdenesTrabajoDetalleArticulos
//                                    WHERE OTDA_OT_OrdenTrabajoId = '$idOT'
//                                "))[0]->OTDA_ART_ArticuloId;

            $datosSeguimientoOT = \DB::select(\DB::raw("SELECT OTS_OrdenesTrabajoSeguimientoId, replace(cast(getdate() as date),'-','') AS FECHA FROM OrdenesTrabajoSeguimiento WHERE OTS_OT_OrdenTrabajoId = '$idOT'"));

            $isNuevo = count($datosSeguimientoOT) <= 0 ? true : false;

            $seguimientoOT = null;

//            $isTiemposSecuencial = ArticulosGestionOperativa::isTiemposFabricacionSecuencial($idArticuloOT);
//
//            if($isTiemposSecuencial) {
//                $hayAnterioresOT = self::isOTsSinTiempos($idArticuloOT, $OT->OT_Codigo);
//
//                if ($hayAnterioresOT) {
//                    throw new \Exception(" Existen OTs anteriores por asignar material", 310);
//                }
//            }

            if($isNuevo){
                $seguimientoOT = new OrdenesTrabajoSeguimiento();
                $seguimientoOT->OTS_OrdenesTrabajoSeguimientoId = EmbarquesController::getNuevoId();//$arraySeguimiento['IdSeguimientoOT'];;
                $seguimientoOT->OTS_OT_OrdenTrabajoId = $idOT;
                clock($seguimientoOT->OTS_OrdenesTrabajoSeguimientoId);

//                $seguimientoOT->OTS_TiempoInicio = $arraySeguimiento['TiempoInicio'];
//                $seguimientoOT->OTS_TiempoFin = $arraySeguimiento['TiempoFin'];
//                $seguimientoOT->OTS_TiempoTotal = $arraySeguimiento['TiempoTotal'];
                try {
                    $seguimientoOT->save();
                } catch (\Illuminate\Database\QueryException $ex) {
                    $results = \DB::select(\DB::raw("select * from OrdenesTrabajoSeguimiento
                                    where OTS_OrdenesTrabajoSeguimientoId  = '" . $seguimientoOT->OTS_OrdenesTrabajoSeguimientoId . "'"));

                    if (sizeof($results) > 0)
                        throw new \Exception(" Informacion Enviada ", 301);
                    throw $ex;
                    // throw new \Exception(" Guardar Traspaso ", 304);
                }
            }
            else{
                $seguimientoOT = OrdenesTrabajoSeguimiento::find($datosSeguimientoOT[0]->OTS_OrdenesTrabajoSeguimientoId);
//                $seguimientoOT->OTS_TiempoInicio = $arraySeguimiento['TiempoInicio'];
//                $seguimientoOT->OTS_TiempoFin = $arraySeguimiento['TiempoFin'];
//                $seguimientoOT->OTS_TiempoTotal = $arraySeguimiento['TiempoTotal'];
                $seguimientoOT->OTS_FechaUltimaModificacion = $datosSeguimientoOT[0]->FECHA;
                $seguimientoOT->save();
            }

            $idSeguimiento = $seguimientoOT->OTS_OrdenesTrabajoSeguimientoId;

            $arrayOperacion = $arraySeguimiento['Operaciones'];
           
            $longArrayOperacion = count($arrayOperacion);

            //$dao = new DAOGeneralController();

            for($x=0;$x<$longArrayOperacion;$x++){

                $idOperacion = $arrayOperacion[$x]['OperacionId'];
                  
                $informacionOperacion = DB::select("SELECT OTSO_OrdenTrabajoSeguimientoOperacionId
                                                        FROM OrdenesTrabajoSeguimientoOperacion
                                                        WHERE OTSO_FAE_EstructuraId  = ?
                                                        AND  OTSO_OTS_OrdenesTrabajoSeguimientoId = ?", [$idOperacion, $idSeguimiento]);
                
               
                $isNuevaOperacion = count($informacionOperacion) <= 0 ? true : false;

                $seguimientoOperacionOT = null;
                
                clock('es nuevo? ',$isNuevaOperacion);
                if($isNuevaOperacion){
                    $seguimientoOperacionOT = new OrdenesTrabajoSeguimientoOperacion();
                    $seguimientoOperacionOT->OTSO_OrdenTrabajoSeguimientoOperacionId =  EmbarquesController::getNuevoId();
                    clock('id del nuevo OrdenesTrabajoSeguimientoOperacion ',$seguimientoOperacionOT->OTSO_OrdenTrabajoSeguimientoOperacionId);
                    $seguimientoOperacionOT->OTSO_FAE_EstructuraId = $idOperacion;
                    $seguimientoOperacionOT->OTSO_OTS_OrdenesTrabajoSeguimientoId = $idSeguimiento;
//                    $seguimientoOperacionOT->OTSO_TiempoInicio = $arrayOperacion[$x]['TiempoInicio'];
//                    $seguimientoOperacionOT->OTSO_TiempoFinal = $arrayOperacion[$x]['TiempoFinal'];
//                    $seguimientoOperacionOT->OTSO_TiempoTotal = $arrayOperacion[$x]['TiempoTotal'];

                    try {
                        $seguimientoOperacionOT->save();
                    } catch (\Illuminate\Database\QueryException $ex) {
                        $results = \DB::select(\DB::raw("select * from OrdenesTrabajoSeguimientoOperacion
                                    where OTSO_OrdenTrabajoSeguimientoOperacionId  = '" . $seguimientoOperacionOT->OTSO_OrdenTrabajoSeguimientoOperacionId . "'"));

                        if (sizeof($results) > 0)
                            throw new \Exception(" Informacion Enviada ", 301);
                        throw $ex;
                        // throw new \Exception(" Guardar Traspaso ", 304);
                    }
                }
                else{
                    $seguimientoOperacionOT = OrdenesTrabajoSeguimientoOperacion::find($informacionOperacion[0]->OTSO_OrdenTrabajoSeguimientoOperacionId);
//                    $seguimientoOperacionOT->OTSO_TiempoInicio = $arrayOperacion[$x]['TiempoInicio'];
//                    $seguimientoOperacionOT->OTSO_TiempoFinal = $arrayOperacion[$x]['TiempoFinal'];
//                    $seguimientoOperacionOT->OTSO_TiempoTotal = $arrayOperacion[$x]['TiempoTotal'];
//                    $seguimientoOperacionOT->save();
                }
           

                $idSeguimientoOperacion = $seguimientoOperacionOT->OTSO_OrdenTrabajoSeguimientoOperacionId;
                clock($idSeguimientoOperacion);
                $operacion = FabricacionEstructura::find($seguimientoOperacionOT->OTSO_FAE_EstructuraId);
                $operacionNombre = $operacion->FAE_Descripcion;

                $empleadosAsignados = \DB::select(\DB::raw("
                        select OTARE_EMP_EmpleadoId, GETDATE() AS FechaHoy from OrdenesTrabajoAsignacionRecursosEmpleados
                        where OTARE_OT_OrdenTrabajoId = '$idOT' and OTARE_Eliminado = 0
                    "));
               
                $longEmpleadosAsignados = count($empleadosAsignados);
                $longEmpleadosAsignados = 1;
                if($longEmpleadosAsignados == 0){
                    throw new \Exception("Error: " . 'No hay empleados asignados.',304);
                }
                else {

                    $detalles = $arrayOperacion[$x]['Detalle'];
                    $cantDetalles = count($detalles);
                   
                    for($z=0; $z<$cantDetalles; $z++){

                        $referenciaId = $detalles[$z]['ReferenciaId'] == '' ? null : $detalles[$z]['ReferenciaId'];
                        $turnoId = $detalles[$z]['TurnoId'];
                        $centroTrabajoId = null;
                        $maquinaId = null;

                        if($turnoId == '' || $turnoId == null){
                            throw new \Exception("Error: " . 'El turno es un dato obligatorio.',304);
                        }

                        if($detalles[$z]['CantidadTrabajada'] == '' || $detalles[$z]['CantidadTrabajada'] == null
                            || $detalles[$z]['CantidadTrabajada'] == 0){
                            throw new \Exception("Error: " . 'La cantidad trabajada es un dato obligatorio.',304);
                        }
                       
                        if($detalles[$z]['TiempoEfectivo'] != null && $detalles[$z]['ReferenciaId'] != null){

                            list($horas, $minutos, $segundos) = explode(':', $detalles[$z]['TiempoEfectivo']);

                            if($horas > 23){
                                throw new \Exception("Error: " . 'Las horas del tiempo efectivo deben ser menor a 23.',304);
                            }
                            else if($minutos > 59){
                                throw new \Exception("Error: " . 'Los minutos del tiempo efectivo deben ser menor a 59.',304);
                            }
                            else if($segundos > 59){
                                throw new \Exception("Error: " . 'Los segundos del tiempo efectivo deben ser menor a 59.',304);
                            }


                            /*
                            //cambio para obtener el id de la maquina. 25/11/2021 - Beto
                            $maquina = \DB::select(\DB::raw("
                                select MAQ_MaquinaId 
                                from Maquinas where MAQ_MaquinaId = '$referenciaId'
                                and MAQ_Borrado = 0
                            "));
                            */
                            $maquina = \DB::select(\DB::raw("SELECT MAQ_MaquinaId 
                                from CentrosTrabajo
                                inner join Maquinas on CET_CentroTrabajoId = MAQ_CET_CentroTrabajoId
                                Where MAQ_CMM_TipoMaquinaId = '7E6CD62F-B9CD-4909-A37E-8AA7BF400BC6' and MAQ_Borrado = 0
                                and CET_CentroTrabajoId = '$referenciaId'"));                     

                            if(count($maquina) > 0){
                                $maquinaId = $maquina[0]->MAQ_MaquinaId;
                            }
                            else{
                                $centroTrabajo = \DB::select(\DB::raw("
                                    select CET_CentroTrabajoId 
                                    from CentrosTrabajo where CET_CentroTrabajoId = '$referenciaId'
                                    and CET_Borrado = 0
                                "));

                                if(count($centroTrabajo) > 0){
                                    $centroTrabajoId = $centroTrabajo[0]->CET_CentroTrabajoId;
                                }
                                else{
                                    throw new \Exception("Error: " . 'Es obligatorio elegir una máquina o centro de trabajo.',304);
                                }
                            }

                            if(strlen($detalles[$z]['Fecha']) == 7){
                                $fechaArray = str_split($detalles[$z]['Fecha'], 4);
                                $diaMes = str_split($fechaArray[1],1);
                                $detalles[$z]['Fecha'] = $fechaArray[0] . "0" . $diaMes[0] . $diaMes[1] . $diaMes[2];
                            }
                            else if(strlen($detalles[$z]['Fecha']) == 6){
                                $fechaArray = str_split($detalles[$z]['Fecha'], 4);
                                $diaMes = str_split($fechaArray[1],1);
                                $detalles[$z]['Fecha'] = $fechaArray[0] . "0" . $diaMes[0] . "0" . $diaMes[1];
                            }

                            $fechaDato = $detalles[$z]['Fecha'];

                            $fechaHoy = date("Ymd");
                            $fechaUsuario = date("Ymd", strtotime($fechaDato));

                            if($fechaUsuario > $fechaHoy){
                                throw new \Exception("Error: " . 'No es posible asignar los tiempos a una fecha posterior al día de hoy.',304);
                            }

                            /*$tiempoRegistrado = \DB::select(\DB::raw("
                                select OTSOD_DetalleId from OrdenesTrabajoSeguimientoOperacionDetalle
                                where OTSOD_OTSO_OrdenTrabajoSeguimientoOperacionId = '$idSeguimientoOperacion'
                                AND OTSOD_MAQ_MaquinaId = '$referenciaId'
                                and OTSOD_Eliminado = 0
                                AND OTSOD_TUR_TurnoId = '$turnoId'
                                AND CAST(OTSOD_FechaRegistroInicio AS DATE) = '$fechaDato'
                            "));

                            if (count($tiempoRegistrado) <= 0) {
                                $tiempoRegistrado = \DB::select(\DB::raw("
                                select OTSOD_DetalleId from OrdenesTrabajoSeguimientoOperacionDetalle
                                where OTSOD_OTSO_OrdenTrabajoSeguimientoOperacionId = '$idSeguimientoOperacion'
                                AND OTSOD_CET_CentroTrabajoId = '$referenciaId'
                                and OTSOD_Eliminado = 0
                                AND OTSOD_TUR_TurnoId = '$turnoId'
                                AND CAST(OTSOD_FechaRegistroInicio AS DATE) = '$fechaDato'
                                "));
                            }



                            if (count($tiempoRegistrado) > 0) {
                                $seguimientoDetalle = OrdenesTrabajoSeguimientoOperacionDetalle::find($tiempoRegistrado[0]->OTSOD_DetalleId);
                            } else {
                                $seguimientoDetalle = new OrdenesTrabajoSeguimientoOperacionDetalle();
                            }*/

                            $seguimientoDetalle = new OrdenesTrabajoSeguimientoOperacionDetalle();
                            $seguimientoDetalle->OTSOD_DetalleId = $z == 0 ? $arrayOperacion[$x]['DetalleId'] : EmbarquesController::getNuevoId();
                            clock($seguimientoDetalle->OTSOD_DetalleId);
                            $seguimientoDetalle->OTSOD_OTSO_OrdenTrabajoSeguimientoOperacionId = $idSeguimientoOperacion;
                            $seguimientoDetalle->OTSOD_TiempoTotal = $detalles[$z]['TiempoEfectivo'];
                            //$seguimientoDetalle->OTSOD_CantidadOperadores= $detalles[$z]['Operadores'] == '' ? 0 : $detalles[$z]['Operadores'];
                            $seguimientoDetalle->OTSOD_EMP_OperadorId= $detalles[$z]['Operadores'] == '' ? null : $detalles[$z]['Operadores'];
                            $seguimientoDetalle->OTSOD_FechaRegistroInicio = $detalles[$z]['Fecha'];
                            $seguimientoDetalle->OTSOD_FechaRegistroFin = $detalles[$z]['Fecha'];
                            $seguimientoDetalle->OTSOD_EMP_ModificadoPorId = $empleadoId;
                            $seguimientoDetalle->OTSOD_CantidadTrabajada = $detalles[$z]['CantidadTrabajada'];
                            $seguimientoDetalle->OTSOD_TiempoCalidad = $detalles[$z]['Calidad'] == '' ? null : $detalles[$z]['Calidad'];
                            $seguimientoDetalle->OTSOD_TiempoMantenimiento = $detalles[$z]['Mantenimiento'] == '' ? null : $detalles[$z]['Mantenimiento'];
                            $seguimientoDetalle->OTSOD_TiempoPlaneacion = $detalles[$z]['Planeacion'] == '' ? null : $detalles[$z]['Planeacion'];
                            $seguimientoDetalle->OTSOD_TiempoProduccion = $detalles[$z]['Produccion'] == '' ? null : $detalles[$z]['Produccion'];
                            $seguimientoDetalle->OTSOD_TiempoCalidadComentario = $detalles[$z]['CalidadComentario'];
                            $seguimientoDetalle->OTSOD_TiempoMantenimientoComentario = $detalles[$z]['MantenimientoComentario'];
                            $seguimientoDetalle->OTSOD_TiempoPlaneacionComentario = $detalles[$z]['PlaneacionComentario'];
                            $seguimientoDetalle->OTSOD_TiempoProduccionComentario = $detalles[$z]['ProduccionComentario'];
                            $seguimientoDetalle->OTSOD_Desperdicio = $detalles[$z]['Desperdicio'] == '' ? 0 : $detalles[$z]['Desperdicio'];
                            $seguimientoDetalle->OTSOD_TUR_TurnoId = $detalles[$z]['TurnoId'];
                            $seguimientoDetalle->OTSOD_TiempoExtra = $detalles[$z]['TiempoExtra'];
                            if($maquinaId != null) {
                                $seguimientoDetalle->OTSOD_MAQ_MaquinaId = $maquinaId;
                            }
                            elseif($centroTrabajoId != null){
                                $seguimientoDetalle->OTSOD_CET_CentroTrabajoId = $centroTrabajoId;
                            }

                            try {
                                $seguimientoDetalle->save();
                            } catch (\Illuminate\Database\QueryException $ex) {

                                $results = \DB::select(\DB::raw("select * from OrdenesTrabajoSeguimientoOperacionDetalle
                                      where OTSOD_DetalleId  = '" . $seguimientoDetalle->OTSOD_DetalleId . "'"));

                                if (sizeof($results) > 0)
                                    throw new \Exception(" Informacion Enviada ", 301);

                                throw $ex;
                                // throw new \Exception(" Guardar Traspaso ", 304);
                            }

                        }

                    }

                }


            }

            \DB::commit();
            //\DB::rollback();

            if($movil == 1){

                echo json_encode(
                    [
                        "Respuesta" => [
                            [
                                'InformacionError' => [],
                                'Estatus' => 'Procesado',
                                'Mensaje' => "La transacción fue realizada exitosamente."
                            ]
                        ]
                    ]
                );

            }
            else{

                return //json_encode(
                    [
                        "Respuesta" => [
                            [
                                'InformacionError' => [],
                                'Estatus' => 'Procesado',
                                'Mensaje' => "La transacción fue realizada exitosamente."
                            ]
                        ]
                    ];
                //);

            }

        } catch (\Exception $e) {
            \DB::rollback();
            if($movil == 1){

                echo json_encode(
                    [
                        "Respuesta" => [
                            [
                                'InformacionError' => "Error: " .$e->getMessage(). " Linea: ".$e->getLine(),
                                'Estatus' => 'Error',
                                'Mensaje'=> "Error: " .$e->getMessage(). " Linea: ".$e->getLine()
                            ]
                        ]
                    ]
                );

            }
            else{

                return //json_encode(
                    [
                        "Respuesta" => [
                            [
                                'InformacionError' => "Error: " .$e->getMessage(). " Linea: ".$e->getLine(),
                                'Estatus' => 'Error',
                                'Mensaje'=> "Error: " .$e->getMessage(). " Linea: ".$e->getLine()
                            ]
                        ]
                    ];
                //);

            }

        }

    }

   
} 