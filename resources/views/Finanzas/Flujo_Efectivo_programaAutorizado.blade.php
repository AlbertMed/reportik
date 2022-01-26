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
            <a href="{{ url('home/FINANZAS/flujoefectivo-programas') }}" class="btn btn-primary">Atras</a>
            <a id="btn_download_layout" class="btn btn-success" href="{{url('home/FINANZAS/generaLayout/'.$programa->PPCXP_Codigo)}}"><i class="fa fa-download"></i> Layout</a>
            <a class="btn btn-success" href="<?php $_SERVER['PHP_SELF']; ?>"><i class="fa fa-refresh"></i> Recargar</a>
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
      //  $(window).on('load', function() {
            var PRECIOS_DECIMALES = 2;
            var wrapper = $('#page-wrapper2');
                var resizeStartHeight = wrapper.height();
                var height = (resizeStartHeight *75)/100;
                if ( height < 200 ) {
                    height = 200;
                }
                console.log('height_datatable' + height)
            $("#tableProgramaAutorizado").DataTable({

                language: {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },       
                deferRender: true,
                dom: 'lrfti',
                paging: true,
                "scrollX": true,
                scrollY: height ,
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
                        data: "BANCO_RECEPTOR"
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
                   {
                        "targets": [ 4 ],
                        "searchable": false,
                        "orderable": false,
                        'className': "dt-body-center",
                        "render": function ( data, type, row ) {

                            return '$ ' + number_format(row['IMPORTE'],PRECIOS_DECIMALES,'.',',');

                        }
                    },
                ],
                "rowCallback": function( row, data, index ) {
                    var txt_mensaje = '';
                    if ( data['CTA_CARGO'] == null || data['CTA_CARGO'] == ''
                        || data['CTA_ABONO'] == null || data['CTA_ABONO'] == ''
                        || data['BANCO_RECEPTOR'] == null || data['BANCO_RECEPTOR'] == ''
                        || data['BENEFICIARIO'] == null || data['BENEFICIARIO'] == ''
                        || data['IMPORTE'] == null || data['IMPORTE'] == ''
                        || data['CONCEPTO'] == null || data['CONCEPTO'] == ''
                        || data['RFC'] == null || data['RFC'] == ''
                        || data['IVA'] == null || data['IVA'] == ''
                        )
                        {
                        
                        //    || data['EMAIL'] == null || data['EMAIL'] == ''
                        $('td',row).addClass("ignoreme");
                        $('#btn_download_layout').attr('disabled', true);
                        $('#btn_download_layout').removeAttr( 'href' );
                    }
                    if ( data['CTA_CARGO'] == null || data['CTA_CARGO'] == '')
                    {
                        txt_mensaje += ', Cuenta Cargo ';
                    }
                    if ( data['CTA_ABONO'] == null || data['CTA_ABONO'] == '')
                    {
                        txt_mensaje += ', Cuenta Abono ';
                    }
                    if ( data['BANCO_RECEPTOR'] == null || data['BANCO_RECEPTOR'] == '')
                    {
                        txt_mensaje += ',Banco Receptor ';
                    }
                    if ( data['BENEFICIARIO'] == null || data['BENEFICIARIO'] == '')
                    {
                        txt_mensaje += ',Beneficiario ';
                    }
                    if ( data['IMPORTE'] == null || data['IMPORTE'] == '')
                    {
                        txt_mensaje += ',Error en Importe ';
                    }
                    if ( data['CONCEPTO'] == null || data['CONCEPTO'] == '')
                    {
                        txt_mensaje += ',Definir Concepto ';
                    }
                    if ( data['RFC'] == null || data['RFC'] == '')
                    {
                        txt_mensaje += ',RFC ';
                    }
                    if ( data['IVA'] == null || data['IVA'] == '')
                    {
                        txt_mensaje += ',IVA ';
                    }

                    $(row).attr({
                            'data-toggle': 'tooltip',
                            'data-placement': 'right',
                            'title': 'Campo(s) Faltante(s): '+ txt_mensaje.slice(1)+'.',
                            'container': 'body'
                    });
                },                  
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

          

       // }); //fin on load

    } //fin js_iniciador               
</script>