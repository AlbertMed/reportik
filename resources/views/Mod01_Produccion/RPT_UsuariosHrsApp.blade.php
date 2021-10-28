@extends('home')

@section('homecontent')
<style>
    th,
    td {
        white-space: nowrap;
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

    table {
        table-layout: auto;
    }

    .width-full {
        margin: 5px;
    }

    .dataTables_wrapper.no-footer .dataTables_scrollBody {
        border-bottom: 1px solid #111;
        max-height: 250px;
    }


    .ignoreme {
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

    .DTFC_LeftBodyWrapper {}

    .DTFC_LeftHeadWrapper {
        display: none;
    }

    .DTFC_LeftBodyLiner {
        overflow: hidden;
        overflow-y: hidden;
    }
</style>

<div class="container">
    <hr>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                REPORTE PRODUCCION APP 
                <small><b>Agrupado por Empleado</b></small>

            </h3>
          
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->

    
    <div class="col-md-12" style="margin-bottom: 5px">
       
        <div class="form-group">
            <div class="col-md-3">
                <label><strong>
                        <font size="2">Fecha Inicio</font>
                    </strong></label>
                <input type="date" id="fi" class="form-control" >
            </div>
            <div class="col-md-3">
                <label><strong>
                        <font size="2">Fecha Final</font>
                    </strong></label>
                <input type="date" id="ff" class="form-control" >
            </div>
          
            <div class="col-md-2">
                <p style="margin-bottom: 23px"></p>
                <button  type="button" class="form-control btn btn-success m-r-5 m-b-5" id="btn-mostrar"><i class="fa fa-cogs"></i> Filtrar</button>
            </div>

        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="table-responsive">
                <table id="table_empleados" class="table table-striped table-bordered nowrap" width="100%">
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
        //$("#flujoEfectivoDetalle").hide();
        document.onkeyup = function(e) {
            if (e.shiftKey && e.which == 112) {
                var namefile = 'RG_' + $('#btn_pdf').attr('ayudapdf') + '.pdf';
                console.log(namefile)
                $.ajax({
                    url: "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile,
                    type: 'HEAD',
                    error: function() {
                        //file not exists
                        window.open("{{ URL::asset('ayudas_pdf') }}" + "/AY_00.pdf", "_blank");
                    },
                    success: function() {
                        //file exists
                        var pathfile = "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile;
                        window.open(pathfile, "_blank");
                    }
                });


            }
        }
        var today = moment().format('YYYY-MM-DD');
        $('#fi').val(today);
        $('#ff').val(today);
        
        $(window).on('load', function() {
            var xhrBuscador = null;

            var data,
                tableName = '#table_empleados',
                tableproy,
                str, strfoot, contth,
                jqxhr = $.ajax({
                    //cache: false,
                    async: false,
                    dataType: 'json',
                    type: 'GET',
                    data: {
                       fi: $('#fi').val(),
                       ff: $('#ff').val()
                       
                    },
                    url: '{!! route('datatables_empleados_app') !!}',
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
                    success: function(data, textStatus, jqXHR) {
                        createTable(jqXHR, data);
                    },

                    complete: function() {
                        setTimeout($.unblockUI, 1500);
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
            $('#btn-mostrar').off().on( 'click', function (e)
            {
                $(tableName).DataTable().clear().draw();
                reloadTable();
            });    
            function createTable(jqXHR, data) {
                data = JSON.parse(jqXHR.responseText);
                // Iterate each column and print table headers for Datatables
                contth = 1;
                $.each(data.columns, function(k, colObj) {
                    if (contth <= 2) {
                        str = '<th class="segundoth">' + colObj.name + '</th>';
                        strfoot = '<th class="segundoth"></th>';
                    } else {
                        str = '<th>' + colObj.name + '</th>';
                        strfoot = '<th></th>';
                    }
                    contth++;
                    $(str).appendTo(tableName + '>thead>tr');
                    $(strfoot).appendTo(tableName + '>tfoot>tr');
                    console.log("adding col " + colObj.name);
                });

               

                table_cxp = $(tableName).DataTable({
                    "order": [[1, "asc"], [ 6, "asc" ], [3, "asc"]],
                    deferRender: true,
                    "lengthMenu": [[100, 50, 25, -1], [100, 50, 25, "Todo"]],
                   
                    dom: 'lfrtip',
                    scrollX: true,
                    scrollCollapse: true,
                    scrollY: "200px",
                    fixedColumns: false,
                    processing: true,
                    columns: data.columns,
                    data: data.data,
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [{
                            "targets": 0,
                            "visible": true
                        },

                    ],

                });

            }
            function reloadTable(){
           
            $.ajax({
            type: 'GET',
            async: true,
            data: {
                fi: $('#fi').val(),
                ff: $('#ff').val()            
            },
            url: '{!! route('datatables_empleados_app') !!}',
            beforeSend: function() {
            $.blockUI({
                message: '<h1>Actualizando tabla...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            console.log(data)
            
                    if(data.data.length > 0){
                    $(tableName).dataTable().fnAddData(data.data);
                    }else{
                
                    }
                
                
            }
            });
            }   

        }); //fin on load

    } //fin js_iniciador               
</script>