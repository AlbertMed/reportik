@extends('home')

@section('homecontent')
<style>
    .btn {
        border-radius: 4px;
    }

    th {
    background: #dadada;
    color: black;
    font-weight: bold;
    font-style: italic;
    font-family: 'Helvetica';
    font-size: 12px;
    border: 0px;
    }

    td {
        font-size: 11px;
    }

    th,
    td {
        white-space: nowrap;
    }

    div.container {
        min-width: 980px;
        margin: 0 auto;
    }

    .dataTables_scrollHeadInner th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .segundoth {
        position: -webkit-sticky;
        position: sticky;
        left: 155px;
        z-index: 5;
    }

    table.dataTable thead .sorting {
        position: sticky;
    }

    .DTFC_LeftBodyWrapper {
        margin-top: 80px;
    }

    .DTFC_LeftHeadWrapper {
        display: none;
    }

    .DTFC_LeftBodyLiner {
        overflow: hidden;
        overflow-y: hidden;
    }

    .dataTables_filter {
        display: none;
    }

    div.dt-buttons {
        float: right;
        margin-bottom: 6px;
        margin-top: 0px;
    }

    .btn-group>.btn {
        float: none;
    }

    .btn {
        border-radius: 4px;
    }

    .btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
        border-radius: 4px;
    }

    .btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .btn-group>.btn:last-child:not(:first-child),
    .btn-group>.dropdown-toggle:not(:first-child) {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length {
        /*mueve el selector de registros a visualizar*/
        float: right;
    }

    div.dataTables_wrapper div.dataTables_processing {
        /*Procesing mas visible*/
        z-index: 10;
    }

    input {
        color: black;
    }

    .bootbox.modal {
        z-index: 9999 !important;
    }
</style>

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                Detalle CXC & CXP
                <small><b>Flujo Efectivo</b></small>
            </h3>

        </div>
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div>

    <!-- begin row -->
    <div class="col-md-12">
        <div class="row">
            <a onclick="cargando()" href="{{ url('home/FINANZAS/01 FLUJO EFECTIVO') }}" class="btn btn-primary">Atras</a>
        </div>
    </div>

    <div class="row hide" style="margin-bottom: 40px">
        <div class="form-group">
            <div class="col-md-3">
                <label><strong>
                        <font size="2">Estatus</font>
                    </strong></label>
                {!! Form::select("estado", $estado, null, [
                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                =>"estado", "data-size" => "8", "data-style"=>"btn-success"])
                !!}
            </div>
            <div class="col-md-3">
                <label><strong>
                        <font size="2">Cliente</font>
                    </strong></label>
                {!! Form::select("cliente[]", $cliente, null, [
                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                =>"cliente", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple
                data-actions-box"=>"true",
                'data-live-search' => 'true', 'multiple'=>'multiple'])
                !!}
            </div>
            <div class="col-md-3">
                <label><strong>
                        <font size="2">Comprador</font>
                    </strong></label>
                {!! Form::select("comprador[]", $comprador, null, [
                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                =>"comprador", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple
                data-actions-box"=>"true",
                'data-live-search' => 'true', 'multiple'=>'multiple'])
                !!}
            </div>

            <div class="col-md-2">
                <p style="margin-bottom: 23px"></p>
                <button type="button" class="form-control btn btn-primary m-r-5 m-b-5" id="boton-mostrar"><i
                        class="fa fa-cogs"></i> Mostrar</button>
            </div>
            <div class="col-md-1">
                <p style="margin-bottom: 23px"></p>
                <button type="button" class="form-control btn btn-danger m-r-5 m-b-5"
                    id="boton-mostrar-OValertadas"><i class='fa fa-bell'></i>
                </button>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12">
            <div class="table-scroll" id="registros-ordenes-proyeccion">
                <table id="t_ordenes_proyeccion" class="table table-striped table-bordered hover" width="100%">
                    <thead>
                        <tr></tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

<br><br>
    <div class="col-md-4" style="display: none;">
        <input type="text" class="form-control" id="semana_actual" placeholder="" value="{{$sem}}"
            style="font-size: 130%; text-align: right;" size="100" disabled />
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-scroll">
                <table id="tableFTPDCXPPesos" class="table table-striped table-bordered hover" width="100%">
                    <thead>
                        <tr>
                            <th>TIPO REQUISICION</th>
                            <th>PROVEEDOR</th>
                            <th>FACTURA</th>
    
                            <th>FECHA</th>
                            <th>F. VENCIMIENTO</th>
                            <th>DIAS VENCIDA</th>
                            <th>MONEDA</th>
                            <th>MONTO</th>
    
                            <th>SALDO MN</th>
                            <th>VENCIDO</th>
                            <th>SEM ACTUAL {{$sem}}</th>
                            <th>SEM {{$sem + 1}}</th>
                            <th>SEM {{$sem + 2}}</th>
    
                            <th>SEM {{$sem + 3}}</th>
                            <th>SEM {{$sem + 4}}</th>
                            <th>SEM {{$sem + 5}}</th>
                            <th>RESTO</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
    
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right">Totales:</th>
                            <th style="text-align:right"></th>
    
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
    
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
    
        </div>
    </div>
    <!-- end row -->


</div> <!-- /.container -->


<div class="modal fade" id="agregar" style="z-index: 1600;" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar Provisión</h4>
            </div>

            <div class="modal-body" style='padding:16px'>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_provision">Fecha</label>
                            <input type="text" id="fecha_provision" name="fecha_provision" class='form-control'>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cant">Cantidad *</label>
                            <input type="number" class="form-control" id="cant" name="cant" step="0.01"
                                autocomplete="off">
                            <input style="display:none" type="number" class="form-control" id="cant_max_permitida"
                                hidden>
                        </div>
                    </div>
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cant">Descripción *</label>
                            {!! Form::select("cboprovdescripciones", $provdescripciones, null, [
                            "class" => "form-control selectpicker","id"=>"cboprovdescripciones", "data-style" =>
                            "btn-success btn-sm"])
                            !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observacion">Observación</label>
                            <textarea class="form-control" maxlength="30" rows="2" id="comment"
                                style="text-transform:uppercase;" value=""
                                onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <button id='btn-provisionar' style="margin-top: 23px;" class="btn btn-primary form-control"
                                style="margin-top:4px"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="codigo"></h4>
            </div>

            <div class="modal-body" style='padding:16px'>
                <input type="text" style="display: none" class="form-control input-sm" id="input_id">
                <ul class="nav nav-pills">
                    <li id="lista-tab1" class="active"><a href="#default-tab-1" data-toggle="tab"
                            aria-expanded="true">Provisionar</a></li>
                    <li id="lista-tab2" class=""><a href="#default-tab-2" data-toggle="tab"
                            aria-expanded="false">Alertas</a></li>
                    <form class="form-horizontal">
                        <div class="dt-buttons form-group">
                            <label class="col-sm-4 control-label text-right">Estatus</label>
                            <div class="col-sm-8">
                                {!! Form::select("estado_save", $estado_save, null, [
                                "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                =>"estado_save", "data-size" => "8", "data-style"=>"btn-success "])
                                !!}
                            </div>
                        </div>
                    </form>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active in" id="default-tab-1">

                        <div class="pull-left">
                            <button id='btn-modal' style="margin-top: 23px;" class="btn btn-sm btn-success form-control"
                                style="margin-top:4px" data-toggle="modal" data-target="#agregar"><i
                                    class="fa fa-plus"></i> Agregar</button>
                        </div>
                        <div class="table-scroll" id="registros-provisionar">
                            <table id="table-provisiones" class="table table-striped table-bordered hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>Activa</th>
                                        <th>Acciones</th>
                                        <th># Provisión</th>
                                        <th>Fecha Pago</th>
                                        <th>Provisión Pago</th>
                                        <th>Provisión menos Pagos</th>
                                        <th>Descripción</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade " id="default-tab-2">
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cbonumpago"># Provisión</label>
                                    {!! Form::select("cbonumpago", $cbonumpago, null, [
                                    "class" => "form-control selectpicker","id"=>"cbonumpago", "data-style" =>
                                    "btn-success btn-sm"])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_alerta">Fecha</label>
                                    <input type="text" id="fecha_alerta" name="fecha_alerta" class='form-control'>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cant">Alerta</label>
                                    {!! Form::select("cboprovalertas", $provalertas, null, [
                                    "class" => "form-control selectpicker","id"=>"cboprovalertas", "data-style" =>
                                    "btn-success btn-sm"])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <button id='btn-alertar' style="margin-top: 23px;"
                                        class="btn btn-primary form-control" style="margin-top:4px">Agregar</button>
                                </div>
                            </div>
                        </div><!-- /.row -->
                        <div class="table-scroll" id="registros-provisionar">
                            <table id="table-alertas" class="table table-striped table-bordered hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ALERT_Usuarios</th>
                                        <th>Acciones</th>
                                        <th># Provisión</th>
                                        <th>Fecha Alerta</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div> <!-- /.tab-content -->
            </div>


        </div>
    </div>
</div>

<div class="modal fade" id="editalert" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar alerta</h4>
            </div>

            <div class="modal-body" style='padding:16px'>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cant">Usuarios Notificados</label>
                            <input type="text" name="editalert-idalerta" id="editalert-idalerta" hidden>
                            {!! Form::select("cbousuarios[]", $cbousuarios, null, [
                            "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                            =>"cbousuarios", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple
                            data-actions-box"=>"true",
                            'data-live-search' => 'true', 'multiple'=>'multiple'])
                            !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id='btn-guarda-usuarios-alert' class="btn btn-success"> Guardar</a>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="editprov" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Editar Provisión</h4>
            </div>

            <div class="modal-body" style='padding:16px'>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_provision">Fecha</label>
                            <input type="text" id="edit_fecha_provision" name="fecha_provision" class='form-control'>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cant">Cantidad *</label>
                            <input type="number" class="form-control" id="editcant" name="cant" step="0.01"
                                autocomplete="off">

                        </div>
                    </div>
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cant">Descripción *</label>
                            {!! Form::select("editcboprovdescripciones", $provdescripciones, null, [
                            "class" => "form-control selectpicker","id"=>"editcboprovdescripciones", "data-style" =>
                            "btn-success btn-sm"])
                            !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observacion">Observación</label>
                            <textarea class="form-control" maxlength="30" rows="2" id="editcomment"
                                style="text-transform:uppercase;" value=""
                                onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id='btn-guarda-prov' class="btn btn-success"><i class="fa fa-save"></i> Guardar</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="delete_alert" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remover alerta</h4>
            </div>

            <div class="modal-body" style='padding:16px'>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="cant">Acción Tomada / Evidencia</label>
                            <input type="text" name="id_delete_alert" id="id_delete_alert" hidden>
                            <textarea class="form-control" id="textarea_delete" rows="3" maxlength="50"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id='btn-delete-alert' class="btn btn-success"> Remover</a>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="delete_prov" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Remover Provisión</h4>
            </div>

            <div class="modal-body" style='padding:16px'>
                ¿Deseas continuar?
                <input type="text" name="id_prov" id="id_prov" hidden>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id='btn-delete-prov' class="btn btn-success"> Remover</a>
            </div>

        </div>
    </div>
</div>
@endsection
<script>
    function js_iniciador() {
   startjs()
     $('#tableFTPDCXPPesos thead tr').clone().appendTo( $("#tableFTPDCXPPesos thead") );
    var semana = $('#semana_actual').val();
    var mod_cont = 1;
    var table_cxp = $("#tableFTPDCXPPesos").DataTable({
        language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
            searching: true,
            iDisplayLength: 6,
            aaSorting: [],
            "processing": true,
            deferRender: true,
            dom: 'rtip',
            
            fixedColumns: true,
            paging: false,
            "scrollX": true,
            scrollY:        "300px",
            columns: [

            
                {data: "TipoRequisicion"},
                {data: "PROVEEDOR"},
                {data: "FP_CodigoFactura"},
                {data: "FP_FechaFactura"},
                {data: "FECHA_VENCIMIENTO"},
                {data: "DiasTranscurridosVencimiento"},
                {data: "MON_Nombre"},
                {data: "montoOriginal"},
                {data: "montoActualTC"},
                {data: "S0"},
                {data: "S1"},
                {data: "S2"},
                {data: "S3"},
                {data: "S4"},
                {data: "S5"},
                {data: "S6"},
                {data: "S7"}

            ],
            "columnDefs": [

            
                {

                    "targets": [ 0 ],
                    "searchable": true,
                    "orderable": true,
                    "render": function ( data, type, row ) {
                        if (row['TipoRequisicion'] != null) {
                            return row['TipoRequisicion'].substr(0,14);                        
                        } else {
                            return '';
                        }
                    }

                },
                {

                    "targets": [ 1 ],
                    "searchable": true,
                    "orderable": true,
                    "render": function ( data, type, row ) {
                        if (row['PROVEEDOR'] != null) {
                            return row['PROVEEDOR'].substr(0,44);
                        } else {
                            return '';
                        }
                            
                    }

                },
                {

                    "targets": [ 2 ],
                    "searchable": true,
                    "orderable": true,
                    "render": function ( data, type, row ) {
                        if (row['FP_CodigoFactura'] != null) {
                            return row['FP_CodigoFactura'].substr(0,12);
                        } else {
                            return '';
                        }
                            
                    }

                },
                {

                    "targets": [ 7 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {
                        if(row['montoOriginal'] != ''){

                            return '$ ' + number_format(row['montoOriginal'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return '';

                        }

                    }

                },
                {

                    "targets": [ 8 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['montoActualTC'] != ''){

                            return '$ ' + number_format(row['montoActualTC'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return '';

                        }

                    }

                },
                
                {

                    "targets": [ 9 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S0'] == '' || row['S0'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S0'].substr(0,1))){

                                return '$ ' + number_format(row['S0'],PRECIOS_DECIMALES,'.',',');
                                
                            }
                            else{

                                return row['S0'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 10 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S1'] == '' || row['S1'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S1'].substr(0,1))){

                                return '$ ' + number_format(row['S1'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S1'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 11 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S2'] == '' || row['S2'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S2'].substr(0,1))){

                                return '$ ' + number_format(row['S2'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S2'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 12 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S3'] == '' || row['S3'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S3'].substr(0,1))){

                                return '$ ' + number_format(row['S3'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S3'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 13 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S4'] == '' || row['S4'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S4'].substr(0,1))){

                                return '$ ' + number_format(row['S4'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S4'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 14 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S5'] == '' || row['S5'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S5'].substr(0,1))){

                                return '$ ' + number_format(row['S5'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S5'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 15 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S6'] == '' || row['S6'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S6'].substr(0,1))){

                                return '$ ' + number_format(row['S6'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S6'];

                            }

                        }

                    }

                },
                {

                    "targets": [ 16 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['S7'] == '' || row['S7'] == null){

                            return '';

                        }
                        else{

                            if(!isNaN(row['S7'].substr(0,1))){

                                return '$ ' + number_format(row['S7'],PRECIOS_DECIMALES,'.',',');

                            }
                            else{

                                return row['S7'];

                            }

                        }

                    }

                }
            ],
            "footerCallback": function ( tfoot, data, start, end, display ) {
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
                };
                
                //
                for (let index = 7; index < (17); index++) {
                
                    pageTotal = api
                    .column( index, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                    }, 0 );
                    var pageT = pageTotal.toLocaleString("es-MX", {minimumFractionDigits:2})                
                    $( api.column( index ).footer() ).html(pageT);
                }
               
            },     
    });

    $('#tableFTPDCXPPesos thead tr:eq(0) th').each( function (i) {
        var title = $(this).text();
        //console.log($(this).text());
        $(this).html( '<input style="color:black" type="text" placeholder="Filtro '+title+'" />' );
        $( 'input', this ).on( 'keyup change', function () {
        
        if ( table_cxp.column(i).search() !== this.value ) {
        table_cxp
        .column(i)
        .search(this.value, true, false)
        .draw();
        
        }
        
        } );
    
    } );


   $.ajax({
    type: 'GET',
    async: true,       
    url: '{!! route('datatables.FTPDCXPPesos') !!}',
    data: {
        "_token": "{{ csrf_token() }}",
        "programaId":"",
        "detalle":1
    },
    beforeSend: function() {
        console.log("enviando CXP...");
        $.blockUI({
        message: '<h1>Actualizando,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
        css: {
        border: 'none',
        padding: '16px',
        width: '50%',
        top: '40%',
        left: '30%',
        backgroundColor: '#fefefe',
        '-webkit-border-radius': '10px',
        '-moz-border-radius': '10px',
        opacity: .7,
        color: '#000000'
        }
        });
    },
    complete: function() {
        setTimeout($.unblockUI, 1500);
    },
    success: function(data){     
        console.log("success CXP ...")
        console.log(data.FTPDCXPPesos);       
        console.log(data)
        if (data == 'tc') {
            bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'>Capture tipo de cambio en Muliix",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
        }else{
            if(data.FTPDCXPPesos.length > 0){
               // createTableCXP();
               console.log("inicia carga CXP ...")
                $("#tableFTPDCXPPesos").dataTable().fnAddData(data.FTPDCXPPesos);
                console.log("temino carga CXP ...")
            }else{
            
            }
        }   
    },
    error: function(jqXHR, textStatus, errorThrown) {
    var msg = '';
    if (jqXHR.status === 0) {
    msg = 'Not connect.\n Verify Network.';
    } else if (jqXHR.status == 404) {
    msg = 'Requested page not found. [404]';
    } else if (jqXHR.status == 500) {
    msg = 'Internal Server Error [500].';
    
    } else {
    msg = 'Uncaught Error.\n' + jqXHR.responseText;
    }
    console.log(msg);
    }
});
    var xhrBuscador = null;
    var PRECIOS_DECIMALES = 2;
    $('#cboprovdescripciones').selectpicker();
    $('#editcboprovdescripciones').selectpicker();

    $('#cboprovalertas').selectpicker({
        noneSelectedText: 'Selecciona una opción',
    });
    $('#cbonumpago').selectpicker({
        noneSelectedText: 'Selecciona una opción',
    });
    const today = new Date()
    const tomorrow = new Date(today)
    tomorrow.setDate(tomorrow.getDate() + 1)
   
    $("#fecha_provision").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,  
    });
    $("#edit_fecha_provision").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,  
    });
    $('#fecha_provision').datepicker('setStartDate', tomorrow);
    $('#fecha_provision').datepicker('setDate', tomorrow);
    $('#edit_fecha_provision').datepicker('setStartDate', tomorrow);
    $('#edit_fecha_provision').datepicker('setDate', tomorrow);
    $("#fecha_alerta").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,            
    });
    $('#fecha_alerta').datepicker('setStartDate', tomorrow);
    $('#fecha_alerta').datepicker('setDate', tomorrow);


var data,
tableName= '#t_ordenes_proyeccion',
tableproy,
str, strfoot, contth,
jqxhr =  $.ajax({
        dataType:'json',
        type: 'GET',
        data:  {
             moneda:''
            },
        url: '{!! route('datatables.cxc_proyeccion') !!}',
        beforeSend: function () {
          
        },
        success: function(data, textStatus, jqXHR) {
           createTableCXC(jqXHR,data);           
        },
        
        complete: function(){
          // setTimeout($.unblockUI, 1500);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.log(msg);
        }
        });

       
    ///////////////////
var table2 = $("#table-provisiones").DataTable(
    {language:{
    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
    deferRender: true,
    iDisplayLength: 6,
    scrollX: true,
    "aaSorting": [],
    dom: 'T<"clear">lfrtip',
        processing: true,
        "order": [[0, "desc"], [ 1, "asc" ]],
        columns: [
        {data: "PCXC_Activo"},
        {data: "ELIMINAR", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if ( oData.PCXC_Activo != 0 ) {
                $(nTd).html("<a id='btneliminarprov' role='button' class='btn btn-danger' style='margin-right: 5px;'><i class='fa fa-trash'></i></a><a id='btneditprov role='button' class='btn btn-primary'><i class='fa fa-edit'></i></a>");
            }
        }},
        {data: "PCXC_ID"},
        {data: "PCXC_Fecha", 
            render: function(data){
                if (data === null){return data;}
                var d = new Date(data);
                return moment(d).format("DD-MM-YYYY");
            }
        },
        {data: "PCXC_Cantidad",
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return "$" + val;
        }
        },
        {data: "PCXC_Cantidad_provision",
        render: function(data){
        var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
        return "$" + val;
        }
        },
        {data: "PCXC_Concepto"},
        {data: "PCXC_Observaciones"},
        ],
        "columnDefs": [
        {
        "targets": [ 0 ],
        "visible": false
        },
       
        ],
        "rowCallback": function (row, data) {
            //console.log(data)
            if ( data.PCXC_Activo == 0 ) {
                $(row).addClass('info');

            }
        
        }
        }
        );
    ////////////////////////////   
        var table_alertas = $("#table-alertas").DataTable(
        {language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "aaSorting": [],
        dom: 'T<"clear">lfrtip',
            processing: true,
        
            columns: [
                {data: "ALERT_Id"},               
                {data: "ALERT_Usuarios"},
                {data: "ELIMINAR", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html("<a id='btneliminaralerta' role='button' class='btn btn-danger' style='margin-right: 5px;'><i class='fa fa-trash'></i></a><a id='btneditalert' role='button' class='btn btn-primary'><i class='fa fa-edit'></i></a>");
            }},
               
                {data: "PCXC_ID"},
                {data: "ALERT_FechaAlerta",
                render: function(data){
                if (data === null){return data;}
                var d = new Date(data);
                return moment(d).format("DD-MM-YYYY");
                }},
                {data: "ALERT_Descripcion"},           
            ],
            "columnDefs": [            
                {
                "targets": [ 0 ],
                "visible": false
                },
                {
                "targets": [ 1 ],
                "visible": false
                }
              
            ],
            
            }
            );
/////////////////////////////
    var options = [];         
    var options_edo = [];
    var opciones = [ //tambien estan los IDs estaticos en el controlador
    { 'llave': '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5', 'valor': 'Abierta' },
    { 'llave': '2209C8BF-8259-4D8C-A0E9-389F52B33B46', 'valor': 'Cerrada por Usuario' },
    { 'llave': 'D528E9EC-83CF-49BE-AEED-C3751A3B0F27', 'valor': 'Embarque Completo' },
    ];
    for (var i = 0; i < opciones.length; i++) { 
        options_edo.push('<option value="' + opciones[i]['llave'] + '">' +
        opciones[i]['valor'] + '</option>');
        }
    $('#estado').append(options_edo).selectpicker('refresh');
    $('#estado').val('3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5').selectpicker('refresh');
    $('#estado_save').append(options_edo).selectpicker('refresh');


$("#estado").on('changed.bs.select', 
function (e, clickedIndex, isSelected, previousValue) {
   
    var options = [];         
    var estado =($('#estado').val() == null) ? '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5': $('#estado').val();    
        $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado: estado
                        },
                        url: "cxc_combobox",
                        success: function(data){
                          //quitar OV
                          //obtener OV y remover de datatable
                        }
                        });
});

$("#estado_save").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var options = [];         
    var estado_save =($('#estado_save').val() == null) ? 0 : $('#estado_save').val();    
       if (estado_save != 0) {
           $.ajax({
                        type: 'GET',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado_save: estado_save,
                            idov : $('#input_id').val()
                        },
                        url: "cxc_guardar_estado_ov",
                        success: function(data){
                            if (data.ots.length > 0) {
                                bootbox.dialog({
                                    title: "Mensaje",
                                    message: "<div class='alert alert-danger m-b-0'>Esta OV no se puede cambiar porque tiene estas ordenes Abiertas o en Proceso.</div><div class='table-scroll' id='cxc_ots'> <table id='table_ots' class='table table-striped table-bordered hover' width='100%'> <thead> <tr> <th>OT Codigo</th> <th>Articulo</th> <th>Cantidad</th> </tr> </thead> </table>",
                                    buttons: {
                                        success: {
                                            label: "Ok",
                                            className: "btn-success m-r-5 m-b-5"
                                        }
                                    }
                                }).find('.modal-content').css({'font-size': '14px'} );
                                //$("#table_ots").DataTable().clear().draw();
                                inicializatabla();
                                $("#table_ots").dataTable().fnAddData(data.ots);
                                $('#estado_save').val(previousValue).selectpicker('refresh');
                            } else {
                                bootbox.dialog({
                                    title: "Mensaje",
                                    message: "<div class='alert alert-success m-b-0'> Se guardo estado de OV.</div>",
                                    buttons: {
                                        success: {
                                            label: "Ok",
                                            className: "btn-success m-r-5 m-b-5"
                                        }
                                    }
                                }).find('.modal-content').css({'font-size': '14px'} );
                                reloadBuscadorOV();
                            }
        
                        }
                        });
       }
        
});
function inicializatabla(){
     var table_ots = $("#table_ots").DataTable(
        {
            language:{
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "aaSorting": [],
            dom: 'T<"clear">lfrtip',
                processing: true,
                columns: [
                {data: "Codigo"},
                {data: "Articulo"},
                {data: "Cantidad",
                    render: function(data){
                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                    return val;
                }}
                ]
        });

}


function createTableCXC(jqXHR,data){
     data = JSON.parse(jqXHR.responseText);
            // Iterate each column and print table headers for Datatables
            contth = 1;
            $.each(data.columns, function (k, colObj) {
                if (contth == 4) {
                    str = '<th class="segundoth">' + colObj.name + '</th>';
                    strfoot = '<th class="segundoth"></th>';
                }else{
                    str = '<th>' + colObj.name + '</th>';
                    strfoot = '<th></th>';
                }
                contth ++;
                $(str).appendTo(tableName+'>thead>tr');
                $(strfoot).appendTo(tableName+'>tfoot>tr');
                console.log("adding col "+ colObj.name);
            });
            
            for (let index = 8; index < Object.keys(data.columns).length; index++) {
                data.columns[index].render = function (data, type, row) {            
                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                    return val;
                }
            }
                    // Debug? console.log(data.columns[0]);
                   $('#t_ordenes_proyeccion thead tr').clone().appendTo( $("#t_ordenes_proyeccion thead") );   
            
         tableproy = $(tableName).DataTable({
                "pageLength": 6,
                deferRender: true,
                "lengthMenu": [[6, 10, 25, 50, -1], [6, 10, 25, 50, "Todo"]],
                dom: 'rtip',
                scrollX: true,
                scrollCollapse: true,
                scrollY: "300px",
                fixedColumns: {
                leftColumns: 4
                },
                aaSorting: [[8, "desc" ]],
                processing: true,
                columns: data.columns,
                data:data.data,
                paging: false,
                "language": {
                    "url": "{{ asset('assets/lang/Spanish.json') }}",                    
                },
                columnDefs: [
                    {
                    "targets": 0,
                    "visible": false
                    },
                    {
                    "targets": 1,
                    "visible": false
                    },
                    
                ],

                "initComplete": function( settings, json ) {
                },
                "footerCallback": function ( tfoot, data, start, end, display ) {
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
                };
                
                //
                for (let index = 8; index < (contth-1); index++) {
                
                    pageTotal = api
                    .column( index, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                    }, 0 );
                    var pageT = pageTotal.toLocaleString("es-MX", {minimumFractionDigits:2})                
                    $( api.column( index ).footer() ).html(pageT);
                }
               
                }, 
            });
            $('#t_ordenes_proyeccion thead tr:eq(0) th').each( function (i) {
            var title = $(this).text();
            //console.log($(this).text());
            $(this).html( '<input style="color:black" type="text" placeholder="Filtro '+title+'" />' );
            $( 'input', this ).on( 'keyup change', function () {
            
            if ( tableproy.column(i).search() !== this.value ) {
            tableproy
            .column(i)
            .search(this.value, true, false)
            .draw();
            
            }
            
            } );
            
            } );
}



function reloadBuscadorOV(){
    
        
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.cxc_proyeccion') !!}',
        data: {
            moneda:''
        },
        beforeSend: function() {
             $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
            css: {
            border: 'none',
            padding: '16px',
            width: '50%',
            top: '40%',
            left: '30%',
            backgroundColor: '#fefefe',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .7,
            color: '#000000'
            }  
            });
        },
        complete: function() {
            //setTimeout($.unblockUI, 1500);
        },
        success: function(data, textStatus, jqXHR) {
                console.log('longitud '+data.data.length)
            if(data.data.length > 0){
                //Destroy the old Datatable
                $(tableName).DataTable().clear().destroy();
                $(tableName + " tfoot").empty();
                $(tableName + " thead").empty();
                $('<tr></tr>').appendTo(tableName+'>thead');
                $('<tr></tr>').appendTo(tableName+'>tfoot');
                console.log('destroy ')
                //Create new Datatable
                createTableCXC(jqXHR, data);           
            }else{
                bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay Ordenes de Venta que cumplan los parámetros.</div>",
                buttons: {
                success: {
                label: "Ok",
                className: "btn-success m-r-5 m-b-5"
                }
                }
                }).find('.modal-content').css({'font-size': '14px'} );
            }            
        }
    });
}  

