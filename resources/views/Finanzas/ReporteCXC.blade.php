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
                
                .segundoth {
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
                .DTFC_LeftBodyLiner {
                overflow: hidden;
                overflow-y: hidden;
                }
                .dataTables_filter {
                display: none;
                }
                div.dt-buttons {
                    float: right;
                    margin-bottom: 6px;
                    margin-top: 0px;
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
               .bootbox.modal {z-index: 9999 !important;}
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Reporte CXC
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                    <!-- begin row -->
                    <div id="btnBuscadorOrdenVenta">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-scroll" id="registros-ordenes-venta">
                                    <table id="ordenes-venta" class="table table-striped table-bordered hover" width="100%">
                                        <thead>
                                            <tr>
                    
                                                <th>Cliente</th>
                                                <th class="">Proyecto</th>
                                                <th>OC</th>
                                                <th>OV</th>
                                                <th>Importe</th>
                                                <th>Moneda</th>
                                                <th>Importe Facturado</th>
                                                <th>Importe Cobrado</th>
                                                <th>Por Cobrar Fac.</th>
                                                <th>Por Cobrar Sin</th>
                                                <th>Total de Adeudo</th>
                                               
                                                
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
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
                                               
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    
                    
                    </div>

                    <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Facturas / Notas de Crédito <b id="modal_ov"></b></h4>
                                </div>
                    
                                <div class="modal-body" style='padding:16px'>
                    
                                    <div class="row">
                                        <div class="card shadow mb-4">
                                            <div class="card-body p-5">
                                                <ul class="list-group" id="lista_fac">
                                                   
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                </div>
                    
                            </div>
                        </div>
                    </div>
                    <!-- end row -->     
                    </div>   <!-- /.container -->
@endsection
<script>
function js_iniciador() {
    $('.boot-select').selectpicker();
    $('.toggle').bootstrapSwitch();
    $('.dropdown-toggle').dropdown();
   
    var xhrBuscador = null;
   
    var wrapper = $('#page-wrapper2');
                var resizeStartHeight = wrapper.height();
                var height = (resizeStartHeight *70)/100;
                if ( height < 200 ) {
                    height = 200;
                }
                console.log('height_datatable' + height)
    var table = $("#ordenes-venta").DataTable({
        language:{
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        iDisplayLength: 6,
        deferRender: true,
        dom: 'lrtip',
        scrollX: true,
        scrollY: height,
        scrollCollapse: true,
        "pageLength": 100,
        "lengthMenu": [[100, 50, 25, -1], [100, 50, 25, "Todo"]],
       
        aaSorting: [[4, "desc" ]],
        processing: true,
        
        columns: [
            {data: "CLIENTE"},
            {data: "PROYECTO"},
            {data: "OC"},
            {data: "OV"},
            {data: "IMPORTE",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "MONEDA"},
            {data: "IMPORTE_FAC",
            render: function(data, type, row, meta ) {
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "IMPORTE_COBRADOS",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "XCOBRARFAC",
            render: function(data, type, row, meta ) {
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "XCOBRARSIN",
            render: function(data, type, row, meta ) {            
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "TOTAL",
            render: function(data, type, row, meta ) {
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }}
            
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
            var columnas = [4,6,7,8,9,10];
            var pageTotal = 0;
            // Total over all pages
            columnas.forEach(element => {
                pageTotal = api
                .column( element , {page: 'current'})
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                var pageT = '$' + pageTotal.toLocaleString("es-MX", {minimumFractionDigits:2})                
                $( api.column( element ).footer() ).html(pageT);
              
               
            });
            

        }
    });

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
reloadBuscadorOV();
function reloadBuscadorOV(){
    $("#ordenes-venta").DataTable().clear().draw();
        var estado =($('#estado').val() == null) ? '3CE37D96-1E8A-49A7-96A1-2E837FA3DCF5': $('#estado').val();
    $.ajax({
        type: 'GET',
        async: true,       
        url: '{!! route('data_cxc_reporte') !!}',
        data: {
            "_token": "{{ csrf_token() }}",
          
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
            if(data.data.length > 0){
                $("#ordenes-venta").dataTable().fnAddData(data.data);           
            }else{
                bootbox.dialog({
                title: "Mensaje",
                message: "<div class='alert alert-danger m-b-0'>No hay Ordenes de Venta que cumplan los parámetros.</div>",
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
$('#ordenes-venta').on('dblclick', 'tr', function () {
    var fila = table.rows(this).data()
    var facturas = fila[0]['N_FAC'];
    var notas = fila[0]['N_NC'];
    var myArray = facturas.split(",");
    var myArray2 = notas.split(",");
    $('#lista_fac').empty()
    myArray.forEach(element => {
        $("#lista_fac").append('<li class="list-group-item">'+element+'</li>');
    });
    myArray2.forEach(element => {
        $("#lista_fac").append('<li class="list-group-item">'+element+'</li>');
    });
    $('#modal_ov').text(fila[0]['OV'])
    $('#modal_detail').modal("show");
 }); //end dblclick   
                                 
      }                                                                                                    
                </script>
