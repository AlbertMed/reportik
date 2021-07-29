<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\DigitalStorage as DigStrore;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class DigitalStorage extends Controller
{

    public function __construct()
    {
        if (!Auth::check()) {
            return redirect()->route('auth/login');
        }
        $this->deptIds = ["SID"];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function SACIndex(Request $request)
    {
        $moduleType = 'SAC';
        $editable = true;
        return $this->index($request, $moduleType, $editable);
    }

    public function COMIndex(Request $request)
    {
        $moduleType = 'COM';
        $editable = true;
        return $this->index($request, $moduleType, $editable);
    }
    public function SIDView(Request $request)
    {
        $moduleType = 'SID';
        $editable = false;
        return $this->index($request, $moduleType, $editable);
    }
    public function SACView(Request $request)
    {
        $moduleType = 'SAC';
        $editable = false;
        return $this->index($request, $moduleType, $editable);
    }

    public function COMView(Request $request)
    {
        $moduleType = 'COM';
        $editable = false;
        return $this->index($request, $moduleType, $editable);
    }
    public function SIDIndex(Request $request)
    {
        $moduleType = 'SID';
        $editable = true;
        return $this->index($request, $moduleType, $editable);
    }

    public function ConfigView(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $configurationHeaders = $digStoreModel->getConfiguration();
        $configurationValues = $digStoreModel->getConfigValues($request);
        return view("DigitalStorage.config", compact('actividades', 'ultimo', 'configurationHeaders', 'configurationValues'));
    }

    public function editConfigView(Request $request, $id)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();

        $values = $digStoreModel->getConfigValues($request, $id);
        return view("DigitalStorage.configedit", compact('actividades', 'ultimo', 'values'));
    }

    public function newConfigView(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $configurationHeaders = $digStoreModel->getConfiguration();
        $values = false;
        return view("DigitalStorage.configedit", compact('actividades', 'ultimo', 'configurationHeaders', 'values'));
    }
    public function insertConfigView(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $validator = Validator::make($request->all(), [
            "group_name" => "required",
            "url" => "required",
        ]);

        // if ($validator->fails()) {
        //     return redirect('home/ALMACENDIGITAL/config/new')
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        $params = [
            'created_at' => Db::raw("current_date()"),
            'GROUP_NAME' => $request->input('group_name'),
            'URL' => $request->input('url'),
            'enabled' => $request->input('enabled') == 'on' ? TRUE : FALSE,
        ];

        $digStoreModel = new DigStrore();
        $configId = $digStoreModel->newConfigRow($params);
        return redirect('home/ALMACENDIGITAL/config');
    }
    public function updateConfigView(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $validator = Validator::make($request->all(), [
            "group_name" => "required",
            "url" => "required",
        ]);

        // if ($validator->fails()) {
        //     return redirect('home/ALMACENDIGITAL/config/new')
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        $params = [
            'GROUP_NAME' => $request->input('group_name'),
            'URL' => $request->input('url'),
            'enabled' => $request->input('enabled') == 'on' ? TRUE : FALSE,
        ];
        $digStoreModel = new DigStrore();
        $configId = $digStoreModel->updateConfigRow($params, $request->input('id'));
        return redirect('home/ALMACENDIGITAL/config');
    }

    public function index(Request $request, $moduleType = "", bool $editable = false)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        return view("DigitalStorage.index", compact('actividades', 'ultimo', 'moduleType', 'editable'));
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
    public function create(Request $request, $moduleType)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digRowDetails = $digStoreModel->getSchema();
        $deptIds = $this->deptIds;
        $deptRows = [];
        if (in_array($moduleType, $deptIds)) {
            $deptRows = $digStoreModel->getDepartments();
        }

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
                $inputType[$colName]["class"] = "form-control input-form";
                $inputType[$colName]["value"] = "";
                $inputType[$colName]["text"] = $colName;
                $inputType[$colName]["readonly"] = in_array($moduleType, $deptIds) && $colName == "LLAVE_ID" ? true : false;
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

        return view("DigitalStorage.edit", compact(
            'actividades',
            'ultimo',
            'inputType',
            'digRowDetails',
            'user',
            'insert',
            'moduleType',
            'deptIds',
            'deptRows'
        ));
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

        // FOR SID AND OTHER GROUP TYPE INPUTS
        if (in_array($request->get('moduleType'), $this->deptIds)) {
            $validator = Validator::make($request->all(), [
                "GRUPO_ID" => "required",
                "DOC_ID" => "required",
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
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
            "last_modified" => db::raw("current_date()"),
        );

        // FOR SID AND OTHER GROUP TYPE INPUTS
        if (in_array($request->get('moduleType'), $this->deptIds)) {
            $params = array(
                "DOC_ID" => $request->get('DOC_ID'),
                "importe" => $request->get('importe'),
                "user_modified" => $request->get('user_modified'),
                "POLIZA_MUL" => $request->get('POLIZA_MUL'),
                "CAPUTRADA" => $request->get('CAPUTRADA'),
                "CAPT_POR" => $request->get('CAPT_POR'),
                "AUTORIZADO" => $request->get('AUTORIZADO'),
                "AUTO_POR" => $request->get('AUTO_POR'),
                "POLIZA_CONT" => $request->get('POLIZA_CONT'),
                "last_modified" => db::raw("current_date()"),
            );
        }

        $id = $digStoreModel->newRow($params);
        $newDestinationPath = $destinationPath . $id . "/";
        foreach ($fileArray as $fileName) {
            $file = $request->file($fileName);
            $saveDataFiles[$fileName] = "";
            if (!is_null($file)) {
                $originalFile = $file->getClientOriginalName();
                if (in_array($request->get('moduleType'), $this->deptIds)) {
                    $fileNameArray = explode("_", $fileName);
                    $fileNameSequence = "xml.xml";
                    if (is_numeric($fileNameArray[1])) {
                        $fileNameSequence = $fileNameArray[1] . ".pdf";
                    }
                    $originalFile = $request->get("DOC_ID") . $request->get("department") . $id . $fileNameSequence;
                }
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
        if (in_array($request->get('moduleType'), $this->deptIds)) {
            $fileUploads = array(
                "LLAVE_ID" => "SID" . $request->get("DOC_ID") . $request->get("department") . $id,
                "GRUPO_ID" => $request->get("DOC_ID") . $request->get("department"),
                "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
                "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
                "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
                "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
                "ARCHIVO_XML" => $saveDataFiles["ARCHIVO_XML"],
            );
        }
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
     * @param  string  $moduleType
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $moduleType)
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
            'user_modified',
            "LLAVE_ID",
            "GRUPO_ID",
            "DOC_ID",

        );
        $hiddenValues = array(
            "LLAVE_ID",
            "id",
            "last_modified",
            "created_at",
        );
        $deptIds  = $this->deptIds;
        $deptRows = [];
        if (in_array($moduleType, $deptIds)) {
            $deptRows = $digStoreModel->getDepartments();
        }
        foreach ($digRowDetails as $colName => $value) {
            if (!in_array($colName, $hiddenValues)) {
                $inputType[$colName]["title"] = $tblColsToSpan[$colName];
                $inputType[$colName]["type"] = "text";
                $inputType[$colName]["class"] = "form-control input-form";
                $inputType[$colName]["value"] = $value;
                $inputType[$colName]["text"] = $colName;
                $inputType[$colName]["readonly"] = in_array($moduleType, $deptIds) && $colName == "LLAVE_ID" ? true : false;;
                if (in_array($colName, $readonlyValues)) {
                    $inputType[$colName]["readonly"] = true;
                }
                if (in_array($colName, $hiddenValues)) {
                    $inputType[$colName]["class"] .= " hidden";
                }
                if (str_contains($colName, "ARCHIVO")) {
                    $inputType[$colName]["type"] = "file";
                    $inputType[$colName]["text"] = "Selecciona un archivo para " . $inputType[$colName]["title"];
                }
            }
        }
        $insert = false;
        return view("DigitalStorage.edit", compact(
            'actividades',
            'ultimo',
            'inputType',
            'digRowDetails',
            'user',
            'insert',
            'moduleType',
            'deptRows',
            'deptIds'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $moduleType)
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
        $group_idArray = explode($request->get("DOC_ID"), $request->get('GRUPO_ID'));
        $saveDataFiles = array();
        $digStoreModel = new DigStrore();
        $previousData = (array)$digStoreModel->getRowData($id);
        foreach ($fileArray as $fileName) {
            $file = $request->file($fileName);
            $saveDataFiles[$fileName] = $previousData[$fileName] == "" ? "" : $previousData[$fileName];
            if (!is_null($file)) {
                $originalFile = $file->getClientOriginalName();
                if (in_array($request->get('moduleType'), $this->deptIds)) {
                    $fileNameArray = explode("_", $fileName);
                    $fileNameSequence = "xml.xml";
                    if (is_numeric($fileNameArray[1])) {
                        $fileNameSequence = $fileNameArray[1] . ".pdf";
                    }
                    $originalFile = $request->get("DOC_ID") . $group_idArray[1] . $id . $fileNameSequence;
                }
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
        // FOR SID AND OTHER GROUP TYPE INPUTS
        if (in_array($request->get('moduleType'), $this->deptIds)) {
            $fields2Validate = [
                "GRUPO_ID" => "required",
                "DOC_ID" => "required",
            ];
        }

        if ($request->file($fileArray[0]) == "" && $previousData[$fileArray[0]] != "") {
            unset($fields2Validate[$fileArray[0]]);
        }

        $validator = Validator::make($request->all(), $fields2Validate);
        if ($validator->fails()) {
            return redirect('home/AlmacenDigital/edit/' . $id . "/" . $moduleType)
                ->withErrors($validator)
                ->withInput();
        }
        $fileUploads = array(
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
            "last_modified" => Db::raw("current_date()"),
        );

        if (in_array($request->get('moduleType'), $this->deptIds)) {
            $fileUploads = array(
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
                "last_modified" => Db::raw("current_date()"),
            );
        }
        $digStoreList = $digStoreModel->updateData($fileUploads, $id);
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
                $row->EDIT_URL = url("/home/AlmacenDigital/edit", [$row->id, $request->get('moduleType')]);
            }
        }
        $resultArray = [
            "digStoreList" => $digStoreList,
        ];
        return $resultArray;
        //return view("DigitalStorage.index", compact('actividades', 'ultimo', 'digStoreList'));
    }
    /**
     * Sync Order sales 
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncSales(DigStrore $digStoreModel, $digStoreList, Request $request)
    {

        $orderSalesCollection = $digStoreModel->getSalesOrderCollection($request);

        foreach ($orderSalesCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $values->ARCHIVO_1,
                "ARCHIVO_2" => $values->ARCHIVO_2,
                "ARCHIVO_3" => $values->ARCHIVO_3,
                // "ARCHIVO_XML" => $values->ARCHIVO_XML,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                "last_modified" => Db::raw("current_date()"),
            );
            foreach ($digStoreList as $digStoreRow => $digStoreVal) {
                if ($digStoreVal->LLAVE_ID == $values->LLAVE_ID) {
                    $found = true;
                }
            }
            if ($found) {
                $digStoreModel->updateSyncData($params, $values->LLAVE_ID);
            } else {
                $digStoreModel->newRow($params);
            }
        }
    }
    /**
     * Sync Invoices 
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncInvoice(DigStrore $digStoreModel, $digStoreList, Request $request)
    {
        $facturaCollection = $digStoreModel->getInvoiceCollection($request);
        foreach ($facturaCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $values->ARCHIVO_1,
                "ARCHIVO_XML" => $values->ARCHIVO_XML,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                "last_modified" => Db::raw("current_date()"),
            );
            foreach ($digStoreList as $digStoreRow => $digStoreVal) {
                if ($digStoreVal->LLAVE_ID == $values->LLAVE_ID) {
                    $found = true;
                }
            }
            if ($found) {
                $digStoreModel->updateSyncData($params, $values->LLAVE_ID);
            } else {
                $digStoreModel->newRow($params);
            }
        }
    }
    /**
     * Sync Credit
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncRequisition(DigStrore $digStoreModel, $digStoreList, Request $request)
    {
        $collection = $digStoreModel->getRequisitionCollection($request);
        foreach ($collection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $values->ARCHIVO_1,
                "ARCHIVO_2" => $values->ARCHIVO_2,
                "ARCHIVO_3" => $values->ARCHIVO_3,
                "CAPT_POR" => -1,
                "last_modified" => Db::raw("current_date()"),
            );
            foreach ($digStoreList as $digStoreRow => $digStoreVal) {
                if ($digStoreVal->LLAVE_ID == $values->LLAVE_ID) {
                    $found = true;
                }
            }
            if ($found) {
                $digStoreModel->updateSyncData($params, $values->LLAVE_ID);
            } else {
                $digStoreModel->newRow($params);
            }
        }
    }
    /**
     * Sync Credit
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncCredit(DigStrore $digStoreModel, $digStoreList, Request $request)
    {
        $creditNoteCollection = $digStoreModel->getCreditNoteCollection($request);
        foreach ($creditNoteCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $values->ARCHIVO_1,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                "last_modified" => Db::raw("current_date()"),
            );
            foreach ($digStoreList as $digStoreRow => $digStoreVal) {
                if ($digStoreVal->LLAVE_ID == $values->LLAVE_ID) {
                    $found = true;
                }
            }
            if ($found) {
                $digStoreModel->updateSyncData($params, $values->LLAVE_ID);
            } else {
                $digStoreModel->newRow($params);
            }
        }
    }

    public function syncOrdersWithDigitalStorage(Request $request)
    {
        set_time_limit(0);
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digStoreList = $digStoreModel->getList($request, false);
        if ($request->input('moduleType') == 'SAC') {
        }
        $this->_syncSales($digStoreModel, $digStoreList, $request);
        $this->_syncInvoice($digStoreModel, $digStoreList, $request);
        $this->_syncCredit($digStoreModel, $digStoreList, $request);
        $this->_syncRequisition($digStoreModel, $digStoreList, $request);
        if ($request->input('moduleType') == 'COM') {
        }
        //UPDATE ALL FIRST
        return redirect()->back();
    }
}
