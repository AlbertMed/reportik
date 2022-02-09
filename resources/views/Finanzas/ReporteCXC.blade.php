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
                                                <th>Factura</th>
                                                <th>Nota de Crédito</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
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
       
        aaSorting: [[8, "desc" ]],
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
            {data: "IMPORTE_FACTURADO",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "IMPORTE_COBRADOS",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "IMPORTE",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},

            {data: "IMPORTE",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "IMPORTE",
            render: function(data){
            var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
            return "$" + val;
            }},
            {data: "N_FAC"}, 
            {data: "N_NC"} 
            
        ],
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
    
                                 
      }                                                                                                    
                </script>
