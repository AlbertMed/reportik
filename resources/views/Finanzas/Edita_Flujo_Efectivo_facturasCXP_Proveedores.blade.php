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
    table.dataTable tr.odd { background-color: white; }
    table.dataTable tr.even { background-color: white; }
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
    .diferente_semana{
    background-color: rgba(236, 236, 236, 0.972) !important;
    }
    .resto_semana{
    background-color: rgba(200, 241, 194, 0.944) !important;
    }
</style>

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Programar Pagos
                <small><b>Flujo Efectivo</b></small>
            
            </h3>                                        
        </div>
            
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
        </div> <!-- /.row -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableBancos" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Banco</th>
                                <th>Cuenta</th>
                                <th>Moneda</th>
                                <th>Tipo de Cambio</th>
                                <th>Saldo USD </th>
                                <th>Saldo MN</th>
                               
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6" style="text-align:right">Total MN:</th>
                                <th style="text-align:right"></th>
                    
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
        </div>
        <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
        <br>
        <div class="row">
            <div class="form-group">
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">MONTO A DISPERSAR</font>
                        </strong></label>
                    <input type="text" class="form-control" id="totalSaldoDisponible" placeholder="0"
                        style="font-size: 130%; text-align: right;" size="100" value="{{number_format($programa->PPCXP_MontoDispersar, 2, '.', ',')}}"/>
                </div>
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">MONTO DEL PROGRAMA</font>
                        </strong></label>
                    <input type="text" class="form-control" id="sumCtas" placeholder="0"
                        style="font-size: 130%; text-align: right;" size="100" value="{{number_format($programa->PPCXP_Monto, 2, '.', ',')}}" disabled />
                </div>
                <div class="col-md-3">
                    <label><strong>
                            <font size="2">DIFERIENCIA PROGRAMA</font>
                        </strong></label>
                    <input type="text" class="form-control" id="diferiencia" placeholder="0"
                        style="font-size: 130%; text-align: right;" size="100" value="0" disabled />
                </div>
                
                <div class="col-md-4" style="display: none;">
                    <input type="text" class="form-control" id="input-cuenta" value="{{$programa->PPCXP_BCS_BancoCuentaId}}"
                        style="font-size: 130%; text-align: right;" size="100" disabled />
                    <input type="text" class="form-control" id="programaId" value="{{$programa->PPCXP_ProgramaId}}"
                        style="font-size: 130%; text-align: right;" size="100" disabled />
                    <input type="text" class="form-control" id="tipo_cambio" placeholder=""
                      value="1"  style="font-size: 130%; text-align: right;" size="100" disabled />
                    <input type="text" class="form-control" id="rowcall" placeholder=""
                      value="1"  style="font-size: 130%; text-align: right;" size="100" disabled />
                    <input type="text" class="form-control" id="semana_actual" placeholder="" 
                    value="{{$sem}}" style="font-size: 130%; text-align: right;" size="100" disabled />
                </div>
            </div>
        </div><br>
        <div class="row">
            <div class="form-group">
               <div class="col-md-3">
                <label><strong>
                        <font size="2">NOMBRE DEL PROGRAMA</font>
                    </strong></label>
                <input type="text" class="form-control" id="input-nombre" placeholder="" 
                style="font-size: 150%;" value="{{$programa->PPCXP_Nombre}}"/>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fPago">FECHA DE PAGO</label>
                    <input type="text" id="fPago" class='form-control' autocomplete="off"
                    value="{{$programa->PPCXP_FechaPago}}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for=""></label>
                    <button style="margin-top:20px" type="button" class=" btn btn-success m-r-5 m-b-5" id="guardar"><i class="fa fa-save"></i> Guardar
                        Programa</button>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-scroll">
                    <table id="tableFTPDCXPPesos" class="table table-striped table-bordered hover" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>MONTO PROGRAMA</th>
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
$(window).on('load',function(){            
/*GENERAR OP*/
$("#fPago").datepicker( {
    language: "es",    
    autoclose: true,
    format: "dd-mm-yyyy",  
    }).val('');
$('#fPago').datepicker('setStartDate', new Date());
$('#fPago').datepicker('setDate', new Date());
var PRECIOS_DECIMALES = 2;

$("#tableBancos").DataTable({
    language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
    searching: true,
    iDisplayLength: 5000,
    aaSorting: [],
    deferRender: true,
    dom: 'Blfrtip',
    bInfo: false,
    scrollCollapse: true,
    fixedColumns: false,
    paging: false,
    buttons: [
    ],
    columns: [

        {data: "CHECK_BOX"},
        {data: "Banco"},
        {data: "Cuenta"},
        {data: "Moneda"},
        {data: "TipoCambio"},

        {data: "SaldoDisponible"},
        {data: "SaldoDisponibleMN"},

    ],
    "columnDefs": [

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

            "targets": [ 4 ],
            "searchable": false,
            "orderable": false,
            'className': "dt-body-center",
            "render": function ( data, type, row ) {

               if( parseFloat( row['TipoCambio'] ) > 1){
                
                return '$ ' + number_format(row['TipoCambio'],PRECIOS_DECIMALES,'.',',');
                
                }
                else{
                
                return '';
                
                }
            }

        },
        {

            "targets": [ 5 ],
            "searchable": false,
            "orderable": false,
            'className': "dt-body-center",
            "render": function ( data, type, row ) {

                return '$ ' + number_format(row['SaldoDisponible'],PRECIOS_DECIMALES,'.',',');

            }

        },
        {

            "targets": [ 6 ],
            "searchable": false,
            "orderable": false,
            'className': "dt-body-center",
            "render": function ( data, type, row ) {

                return '$ ' + number_format(row['SaldoDisponibleMN'],PRECIOS_DECIMALES,'.',',');

            }

        },
       

    ],
    "rowCallback": function( row, data, index ) {   
        if (data['DT_RowId'] == $("#input-cuenta").val() && $("#rowcall").val() == 1)
        {
            $('input#selectCheck', row).prop('checked', true);
            data['CHECK_BOX'] = 1; 
            $("#tipo_cambio").val(parseInt(data['TipoCambio']));
            var saldoDisponible = parseFloat(data['SaldoDisponibleMN']);
            var sumCtas = parseFloat($("#sumCtas").val().replaceAll(',', ''));
            if ($("#totalSaldoDisponible").val() == '' || $("#totalSaldoDisponible").val() == 0) {
                $("#totalSaldoDisponible").val(number_format(saldoDisponible,PRECIOS_DECIMALES,'.',','));
            }else{
                saldoDisponible = parseFloat($("#totalSaldoDisponible").val().replaceAll(',', ''));
            }
            //console.log(saldoDisponible + ' - '+ sumCtas )
            var dif = parseFloat(saldoDisponible - sumCtas);
            $("#diferiencia").val(number_format(dif,PRECIOS_DECIMALES,'.',','));
            if (dif >= 0) {
                $('#diferiencia').css({'background-color' : 'green'});
                $('#diferiencia').css({'color': 'white'});
            } else if(dif < 0) { 
                $('#diferiencia').css({'background-color' : 'red' });
                $('#diferiencia').css({'color': 'white'}); 
            }
            reloadTableFTPDCXPPesos();
        }
    },
    tableTools: {sSwfPath: "plugins/DataTables/swf/copy_csv_xls_pdf.swf"},
    footerCallback: function ( row, data, start, end, display ) {
        var api = this.api(), data;

        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
            i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        // Total over all pages
        var totalSaldoDisponible = api
            .column( 6 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Update footer
        $( api.column( 6 ).footer() ).html(
            //'$'+pageTotal +' ( $'+ total +' total)'
            '$ ' + number_format(totalSaldoDisponible,PRECIOS_DECIMALES,'.',',')
        );

    }

});

function consultarDatosInicio(){
    $.ajax({

        type: 'GET',
        async: true,
        url: "{{url().'/home/FINANZAS/consultaDatosInicio'}}",
        /*data:{

            "fechaDesde": $('#input-fechaInicio').val(),
            "fechaHasta": $('#input-fechaFinal').val(),
            "cuentaId": cuentaId

        },*/
        beforeSend: function() {
            $.blockUI({
                message: '<h1>Actualizando tabla Bancos,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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

            $("#tableBancos").DataTable().clear().draw();

            if(JSON.parse(data.bancos).consulta != ''){

                $("#tableBancos").dataTable().fnAddData(JSON.parse(data.bancos).consulta);
            }

        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#rowcall").val(1);
            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });
}

function reloadTableFTPDCXPPesos(){
    mod_cont = 1;

    $.ajax({
    type: 'GET',
    async: true,       
    url: '{!! route('datatables.FTPDCXPPesos') !!}',
    data: {
        rowcall : $("#rowcall").val(),
        programaId : $("#programaId").val()
    },
    beforeSend: function() {
        $.blockUI({
        message: '<h1>Actualizando tabla de CXP,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
        $("#rowcall").val(0)
       //$("#programaId").val(null) 

        setTimeout($.unblockUI, 1500);
    },
    success: function(data){            
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
                $("#tableFTPDCXPPesos").dataTable().fnAddData(data.FTPDCXPPesos);
            }else{
            
            }
        }   
    }
});
}

$('#tableBancos').on( 'change', 'input#selectCheck', function (e) {

    e.preventDefault();
    console.log('check')
    var tblBancos = $('#tableBancos').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblBancos.row(fila).data();
    var check = datos['CHECK_BOX'];
    var saldoDisponible = parseFloat(datos['SaldoDisponibleMN']);
    var idBanco = datos['DT_RowId'];
    var node = tblBancos.row(fila).node();
    $(node).removeClass('activo');
    var sumCtas = parseFloat(($("#sumCtas").val()).replaceAll(',', ''));
    var dif = parseFloat(saldoDisponible - sumCtas);
    console.log(check)
    if(check == 0){
        datos['CHECK_BOX'] = 1;
        $(node).addClass('activo');

        $("#totalSaldoDisponible").val(number_format(saldoDisponible,PRECIOS_DECIMALES,'.',','));
        $("#diferiencia").val(number_format(dif,PRECIOS_DECIMALES,'.',','));

        //$('#sumCtas').css({'background-color' : 'red'});
        //$('#sumCtas').css({'color': 'white'});
        $("#input-cuenta").val(idBanco);
        //console.log('tipocambio ' + parseInt(datos['TipoCambio']))
        $("#tipo_cambio").val(parseInt(datos['TipoCambio']));
        reloadTableFTPDCXPPesos();
    } else {
        datos['CHECK_BOX'] = 0;
        $("#totalSaldoDisponible").val(0);
        $("#diferiencia").val(0);
        $("#sumCtas").val(0);
        $("#input-cuenta").val("");
    }
    if (dif >= 0) {
        $('#diferiencia').css({'background-color' : 'green'});
        $('#diferiencia').css({'color': 'white'});
    } else if(dif < 0) { 
        $('#diferiencia').css({'background-color' : 'red' });
        $('#diferiencia').css({'color': 'white'}); 
    }
    var arrayDatos = tblBancos.rows().data();
    var rows = arrayDatos.length;
    for (var x = 0; x < rows; x++) {

        var valores = tblBancos.row(x).data();
        if(valores['DT_RowId'] != idBanco){

            $('input#selectCheck', tblBancos.row(x).node()).prop('checked', false);
            valores['CHECK_BOX'] = 0;

        }

    }
    
   /* var tblCXPPesos = $('#tableFTPDCXPPesos').DataTable();
    var arrayDatos2 = tblCXPPesos.rows().data();
    var rows2 = arrayDatos2.length;
    for (var x = 0; x < rows2; x++) {

        var valores = tblCXPPesos.row(x).data();
        $('input#selectCheck', tblCXPPesos.row(x).node()).prop('checked', false);
        valores['CHECK_BOX'] = 0;


    }*/


/*
    var tblCXPDolar = $('#tableFTPDCXPDolar').DataTable();
    var arrayDatos3 = tblCXPDolar.rows().data();
    var rows3 = arrayDatos3.length;
    for (var x = 0; x < rows3; x++) {

        var valores = tblCXPDolar.row(x).data();
        $('input#selectCheck', tblCXPDolar.row(x).node()).prop('checked', false);
        valores['CHECK_BOX'] = 0;


    }
*/
});

var semana = $('#semana_actual').val();
var mod_cont = 1;
$("#tableFTPDCXPPesos").DataTable({
    language:{
    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
    },
        searching: true,
        iDisplayLength: 6,
        aaSorting: [],
        deferRender: true,
        dom: 'T<"clear">lfrtip',
        bInfo: false,
        fixedColumns: false,
        paging: false,
        "scrollX": true,
        scrollY:        "300px",
        columns: [

            {data: "CHECK_BOX"},
            {data: "montoActualTC"},
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
        "rowCallback": function( row, data, index ) {
            
            if ( $("#tipo_cambio").val() != '1' && data['MON_Nombre'] == 'Pesos')
            {
                $('td',row).addClass("ignoreme");
            }
                
            if (data['CHECKB'] == 1 && $("#rowcall").val() == 1)
            {
                $('input#selectCheck', row).prop('checked', true);
                $('input#saldoFacturaPesos',row).prop('disabled', true);
                data['CHECK_BOX'] = 1;

            }

            if (mod_cont == 1 && data['SEMANA'] < parseInt(semana)) {
                
            }else{
                if (data['SEMANA'] == parseInt(semana)) {
                
                } else {
                    mod_cont++;
                    semana = parseInt(data['SEMANA']) ;
                }
                if ( mod_cont % 2 == 1) {
                    $('td',row).addClass("diferente_semana");
                } 
            }
            
            if (data['SEMANA'] >=  (parseInt(semana)  + 5)) {
               
                    $('td',row).addClass("resto_semana");
                
            }
            
        },
        "columnDefs": [

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

                "targets": [ 1 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                  

                        return '<input id= "saldoFacturaPesos" style="width: 100px" class="form-control input-sm" value="' + number_format(row['montoActualTC'],PRECIOS_DECIMALES,'.','') + '" type="number" max="'+number_format(row['montoActualTC'],PRECIOS_DECIMALES,'.','')+'" min="0">'

                 
                }

            },
            {

                "targets": [ 2 ],
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

                "targets": [ 3 ],
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

                "targets": [ 4 ],
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

                "targets": [ 9 ],
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

                "targets": [ 10 ],
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

                "targets": [ 11 ],
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

                "targets": [ 12 ],
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

                "targets": [ 13 ],
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

                "targets": [ 14 ],
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

                "targets": [ 15 ],
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

                "targets": [ 16 ],
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

                "targets": [ 17 ],
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

                "targets": [ 18 ],
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
       
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };


            // Total over all pages
            var totalSaldo9 = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 9 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo9,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo10 = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 10 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo10,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo11 = api
                .column( 11 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 11 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo11,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo12 = api
                .column( 12 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 12 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo12,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo13 = api
                .column( 13 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 13 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo13,PRECIOS_DECIMALES,'.',',')
            );
            // Total over all pages
            var totalSaldo14 = api
                .column( 14 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 14 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo14,PRECIOS_DECIMALES,'.',',')
            );
            // Total over all pages
            var totalSaldo15 = api
                .column( 15 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 15 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo15,PRECIOS_DECIMALES,'.',',')
            );
            // Total over all pages
            var totalSaldo16 = api
                .column( 16 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 16 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo16,PRECIOS_DECIMALES,'.',',')
            );
            // Total over all pages
            var totalSaldo17 = api
                .column( 17 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 17 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo17,PRECIOS_DECIMALES,'.',',')
            );
            // Total over all pages
            var totalSaldo18 = api
                .column( 18 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 18 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo18,PRECIOS_DECIMALES,'.',',')
            );

        }
        /*"createdRow": function ( row, data, index ) {

            $('td', row).eq(8).addClass('rojo');
            $('td', row).eq(9).addClass('rojo');
            $('td', row).eq(10).addClass('verde');
            $('td', row).eq(11).addClass('azul');
            $('td', row).eq(12).addClass('azul');
            $('td', row).eq(13).addClass('azul');
            $('td', row).eq(14).addClass('azul');
            $('td', row).eq(15).addClass('azul');
            $('td', row).eq(16).addClass('azul');
            $('td', row).eq(17).addClass('azul');
            $('td', row).eq(18).addClass('azul');
            $('td', row).eq(19).addClass('azul');

        }*/

});
consultarDatosInicio();


$('#tableFTPDCXPPesos').on( 'change', 'input#selectCheck', function (e) {
    var tblCXPPesos = $('#tableFTPDCXPPesos').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblCXPPesos.row(fila).data();
    var check = datos['CHECK_BOX'];
    var node = tblCXPPesos.row(fila).node();
    //console.log('datos',datos)
    e.preventDefault();
    $(node).removeClass('activo');

    if ($("#tipo_cambio").val() != '1' && datos['MON_Nombre'] == 'Pesos') {
        console.log('option 1')
        datos['CHECK_BOX'] = 0;
        $('input#selectCheck', tblCXPPesos.row(fila).node()).prop('checked', false);
    }else{
        console.log('option 2')
        var saldoDisponible = parseFloat(($("#sumCtas").val()).replaceAll(',', ''));
        console.log('saldoDisponible(sumCtas)'+saldoDisponible)
        if(check == 0){
            var  cantInput = parseFloat($('input#saldoFacturaPesos',tblCXPPesos.row(fila).node()).val());
            //si el cantidadInput es mayor que el montoActual se asigna montoActual
            if ( cantInput > datos['montoActualTC']) {
                cantInput = parseFloat(datos['montoActualTC']);
                $('input#saldoFacturaPesos',
                tblCXPPesos.row(fila).node()).val(number_format(datos['montoActualTC'],PRECIOS_DECIMALES,'.',''));
                bootbox.dialog({
                    title: "Info",
                    message: "<div class='alert alert-info m-b-0'>El monto ingresado rebasa la cantidad permitida, se aplicó la cantidad del monto Actual de la Factura.",
                        buttons: {
                        success: {
                        label: "Ok",
                        className: "btn-success m-r-5 m-b-5"
                        }
                        }
                }).find('.modal-content').css({'font-size': '14px'} );
            }
            datos['CHECK_BOX'] = 1;
            $(node).addClass('activo');
            saldoDisponible = 
            saldoDisponible + 
            cantInput;
            $('input#saldoFacturaPesos',tblCXPPesos.row(fila).node()).prop('disabled', true);
            console.log('saldoDisponible(sumCtas)'+saldoDisponible)
            $("#sumCtas").val(number_format(saldoDisponible,PRECIOS_DECIMALES,'.',','));
        } else {
            saldoDisponible = saldoDisponible - parseFloat($('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val());
            console.log('saldoDisponible(sumCtas)'+saldoDisponible)
            if (saldoDisponible > 0) {
                $("#sumCtas").val(number_format(saldoDisponible,PRECIOS_DECIMALES,'.',','));
            }else{
                $("#sumCtas").val(0);
            }
            datos['CHECK_BOX'] = 0;
            $('input#saldoFacturaPesos',tblCXPPesos.row(fila).node()).prop('disabled', false);
            $('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val(number_format(datos['montoActualTC'],PRECIOS_DECIMALES,'.',''));

        }
        var total = parseFloat(($("#totalSaldoDisponible").val()).replaceAll(',', ''));
        //console.log(total)
        var diferiencia = total - saldoDisponible;
        $("#diferiencia").val(number_format(diferiencia,PRECIOS_DECIMALES,'.',','));
        if (diferiencia >= 0) {
            $('#diferiencia').css({'background-color' : 'green'});
            $('#diferiencia').css({'color': 'white'});
        } else if(diferiencia < 0) { 
            $('#diferiencia').css({'background-color' : 'red' });
            $('#diferiencia').css({'color': 'white'}); 
        }
    }
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
$('#guardar').off().on( 'click', function (e) 
{

    var nombre = $("#input-nombre").val();
    montoTotalPrograma = 0;

    var datosTablaCXPPesos;
    datosTablaCXPPesos = getTblCXPPesos();

    //var datosTablaCXPDolar;
   // datosTablaCXPDolar = getTblCXPDolar();

    var total = parseInt(datosTablaCXPPesos.length);// + parseInt(datosTablaCXPDolar.length);
    
    if(nombre == ''){
        bootbox.dialog({
            title: "Error",
            message: "<div class='alert alert-danger m-b-0'>Ingrese un nombre de Programa",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if(total < 1){
        bootbox.dialog({
            title: "Error",
            message: "<div class='alert alert-danger m-b-0'>Elije al menos una factura",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else if($('#fPago').val() == ''){
        bootbox.dialog({
            title: "Error",
            message: "<div class='alert alert-danger m-b-0'>Establece una fecha de Pago",
            buttons: {
                success: {
                    label: "Ok",
                    className: "btn-success m-r-5 m-b-5"
                }
            }
        }).find('.modal-content').css({'font-size': '14px'} );

    }
    else{

        datosTablaCXPPesos = JSON.stringify(datosTablaCXPPesos);
        //datosTablaCXPDolar = JSON.stringify(datosTablaCXPDolar);
        datosTablaCXPDolar = null;
        //guardaPrograma
         $.ajax({
            url: "{{url().'/home/FINANZAS/registraPrograma'}}",
            data: {
                "descripcion": $("#input-nombre").val(),
                "cuentaId": $("#input-cuenta").val(),
                "montoTotalPrograma": montoTotalPrograma,
                "montoDispersar": parseFloat(($("#totalSaldoDisponible").val()).replaceAll(',', '')),
                "TablaCXPPesos": datosTablaCXPPesos,
                "TablaCXPDolar": datosTablaCXPDolar,
                "fechapago": $('#fPago').val(),
                "programaId" : $("#programaId").val()
            },
            type: "GET",
            async:false,
            beforeSend: function() {
                $.blockUI({
                    message: '<h1>Guardando Programa,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                window.location.href = "{{url().'/home/FINANZAS/flujoefectivo-programas'}}";
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
                    consultarDatosInicio();
                    $("#tableFTPDCXPPesos").DataTable().clear().draw();
                    $("#diferiencia").val(0);
                    $("#sumCtas").val(0);
                    $("#input-nombre").val('');
                    $("#totalSaldoDisponible").val(0);
                    $('#fPago').datepicker('setDate', new Date());

                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-success m-b-0'>Programa registrado",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({'font-size': '14px'} );
                    //limpiarTodo();
                    $('#tableProgramas').DataTable().ajax.reload();
                    $("#btnBuscadorFlujoEfectivo").show();
                    $("#flujoEfectivo").hide();

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

    } //end else

});

function getTblCXPPesos(){

    var tabla = $('#tableFTPDCXPPesos').DataTable();
    var fila = $('#tableFTPDCXPPesos tbody tr').length;
    var datos_Tabla = tabla.rows().data();
    var tblCXPPesos = new Array();

    if (datos_Tabla.length != 0){

        var siguiente = 0;
        for (var i = 0; i < fila; i++) {

            if(datos_Tabla[i]["CHECK_BOX"] == 1){

                tblCXPPesos[siguiente]={

                    "facturaProveedorId" : datos_Tabla[i]["DT_RowId"]
                    ,"montofactura" : $('input#saldoFacturaPesos', tabla.row(i).node()).val()

                }
                montoTotalPrograma = montoTotalPrograma + parseFloat($('input#saldoFacturaPesos', tabla.row(i).node()).val());
                siguiente++;

            }

        }
        return tblCXPPesos;

    }
    else{

        return tblCXPPesos;

    }

}
$( "#totalSaldoDisponible" ).keyup(function() {
    
    var  saldoDisponible = parseFloat($("#totalSaldoDisponible").val().replaceAll(',', ''));
    if(isNaN(saldoDisponible)){
        saldoDisponible = 0;
    }
    $("#totalSaldoDisponible").val(saldoDisponible);
    
    //console.log(saldoDisponible)
    //console.log($( "#totalSaldoDisponible" ).val().replaceAll(',', ''))
   
    var sumCtas = parseFloat($("#sumCtas").val().replaceAll(',', ''));
    var dif = parseFloat(saldoDisponible - sumCtas);
    $("#diferiencia").val(number_format(dif,PRECIOS_DECIMALES,'.',','));
    if (dif >= 0) {
        $('#diferiencia').css({'background-color' : 'green'});
        $('#diferiencia').css({'color': 'white'});
    } else if(dif < 0) { 
        $('#diferiencia').css({'background-color' : 'red' });
        $('#diferiencia').css({'color': 'white'}); 
    }

});
function getTblCXPDolar(){

    var tabla = $('#tableFTPDCXPDolar').DataTable();
    var fila = $('#tableFTPDCXPDolar tbody tr').length;
    var datos_Tabla = tabla.rows().data();
    var tblCXPDolar = new Array();

    if (datos_Tabla.length != 0){

        var siguiente = 0;
        for (var i = 0; i < fila; i++) {

            if(datos_Tabla[i]["CHECK_BOX"] == 1){

                tblCXPDolar[siguiente]={

                    "facturaProveedorId" : datos_Tabla[i]["DT_RowId"]
                    ,"montofactura" : $('input#saldoFacturaDolar', tabla.row(i).node()).val()
                }
                montoTotalPrograma = montoTotalPrograma + parseFloat($('input#saldoFacturaDolar', tabla.row(i).node()).val());
                siguiente++;

            }

        }
        return tblCXPDolar;

    }
    else{

        return tblCXPDolar;

    }

}

});//fin on load


}  //fin js_iniciador               

</script>
