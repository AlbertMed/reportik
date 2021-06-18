<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use App\DigitalStorage as DigStrore;
use Illuminate\Support\Facades\Validator;


class DigitalStorage extends Controller
{

    public function __construct()
    {
        if (!Auth::check()) {
            return redirect()->route('auth/login');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digStoreList = $digStoreModel->getList($request);

        // $ventasList = [];//$digStoreModel->getSalesList();

        return view("DigitalStorage.index", compact('actividades', 'ultimo', 'digStoreList'));
    }

    public function notFound()
    {
        return "Pagina en construccion, porfavor espera";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $actividades = $user->getTareas();
            $ultimo = count($actividades);
            $digStoreModel = new DigStrore();
            $digRowDetails = $digStoreModel->getSchema();
            $inputType = [];
            $hiddenValues = array(
                "created_at",
                "last_modified",
                "id",
                "user_modified"
            );
            $tblColsToSpan = array(
                "created_at" => "Creado en",
                "last_modified" => "Ultima modificacion",
                "id" => "Id",
                "LLAVE_ID" => "LLAVE ID",
                "GRUPO_ID" => "Grupo Id",
                "DOC_ID" => "Doc Id",
                "ARCHIVO_1" => "Archivo 1",
                "ARCHIVO_2" => "Archivo 2",
                "ARCHIVO_3" => "Archivo 3",
                "ARCHIVO_4" => "Archivo 4",
                "ARCHIVO_XML" => "Archivo XML",
                "importe" => "Importe",
                "user_modified" => "Ultimo usuario modificado",
                "POLIZA_MUL" => "POLIZA MUL",
                "CAPUTRADA" => "Capturada",
                "CAPT_POR" => "Capturada Por",
                "AUTORIZADO" => "Autorizado",
                "AUTO_POR" => "Autorizado Por",
                "POLIZA_CONT" => "POLIZA CONT",
            );
            $readonlyValues = array(
                //'AUTO_POR',
                'user_modified',

            );
            foreach ($digRowDetails as $colName) {
                if (!in_array($colName, $hiddenValues)) {
                    $inputType[$colName]["title"] = $tblColsToSpan[$colName];
                    $inputType[$colName]["type"] = "text";
                    $inputType[$colName]["class"] = "input-form";
                    $inputType[$colName]["value"] = "";
                    $inputType[$colName]["text"] = $colName;
                    $inputType[$colName]["readonly"] = false;
                    if (in_array($colName, $readonlyValues)) {
                        $inputType[$colName]["readonly"] = true;
                    }
                    if (str_contains($colName, "ARCHIVO")) {
                        $inputType[$colName]["type"] = "file";
                        $inputType[$colName]["text"] = "Selecciona un archivo para " . $inputType[$colName]["title"];
                    }
                }
            }
            $insert = true;

            return view("DigitalStorage.edit", compact('actividades', 'ultimo', 'inputType', 'digRowDetails', 'user', 'insert'));
        } else {
            return redirect()->route('auth/login');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $destinationPath = 'digitalStorage/';
        $digStoreModel = new DigStrore();

        $fileArray = array(
            "ARCHIVO_1",
            "ARCHIVO_2",
            "ARCHIVO_3",
            "ARCHIVO_4",
            "ARCHIVO_XML",
        );
        $saveDataFiles = array();

        // $previousData = (array)$digStoreModel->getRowData($id);


        $validator = Validator::make($request->all(), [
            "LLAVE_ID" => "required",
            "GRUPO_ID" => "required",
            "DOC_ID" => "required",
            "ARCHIVO_1" => "required"
        ]);
        if ($validator->fails()) {
            return redirect('home/AlmacenDigital')
                ->withErrors($validator)
                ->withInput();
        }

        $params = array(
            "LLAVE_ID" => $request->get('LLAVE_ID'),
            "GRUPO_ID" => $request->get('GRUPO_ID'),
            "DOC_ID" => $request->get('DOC_ID'),
            "importe" => $request->get('importe'),
            "user_modified" => $request->get('user_modified'),
            "POLIZA_MUL" => $request->get('POLIZA_MUL'),
            "CAPUTRADA" => $request->get('CAPUTRADA'),
            "CAPT_POR" => $request->get('CAPT_POR'),
            "AUTORIZADO" => $request->get('AUTORIZADO'),
            "AUTO_POR" => $request->get('AUTO_POR'),
            "POLIZA_CONT" => $request->get('POLIZA_CONT'),
            //"last_modified" => date("d-m-y h:i:s"),
        );
        $id = $digStoreModel->newRow($params);
        $newDestinationPath = $destinationPath . $id . "/";
        foreach ($fileArray as $fileName) {
            $file = $request->file($fileName);
            $saveDataFiles[$fileName] = "";
            if (!is_null($file)) {
                $originalFile = $file->getClientOriginalName();
                $saveDataFiles[$fileName] = $newDestinationPath . $originalFile;
                $file->move($newDestinationPath, $originalFile);
            }
        }
        $fileUploads = array(
            "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
            "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
            "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
            "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
            "ARCHIVO_XML" => $saveDataFiles["ARCHIVO_XML"],
        );
        $digStoreModel->updateData($fileUploads, $id);
        return redirect('home/AlmacenDigital/');
    }

    private function _saveAll($request)
    {
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
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digRowDetails = $digStoreModel->getRowData($id);
        $inputType = [];
        $hiddenValues = array(
            "created_at",
            "last_modified",
            "id",
        );
        $tblColsToSpan = array(
            "created_at" => "Creado en",
            "last_modified" => "Ultima modificacion",
            "id" => "Id",
            "LLAVE_ID" => "LLAVE ID",
            "GRUPO_ID" => "Grupo Id",
            "DOC_ID" => "Doc Id",
            "ARCHIVO_1" => "Archivo 1",
            "ARCHIVO_2" => "Archivo 2",
            "ARCHIVO_3" => "Archivo 3",
            "ARCHIVO_4" => "Archivo 4",
            "ARCHIVO_XML" => "Archivo XML",
            "importe" => "Importe",
            "user_modified" => "Ultimo usuario modificado",
            "POLIZA_MUL" => "POLIZA MUL",
            "CAPUTRADA" => "Capturada",
            "CAPT_POR" => "Capturada Por",
            "AUTORIZADO" => "Autorizado",
            "AUTO_POR" => "Autorizado Por",
            "POLIZA_CONT" => "POLIZA CONT",
        );
        $readonlyValues = array(
            //'AUTO_POR',
            'user_modified',

        );
        foreach ($digRowDetails as $colName => $value) {
            if (!in_array($colName, $hiddenValues)) {
                $inputType[$colName]["title"] = $tblColsToSpan[$colName];
                $inputType[$colName]["type"] = "text";
                $inputType[$colName]["class"] = "input-form";
                $inputType[$colName]["value"] = $value;
                $inputType[$colName]["text"] = $colName;
                $inputType[$colName]["readonly"] = false;
                if (in_array($colName, $readonlyValues)) {
                    $inputType[$colName]["readonly"] = true;
                }
                if (str_contains($colName, "ARCHIVO")) {
                    $inputType[$colName]["type"] = "file";
                    $inputType[$colName]["text"] = "Selecciona un archivo para " . $inputType[$colName]["title"];
                }
            }
        }
        $insert = false;
        return view("DigitalStorage.edit", compact('actividades', 'ultimo', 'inputType', 'digRowDetails', 'user', 'insert'));
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
        $destinationPath = 'digitalStorage/';
        $newDestinationPath = $destinationPath . $id . "/";
        $fileArray = array(
            "ARCHIVO_1",
            "ARCHIVO_2",
            "ARCHIVO_3",
            "ARCHIVO_4",
            "ARCHIVO_XML",
        );
        $saveDataFiles = array();
        $digStoreModel = new DigStrore();
        $previousData = (array)$digStoreModel->getRowData($id);
        foreach ($fileArray as $fileName) {
            $file = $request->file($fileName);
            $saveDataFiles[$fileName] = $previousData[$fileName] == "" ? "" : $previousData[$fileName];
            if (!is_null($file)) {
                $originalFile = $file->getClientOriginalName();
                $saveDataFiles[$fileName] = $newDestinationPath . $originalFile;
                $file->move($newDestinationPath, $originalFile);
            }
        }

        $fields2Validate = [
            "LLAVE_ID" => "required",
            "GRUPO_ID" => "required",
            "DOC_ID" => "required",
            "ARCHIVO_1" => "required"
        ];
        if ($request->file($fileArray[0]) == "" && $previousData[$fileArray[0]] != "") {
            unset($fields2Validate[$fileArray[0]]);
        }

        $validator = Validator::make($request->all(), $fields2Validate);
        if ($validator->fails()) {
            return redirect('home/AlmacenDigital/edit/' . $id)
                ->withErrors($validator)
                ->withInput();
        }
        $params = array(
            "LLAVE_ID" => $request->get('LLAVE_ID'),
            "GRUPO_ID" => $request->get('GRUPO_ID'),
            "DOC_ID" => $request->get('DOC_ID'),
            "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
            "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
            "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
            "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
            "ARCHIVO_XML" => $saveDataFiles["ARCHIVO_XML"],
            "importe" => $request->get('importe'),
            "user_modified" => $request->get('user_modified'),
            "POLIZA_MUL" => $request->get('POLIZA_MUL'),
            "CAPUTRADA" => $request->get('CAPUTRADA'),
            "CAPT_POR" => $request->get('CAPT_POR'),
            "AUTORIZADO" => $request->get('AUTORIZADO'),
            "AUTO_POR" => $request->get('AUTO_POR'),
            "POLIZA_CONT" => $request->get('POLIZA_CONT'),
            //"last_modified" => date("d-m-y h:i:s"),
        );

        $digStoreList = $digStoreModel->updateData($params, $id);
        return redirect('home/AlmacenDigital/');
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
    public function find(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digStoreList = [];
        $digStoreList = $digStoreModel->getList($request);
        if (count($digStoreList) > 0) {
            foreach ($digStoreList as $row) {
                $row->EDIT_URL = url("/home/AlmacenDigital/edit", [$row->id]);
            }
        }
        $resultArray = [
            "digStoreList" => $digStoreList,
        ];
        return $resultArray;
        //return view("DigitalStorage.index", compact('actividades', 'ultimo', 'digStoreList'));
    }


    public function syncOrdersWithDigitalStorage(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $orderSalesCollection = $digStoreModel->getSalesOrderCollection($request);
        $digStoreList = $digStoreModel->getList($request);
        //UPDATE ALL FIRST
        foreach ($orderSalesCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $values->ARCHIVO_1,
                "ARCHIVO_2" => $values->ARCHIVO_2,
                "ARCHIVO_3" => $values->ARCHIVO_3,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                //"last_modified" => date("d-m-y h:i:s"),
            );
            foreach ($digStoreList as $digStoreRow => $digStoreVal) {
                if ($digStoreVal->LLAVE_ID == $values->LLAVE_ID) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $digStoreModel->updateSyncData($params, $values->LLAVE_ID);
            } else {
                $digStoreModel->newRow($params);
            }
        }
        return view("DigitalStorage.index", compact('actividades', 'ultimo', 'digStoreList'));
    }
}
