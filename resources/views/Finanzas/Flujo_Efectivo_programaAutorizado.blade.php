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

    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
        visibility: visible;
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

<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Detalle de Programa
                <small><b>{{$programa->PPCXP_Codigo. ' - '. $programa->PPCXP_Nombre}}</b></small>

            </h3>
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->

    <div class="col-md-12">
        <div class="row">
            <a href="{{ URL::previous() }}" class="btn btn-primary">Atras</a>
            <a class="btn btn-success" href="{{url('home/FINANZAS/generaLayout')}}"><i class="fa fa-download"></i> Layout</a>

        </div>
        <hr>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table id="tableProgramaAutorizado" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Cta. Cargo</th>
                            <th>Cta. De Abono </th>
                            <th>Banco Receptor</th>
                            <th>Beneficiario</th>
                            <th>Importe</th>
                            <th>Concepto</th>
                            <th>RFC</th>
                            <th>IVA</th>
                            <th>Email Beneficiario</th>
                        </tr>
                    </thead>
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
        $(window).on('load', function() {
            var PRECIOS_DECIMALES = 2;
            
            $("#tableProgramaAutorizado").DataTable({

                language: {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },       
                deferRender: true,
                dom: 'lrtip',
                
                scrollCollapse: true,
                "pageLength": 100,
                "lengthMenu": [[100, 50, 25, -1], [100, 50, 25, "Todo"]],

                columns: [
                    {
                        data: "CTA_CARGO"
                    },
                    {
                        data: "CTA_ABONO"
                    },
                    {
                        data: "BANCO_RECPTOR"
                    },
                    {
                        data: "BENEFICIARIO"
                    },
                    {
                        data: "IMPORTE"
                    },
                    {
                        data: "CONCEPTO"
                    },
                    {
                        data: "RFC"
                    },
                    {
                        data: "IVA"
                    },
                    {
                        data: "EMAIL"
                    }

                ],
                "columnDefs": [                 
                   
                ]

            });

            function consultarDatosInicio() {
                $.ajax({

                    type: 'GET',
                    async: true,
                    url: "{{url('home/FINANZAS/datatables_programa_autorizado')}}",
                    data:{
                        "id_programa": "{{$id_programa}}"
                    },
                    beforeSend: function() {
                        $.blockUI({
                            message: '<h1>Actualizando Tabla</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    success: function(data) {
                        console.log("tabla p")
                        console.log(data.data_programa_autorizado)
                       
                        $("#tableProgramaAutorizado").DataTable().clear().draw();
                                
                        if ((data.data_programa_autorizado) != '') {
                            $("#tableProgramaAutorizado").dataTable().fnAddData(data.data_programa_autorizado);
                        }
                                  
                    },
                    error: function(xhr, ajaxOptions, thrownError) {

                        $.unblockUI();
                        var error = JSON.parse(xhr.responseText);
                        bootbox.alert({

                            size: "large",
                            title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                            message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                                (error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '') +
                                (error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '') +
                                (error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '') + '</div>'

                        });

                    }

                });
            }
            consultarDatosInicio();

            function number_format(number, decimals, dec_point, thousands_sep) {
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    toFixedFix = function(n, prec) {
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