<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SIDModel;
use Illuminate\Support\Facades\Validator;

class SIDController extends Controller
{

    public function __construct()
    {
        $this->sidModel = new SIDModel();
        $this->_destinationPath = 'digitalStorage/';
        $this->moduleType = 'SID';
        if (!Auth::check()) {
            return redirect()->route('auth/login');
        }
    }


    /**
     * Get Table Columns 
     * @param $moduleType String
     * @return Array
     */
    private function _getTableColumns($moduleType)
    {

        $columns = $this->sidModel->getColumns();
        $titleColumns = [];
        $columnsToAdd = array(
            "LLAVE_ID" => array("title" => "Llave"),
            "GRUPO_ID" => array("title" => "Grupo"),
            "DOC_ID" => array("title" => "DOC ID"),
            "ARCHIVO_1" => array("title" => "Archivo 1"),
            "ARCHIVO_2" => array("title" => "Archivo 2"),
            "ARCHIVO_3" => array("title" => "Archivo 3"),
            "ARCHIVO_4" => array("title" => "Archivo 4"),
        );
        return $columnsToAdd;
    }
    /**
     * Get OT Table Columns 
     * @return Array
     */
    private function _getOTTableColumns()
    {

        $columns = $this->sidModel->getColumns();
        $titleColumns = [];
        $columnsToAdd = array(
            "OT" => array("title" => "OT"),
            "COD_ARTICULO" => array("title" => "Cod Articulo"),
            "NOB_ARTICULO" => array("title" => "Nob Articulo"),
            "OV" => array("title" => "OV"),
            "COD_PROY" => array("title" => "COD Proy"),
            "PROYECTO" => array("title" => "Proyecto"),

        );
        return $columnsToAdd;
    }
    /**
     * Get Columns to insert into db table. Labels, column, type
     * @return array $result
     */
    private function _getInsertColumns()
    {
        $insertColumns = array(
            0 => array("label" => "Area", "name" => "AREA", "id" => "OTInsertArea", "type" => "text", "readonly" => "readonly"),
            1 => array("label" => "OT", "name" => "OT", "id" => "OTInsertOT", "type" => "text", "readonly" => "readonly"),
            2 => array("label" => "Archivo 1",  "name" => "ARCHIVO_1", "id" => "OTInsertArchivo1", "type" => "file", "readonly" => ""),
            3 => array("label" => "Archivo 2",  "name" => "ARCHIVO_2", "id" => "OTInsertArchivo2", "type" => "file", "readonly" => ""),
            4 => array("label" => "Archivo 3",  "name" => "ARCHIVO_3", "id" => "OTInsertArchivo3", "type" => "file", "readonly" => ""),
            5 => array("label" => "Archivo 4",  "name" => "ARCHIVO_4", "id" => "OTInsertArchivo4", "type" => "file", "readonly" => ""),
        );
        return $insertColumns;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $editable = false;
        $dataArray = array("title_page" => $this->sidModel->getConfigRow($this->moduleType)->MENU_NAME);
        $dataArray["columns"] = $this->_getTableColumns($this->moduleType);
        $dataArray["editable"] = true;
        $dataArray["module_type"] = $this->moduleType;
        return view("DigitalStorage.sid.index", compact('actividades', 'ultimo', 'dataArray'));
    }

    /**
     * Find if Work order is in the table
     * @param  \Illuminate\Http\Request $request
     * @return mixed $response
     */
    public function getWorkOrdersData(Request $request, $workOrder)
    {
        //WE FOUND DATA!!
        $detailData = $this->_getColumnsData($this->sidModel->getOTDetailData($workOrder)); //Checks if order has been closed
        $orderClose = $detailData !== false ? true : false;
        $collectionData = $this->_getColumnsData($this->sidModel->getOT($workOrder)); //loads data from db
        $collectionData["workOrderClosed"] = $orderClose;
        return json_encode($collectionData);
    }

    /**
     * Get Columns data from query and returns array of columns and data asoc
     * @param object $collection
     * @return array $result
     */
    private function _getColumnsData($collection)
    {
        if (count($collection) == 0) {
            return false;
        }
        $columns = array_keys((array)$collection[0]);
        return  array("columns" => $columns, "data" => $collection[0]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $dataArray = [];
        $dataArray['insert'] = true;
        $dataArray["module_type"] = $this->moduleType;
        $dataArray["data_area"] = $this->sidModel->getArea();
        $dataArray["orden_trabajo"]['columns'] = $this->_getOTTableColumns();
        $dataArray["insertColumns"] = $this->_getInsertColumns();
        return view("DigitalStorage.sid.edit", compact('user', 'actividades', 'ultimo', 'dataArray'));
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
        $fileArray = array(
            "ARCHIVO_1",
            "ARCHIVO_2",
            "ARCHIVO_3",
            "ARCHIVO_4",
        );
        $saveDataFiles = array();
        $area = explode("_", $request->input("AREA"));
        $ordenTrabajo = $request->input("OT");
        // $archivo_1 = $request->input("ARCHIVO_1");
        // $archivo_2 = $request->input("ARCHIVO_2");
        // $archivo_3 = $request->input("ARCHIVO_3");
        // $archivo_4 = $request->input("ARCHIVO_4");
        $params = array(
            "user_modified" => $request->get('user_modified'),
            "LLAVE_ID" => "SID" . $ordenTrabajo . 'DEP' . $area[0],
            "GRUPO_ID" => 'DEP' . $request->input("AREA"),
            "DOC_ID" => $ordenTrabajo,
        );
        $id = $this->sidModel->newRow($params);
        $newDestinationPath = $this->_destinationPath . $ordenTrabajo . "/";

        foreach ($fileArray as $fileName) {
            $file = $request->file($fileName);
            $saveDataFiles[$fileName] = "";
            if (!is_null($file)) {
                $originalFile = $file->getClientOriginalName();
                $saveDataFiles[$fileName] = url() . "/" . $newDestinationPath . $originalFile;
                $file->move($newDestinationPath, $originalFile);
            }
        }
        $fileUploads = array(
            "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
            "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
            "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
            "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
        );
        $request->get("baseURLAlmacen");
        $fileUploads = array(
            "LLAVE_ID" => "SID" . $ordenTrabajo . 'DEP' . $area[0],
            "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
            "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
            "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
            "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
        );


        $this->sidModel->updateData($fileUploads, $id);
        return redirect()->route("SIDDOCS");
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
