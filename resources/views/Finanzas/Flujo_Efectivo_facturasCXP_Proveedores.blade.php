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
        <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">

        <div class="row">
            <div class="col-md-12">
                <div class="table-scroll">
                    <table id="tableFTPDCXPPesos" class="table table-striped table-bordered hover" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>MONTO PROGRAMA</th>
                                <th>PROVEEDOR</th>
                                <th>FACTURA</th>
                                <th>FECHA FACTURA</th>

                                <th>FECHA VENCIMIENTO</th>
                                <th>DIAS VENCIDOS</th>
                                <th>MONTO FACTURA</th>
                                <th>SALDO FACTURA</th>
                                <th>SIN VENCER</th>

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
var PRECIOS_DECIMALES = 2;
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
            {data: "montoActual"},
            {data: "PROVEEDOR"},
            {data: "FP_CodigoFactura"},
            {data: "FP_FechaFactura"},
            {data: "FECHA_VENCIMIENTO"},
            {data: "DiasTranscurridosVencimiento"},
            {data: "montoOriginal"},
            {data: "montoActual"},
            {data: "S0"},
            {data: "S1"},
            {data: "S2"},
            {data: "S3"},
            {data: "S4"},
            {data: "S5"},
            {data: "S6"},
            {data: "S7"}

        ],
        "columnDefs": [

            {

                "targets": [ 0 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if (row['DT_RowId'] != null){

                        return '<input type="checkbox" id="selectCheck" class="editor-active">';

                    }
                    else{

                        return '<input type="checkbox" id="selectCheck" class="editor-active" disabled>';

                    }

                }

            },
            {

                "targets": [ 1 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if (row['DT_RowId'] != null){

                        return '<input id= "saldoFacturaPesos" style="width: 100px" class="form-control input-sm precio" value="' + row['montoActual'] + '" type="number">'

                    }
                    else{

                        return '<input id= "saldoFacturaPesos" style="width: 100px" class="form-control input-sm precio" type="number" disabled>'

                    }

                }

            },
            {

                "targets": [ 7 ],
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

                "targets": [ 8 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if(row['montoActual'] != ''){

                        return '$ ' + number_format(row['montoActual'],PRECIOS_DECIMALES,'.',',');

                    }
                    else{

                        return '';

                    }

                }

            },
            /*
            {

                "targets": [ 9 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if(row['SALDO_SV'] == '' || row['SALDO_SV'] == null){

                        return '';

                    }
                    else{

                        if(!isNaN(row['SALDO_SV'].substr(0,1))){

                            return '$ ' + number_format(row['SALDO_SV'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return row['SALDO_SV'];

                        }

                    }

                }

            },
            {

                "targets": [ 10 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if(row['SALDO_R1'] == '' || row['SALDO_R1'] == null){

                        return '';

                    }
                    else{

                        if(!isNaN(row['SALDO_R1'].substr(0,1))){

                            return '$ ' + number_format(row['SALDO_R1'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return row['SALDO_R1'];

                        }

                    }

                }

            },
            {

                "targets": [ 11 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if(row['SALDO_R2'] == '' || row['SALDO_R2'] == null){

                        return '';

                    }
                    else{

                        if(!isNaN(row['SALDO_R2'].substr(0,1))){

                            return '$ ' + number_format(row['SALDO_R2'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return row['SALDO_R2'];

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

                    if(row['SALDO_R3'] == '' || row['SALDO_R3'] == null){

                        return '';

                    }
                    else{

                        if(!isNaN(row['SALDO_R3'].substr(0,1))){

                            return '$ ' + number_format(row['SALDO_R3'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return row['SALDO_R3'];

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

                    if(row['SALDO_MAS'] == '' || row['SALDO_MAS'] == null){

                        return '';

                    }
                    else{

                        if(!isNaN(row['SALDO_MAS'].substr(0,1))){

                            return '$ ' + number_format(row['SALDO_MAS'],PRECIOS_DECIMALES,'.',',');

                        }
                        else{

                            return row['SALDO_MAS'];

                        }

                    }

                }

            }
*/
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
            var totalSaldo = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 7 ).footer() ).html(
                '$ ' + number_format(totalSaldo,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo2 = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 8 ).footer() ).html(
                '$ ' + number_format(totalSaldo2,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo3 = api
                .column( 9 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 9 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo3,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo4 = api
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 10 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo4,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo5 = api
                .column( 11 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 11 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo5,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo6 = api
                .column( 12 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 12 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo6,PRECIOS_DECIMALES,'.',',')
            );

            // Total over all pages
            var totalSaldo7 = api
                .column( 13 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 13 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total)'
                '$ ' + number_format(totalSaldo7,PRECIOS_DECIMALES,'.',',')
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

$.ajax({
type: 'GET',
async: true,       
url: '{!! route('datatables.FTPDCXPPesos') !!}',
data: {

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
console.log(data)
if(data.FTPDCXPPesos.length > 0){
$("#tableFTPDCXPPesos").dataTable().fnAddData(data.FTPDCXPPesos);
}else{

}        
}
});

$('#tableFTPDCXPPesos').on( 'change', 'input#selectCheck', function (e) {

    e.preventDefault();

    var tblCXPPesos = $('#tableFTPDCXPPesos').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblCXPPesos.row(fila).data();
    var check = datos['CHECK_BOX'];
    var node = tblCXPPesos.row(fila).node();
    $(node).removeClass('activo');

    if(check == 0){

        datos['CHECK_BOX'] = 1;
        $(node).addClass('activo');

        var saldoDisponible = $("#restoSaldoDisponible").val();
        //console.log(parseFloat(datos['SaldoFactura']) + " - " + parseFloat(saldoDisponible));
        if(parseFloat(saldoDisponible) <= 0){

            BootstrapDialog.show({
                title: 'Error',
                type: BootstrapDialog.TYPE_DANGER,
                message: 'No hay suficiente saldo disponible.',
                cssClass: 'login-dialog',
                buttons: [{
                    label: 'Aceptar',
                    cssClass: 'btn-default',
                    action: function(dialog){

                        $('input#selectCheck', tblCXPPesos.row(fila).node()).prop('checked', false);
                        datos['CHECK_BOX'] = 0;
                        $('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val(datos['SaldoFactura']);
                        dialog.close();

                    }
                }]
            });

        }
        else if(parseFloat($('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val()) <= parseFloat(saldoDisponible)){

            var resto = parseFloat(saldoDisponible) - parseFloat(datos['SaldoFactura']);
            $("#restoSaldoDisponible").val(resto);
            if(resto <= 0){

                $('#restoSaldoDisponible').css({'background-color' : 'green'});
                $('#restoSaldoDisponible').css({'color': 'white'});

            }
            else{

                $('#restoSaldoDisponible').css({'background-color' : 'red'});
                $('#restoSaldoDisponible').css({'color': 'white'});

            }

        }
        else if(parseFloat($('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val()) > parseFloat(saldoDisponible)){

            $('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val(saldoDisponible);
            $("#restoSaldoDisponible").val(0);
            $('#restoSaldoDisponible').css({'background-color' : 'green'});
            $('#restoSaldoDisponible').css({'color': 'white'});

        }

    }else {

        var saldoDisponible = $("#restoSaldoDisponible").val();
        //console.log(parseFloat($('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val()));
        saldoDisponible = parseFloat(saldoDisponible) + parseFloat($('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val());
        $("#restoSaldoDisponible").val(saldoDisponible);

        if(saldoDisponible <= 0){

            $('#restoSaldoDisponible').css({'background-color' : 'green'});
            $('#restoSaldoDisponible').css({'color': 'white'});

        }
        else{

            $('#restoSaldoDisponible').css({'background-color' : 'red'});
            $('#restoSaldoDisponible').css({'color': 'white'});

        }

        datos['CHECK_BOX'] = 0;
        $('input#saldoFacturaPesos', tblCXPPesos.row(fila).node()).val(datos['SaldoFactura']);

    }

});

function number_format(number, decimals, dec_point, thousands_sep) {
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
});//fin on load


}  //fin js_iniciador               

</script>