$("body").on("click", ".editButton", function (e) {
    $(this).bind('click', false);
    e.preventDefault();
    var rowdata = tableproy.row( $(this).parents('tr') ).data();
    var num_text = rowdata['XCOBRAR'];
    console.log('numtext: '+ num_text)
    var cant = num_text.replace(",", ""); //remover comas
    console.log('cant: '+ cant);
    
    $.ajax({
        type: 'GET',
               
        url: '{!! route('getcantprovision') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
           idov : rowdata['OV']
        },
        success: function(data){
            var cantrestante = parseFloat(cant) - parseFloat(data.suma);  
            cantrestante = parseFloat(cantrestante).toFixed(2);   
            cantrestante = parseFloat(cantrestante);   
                  
            console.log('clic ov: '+ parseFloat(cant));
            console.log('clic ov_cant: '+ cant)
            console.log('clic ov_suma: '+ data.suma)
            if(cantrestante < 0){
                cantrestante = 0;
                bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'> Marque pagos recibidos.</div>",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            }
            $('#input_id').val(rowdata['OV']);
            reloadProvisiones();
            options = [];
            options.push('<option value="">Selecciona una opción</option>');
            $("#cbonumpago").empty();
            for (var i = 0; i < data.cboprovisiones.length; i++) { 
                options.push('<option value="' + data.cboprovisiones[i]['llave'] + '">' +
                data.cboprovisiones[i]['valor'] + '</option>');
            }
            $('#cbonumpago').append(options).selectpicker('refresh');                                

            $('#codigo').text('Provisionar '+rowdata['OV'])
           
            
            $('#cant').val(cantrestante) 
            $('#cant_max_permitida').val(cantrestante)                 
            $('#cant').attr('max', cantrestante)
            console.log(data.estado_save)
            $('#estado_save').val(data.estado_save).selectpicker('refresh');
            if (cantrestante <= 0) {
                $('#btn-modal').attr( "style", 'margin-top: 23px; background-color: #5cb85c;' );
                $('#btn-modal').attr("disabled", 'true');
            }else{
                $('#btn-modal').attr( "style", 'margin-top: 23px;' );
                $('#btn-modal').removeAttr("disabled");
            }
            activaTab('default-tab-1'); //para que se muestre siempre en provisionar.
            $('#edit').modal('show');
        },
        complete: function () {
            $('.editButton').unbind('click', false);
        }
    });
   
});

