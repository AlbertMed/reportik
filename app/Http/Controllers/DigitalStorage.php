<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\DigitalStorage as DigStrore;
use Illuminate\Support\Facades\Validator;
use Exception;
use Storage;

class DigitalStorage extends Controller
{

    private $_groupNames = array();
    private $_destinationPath = 'digitalStorage/';
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
        $editable = false;
        return $this->index($request, $moduleType, $editable);
    }

    public function COMIndex(Request $request)
    {
        $moduleType = 'COM';
        $editable = false;
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
        //     return redirect('home/ALMACENDIGITAL/CONFIG/new')
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        $params = [
            // 'created_at' => Db::raw("current_date()"),
            'GROUP_NAME' => $request->input('group_name'),
            'URL' => $request->input('url'),
            'MENU_NAME' => $request->input('menu_name'),
            'enabled' => $request->input('enabled') == 'on' ? TRUE : FALSE,
        ];

        $digStoreModel = new DigStrore();
        $configId = $digStoreModel->newConfigRow($params);
        return redirect('home/ALMACENDIGITAL/CONFIG');
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
        //     return redirect('home/ALMACENDIGITAL/CONFIG/new')
        //         ->withErrors($validator)
        //         ->withInput();
        // }
        $params = [
            'GROUP_NAME' => $request->input('group_name'),
            'URL' => $request->input('url'),
            'MENU_NAME' => $request->input('menu_name'),
            'enabled' => $request->input('enabled') == 'on' ? TRUE : FALSE,
        ];
        $digStoreModel = new DigStrore();
        $configId = $digStoreModel->updateConfigRow($params, $request->input('id'));
        return redirect('home/ALMACENDIGITAL/CONFIG');
    }

    public function index(Request $request, $moduleType = "", bool $editable = false)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $titlePage = $moduleType;
        $digStoreModel = new DigStrore();
        $titlePage = $digStoreModel->getConfigRow($moduleType)->MENU_NAME;
        return view("DigitalStorage.index", compact('actividades', 'ultimo', 'moduleType', 'titlePage', 'editable', 'request'));
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
            "user_modified",
            "LLAVE_ID`"
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
            'AUTO_POR',
            'user_modified',
            "LLAVE_ID",
            // "GRUPO_ID"

        );
        if ($moduleType == "SID") { //hiding all unecesary values to input
            $hiddenValues[] = "POLIZA_MUL";
            $hiddenValues[] = "CAPUTRADA";
            $hiddenValues[] = "CAPT_POR";
            $hiddenValues[] = "AUTORIZADO";
            $hiddenValues[] = "AUTO_POR";
            $hiddenValues[] = "POLIZA_CONT";
            $hiddenValues[] = "importe";
            $hiddenValues[] = "ARCHIVO_XML";
        }
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
     * @param \Illuminate\Http\Request $request
     * @return json $response
     */
    public function workOrders(Request $request)
    {
        $user = Auth::user();
        $actividades = $user->getTareas();
        $ultimo = count($actividades);
        $digStoreModel = new DigStrore();
        $digStoreList = [];
        $digStoreList = $digStoreModel->getWorkOrders($request);

        $resultArray = [
            "digStoreList" => $digStoreList,
        ];
        return $resultArray;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $destinationPath = $this->_destinationPath;
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
            // "LLAVE_ID" => "required",
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
            // "last_modified" => db::raw("current_date()"),
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
                // "last_modified" => db::raw("current_date()"),
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
                $saveDataFiles[$fileName] = url() . "/" . $newDestinationPath . $originalFile;
                $file->move($newDestinationPath, $originalFile);
            }
        }
        $llaveId  = implode(
            "",
            [
                $request->get('moduleType'),
                $request->get('GRUPO_ID'),
                $request->get('DOC_ID'),
                $id
            ]
        );
        $fileUploads = array(
            "ARCHIVO_1" => $saveDataFiles["ARCHIVO_1"],
            "ARCHIVO_2" => $saveDataFiles["ARCHIVO_2"],
            "ARCHIVO_3" => $saveDataFiles["ARCHIVO_3"],
            "ARCHIVO_4" => $saveDataFiles["ARCHIVO_4"],
            "ARCHIVO_XML" => $saveDataFiles["ARCHIVO_XML"],
        );
        $request->get("baseURLAlmacen");

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
        } else {
            $fileUploads["LLAVE_ID"] = $llaveId;
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
            // "LLAVE_ID",
            "id",
            "last_modified",
            "created_at",
        );
        if ($moduleType == "SID") { //hiding all unecesary values to input
            $hiddenValues[] = "POLIZA_MUL";
            $hiddenValues[] = "CAPUTRADA";
            $hiddenValues[] = "CAPT_POR";
            $hiddenValues[] = "AUTORIZADO";
            $hiddenValues[] = "AUTO_POR";
            $hiddenValues[] = "POLIZA_CONT";
            $hiddenValues[] = "importe";
            $hiddenValues[] = "ARCHIVO_XML";
        }
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
            // "last_modified" => Db::raw("current_date()"),
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
                // "last_modified" => Db::raw("current_date()"),
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
     * Will find if file is PDF or XML
     * @param DigStore $digStoreModel
     * @param string llaveID
     * @param string $url 
     * @return string validated URL or EMPTY
     */

    private function _findFIle(DigStrore $digStoreModel, $llaveId, $fileInURL)
    {
        if (strpos($fileInURL, "http") == false) {
            //WE FOUND A NEW DOCUMENT
            $params = array();
            $params["IMAGE_VALIDATED"] = 0;
            $digStoreModel->updateSyncData($params, $llaveId);
        }
        return $fileInURL;
    }
    private function _findFIleImages(DigStrore $digStoreModel, $fileInURL)
    {
        if ($fileInURL == "") {
            return "";
        }

        if (strpos($fileInURL, "http") !== false) {
            return $fileInURL;
        }

        //return $fileInURL;

        if (empty($this->_groupNames)) {
            $configRowAll = $digStoreModel->getConfigRowsAll();
            foreach ($configRowAll as $row) {
                if (!empty($row->URL)) {
                    $this->_groupNames[] = $row->URL;
                }
            }
            $this->_groupNames = array_unique($this->_groupNames);
        }
        $found = false;

        $url = "";
        foreach ($this->_groupNames as $urlPrefix) {
            $urlEncoded = str_replace(' ', '%20', $urlPrefix . $fileInURL);

            if (!$found) {
                try {
                    if (in_array("Content-Type: application/pdf", get_headers($urlEncoded))) {
                        $found = true;
                        $url = $urlEncoded;
                    }
                } catch (Exception $e) {
                    $found = false;
                    $url = "";
                }
                try {
                    if (str_contains($urlEncoded, ".xml") && simplexml_load_string(file_get_contents($urlEncoded)) == true) {
                        $found = true;
                        $url = $urlEncoded;
                    }
                } catch (Exception $e) {
                    $found = false;
                    $url = "";
                }
            }
        }
        // //REMOVING VALIDATION BECAUSE IT'S NOT NEEDED FOR NOW
        // return $urlEncoded;

        return $url;
    }
    /**
     * Sync Order sales 
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncSales(DigStrore $digStoreModel, $digStoreList)
    {

        $orderSalesCollection = $digStoreModel->getSalesOrderCollection();

        foreach ($orderSalesCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_2" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_2),
                "ARCHIVO_3" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_3),
                // "ARCHIVO_XML" => $values->ARCHIVO_XML,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     * Sync Work Orders 
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     */
    private function _syncWorkOrders(DigStrore $digStoreModel, $digStoreList)
    {

        $orderSalesCollection = $digStoreModel->getWorkOrders();

        foreach ($orderSalesCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                // "ARCHIVO_XML" => $values->ARCHIVO_XML,
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
    private function _syncInvoice(DigStrore $digStoreModel, $digStoreList)
    {
        $facturaCollection = $digStoreModel->getInvoiceCollection();
        foreach ($facturaCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_XML),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     * Sync Canceled Invoices 
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncCanceledInvoice(DigStrore $digStoreModel, $digStoreList)
    {
        $facturaCollection = $digStoreModel->getCanceledInvoiceCollection();
        foreach ($facturaCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_2" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_2),
                "ARCHIVO_4" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_4),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_XML),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     */
    private function _syncRequisition(DigStrore $digStoreModel, $digStoreList)
    {
        $collection = $digStoreModel->getRequisitionCollection();
        foreach ($collection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_2" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_2),
                "ARCHIVO_3" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_3),
                "ARCHIVO_4" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_4),
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
    private function _syncCredit(DigStrore $digStoreModel, $digStoreList)
    {
        $creditNoteCollection = $digStoreModel->getCreditNoteCollection();
        foreach ($creditNoteCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_XML),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     * Sync CANCELED Credit NO ACUSE
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncCanceledCredit(DigStrore $digStoreModel, $digStoreList)
    {
        $creditNoteCollection = $digStoreModel->getCanceledSalesOrderCollection();
        foreach ($creditNoteCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_2" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_2),
                "ARCHIVO_4" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_4),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_XML),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     * Sync Complementos no pagados
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncComplementoPagos(DigStrore $digStoreModel, $digStoreList)
    {
        $creditNoteCollection = $digStoreModel->getComplementoPagos();
        foreach ($creditNoteCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_XML),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
     * Sync Clientes Cancelados con acuse
     * @param App\DigitalStorage $digStoreModel
     * @param App\DigitalStorage $digStoreList
     * @param App\Http\Request
     */
    private function _syncClientesCanceladosAcuse(DigStrore $digStoreModel, $digStoreList)
    {
        $creditNoteCollection = $digStoreModel->getClientesCanceladosAcuse();
        foreach ($creditNoteCollection as $row => $values) {
            $found = false;
            $params = array(
                "LLAVE_ID" => $values->LLAVE_ID,
                "GRUPO_ID" => $values->GRUPO_ID,
                "DOC_ID" => $values->DOC_ID,
                "ARCHIVO_1" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_1),
                "ARCHIVO_2" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_2),
                "ARCHIVO_4" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_4),
                "ARCHIVO_XML" => $this->_findFIle($digStoreModel, $values->LLAVE_ID, $values->ARCHIVO_4),
                "importe" => $values->IMPORTE,
                "CAPT_POR" => -1,
                // "last_modified" => Db::raw("current_date()"),
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
    private function _syncImages(DigStrore $digStoreModel, $digStoreList)
    {
        foreach ($digStoreList as $row) {
            $params = array();
            if ($row->ARCHIVO_1 != null or $row->ARCHIVO_1 != "") {
                $params["ARCHIVO_1"] = $this->_findFIleImages($digStoreModel, $row->ARCHIVO_1);
            }
            if ($row->ARCHIVO_1 != null or $row->ARCHIVO_2 != "") {
                $params["ARCHIVO_2"] = $this->_findFIleImages($digStoreModel, $row->ARCHIVO_2);
            }
            if ($row->ARCHIVO_1 != null or $row->ARCHIVO_3 != "") {
                $params["ARCHIVO_3"] = $this->_findFIleImages($digStoreModel, $row->ARCHIVO_3);
            }
            if ($row->ARCHIVO_1 != null or $row->ARCHIVO_4 != "") {
                $params["ARCHIVO_4"] = $this->_findFIleImages($digStoreModel, $row->ARCHIVO_4);
            }
            if ($row->ARCHIVO_1 != null or $row->ARCHIVO_XML != "") {
                $params["ARCHIVO_XML"] = $this->_findFIleImages($digStoreModel, $row->ARCHIVO_XML);
            }
            if (!empty($params)) {
                $params["IMAGE_VALIDATED"] = 1;
                $digStoreModel->updateSyncData($params, $row->LLAVE_ID);
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
            $this->_syncSales($digStoreModel, $digStoreList);
            $this->_syncInvoice($digStoreModel, $digStoreList);
            $this->_syncCredit($digStoreModel, $digStoreList);
            $this->_syncWorkOrders($digStoreModel, $digStoreList);
            $this->_syncCanceledInvoice($digStoreModel, $digStoreList);
            $this->_syncCanceledCredit($digStoreModel, $digStoreList);
            $this->_syncComplementoPagos($digStoreModel, $digStoreList);
            $this->_syncClientesCanceladosAcuse($digStoreModel, $digStoreList);
        }
        if ($request->input('moduleType') == 'COM') {
            $this->_syncRequisition($digStoreModel, $digStoreList);
        }
        //SAVING INTO JSON FILE
        $resultArray = [
            "digStoreList" => $digStoreModel->getDigitalStorageJson(),
        ];
        //\Storage::disk('almacenDigital')->put("ALMACENDIGITAL.json", json_encode($resultArray));
        $path = getcwd() . "/digitalStorage/";

        file_put_contents($path . "ALMACENDIGITAL.json", json_encode($resultArray));
        return redirect()->back();
    }

    public function syncDatabases()
    {
        $digStoreModel = new DigStrore();
        $request = new Request;
        $digStoreList = $digStoreModel->getList($request, false);
        $this->_syncSales($digStoreModel, $digStoreList);
        $this->_syncInvoice($digStoreModel, $digStoreList);
        $this->_syncCredit($digStoreModel, $digStoreList);
        $this->_syncWorkOrders($digStoreModel, $digStoreList);
        $this->_syncCanceledInvoice($digStoreModel, $digStoreList);
        $this->_syncCanceledCredit($digStoreModel, $digStoreList);
        $this->_syncComplementoPagos($digStoreModel, $digStoreList);
        $this->_syncClientesCanceladosAcuse($digStoreModel, $digStoreList);
        $this->_syncRequisition($digStoreModel, $digStoreList);

        //SAVING INTO JSON FILE
        $resultArray = [
            "digStoreList" => $digStoreModel->getDigitalStorageJson(),
        ];
        $path = getcwd() . "/public/digitalStorage/";
        file_put_contents($path . "ALMACENDIGITAL.json", json_encode($resultArray));
        //$this->validateImages();
    }
    public function validateImages()
    {
        $digStoreModel = new DigStrore();
        $request = new Request;
        $digStoreList = $digStoreModel->getListNoImageValidation(200);
        $this->_syncImages($digStoreModel, $digStoreList);
        //SAVING INTO JSON FILE
        $resultArray = [
            "digStoreList" => $digStoreModel->getDigitalStorageJson(),
        ];
        $path = getcwd() . "/public/digitalStorage/";
        file_put_contents($path . "ALMACENDIGITAL.json", json_encode($resultArray));
    }
}
