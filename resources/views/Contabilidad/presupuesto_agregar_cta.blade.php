@extends('home')

@section('homecontent')
<style>
    th,
    td {
        white-space: nowrap;
        vertical-align: middle;
    }
    table.dataTable.nowrap td {
    white-space: nowrap;
    vertical-align: middle;
    }
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
        font-family: 'Helvetica';
        font-size: 12px;
        border: 0px;
        line-height: 1;
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

    
    .DTFC_LeftHeadWrapper {
        overflow-x: hidden;
    }
    .DTFC_LeftBodyLiner{
        overflow-x: hidden;
    }
    .blue{
        background-color: #87cefad3;
    }
    .ignoreme{
                    background-color: rgba(235, 0, 0, 0.288) !important;       
    }
    .hidden {
        display: none;
    }
</style>

<div class="container">
   <br>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Agregar Cuentas a Presupuesto
                <small><b></b></small>

            </h3>
            
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->
    <div class="row">
            <div class="form-group">
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <label class="control-label">Ejercicio - Periodo:</label>
                    <input type="text" name="date" id="periodo" 
                    class="form-control" autocomplete="off" >
                                           
                    
                </div>
                <div class="col-md-4 col-sm-6 col-xs-6">
                    <label class="control-label">Grupo de Cuentas:</label>
                    <select 
                        name="sel_titulos"
                        id="sel_titulos"  
                        class="boot-select form-control" 
                        data-style="btn-success btn-sm"
                        data-live-search="true" 
                        title="Selecciona..." 
                        >
                        
                        @foreach ($cbo_bc_titulos as $titulo)
                        <option value="{{old('sel_titulos',$titulo)}}">
                            <span>{{$titulo}}</span>
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <a style="margin-top:24px" class="btn btn-success btn-sm"
                       id="guardar" ><i class="fa fa-save"></i>
                        Guardar cambios</a>
                </div>
            </div>
                
        </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="table_ctas" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                        <th>Activa</th>
                        <th>Cuenta</th>
                        <th>Descripción</th>
                        <th>Presupuesto</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_add_acabado" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregar Acabado <codigo id='text_categoria'></codigo></h4>
                </div>
                <div class="modal-body" style='padding:16px'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_codigo_acabado">Código del acabado</label>
                                
                                <select data-live-search="true" class="boot-select form-control" id="sel_codigo_acabado" 
                                    name="sel_codigo_acabado" title="Selecciona...">
                                    
                                    
                                </select>
                                
                                <br>
                                <label for="text_acabado_descripcion">Descripción del acabado</label>
                                <input type="text" id="text_acabado_descripcion" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id='btn_guarda_acabado' class="btn btn-success">Agregar</a>
                </div>
    
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_edit_material" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"> Material Acabado</h4>
                </div>

                <div class="modal-body" style='padding:16px'>
                    <input id="material_id" type="hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_codigo">Código</label>
                                <select data-live-search="true" class="boot-select form-control" id="sel_codigo" 
                                name="sel_codigo" title="Selecciona...">
                                    
                                    
                                </select>
                            </div>
                        </div>
                        <input type="button" id="check" hidden>
                    </div><!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sel_surtir">Código a Surtir</label>
                                <select data-live-search="true" class="boot-select form-control" id="sel_surtir" 
                                name="sel_surtir" title="Selecciona...">
                                   
                                   
                                </select>
                            </div>
                        </div>
                        
                    </div><!-- /.row -->                                       
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button id='btn_guarda_material'class="btn btn-primary"> Guardar</button>
                </div>
    
            </div>
        </div>
    </div>

<input type="hidden" id="insert" value="1">
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
            
            $('#btn_add_code').prop('disabled', true);
            $('#btn_del_acabado').prop('disabled', true);
         var date_input=$('input[name="date"]'); 

                        date_input.datepicker( {
                            language: "es",    
                            autoclose: true,
                            format: "yyyy-mm",
                            startView: "months",
                             dropupAuto: false,
                            minViewMode: "months"
                        });
       
            var data,
            tableName = '#table_ctas',
            table_ctas;
        $(window).on('load', function() {
            var xhrBuscador = null;
            createTable();
            var wrapper = $('#page-wrapper');
            var resizeStartHeight = wrapper.height();
            var height = (resizeStartHeight * 65)/100;
            if ( height < 200 ) {   
                height = 200;
            }
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
                        url: '{!! route('datatables_ctas_presupuesto') !!}',
                        data: function (d) {                            
                            d.periodo = $('#periodo').val(),
                            d.titulo = $('#sel_titulos').val()
                                 
                        }              
                    },
                    processing: true,
                    columns: [   
                        {"data" : "CHECKBOX"},
                        {"data" : "RGC_BC_Cuenta_Id"},
                        {"data" : "RGC_descripcion_cuenta"},
                        {"data" : "presupuesto"}
                        
                    ],
                    "rowCallback": function( row, data, index ) {
                        if (data['CHECKBOX'] == 1 )
                        {
                            $('input#selectCheck', row).prop('checked', true);
                            $('input#saldoFacturaPesos',row).prop('disabled', true);
                            data['CHECK_BOX'] = 1;

                        }
                    },
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [
                        {

                            "targets": [ 0 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {

                                

                                    return '<input type="checkbox" id="selectCheck" class="editor-active">';

                            
                            }

                        },
                        {

                            "targets": [ 3 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {

                            

                                    return '<input id= "saldoPresupuesto" style="width: 100px" class="form-control input-sm" value="' + number_format(row['presupuesto'],2,'.','') + '" type="number"  min="0">'

                            
                            }

                        },
                    ],
                    "initComplete": function(settings, json) {
                     
                    }
                });
            }
       
            $("#sel_titulos").on("changed.bs.select", 
                function(e, clickedIndex, newValue, oldValue) {
                e.preventDefault();                
                table_ctas.ajax.reload();
            });
            
           
            
                $('input[name="date"]').change( function() {

                console.log(this.value)
                $.ajax({
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
                });
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
            
$('#guardar').off().on( 'click', function (e) 
{
    var tabla = $('#table_ctas').DataTable();
    tabla.search('');
    tabla.draw();
   
    var datosTablaCtas;
    datosTablaCtas = getdatosTablaCtas();
    datosTablaCtas = JSON.stringify(datosTablaCtas);
      
         $.ajax({
            url: "{{route('guardar_presupuesto')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                "datosTablaCtas": datosTablaCtas,
                "periodo" : $('#periodo').val()    
            },
            type: "GET",
            async:false,
            beforeSend: function() {
                $.blockUI({
                    message: '<h1>Guardando Cuentas,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
        }); //fin on load
    } //fin js_iniciador               
</script>