$('#table-alertas tbody').on( 'click', 'a', function (event) {
    var rowdata = table_alertas.row( $(this).parents('tr') ).data();
    console.log(event.currentTarget.id)
    if(event.currentTarget.id+'' == 'btneliminaralerta'){
        $('#id_delete_alert').val(rowdata['ALERT_Id']);
        $('#delete_alert').modal('show');   
    }else{
        
        if(typeof(rowdata['ALERT_Usuarios']) != 'undefined' && rowdata['ALERT_Usuarios'] != null){
            var usrs = rowdata['ALERT_Usuarios'];
            usrs = usrs.split(',');
            //console.log(usrs);
            $('#cbousuarios').val(usrs);
        }else{
            $('#cbousuarios').val([]);
        }     
        //console.log(usrs)
        $('#editalert-idalerta').val(rowdata['ALERT_Id']);        
        $('#cbousuarios').selectpicker('refresh');
        $('#editalert').modal('show');
    }
    
   
});

$('#table-provisiones tbody').on( 'click', 'a', function (event) {
    var rowdata = table2.row( $(this).parents('tr') ).data();
    console.log(event.currentTarget.id)
    $('#id_prov').val(rowdata['PCXC_ID']);
    if(event.currentTarget.id+'' == 'btneliminarprov'){
        var id_prov = rowdata['PCXC_ID'];
        //hay que checar si hay Alertas
            $.ajax({
            type: 'GET',
            async: true,       
            url: '{!! route('getcantalertas_cxc') !!}',
            data: {
                "_token": "{{ csrf_token() }}",
            idprov : id_prov
            },
            beforeSend: function() {
                $.blockUI({
                    baseZ: 2000,
                    message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                    css: {
                        border: 'none',
                        padding: '16px',
                        width: '50%',
                        top: '40%',
                        left: '30%',
                        backgroundColor: '#fefefe',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .7,
                        color: '#000000'
                    }
                });
            },
            complete: function() {
                setTimeout($.unblockUI, 1500);
                
            }, 
            success: function(data){
                
                if( parseFloat(data.cantalertas).toFixed(2) == 0){
                    $('#delete_prov').modal('show');  
                }else{
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-danger m-b-0'> Borra primero las alertas asociadas a esta Provisión.</div>",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({'font-size': '14px'} );
                }
            }
        });
       
    }else{
        // se ha presionado btn editar prov.
         $.ajax({
            type: 'GET',  
            async: false,   
            url: '{!! route('getconcepto_prov_cxc') !!}',
            data: {
                "_token": "{{ csrf_token() }}",
             textconcepto : rowdata['PCXC_Concepto']
            },
            success: function(data){
                var d = new Date(rowdata['PCXC_Fecha']);
                fechaprov = moment(d).format("DD/MM/YYYY");
                console.log(rowdata['PCXC_Fecha'])  
                
                var newcantidad = parseFloat($('#cant_max_permitida').val()) + parseFloat(rowdata['PCXC_Cantidad']);
                $('#edit_fecha_provision').val(fechaprov);    
                $('#editcant').val(rowdata['PCXC_Cantidad']);
                $('#editcant').attr("max", newcantidad.toFixed(2));   
                $('#editcboprovdescripciones').val(data.idconcepto); 
                console.log(data.idconcepto); 
                $('#editcboprovdescripciones').selectpicker("refresh");
                $('#editcomment').val(rowdata['PCXC_Observaciones']);

               // $('#editprov-id').val(rowdata['PCXC_ID']);        
                $('#editprov').modal('show');
                
            }
        });
       
    }
    
   
});
$('#btn-provisionar').on('click', function(e) {
    e.preventDefault();
    var xpagar = $('#cant_max_permitida').val()*1;
    var cantidadprov = $('#cant').val()*1;
    var accion = 'insert';
    cantprovision(accion ,cantidadprov, xpagar);
});
function cantprovision(accion, cantidadprov, xpagar){
    
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('getcantprovision') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
           idov : $('#input_id').val()
        },
        beforeSend: function() {
            $.blockUI({
                baseZ: 2000,
                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                css: {
                    border: 'none',
                    padding: '16px',
                    width: '50%',
                    top: '40%',
                    left: '30%',
                    backgroundColor: '#fefefe',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .7,
                    color: '#000000'
                }
            });
        },
        complete: function() {
            setTimeout($.unblockUI, 1500);
            
        }, 
        success: function(data){
            console.log('cant: '+parseFloat(cantidadprov.toFixed(2)))
            console.log('max: '+parseFloat(xpagar.toFixed(2)))
           
            if( parseFloat(cantidadprov.toFixed(2)) <= parseFloat(xpagar.toFixed(2))){
                switch (accion) {
                    case 'insert':
                        if($('#cboprovdescripciones option:selected').val() == '' || $('#cboprovdescripciones option:selected').val() == '0'){
                            bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'> Hay campos incorrectos!.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({'font-size': '14px'} );
                        
                        }else{
                            insertprovision();   
                        }
                        break;
                    case 'update':
                        if($('#editcboprovdescripciones option:selected').val() == '' || $('#editcboprovdescripciones option:selected').val() == '0'){
                            bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'> Hay campos incorrectos!.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({'font-size': '14px'} );
                        
                        }else{
                            updateprovision(xpagar);  
                        }
                        break;
                    default:
                        break;
                }
            }else{
                bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'> La cantidad activa provisionada no debe ser rebasada.</div>",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            }
              
        }//endsuccess
    });
}


