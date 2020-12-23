@extends('home')

            @section('homecontent')
            <style>
                .btn{
                    border-radius: 4px;
                }
             th {
            font-size: 12px;
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
                th:first-child {
                    position: -webkit-sticky;
                    position: sticky;
                    left: 0;
                    z-index: 5;
                }
                th:nth-child(2) {
                    position: -webkit-sticky;
                    position: sticky;
                    left: 155px;
                    z-index: 5;
                }
                table.dataTable thead .sorting {                
                    position: sticky;
                }
                .DTFC_LeftBodyWrapper{
                    margin-top: 81px;
                }
                .DTFC_LeftHeadWrapper {
                    display:none;
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
                .btn-group > .btn{
                float: none;
                }
                .btn{
                border-radius: 4px;
                }
                .btn-group > .btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
                border-radius: 4px;
                }
                .btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
                border-top-right-radius: 4px;
                border-bottom-right-radius: 4px;
                }
                .btn-group > .btn:last-child:not(:first-child), .btn-group > .dropdown-toggle:not(:first-child) {
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px;
                }
                .dataTables_wrapper .dataTables_length { /*mueve el selector de registros a visualizar*/
                float: right;
                }
                
                div.dataTables_wrapper div.dataTables_processing { /*Procesing mas visible*/
                z-index: 10;
                }
                input{
                color: black;
                }
               
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Provision CXC
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                           <!-- begin row -->
                            <div  id="btnBuscadorOrdenVenta">
   
                                            <div class="row" style="margin-bottom: 40px">
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
                                                        =>"cliente", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple data-actions-box"=>"true",
                                                        'data-live-search' => 'true', 'multiple'=>'multiple'])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label><strong>
                                                                <font size="2">Comprador</font>
                                                            </strong></label>
                                                        {!! Form::select("comprador[]", $comprador, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"comprador", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple data-actions-box"=>"true",
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
                                                        <button type="button" class="form-control btn btn-danger m-r-5 m-b-5" id="boton-mostrar-OValertadas"><i class='fa fa-bell'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                    <table id="ordenes-venta" class="table table-striped table-bordered hover" width="100%">
                                                        <thead>
                                                            <tr>
                                                
                                                                <th>Provisión</th>
                                                                <th>Orden de Venta</th>
                                                                <th>Estado</th>
                                                                <th>Cliente</th>
                                                                <th>Proyecto</th>
                                                                <th>Comprador</th>
                                                
                                                                <th>Fecha OV</th>
                                                                <th>Referencia OC</th>
                                                                <th>Importe OV</th>
                                                                <th>Moneda</th>
                                                                <th>Importe Facturado</th>
                                                                <th>Importe X Facturar</th>
                                                                <th>Importe Embarcado</th>

                                                                <th>Importe X Embarcar</th>
                                                                <th>Importe Pagado</th>
                                                                <th>Importe X Pagar</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                   
                                
                            </div>
                            <!-- end row -->
                                                   

                    </div>   <!-- /.container -->

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
                <ul class="nav nav-tabs" >
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
                        <br>
                        <div class="row">
                            <div class="col-md-3">			
                                <div class="form-group">
                                    <label for="fecha_provision">Fecha</label>
                                    <input type="text" id="fecha_provision" name="fecha_provision" class='form-control'>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cant">Cantidad</label>
                                    <input type="number" class="form-control" id="cant" name="cant" step="0.01" autocomplete="off"> 
                                    <input style="display:none" type="number" class="form-control" id="cant_tabla" name="cant_tabla" hidden> 
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cant">Descripción</label>
                                    {!! Form::select("cboprovdescripciones", $provdescripciones, null, [
                                     "class" => "form-control selectpicker","id"=>"cboprovdescripciones", "data-style" => "btn-success btn-sm"])
                                    !!}
                                </div>
                            </div>			
                            <div class="col-md-3">
                                <div class="form-group">
                                        
                                        <button id='btn-provisionar' style="margin-top: 23px;" class="btn btn-primary form-control" style="margin-top:4px">Agregar</button>													
                                </div>
                            </div>
                        </div><!-- /.row -->
                        <div class="table-scroll" id="registros-provisionar">
                            <table id="table-provisiones" class="table table-striped table-bordered hover" width="100%">
                                <thead>
                                    <tr>                        
                                        <th>Activa</th>
                                        <th># Provisión</th>
                                        <th>Fecha Pago</th>
                                        <th>Provisión Pago</th>                                                                                
                                        <th>Provisión menos Pagos</th>                                                                                
                                        <th>Descripción</th>                                                                                
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
                                    "class" => "form-control selectpicker","id"=>"cbonumpago", "data-style" => "btn-success btn-sm"])
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
                                    "class" => "form-control selectpicker","id"=>"cboprovalertas", "data-style" => "btn-success btn-sm"])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">                    
                                    <button id='btn-alertar' style="margin-top: 23px;" class="btn btn-primary form-control"
                                        style="margin-top:4px">Agregar</button>
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
                </div>  <!-- /.tab-content -->                               
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
                <h4 class="modal-title" >Editar alerta</h4>
            </div>

            <div class="modal-body" style='padding:16px'>
                <input type="text" style="display: none" class="form-control input-sm" id="input_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cant">Usuarios Notificados</label>
                                    <input type="text" name="editalert-idalerta" id="editalert-idalerta" hidden>
                                   {!! Form::select("cbousuarios[]", $cbousuarios, null, [
                                    "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                    =>"cbousuarios", "data-size" => "8", "data-style" => "btn-success btn-sm", "multiple data-actions-box"=>"true",
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
<div class="modal fade" id="delete_alert" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Remover alerta</h4>
            </div>

            <div class="modal-body" style='padding:16px'>
                <input type="text" style="display: none" class="form-control input-sm" id="input_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cant">Acción Tomada  / Evidencia</label>
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
@endsection
<script>
function js_iniciador() {
    $('.boot-select').selectpicker();
    $('.toggle').bootstrapSwitch();
    $('.dropdown-toggle').dropdown();
   
    var xhrBuscador = null;
    $('#cliente').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#cbousuarios').selectpicker({    
        noneSelectedText: 'Selecciona una opción',   
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#comprador').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#cboprovdescripciones').selectpicker({
        noneSelectedText: 'Selecciona una opción',
    });
    $('#cboprovalertas').selectpicker({
        noneSelectedText: 'Selecciona una opción',
    });
    $('#cbonumpago').selectpicker({
        noneSelectedText: 'Selecciona una opción',
    });
    $("#fecha_provision").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,  
        minDate: new Date(),                     
    });
    $('#fecha_provision').datepicker('setStartDate', new Date());
    $('#fecha_provision').datepicker('setDate', new Date());
    $("#fecha_alerta").datepicker({
        format: "dd/mm/yyyy",
        language: "es",
        autoclose: true,            
    });
    $('#fecha_alerta').datepicker('setStartDate', new Date());
    $('#fecha_alerta').datepicker('setDate', new Date());
    
    var table = $("#ordenes-venta").DataTable({
        language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        iDisplayLength: 6,
        aaSorting: [],
        deferRender: true,
        dom: 'T<"clear">lfrtip',       
        scrollX: true,
        fixedColumns: {
        leftColumns: 2
        },
        scrollCollapse: true,
        columns: [
            
            {data: "PROVISION", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            $(nTd).html("<a id='btneditar' role='button'>"+oData.PROVISION+"</a>");
            }},
            {data: "CODIGO"},
            {data: "ESTATUS_OV"},
            {data: "CLIENTE"},
            {data: "PROYECTO"},
            {data: "COMPRADOR"},

            {data: "FECHA_OV"},
            {data: "OV_ReferenciaOC"},
                {data: "TOTAL",
                render: function(data){
                var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                return "$" + val;
                }},
                {data: "Moneda"},
                {data: "IMPORTE_FACTURADO",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "IMPORTE_XFACTURAR",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "EMBARCADO",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},

            {data: "IMPORTE_XEMBARCAR",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "PAGOS_FACTURAS",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "X_PAGAR",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }} 
            
        
        ],
    });
