@extends('home')

@section('homecontent')
<style>
    th, td { white-space: nowrap; }
    .btn{
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
    font-family: 'Helvetica';
    font-size: 11px;
    border: 0px;
    line-height: 1;
    }
    tr:nth-of-type(odd) {
    background: white;
    }
    .row-id {
    width: 15%;
    }
    .row-nombre {
    width: 60%;
    }
    .row-movimiento {
    width: 25%;
    }
    table{
        table-layout: auto;
    }
    .width-full{
        margin: 5px;
    }
    .dataTables_wrapper.no-footer .dataTables_scrollBody {
    border-bottom: 1px solid #111;
    max-height: 250px;
    }
    .dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
    visibility: visible;
    }
    .ignoreme{
        background-color: hsla(0, 100%, 46%, 0.10) !important;       
    }
    .dataTables_scrollHeadInner th:first-child {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 5;
    }
    
    .segundoth {
    position: -webkit-sticky;
    position: sticky !important;
    left: 0px;
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
   
</style>

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Añadir Cuentas a Presupuesto
                <small>Sociedad: <b>{{$sociedad}}</b> </small>
            
            </h3>                                        
        </div>
            
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
        </div> <!-- /.row -->      
       
        <div class="row">
            <div class="form-group">
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <label class="control-label">Ejercicio:</label>
                    <input type="text" name="date" id="periodo" 
                    class="form-control" autocomplete="off" >
                                           
                    
                </div>
                
                <div class="col-md-4 col-sm-6 col-xs-6">
                    <a  style="margin-top:24px" class="btn btn-success btn-sm" id="btn_guardar"><i class="fa fa-save"></i>
                    Guardar Ejercicio</a>   
                    <a  style="margin-top:24px" class="btn btn-success btn-sm" href="{{url('home/PRESUPUESTOS/presupuesto_agregar_cta/')}}"><i class="fa fa-plus"></i>
                    Capturar Periodos</a>
                    <a style="margin-top:24px" class="btn btn-success btn-sm" id="btn_add_cta"><i class="fa fa-plus"></i>
                        Alta de Cuenta</a>                 
                </div>
            </div>
                
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="table_ctas" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Añadir</th>
                                <th>Cuenta</th>
                                <th>Descripción</th>
                                <th>Grupo</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
<div class="modal fade" id="modal_add_cta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Agregar Cuenta<codigo id='text_categoria'></codigo>
                </h4>
            </div>
            <div class="modal-body" style='padding:16px'>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="text_cuenta_codigo"># Cuenta</label>
                            <input autocomplete="off" type="text" id="text_cuenta_codigo" class="form-control">

                            <br>
                            <label for="text_cuenta_descripcion">Descripción</label>
                            <input autocomplete="off" type="text" id="text_cuenta_descripcion" class="form-control">
                            <br>  
                            <label class="control-label">Reporte Gerencial:</label>
                            <select name="sel_hojas_reporte" id="sel_hojas_reporte" class="boot-select form-control" data-style="btn-success btn-sm"
                                data-live-search="true" title="Selecciona...">
                            
                                @foreach ($hojas_reporte as $val)
                                <option value="{{old('sel_hojas_reporte',$val->k)}}">
                                    <span>{{$val->d}}</span>
                                </option>
                                @endforeach
                            </select>
                            <br>
                            <label class="control-label">Título Reporte:</label>
                            <select name="sel_titulos" id="sel_titulos" class="boot-select form-control" data-style="btn-success btn-sm"
                                data-live-search="true" title="Selecciona...">
                            
                               
                            </select> 
                            <br>
                            <label for="text_catalogo">Catálogo Cuentas</label>
                            <input type="number" id="text_catalogo" class="form-control">
                            <br>
                            <label for="text_multiplicador">Multiplicador</label>
                            <input type="number" id="text_multiplicador" class="form-control"> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <a id='btn_guarda_cta' class="btn btn-success">Agregar Cuenta</a>
            </div>

        </div>
    </div>
</div>
</div> <!-- /.container -->  

@endsection

<script>
function js_iniciador() {
    $('.toggle').bootstrapSwitch();
    $('[data-toggle="tooltip"]').tooltip();
    $('.boot-select').selectpicker();
    $('.dropdown-toggle').dropdown();
    setTimeout(function() {
    $('#infoMessage').fadeOut('fast');
    }, 5000); // <-- time in milliseconds
    $("#sidebarCollapse").on("click", function() {
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper").toggleClass("content"); 
        $(this).toggleClass("active"); 
    });
    ignore_blockUI = true;
    //$("#flujoEfectivoDetalle").hide();
    document.onkeyup = function(e) {
        if (e.shiftKey && e.which == 112) {
        var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
        console.log(namefile)
        $.ajax({
        url:"{{ URL::asset('ayudas_pdf') }}"+"/"+namefile,
        type:'HEAD',
        error: function()
        {
            //file not exists
            window.open("{{ URL::asset('ayudas_pdf') }}"+"/AY_00.pdf","_blank");
        },
        success: function()
        {
            //file exists
            var pathfile = "{{ URL::asset('ayudas_pdf') }}"+"/"+namefile;
            window.open(pathfile,"_blank");
        }
        });

        
        }
    } 
    var date_input=$('input[name="date"]'); 

    date_input.datepicker( {
        language: "es",    
        autoclose: true,
        format: "yyyy",
        startView: "years",
            dropupAuto: false,
        minViewMode: "years"
    });     
    var data, tableName = '#table_ctas', table_ctas;
    
    $(window).on('load', function() {
        
        var xhrBuscador = null;
        var wrapper = $('#page-wrapper2');
        var resizeStartHeight = wrapper.height();
        var height = (resizeStartHeight * 65)/100;
        if ( height < 200 ) {   
            height = 200;
        }
        createTable();
        $('#table_ctas thead tr').clone(true).appendTo('#table_ctas thead');

        $('#table_ctas thead tr:eq(1) th').each(function (i) {
            var title = $(this).text();
            if (title != 'Añadir') {                
                $(this).html('<input style="color: black;" type="text" placeholder="Filtro ' + title + '" />');
                $('input', this).on('keyup change', function () {

                if (table_ctas.column(i).search() !== this.value) {
                    table_ctas
                        .column(i)
                        .search(this.value, true, false)
                        .draw();
                }
            });
            } else{
                $(this).html('');
            }
        });
        console.log('height_datatable' + height)
        console.log('wrapp' + wrapper)
        console.log('resizeStartHeight' + resizeStartHeight)
        console.log('(resizeStartHeight *70)/100' + resizeStartHeight *75)
        function createTable(){
            table_ctas = $(tableName).DataTable({
                deferRender: true,
                "paging": true,
                dom: 'lrftip',
                "pageLength": 100,
                "lengthMenu": [[100, 50, 25, 10, -1], [100, 50, 25, 10, "Todo"]],
                scrollX: true,
                scrollY: height,
                
                ajax: {
                    url: '{!! route('datatables_ctas_conf') !!}',
                    data: function (d) {
                        d.ejercicio = $('#periodo').val()         
                    }              
                },
                processing: true,
                columns: [   
                    {"data" : "CHECKBOX"},
                    {"data" : "RGC_BC_Cuenta_Id"},
                    {"data" : "RGC_descripcion_cuenta"},
                    {"data" : "Reporte"}
                    
                ],
                "rowCallback": function( row, data, index ) {
                    if (data['CHECKBOX'] == 1 )
                    {
                        $('input#selectCheck', row).prop('checked', true);
                        $('input#saldoFacturaPesos',row).prop('disabled', true);
                        data['CHECKBOX'] = 1;

                    }
                },
                "language": {
                    "url": "{{ asset('assets/lang/Spanish.json') }}",
                },
                columnDefs: [
                    {

                        "targets": [ 0 ],
                        "searchable": false,
                        "orderable": true,
                        "orderDataType": 'dom-checkbox',
                        'className': "dt-body-center",
                        "render": function ( data, type, row ) {

                            

                                return '<input type="checkbox" id="selectCheck" class="editor-active">';

                        
                        }

                    },
                    
                ],
                "initComplete": function(settings, json) {
                    ignore_blockUI = false;
                }
            });
        }
        function getdatosTablaCtas(){

            var tabla = $('#table_ctas').DataTable();
            var fila = $('#table_ctas tbody tr').length;
            var datos_Tabla = tabla.rows().data();
            var tbl = new Array();
   
            if (datos_Tabla.length != 0){

                var siguiente = 0;
                for (var i = 0; i < fila; i++) {
                    tbl[siguiente]={

                        "CHECKBOX" : datos_Tabla[i]["CHECKBOX"]
                        ,"RGC_BC_Cuenta_Id" : datos_Tabla[i]["RGC_BC_Cuenta_Id"]
                        ,"RGC_descripcion_cuenta" : datos_Tabla[i]["RGC_descripcion_cuenta"]
                        ,"Reporte" : datos_Tabla[i]["Reporte"]

                    }
                    //montoTotalPrograma = montoTotalPrograma + parseFloat($('input#saldoFacturaPesos', tabla.row(i).node()).val());
                    siguiente++;
                }
                return tbl;

            }
            else{

                return tbl;

            }

        }
        $('#table_ctas').on( 'change', 'input#selectCheck', function (e) {
            e.preventDefault();

            var tbl = $('#table_ctas').DataTable();
            var fila = $(this).closest('tr');
            var datos = tbl.row(fila).data();
            //var check = datos['CHECKBOX'];
            var node = tbl.row(fila).node();
            datos['CHECKBOX'] = 0;
            if ($('input#selectCheck', node).is( ":checked" ) ){       
                datos['CHECKBOX'] = 1;
            }
            console.log('datos',datos)
            var datos = tbl.row(fila).data(datos);
            //$(node).removeClass('activo');
        });
        $('input[name="date"]').change( function(e) {

            console.log(this.value)
            e.preventDefault();                
            table_ctas.ajax.reload();
            /* $.ajax({
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { 
                    "_token": "{{ csrf_token() }}",
                    'periodo' : $('#periodo').val()
                },
                url: '{!! route('reload_cbo_titulos') !!}',
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
                    options = [];
                    
                    $("#sel_titulos").empty();
                    
                    for (var i = 0; i < data.cbo_titulos.length; i++) {
                        options.push('<option value="' + data.cbo_titulos[i] + '">' +
                        data.cbo_titulos[i] + '</option>');
                    }
                        
                    $('#sel_titulos').append(options).selectpicker('refresh');
                }                                         
            }); */
        });
        $('#table_ctas').on('preXhr.dt', function (e, settings, data) {
            if(!ignore_blockUI){
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
            }            
        })
        
        $('#table_ctas').on('xhr.dt', function (e, settings, json, xhr) {
            setTimeout($.unblockUI, 1500);
        })
        $('#btn_guardar').off().on( 'click', function (e) 
        {
            var datosTablaCtas;
            datosTablaCtas = getdatosTablaCtas();
            datosTablaCtas = JSON.stringify(datosTablaCtas);
            //console.log(datosTablaCtas);
                $.ajax({
                    url: "{{route('guardar_ctas_ejercicio')}}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        "datosTablaCtas": datosTablaCtas,
                        "ejercicio" : $('#periodo').val()    
                    },
                    type: "POST",
                    beforeSend: function() {
                        $.blockUI({
                            message: '<h1>Añadiendo Cuentas,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    success: function (datos, x, z) {
                        if(datos["Status"] == "Error"){
                            bootbox.dialog({
                                title: "Error",
                                message: "<div class='alert alert-danger m-b-0'>"+datos["Mensaje"],
                                buttons: {
                                    success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                    }
                                }
                            }).find('.modal-content').css({'font-size': '14px'} );

                        }
                        else{
                            bootbox.dialog({
                                title: "Mensaje",
                                message: "<div class='alert alert-success m-b-0'>Cuentas registradas",
                                buttons: {
                                    success: {
                                        label: "Ok",
                                        className: "btn-success m-r-5 m-b-5"
                                    }
                                }
                            }).find('.modal-content').css({'font-size': '14px'} );
                            
                            //$('#tableProgramas').DataTable().ajax.reload();
                            

                        }
                    },
                    error: function (x, e) {
                        var errorMessage = 'Error \n' + x.responseText;
                        
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>"+errorMessage,
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({'font-size': '14px'} );
                            
                    }
                });

        });
        $('#btn_add_cta').off().on('click', function(e){
            $("#sel_hojas_reporte").val('default');
            $("#sel_hojas_reporte").selectpicker("refresh");
            $('#text_catalogo').val('0');
            $('#text_multiplicador').val('1');
            $('#text_cuenta_codigo').attr('autofocus', 'true');

            $('#modal_add_cta').modal('show');
        });
        $('#btn_guarda_cta').off().on('click', function(e){
           if($('#text_cuenta_codigo').val() == ''){
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Cuenta no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );

            } else if ($('#text_cuenta_descripcion').val() == ''){
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Descripción no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            } else if ($('#sel_hojas_reporte').val() == '' || $('#sel_hojas_reporte').val() === null ){
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Reporte Gerencial no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            } else if ($('#sel_titulos').val() == ''){
                console.log($('#sel_hojas_reporte').val() || $('#sel_hojas_reporte').val() === null)
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Título Reporte no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            } else if ($('#text_catalogo').val() == ''){
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Catálogo no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            } else if ($('#text_multiplicador').val() == ''){
                bootbox.dialog({
                    title: "Error",
                    message: "<div class='alert alert-danger m-b-0'>El campo Multiplicador no debe estar vacío",
                    buttons: {
                        success: {
                            label: "Ok",
                            className: "btn-success m-r-5 m-b-5"
                        }
                    }
                }).find('.modal-content').css({'font-size': '14px'} );
            } else {
                let box = bootbox.confirm({
                    size: "small",
                    centerVertical: true,
                    message: "Confirmación de alta de Cuenta",
                    callback: function(result){ 
                    
                        if (result) {
                            $.ajax({
                            type: 'POST',       
                            url: '{!! route('alta_cta') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                "cuenta_codigo" : $("#text_cuenta_codigo").val()              
                                ,"cuenta_descripcion" : ($("#text_cuenta_descripcion").val()).toUpperCase()
                                ,"cuenta_hoja" : $("#sel_hojas_reporte").val()        
                                ,"cuenta_hoja_descripcion" : ($("#sel_hojas_reporte option:selected").text()).trim()
                                ,"cuenta_titulo" : $("#sel_titulos").val()              
                                ,"cuenta_catalogo" : $("#text_catalogo").val()              
                                ,"cuenta_multiplicador" : $("#text_multiplicador").val()              
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
                            success: function(datos, x, z){
                                if(datos["Status"] == "Error"){
                                    bootbox.dialog({
                                        title: "Error",
                                        message: "<div class='alert alert-danger m-b-0'>"+datos["Mensaje"],
                                        buttons: {
                                            success: {
                                                label: "Ok",
                                                className: "btn-success m-r-5 m-b-5"
                                            }
                                        }
                                    }).find('.modal-content').css({'font-size': '14px'} );

                                } else {
                                    table_ctas.ajax.reload();
                                    $('#modal_add_cta').modal('hide');
                                    bootbox.dialog({
                                        title: "Mensaje",
                                        message: "<div class='alert alert-success m-b-0'>Cuenta registrada",
                                        buttons: {
                                            success: {
                                                label: "Ok",
                                                className: "btn-success m-r-5 m-b-5"
                                            }
                                        }
                                    }).find('.modal-content').css({'font-size': '14px'} );
                                }
                            },
                            error: function (x, e) {
                                var errorMessage = 'Error \n' + x.responseText;
                                
                                bootbox.dialog({
                                    title: "Mensaje",
                                    message: "<div class='alert alert-danger m-b-0'>"+errorMessage,
                                    buttons: {
                                        success: {
                                            label: "Ok",
                                            className: "btn-success m-r-5 m-b-5"
                                        }
                                    }
                                }).find('.modal-content').css({'font-size': '14px'} );
                                    
                            }
                            });
                        }
                    }
                });
                box.css({
                    'top': '50%',
                    'margin-top': function () {
                        return -(box.height() / 2);
                    }
                    });
            }
        });
        $("#sel_hojas_reporte").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {   
            var options = [];                         
                $.ajax({
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: { "_token": "{{ csrf_token() }}",
                        hoja: $('#sel_hojas_reporte').val()
                    },
                    url: "{{route('reload_cbo_titulos_xreporte')}}",
                    success: function(data){
                        $("#sel_titulos").empty();
                        for (var i = 0; i < data.cbo.length; i++) { options.push('<option value="' + data.cbo[i]['k'] + '">' +
                            data.cbo[i]['k'] + '</option>');
                        }
                        $('#sel_titulos').append(options).selectpicker('refresh');   
                    }
                });
        });
        $(':checkbox').on('change', function(e) {

            var row = $(this).closest('tr');
            var datos = table_ctas.row(row).data();
            var hmc = row.find(':checkbox:checked').length;
            var kluj = parseInt(hmc);
            datos['CHECKBOX'] = kluj;
            table_ctas.row(row).data(datos);
            row.find('td.counter').text(kluj);
            table_ctas.row(row).invalidate('dom');
        });
        $('#table_ctas tbody').on( 'click', 'a', function (event) {
                event.preventDefault();
                var rowdata = table_ctas.row( $(this).parents('tr') ).data();

                console.log(event.currentTarget.id);
                var id = rowdata['ID'];  
                var codigo = rowdata['Arti'];  
                var codigo_surtir = rowdata['Surtir'];  
                
                if ( event.currentTarget.id+'' == 'btneliminar' ) {
                    bootbox.confirm({
                    size: "small",
                    centerVertical: true,
                    message: "Confirma para eliminar...",
                    callback: function(result){ 
                    
                        if (result) {
                            $.ajax({
                            type: 'POST',       
                            url: '{!! route('datatables_ctas_presupuesto') !!}',
                            data: {
                                "_token": "{{ csrf_token() }}",                       
                                id_mat : id                
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
                                table_ctas.ajax.reload();
                            
                            }
                            });
                        }
                    }
                    });
                     
                }else{
                    $('#material_id').val(id);
                    $("#sel_codigo").val(codigo);
                    $("#sel_codigo").selectpicker("refresh");
                    $("#sel_surtir").val(codigo_surtir);
                    $("#sel_surtir").selectpicker("refresh");
                    $('#modal_edit_material').modal('show');
                }
                
            });
});//fin on load
$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}

}  //fin js_iniciador               
</script>