$('#btn-alertar').on('click', function(e) {   
    if($('#fecha_alerta').val() == '' || $('#cbonumpago option:selected').val() == '' || $('#cboprovalertas option:selected').val() == ''){
        bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'> Hay campos incorrectos!.</div>",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );
        
    }else{
    var cantidadprov = $('#cant').val()*1;
                insertalerta();  
        }
});
$('#btn-guarda-usuarios-alert').on('click', function(e) { 
    if(typeof($('#cbousuarios').val()) == 'undefined' && $('#cbousuarios').val() == null){          
            $('#cbousuarios').val('');
    }     
    $.ajax({
    type: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {
    "_token": "{{ csrf_token() }}",
    cbousuarios: $('#cbousuarios').val(),
    idalerta: $('#editalert-idalerta').val()
    },
    url: '{!! route('cxc_guarda_edit_alerta') !!}',
    beforeSend: function() {
    $.blockUI({
    message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
    css: {
    border: 'none',
    padding: '16px',
    width: '50%',
    top: '40%',
    left: '30%',
    backgroundColor: '#fefefe',
    '-webkit-border-radius': '10px',
    '-moz-border-radius': '10px',
    opacity: .7,
    color: '#000000'
    }
    });
    },
    complete: function() {
        reloadProvisiones();
        setTimeout($.unblockUI, 1500);
    },
    success: function(data){   
       $('#editalert').modal('hide');
    }
    });
    
});
$('#btn-guarda-prov').on('click', function(e) { 
   e.preventDefault();
    var xpagar = $('#editcant').attr('max') * 1;
    var cantidadprov = $('#editcant').val() * 1;
    var accion = 'update';
    console.log('update max:' + xpagar)
    console.log('update cant:' + cantidadprov)
    cantprovision(accion ,cantidadprov, xpagar);
});