var table2 = $("#table-provisiones").DataTable(
    {language:{
    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
    "aaSorting": [],
    dom: 'T<"clear">lfrtip',
        processing: true,
        "order": [[0, "desc"], [ 1, "asc" ]],
        columns: [
        {data: "PCXC_Activo"},
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
        ],
        "columnDefs": [
        {
        "targets": [ 0 ],
        "visible": false
        },
       
        ],
        "rowCallback": function (row, data) {
        console.log(data)
        if ( data.PCXC_Activo == 0 ) {
        $(row).addClass('danger');
        }
        
        }
        }
        );


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
$('#ordenes-venta thead tr').clone(true).appendTo( '#ordenes-venta thead' );

$('#ordenes-venta thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input type="text" placeholder="Filtro '+title+'" />' );
   
    $( 'input', this ).on( 'keyup change', function () {       
            
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search(this.value, true, false)                    
                    .draw();
            } 
                
    } );
} );


$('#estado').selectpicker({
noneSelectedText: 'Selecciona una opción',
});

                      var options = [];         
        $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado: '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5'
                        },
                        url: "cxc_combobox",
                        success: function(data){
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#cliente").empty();
                            for (var i = 0; i < data.clientes.length; i++) { options.push('<option value="' + data.clientes[i]['llave'] + '">' +
                                data.clientes[i]['valor'] + '</option>');
                                }
                            $('#cliente').append(options).selectpicker('refresh');                               
                            options = [];
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#comprador").empty();
                            for (var i = 0; i < data.compradores.length; i++) { options.push('<option value="' + data.compradores[i]['llave'] + '">' +
                                data.compradores[i]['valor'] + '</option>');
                                }
                            $('#comprador').append(options).selectpicker('refresh');                            
                        }
                        });
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
$.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",                            
                        },
                        url: "cxc_combobox2",
                        success: function(data){
                            options = [];
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#cboprovdescripciones").empty();
                            for (var i = 0; i < data.provdescripciones.length; i++) { 
                                options.push('<option value="' + data.provdescripciones[i]['CMM_ControlId'] + '">' +
                                data.provdescripciones[i]['CMM_Valor'] + '</option>');
                            }
                            $('#cboprovdescripciones').append(options).selectpicker('refresh');                               
                            options = [];
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#cboprovalertas").empty();
                            for (var i = 0; i < data.provalertas.length; i++) { 
                                options.push('<option value="' + data.provalertas[i]['CMM_ControlId'] + '">' +
                                data.provalertas[i]['CMM_Valor'] + '</option>');
                                }
                            $('#cboprovalertas').append(options).selectpicker('refresh');
                            options = [];
                           
                            $("#cbousuarios").empty();
                            for (var i = 0; i < data.cbousuarios.length; i++) { options.push('<option value="' + data.cbousuarios[i]['llave'] + '">'+data.cbousuarios[i]['valor'] + '</option>');
                            }
                            $('#cbousuarios').append(options).selectpicker('refresh');
                        }
                        });

