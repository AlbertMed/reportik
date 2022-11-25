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
    .big-col {
        width: 250px !important;
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
            <a onclick="cargando()" href="{{ url('home/FINANZAS/flujoefectivo-programas') }}" class="btn btn-primary">Atras</a>
            <a id="btn_download_layout" class="btn btn-success"><i class="fa fa-download"></i> Layout</a>
            
            <a class="btn btn-success" href="<?php $_SERVER['PHP_SELF']; ?>"><i class="fa fa-refresh"></i> Recargar</a>
        </div>
        <hr>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table id="tableProgramaAutorizado" 
                class="table table-striped table-bordered nowrap" width="100%" style="min-width: 100%">
                    <thead>
                        <tr>
                            <th>Cta. Cargo</th>
                            <th>Cta. De Abono </th>
                            <th>Banco Receptor</th>
                            <th>Beneficiario</th>
                            <th>Importe</th>
                            <th class="big-col">Concepto</th>
                            <th>RFC</th>
                            <th>IVA</th>
                            <th>Email Beneficiario</th>
                            <!-- -->
                            <th>MISMO_BANCO</th>
                            <th>BANCO_CLAVE</th>
                            <th>SUCURSAL_BANCO</th>
                            <th>PLAZA_BANXICO</th>
                            <th>EDO_CTA_FISCAL</th>
                            <th>REF_ORDENANTE</th>
                            <th>FORMA_APLICACION</th>
                            <th>FECHA_APLICACION</th>
                            
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
            $("#page-wrapper2").toggleClass("content");
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
        //$(window).on('load', function() {
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
                    //marcados con * son necesarios para layout
                    {                        
                        data: "CTA_CARGO"//*
                    },
                    {
                        data: "CTA_ABONO"//*
                    },
                    {
                        data: "BANCO_RECEPTOR"
                    },
                    {
                        data: "BENEFICIARIO"//*
                    },
                    {
                        data: "IMPORTE"//*
                    },
                    {
                        data: "CONCEPTO"//*
                    },
                    {
                        data: "RFC"//*
                    },
                    {
                        data: "IVA"//*
                    },
                    {
                        data: "EMAIL"//*
                    },
                   /////////////
                    {
                        data: "MISMO_BANCO"//*9
                    },
                    {
                        data: "BANCO_CLAVE"//*10
                    },
                    {
                        data: "SUCURSAL_BANCO"//*11
                    },
                    {
                        data: "PLAZA_BANXICO"//*12
                    },
                    {
                        data: "EDO_CTA_FISCAL"//*13
                    },
                    {
                        data: "REF_ORDENANTE"//*14
                    },
                    {
                        data: "FORMA_APLICACION"//*15
                    },
                    {
                        data: "FECHA_APLICACION"//*16
                    },
                    

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
                    {

                        "targets": [ 5 ],
                        "searchable": false,
                        "orderable": false,
                        'className': "dt-body-center",
                        "render": function ( data, type, row ) {

                        

                                return '<input id= "concepto" class="form-control input-sm big-col" value="' + row['CONCEPTO'] + '" type="text" maxlength="39" minlength="2">'

                        
                        }

                    },
                    {
                        "targets": [ 7 ],
                        "searchable": false,
                        "orderable": false,
                        'className': "dt-body-center",
                        "render": function ( data, type, row ) {
                            return '<input id="input_iva" class="form-control input-sm big-col" value="' + row['IVA'] + '" type="number">'
                            //return '$ ' + number_format(row['IVA'],PRECIOS_DECIMALES,'.',',');

                        }
                    },
                    {
                        "targets": [ 9 ],
                        "visible": false
                    },
                    {
                        "targets": [ 10 ],
                        "visible": false
                    },
                    {
                        "targets": [ 11 ],
                        "visible": false
                    },
                    {
                        "targets": [ 12 ],
                        "visible": false
                    },
                    {
                        "targets": [ 13 ],
                        "visible": false
                    },
                    {
                        "targets": [ 14 ],
                        "visible": false
                    },
                    {
                        "targets": [ 15 ],
                        "visible": false
                    },
                    {
                        "targets": [ 16 ],
                        "visible": false
                    }
                    
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
                        //|| data['IVA'] == null //|| data['IVA'] == ''
                        )
                        {
                        
                        //    || data['EMAIL'] == null || data['EMAIL'] == ''
                        $('td',row).addClass("ignoreme");
                        $('#btn_download_layout').attr('disabled', true);
                        $('#btn_download_layout').prop('disabled', true);
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
                       // txt_mensaje += ',IVA ';
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

            function getTblProgramaAutorizado(){
                
                var tabla = $('#tableProgramaAutorizado').DataTable();
                var fila = $('#tableProgramaAutorizado tbody tr').length;
                var datos_Tabla = tabla.rows().data();
                var TblProgramaAutorizado = new Array();

                if (datos_Tabla.length != 0){

                    var siguiente = 0;
                    let varconcepto = '';
                    for (var i = 0; i < fila; i++) {

                        //if(datos_Tabla[i]["CHECK_BOX"] == 1){
                            varconcepto = $('input#concepto', tabla.row(i).node()).val();
                            variva = $('input#input_iva', tabla.row(i).node()).val();
                            if(varconcepto.length == 0 || varconcepto.length > 40){
                                
                                return TblProgramaAutorizado[0]={'mensaje':'Concepto no válido, donde RFC='+datos_Tabla[i]["RFC"]}
                            }
                            TblProgramaAutorizado[siguiente]={
                                //validacion de longitud a cadena concepto <=39 && > 0
                                
                                
                                //,"montofactura" : $('input#saldoFacturaPesos', tabla.row(i).node()).val()
                                "SUCURSAL_BANCO": datos_Tabla[i]["SUCURSAL_BANCO"]
                                ,"PLAZA_BANXICO": datos_Tabla[i]["PLAZA_BANXICO"]
                                ,"EDO_CTA_FISCAL": datos_Tabla[i]["EDO_CTA_FISCAL"]
                                ,"REF_ORDENANTE": datos_Tabla[i]["REF_ORDENANTE"]
                                ,"FORMA_APLICACION": datos_Tabla[i]["FORMA_APLICACION"]
                                
                                ,"FECHA_APLICACION": datos_Tabla[i]["FECHA_APLICACION"]
                                ,"MISMO_BANCO": datos_Tabla[i]["MISMO_BANCO"]
                                ,"BANCO_CLAVE": datos_Tabla[i]["BANCO_CLAVE"]
                                ,"CTA_CARGO": datos_Tabla[i]["CTA_CARGO"]
                                ,"CTA_ABONO": datos_Tabla[i]["CTA_ABONO"]
                                
                                ,"BENEFICIARIO": datos_Tabla[i]["BENEFICIARIO"]
                                ,"IMPORTE": datos_Tabla[i]["IMPORTE"]
                                ,"CONCEPTO": varconcepto
                                ,"RFC": datos_Tabla[i]["RFC"]
                                //,"IVA": datos_Tabla[i]["IVA"]
                                ,"IVA": variva
                                
                                ,"EMAIL": datos_Tabla[i]["EMAIL"]
                                ///+"BANCO_RECEPTOR": "BANAMEX"
                                ///+"BANCO_CARGO": "SANTANDER"
                            }
                            
                            siguiente++;

                        //}

                    }
                    return TblProgramaAutorizado;

                }
                else{

                    return TblProgramaAutorizado;

                }

            }
          $('#btn_download_layout').on( 'click', function (e) 
            {
                
                var datosTblProgramaAutorizado;
                datosTblProgramaAutorizado = getTblProgramaAutorizado();
                console.log(datosTblProgramaAutorizado)

               // if(true){
                if(!(datosTblProgramaAutorizado.mensaje === undefined)){
                    bootbox.dialog({
                    title: "Mensaje",
                    message: "<div class='alert alert-danger m-b-0'> "+datosTblProgramaAutorizado.mensaje+".</div>",
                    buttons: {
                    success: {
                        label: "Ok",
                        className: "btn-success m-r-5 m-b-5"
                    }
                    }
                    }).find('.modal-content').css({'font-size': '14px'} );
                }else{

                
                    datosTblProgramaAutorizado = JSON.stringify(datosTblProgramaAutorizado);
                    
                    $.ajax({
                        url: routeapp + 'home/reporte/ajaxtosession/'+"{{$programa->PPCXP_Codigo}}",
                        //url: "{{url('home/FINANZAS/generaLayout/'.$programa->PPCXP_Codigo)}}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "arr": datosTblProgramaAutorizado,
                           
                        },
                        type: "POST",
                      
                        beforeSend: function() {
                            $.blockUI({
                                message: '<h1>Generando...</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                            window.location.href = "{{url('home/FINANZAS/generaLayout/'.$programa->PPCXP_Codigo)}}";
                        }
                    });
                }
            });

        //}); //fin on load

    } //fin js_iniciador               
</script>