$('#btn-delete-alert').on('click', function(e) { 
   var evidencia = $('#textarea_delete').val();
    
    if (evidencia.length == 0 || evidencia == '' || evidencia == 'undefined'|| evidencia == null) {
        bootbox.dialog({
            title: "Remover Alerta",
            message: "<div class='alert alert-danger m-b-0'> Ingresa Evidencia o Acción.</div>",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );
    }
    else{
   
        $.ajax({
            type: 'GET',       
            url: '{!! route('borra-alerta') !!}',
            data: { 
                "_token": "{{ csrf_token() }}",   
            idalerta: $('#id_delete_alert').val(),
            evidencia: $('#textarea_delete').val(),           
        },
        beforeSend: function() {
            $.blockUI({
                baseZ: 2000,
                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                css: {
                    border: 'none',
                    padding: '16px',
                    width: '50%',
                    top: '40%',
                    left: '30%',
                    backgroundColor: '#fefefe',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .7,
                    color: '#000000'
                }
            });
        },
        complete: function() {
            setTimeout($.unblockUI, 1500);
            
        }, 
        success: function(data){
            reloadBuscadorOV();
            reloadComboProvisiones();
            reloadProvisiones();
            $('#delete_alert').modal('hide');
        }
        }); 
    }
});
$('#btn-delete-prov').on('click', function(e) { 
   $.ajax({
        type: 'GET',       
        url: '{!! route('borra-prov') !!}',
        data: {
            "_token": "{{ csrf_token() }}",    
           idprov: $('#id_prov').val(),
           idov : $('#input_id').val()                
        },
        beforeSend: function() {
            $.blockUI({
                baseZ: 2000,
                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                css: {
                    border: 'none',
                    padding: '16px',
                    width: '50%',
                    top: '40%',
                    left: '30%',
                    backgroundColor: '#fefefe',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .7,
                    color: '#000000'
                }
            });
        },
        complete: function() {
            setTimeout($.unblockUI, 1500);
            
        }, 
        success: function(data){

            var nuevaCant = data.cantxprovisionar;
            nuevaCant = parseFloat(nuevaCant).toFixed(2);
            nuevaCant = parseFloat(nuevaCant);

            console.log(nuevaCant)
            $('#cant_max_permitida').val(nuevaCant);
         
            $('#cant').val(nuevaCant);
            $('#cant').attr('max', nuevaCant);
            reloadProvisiones();
            reloadBuscadorOV();
            reloadComboProvisiones();
           
            if (nuevaCant <= 0) {
                $('#btn-modal').attr( "style", 'margin-top: 23px; background-color: #5cb85c;' );
                $('#btn-modal').attr("disabled", 'true');
            }else{
                $('#btn-modal').attr( "style", 'margin-top: 23px;' );
                $('#btn-modal').removeAttr("disabled");
            }
            $('#delete_prov').modal('hide');
        }
        }); 
    
});

function insertprovision(){   
    $.ajax({
    type: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {
    "_token": "{{ csrf_token() }}",
    inputid: $('#input_id').val(),
    fechaprovision: $('#fecha_provision').val(),
    cant: $('#cant').val(),
    descripcion : $('#cboprovdescripciones option:selected').text(),
    comment: $('#comment').val(),
    },
    url: '{!! route('cxc_store_provision') !!}', 
    beforeSend: function() {
            $.blockUI({
                baseZ: 2000,
                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                css: {
                    border: 'none',
                    padding: '16px',
                    width: '50%',
                    top: '40%',
                    left: '30%',
                    backgroundColor: '#fefefe',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .7,
                    color: '#000000'
                }
            });
        },
        complete: function() {
            setTimeout($.unblockUI, 1500);
            
        },   
    success: function(data){
        var nuevaCant =parseFloat($('#cant_max_permitida').val()) - parseFloat($('#cant').val());
        nuevaCant = parseFloat(nuevaCant).toFixed(2);
        nuevaCant = parseFloat(nuevaCant);

        $('#cant').val(nuevaCant);
        $('#cant_max_permitida').val(nuevaCant);
        $('#cant').attr('max', nuevaCant);
        reloadProvisiones();
        reloadBuscadorOV();
        reloadComboProvisiones();
        $('#agregar').modal('hide');
        if (nuevaCant <= 0) {
            $('#btn-modal').attr( "style", 'margin-top: 23px; background-color: #5cb85c;' );
            $('#btn-modal').attr("disabled", 'true');
        }else{
            $('#btn-modal').attr( "style", 'margin-top: 23px;' );
            $('#btn-modal').removeAttr("disabled");
        }
        $("#cboprovdescripciones").val('0');
        $("#cboprovdescripciones").selectpicker("refresh");
        $("#comment").val('');
}
    });
}
function updateprovision(xpagar){   
    $.ajax({
    type: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {
    "_token": "{{ csrf_token() }}",
    fechaprovision: $('#edit_fecha_provision').val(),
    cant: $('#editcant').val(),
    descripcion : $('#editcboprovdescripciones option:selected').text(),
    comment: $('#editcomment').val(),
    id: $('#id_prov').val(),
    idov : $('#input_id').val()
    },
    url: '{!! route('cxc_update_provision') !!}', 
      
    success: function(data){
    
        var nuevaCant = data.cantxprovisionar;
            nuevaCant = parseFloat(nuevaCant).toFixed(2);
            nuevaCant = parseFloat(nuevaCant);
        console.log(nuevaCant)
        $('#cant_max_permitida').val(nuevaCant);
        $('#editcant').attr('max', nuevaCant);
        $('#cant').val(nuevaCant);
        $('#cant').attr('max', nuevaCant);
        reloadProvisiones();
        reloadBuscadorOV();
        reloadComboProvisiones();
        $('#editprov').modal('hide');
        if (nuevaCant <= 0) {
            $('#btn-modal').attr( "style", 'margin-top: 23px; background-color: #5cb85c;' );
            $('#btn-modal').attr("disabled", 'true');
        }else{
            $('#btn-modal').attr( "style", 'margin-top: 23px;' );
            $('#btn-modal').removeAttr("disabled");
        }
        $("#editcboprovdescripciones").val('0');
        $("#editcboprovdescripciones").selectpicker("refresh");
        $("#editcomment").val('');
}
    });
}
function reloadComboProvisiones(){
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('getcantprovision') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
           idov : $('#input_id').val()
        },
        success: function(data){
            options = [];
            options.push('<option value="">Selecciona una opción</option>');
            $("#cbonumpago").empty();
            for (var i = 0; i < data.cboprovisiones.length; i++) { 
                options.push('<option value="' + data.cboprovisiones[i]['llave'] + '">' +
                data.cboprovisiones[i]['valor'] + '</option>');
            }
            $('#cbonumpago').append(options).selectpicker('refresh');    
        }
    });
}
function insertalerta(){   
    $.ajax({
    type: 'POST',
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data: {
    "_token": "{{ csrf_token() }}",
    numpago: $('#cbonumpago option:selected').val(),
    fechaalerta: $('#fecha_alerta').val(),   
    alerta : $('#cboprovalertas option:selected').text()
    },
    url: '{!! route('cxc_store_alerta') !!}',
    beforeSend: function() {
    $.blockUI({
    message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
    css: {
    border: 'none',
    padding: '16px',
    width: '50%',
    top: '40%',
    left: '30%',
    backgroundColor: '#fefefe',
    '-webkit-border-radius': '10px',
    '-moz-border-radius': '10px',
    opacity: .7,
    color: '#000000'
    }
    });
    },
    complete: function() {
    setTimeout($.unblockUI, 1500);
    },
    success: function(data){
    reloadProvisiones();
    }
    });
}
function reloadProvisiones(){
        $("#table-provisiones").DataTable().clear().draw();
        $("#table-alertas").DataTable().clear().draw();

    $.ajax({
        type: 'GET',      
        url: '{!! route('datatables.cxc_provisiones') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
           idov : $('#input_id').val()
        },
        beforeSend: function() {
            $.blockUI({
                baseZ: 2000,
                message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                css: {
                    border: 'none',
                    padding: '16px',
                    width: '50%',
                    top: '40%',
                    left: '30%',
                    backgroundColor: '#fefefe',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .7,
                    color: '#000000'
                }
            });
        },
        complete: function() {
            setTimeout($.unblockUI, 1500);
            
        }, 
        success: function(data){
            //console.log((data.provisiones).length)
            if((data.provisiones).length > 0){
                $("#table-provisiones").dataTable().fnAddData(data.provisiones);
            }
            if((data.alertas).length > 0){
                $("#table-alertas").dataTable().fnAddData(data.alertas);
            }                
        }
    });
}
    function activaTab(tab){
    $('.nav-pills a[href="#' + tab + '"]').tab('show');
    };


   
function number_format(number, decimals, dec_point, thousands_sep) 
{
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        toFixedFix = function (n, prec) {
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            var k = Math.pow(10, prec);
            return Math.round(n * k) / k;
        },
        s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

}                                                                                                    
</script>