$("#estado").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
   
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
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#cliente").empty();
                            for (var i = 0; i < data.clientes.length; i++) { options.push('<option value="' + data.clientes[i]['llave'] + '">' +
                                data.clientes[i]['valor'] + '</option>');
                                }
                            $('#cliente').append(options).selectpicker('refresh');                               
                            options = [];
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#comprador").empty();
                            for (var i = 0; i < data.compradores.length; i++) { options.push('<option value="' + data.compradores[i]['llave'] + '">' +
                                data.compradores[i]['valor'] + '</option>');
                                }
                            $('#comprador').append(options).selectpicker('refresh');
                        }
                        });
});
$("#cliente").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
   
    var options = [];   
    var registros = $('#cliente').val() == null ? 0 : $('#cliente').val().length;
        var cadena = "";
       for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#cliente option:selected')[x]).val();
            } else {
                cadena += $($('#cliente option:selected')[x]).val() + "', '";
            }
        }
        var solocompradores = cadena;      
    var estado =($('#estado').val() == null) ? '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5' : $('#estado').val();   
    if(solocompradores.length > 2 && cadena != '') {
        
         $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado: estado,
                            solocompradores: solocompradores
                        },
                        url: "cxc_combobox",
                        success: function(data){                            
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#comprador").empty();
                            for (var i = 0; i < data.compradores.length; i++) { options.push('<option value="' + data.compradores[i]['llave'] + '">' +
                                data.compradores[i]['valor'] + '</option>');
                                }
                            $('#comprador').append(options).selectpicker('refresh');
                        }
                        });
    }else{
        $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado: estado
                        },
                        url: "cxc_combobox",
                        success: function(data){
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#cliente").empty();
                            for (var i = 0; i < data.clientes.length; i++) { options.push('<option value="' + data.clientes[i]['llave'] + '">' +
                                data.clientes[i]['valor'] + '</option>');
                                }
                            $('#cliente').append(options).selectpicker('refresh');                               
                            options = [];
                            options.push('<option value="">Selecciona una opción</option>');
                            $("#comprador").empty();
                            for (var i = 0; i < data.compradores.length; i++) { options.push('<option value="' + data.compradores[i]['llave'] + '">' +
                                data.compradores[i]['valor'] + '</option>');
                                }
                            $('#comprador').append(options).selectpicker('refresh');
                        }
                        });
    }
});
$("#estado_save").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
   
    var options = [];         
    var estado_save =($('#estado_save').val() == null) ? 0 : $('#estado_save').val();    
       if (estado_save != 0) {
           $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado_save: estado_save,
                            idov : $('#input_id').val()
                        },
                        url: "cxc_guardar_estado_ov",
                        success: function(data){
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
                        });
       }
        
});
function inicializatabla(){

$("#ordenes-venta").dataTable({
    language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
    "aaSorting": [],
    dom: 'T<"clear">lfrtip',
    processing: true,
    ajax: {
    url: '{!! route('datatables.cxc') !!}',
    type:'POST',
    beforeSend: function() {
         $.blockUI({
        message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento... <i class="fa fa-spin fa-spinner"></i></h3>',
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
        setTimeout($.unblockUI, 2000);
    },
    "data": function ( d ) {
    
    }
    },
    });
    }

$('#boton-mostrar').on('click', function(e) {
    e.preventDefault();

    if(true){
        reloadBuscadorOV();
    }
});
$('#boton-mostrar-OValertadas').on('click', function(e) {
    e.preventDefault();

    
    if(true){
        reloadBuscadorOValertadas();
    }
});

    
function validaMostrar(){
    if($('#cliente').val() == '' && $('#comprador').val() == ''){
        bootbox.dialog({
            title: "Filtros",
            message: "<div class='alert alert-danger m-b-0'> Ingresa Cliente o Comprador.</div>",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );
        return false;
    }
    return true;
}
function cantprovision(numclave, xpagar){
    console.log($('#cboprovdescripciones option:selected').text())
   if($('#fecha_provision').val() == '' || $('#cant').val() == '' || $('#cant').val() <=0 || $('#cboprovdescripciones option:selected').val() == ''){
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
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('getcantprovision') !!}',
        data: {
           idov : $('#input_id').val()
        },
        success: function(data){
            var cantProvisionar = data.suma + cantidadprov;
            console.log('insert : '+data.suma+'-'+ cantProvisionar)
            if( parseFloat(cantProvisionar) <= parseFloat(xpagar)){
                insertprovision();    
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
        }
    });
}
} 

function reloadBuscadorOV(){
    var registros = $('#cliente').val() == null ? 0 : $('#cliente').val().length;
        var cadena = "";
        for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#cliente option:selected')[x]).val();
            } else {
                cadena += $($('#cliente option:selected')[x]).val() + "', '";
            }
        }
        var clientes = cadena;

        var registros = $('#comprador').val() == null ? 0 : $('#comprador').val().length;
        var cadena = "";
        for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#comprador option:selected')[x]).val();
            } else {
                cadena += $($('#comprador option:selected')[x]).val() + "', '";
            }
        }
        var compradores = cadena;

    $("#ordenes-venta").DataTable().clear().draw();
        var estado =($('#estado').val() == null) ? '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5': $('#estado').val();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.cxc') !!}',
        data: {
            estado: estado,
            clientes: clientes,
            compradores: compradores,
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
            setTimeout($.unblockUI, 1500);
        },
        success: function(data){            
            if(data.ordenesVenta.length > 0){
                $("#ordenes-venta").dataTable().fnAddData(data.ordenesVenta);           
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
function reloadBuscadorOValertadas(){
    var registros = $('#cliente').val() == null ? 0 : $('#cliente').val().length;
        var cadena = "";
        for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#cliente option:selected')[x]).val();
            } else {
                cadena += $($('#cliente option:selected')[x]).val() + "', '";
            }
        }
        var clientes = cadena;

        var registros = $('#comprador').val() == null ? 0 : $('#comprador').val().length;
        var cadena = "";
        for (var x = 0; x < registros; x++) {
            if (x == registros - 1) {
                cadena += $($('#comprador option:selected')[x]).val();
            } else {
                cadena += $($('#comprador option:selected')[x]).val() + "', '";
            }
        }
        var compradores = cadena;

    $("#ordenes-venta").DataTable().clear().draw();
var estado =($('#estado').val() == null) ? '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5': $('#estado').val();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.cxc_alertadas') !!}',
        data: {
            estado: estado,
            clientes: clientes,
            compradores: compradores,
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
            setTimeout($.unblockUI, 1500);
        },
        success: function(data){   
            if(data.ordenesVenta.length > 0){
                $("#ordenes-venta").dataTable().fnAddData(data.ordenesVenta);           
            }else{
                bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>Sin registros encontrados.</div>",
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
$('#ordenes-venta tbody').on( 'click', 'a', function () {
    var rowdata = table.row( $(this).parents('tr') ).data();
    
    var cant_aux = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(rowdata['X_PAGAR']);
    var cant = cant_aux.replace(",", "");
    $.ajax({
        type: 'GET',
               
        url: '{!! route('getcantprovision') !!}',
        data: {
           idov : rowdata['CODIGO']
        },
        success: function(data){
            var cantrestante = cant - data.suma;           
            console.log('clic ov: '+ cantrestante)
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
            $('#input_id').val(rowdata['CODIGO'])
            reloadProvisiones(rowdata['CODIGO'])
            options = [];
            options.push('<option value="">Selecciona una opción</option>');
            $("#cbonumpago").empty();
            for (var i = 0; i < data.cboprovisiones.length; i++) { 
                options.push('<option value="' + data.cboprovisiones[i]['llave'] + '">' +
                data.cboprovisiones[i]['valor'] + '</option>');
            }
            $('#cbonumpago').append(options).selectpicker('refresh');                                

            $('#codigo').text('Provisionar '+rowdata['CODIGO'])
            cantrestante = parseFloat(new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(cantrestante));
            
            $('#cant').val(cantrestante) 
            $('#cant_tabla').val(cantrestante)                 
            $('#cant').attr('max', cantrestante)
            console.log(data.estado_save)
            $('#estado_save').val(data.estado_save).selectpicker('refresh');
            $('#edit').modal('show');
        }
    });
   
});

$('#table-alertas tbody').on( 'click', 'a', function (event) {
    var rowdata = table_alertas.row( $(this).parents('tr') ).data();
    console.log(event.currentTarget.id)
    if(event.currentTarget.id+'' == 'btneliminaralerta'){
        $('#id_delete_alert').val(rowdata['ALERT_Id']);
        var evidencia = $('#id_delete_alert').val();
        
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
            $('#delete_alert').modal('show');   
        }
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

$('#btn-provisionar').on('click', function(e) {
    e.preventDefault();
    var numclave = $('#input_id').val();
    var xpagar = $('#cant_tabla').val();
    console.log('clic provisionar :'+ xpagar)
    cantprovision(numclave, xpagar);   
});
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
        reloadProvisiones($('#input_id').val());
        setTimeout($.unblockUI, 1500);
    },
    success: function(data){   
       $('#editalert').modal('hide');
    }
    });
    
});

