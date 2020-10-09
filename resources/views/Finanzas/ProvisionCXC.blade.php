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
                table.dataTable thead .sorting {                
                    position: sticky;
                }
                .DTFC_LeftBodyWrapper{
                margin-top: 84px;
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
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-scroll" id="registros-ordenes-venta">
                                                    <table id="ordenes-venta" class="table table-striped table-bordered" width="100%">
                                                        <thead>
                                                            <tr>
                                                
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
                                                                <th>Provisión</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                   
                                
                            </div>
                            <!-- end row -->
                                                   

                    </div>   <!-- /.container -->
<div class="modal fade" id="confirma" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pwModalLabel">Agregar</h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <div>
                        <h4>¿Desea continuar?</h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div id="hiddendiv" class="progress" style="display: none">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                        aria-valuemax="100" style="width: 100%">
                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                    </div>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="submitBtn" onclick="mostrar();" class="btn btn-primary">Guardar</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="confirma_actualiza" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pwModalLabel">Actualizar</h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <div>
                        <h4>Hay Información de ese Periodo ¿desea actualizar?</h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div id="hiddendiv2" class="progress" style="display: none">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                        aria-valuemax="100" style="width: 100%">
                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                    </div>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="submitBtn2" onclick="mostrar();" class="btn btn-primary">Guardar</button>
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
                    var table = $("#ordenes-venta").DataTable(
                    {
            language:{
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
        iDisplayLength: 10,
        aaSorting: [],
        deferRender: true,
        dom: 'T<"clear">lfrtip',       
        scrollX: true,
        fixedColumns: true,
        scrollCollapse: true,
         columns: [

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
    }},
    {data: "PROVISION"},
    
    ],}
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
        console.log(solocompradores);
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
    }

$('#boton-mostrar').on('click', function(e) {
    e.preventDefault();

    {{-- if(validaMostrar()){ --}}
    if(true){
        

        reloadBuscadorOV();
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



                        $('#boton_confirma').on('click', function (e) {
                        e.preventDefault();
                            $.ajax({
                            type: 'POST',                         
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: { "_token": "{{ csrf_token() }}", 
                            date: $('#periodo').val() },
                            url: "checkctas",
                            success: function(data){                           
                            if (data.respuesta) {
                            $('#confirma_actualiza').modal('show');
                            } else {
                            $('#confirma').modal('show');
                            }
                            }
                            });
                        });

                        $("#submitBtn").click(function(){
                        
                        $("#form_archivo").submit(); // Submit the form
                        
                        });
                        $("#submitBtn2").click(function(){
                        
                        $("#form_archivo").submit(); // Submit the form
                        
                        });

                        $("#archivo").change(function(){
                            if($('#periodo').val() !== ''){
                                $("#boton_confirma").prop("disabled", this.files.length == 0);
                            }
                        });
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
        success: function(data){
            //console.log(data.ordenesVenta);
            
                $("#ordenes-venta").dataTable().fnAddData(data.ordenesVenta);
          

            //$.unblockUI();
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
