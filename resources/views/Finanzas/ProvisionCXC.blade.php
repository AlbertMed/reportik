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
                .dataTables_filter {
                display: none;
                }
                div.dt-buttons {
                // float: right;
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
                                                                <font size="2">Estado (Activo / Eliminado)</font>
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
                                        <th># Provisión</th>
                                        <th>Fecha Pago</th>
                                        <th>Cantidad Pago</th>                                                                                
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
                                        <th>Eliminar</th>
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

@endsection

@section('homescript')
    var xhrBuscador = null;
    $('#cliente').selectpicker({
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
        
        columns: [
        {data: "PCXC_ID"},
        {data: "PCXC_Fecha", 
            render: function(data){
                if (data === null){return data;}
                var d = new Date(data);
                return moment(d).format("DD-MM-YYYY");
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
            {data: "ELIMINAR", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            $(nTd).html("<a id='btneliminaralerta' role='button' class='btn btn-danger'><i class='fa fa-trash'></i></a>");
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
{{-- yadcf.init(table,
            [
           
            {
                column_number : [6],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },
            {
                column_number : [7],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },
            {
                column_number : [8],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },
            {
                column_number : [9],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },
            {
                column_number : [10],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },
            {
                column_number : [11],
                filter_type: 'range_number',
                filter_default_label: ["Min", "Max"]
            },                      
            
            ],
            ); --}}

$('#estado').selectpicker({
noneSelectedText: 'Selecciona una opción',
});

                      var options = [];         
        $.ajax({
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: { "_token": "{{ csrf_token() }}",
                            estado: 0
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
                        var opciones = [ 
                        { 'llave': '0', 'valor': 'Activo' },
                        { 'llave': '1', 'valor': 'Eliminado' },
                        ];
                        for (var i = 0; i < opciones.length; i++) { 
                            options_edo.push('<option value="' + opciones[i]['llave'] + '">' +
                            opciones[i]['valor'] + '</option>');
                            }
                        $('#estado').append(options_edo).selectpicker('refresh');
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
                        }
                        });

$("#estado").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
   
    var options = [];         
    var estado =($('#estado').val() == null) ? 0 : $('#estado').val();    
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
    var estado =($('#estado').val() == null) ? 0 : $('#estado').val();   
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
        {{-- $.blockUI({
        message: '<h1>Su petición esta siendo procesada,</h1>
        <h3>por favor espere un momento... <i class="fa fa-spin fa-spinner"></i></h3>',
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
        });--}}
    },
    complete: function() {
        {{-- setTimeout($.unblockUI, 2000); --}}
    },
    "data": function ( d ) {
    
    }
    },
    });
    }{{--FIN INICIALIZA TABLA--}}

$('#boton-mostrar').on('click', function(e) {
    e.preventDefault();

    {{-- if(validaMostrar()){ --}}
    if(true){
        reloadBuscadorOV();
    }
});
$('#boton-mostrar-OValertadas').on('click', function(e) {
    e.preventDefault();

    {{-- if(validaMostrar()){ --}}
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
   if($('#fecha_provision').val() == '' || $('#cant').val() == '' || $('#cboprovdescripciones option:selected').val() == ''){
        bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'> Campos incompletos.</div>",
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
            console.log('insert : '+data.suma+'-'+data.suma + cantidadprov)
            if((data.suma + cantidadprov) <= xpagar){
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

    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.cxc') !!}',
        data: {
            estado: $('#estado').val(),
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

    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('datatables.cxc_alertadas') !!}',
        data: {
            estado: $('#estado').val(),
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
           
            $('#cant').val(cantrestante) 
            $('#cant').attr('max', cant)
            $('#edit').modal('show');
        }
    });
   
});


$('#btn-provisionar').on('click', function(e) {
    e.preventDefault();
    var numclave = $('#input_id').val();
    var xpagar = $('#cant').attr('max');
    console.log('clic provisionar :'+ xpagar)
    cantprovision(numclave, xpagar);
});
$('#btn-alertar').on('click', function(e) {   
    if($('#fecha_alerta').val() == '' || $('#cbonumpago option:selected').val() == '' || $('#cboprovalertas option:selected').val() == ''){
        bootbox.dialog({
            title: "Mensaje",
            message: "<div class='alert alert-danger m-b-0'> Campos incompletos.</div>",
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

                    @endsection                                      
                <script>
                    function mostrar(){
                                            $("#hiddendiv").show();
                                            $("#hiddendiv2").show();
                                        };
                                      
                                                                                                          
                </script>