$('#btn-delete-alert').on('click', function(e) { 
   $.ajax({
        type: 'GET',       
        url: '{!! route('borra-alerta') !!}',
        data: {    
           idalerta: $('#id_delete_alert').val(),
           evidencia: $('#textarea_delete').val(),           
        },
        success: function(data){
          reloadProvisiones($('#input_id').val());
          $('#delete_alert').modal('hide');
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
    descripcion : $('#cboprovdescripciones option:selected').text()
    },
    url: '{!! route('cxc_store_provision') !!}',
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
    var nuevaCant =parseFloat($('#cant_tabla').val()) - parseFloat($('#cant').val());
    nuevaCant = parseFloat(new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(nuevaCant));
    
    $('#cant').val(nuevaCant);
    $('#cant_tabla').val(nuevaCant)
    $('#cant').attr('max', nuevaCant)
    reloadProvisiones($('#input_id').val());
    reloadBuscadorOV();
    reloadComboProvisiones();
    }
    });
}
function reloadComboProvisiones(){
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('getcantprovision') !!}',
        data: {
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
    reloadProvisiones($('#input_id').val());
    }
    });
}
function reloadProvisiones(numclave){
        $("#table-provisiones").DataTable().clear().draw();
        $("#table-alertas").DataTable().clear().draw();

    $.ajax({
        type: 'GET',      
        url: '{!! route('datatables.cxc_provisiones') !!}',
        data: {
           idov : $('#input_id').val()
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
}
                    function mostrar(){
                                            $("#hiddendiv").show();
                                            $("#hiddendiv2").show();
                                        };
                                      
                                                                                                          
                </script>
