<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SIDModel;

class SIDController extends Controller
{

    public function __construct()
    {
        $this->sidModel = new SIDModel();
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
        $titlePage = $this->sidModel->getConfigRow($moduleType)->MENU_NAME;
        $dataArray = [];
        $dataArray = array("title_page" => $titlePage);
        $dataArray["columns"] = $this->_getTableColumns($this->moduleType);

        $dataArray["editable"] = true;
        $dataArray["module_type"] = $this->moduleType;
        return view("DigitalStorage.sid.index", compact('actividades', 'ultimo', 'dataArray'));
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
