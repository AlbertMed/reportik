@extends('home')

            @section('homecontent')
<style>
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
    th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }
    table.dataTable thead .sorting_asc{
        position: sticky;
    }
    .DTFC_LeftBodyWrapper{
        margin-top: 87px;
    }
    .DTFC_LeftHeadWrapper {
        display:none;
    }
    .dataTables_filter {
        display: none;
    } 
    div.dt-buttons {
       // float: right;
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
    .yadcf-filter-range-number-seperator {
    margin-left: 0px;
    margin-right: 10px;
    }
    .yadcf-filter-reset-button {
    display: inline-block;
    background-color: #337ab7;
    border-color: #2e6da4;
    }
    input{
        color: black;
    }
</style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                                Reporte Precios Materias Primas
                                <small>{{$sociedad}}</small>
                            </h3>
                    
                     <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5>                         
                    </div>
                    </div>
                                          
                    </div> 
                    <br>  
                 <!-- /.row -->
                    <div class="table-scroll">
                        <div class="col-md-12 pane">
                            <table id="tentradas" border="1px" class="table table-striped">
                                <thead class="table table-striped table-bordered table-condensed" >
                                    <tr>                      
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">CODIGO</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">NOMBRE</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">FAMILIA</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">SUB_CAT</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">UDM</th>
                                       
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">EXISTENCIA</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">ESTANDAR</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">PROMEDIO</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">U_COMPRA</th>
                                        
                                       
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                               
                            </table>
                        </div>
                   
                   
                   
                    </div>
<input hidden value="{{$sociedad}}" id="sociedad" name="sociedad" />
<input hidden value="{{$tipo}}" id="tipo" name="tipo" />
                    </div>
                    <!-- /.container -->

                    @endsection

                    @section('homescript')
$('#tentradas thead tr').clone(true).appendTo( '#tentradas thead' );

$('#tentradas thead tr:eq(1) th').each( function (i) {
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
var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
var diasSemana = new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
var f=new Date();
var hours = f.getHours();
var ampm = hours >= 12 ? 'pm' : 'am';
var fecha = 'ACTUALIZADO: '+ diasSemana[f.getDay()] + ', ' + f.getDate() + ' de ' + meses[f.getMonth()] + ' del ' + f.getFullYear()+', A LAS '+hours+":"+f.getMinutes()+ ' ' + ampm; 
var f = fecha.toUpperCase();

var table = $('#tentradas').DataTable({
    "order": [[1, "asc"], [0, "asc" ]],
    "dom": 'Blrtfip',
    orderCellsTop: true,
    scrollY: "300px",
    scrollX: true,
    scrollCollapse: true,
    fixedColumns: true,
    processing: true,
    deferRender: true,
    serverSide: false,
    paging: true,
    "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"] ],
    "pageLength": 100,
    buttons: [
        {
            text: '<i class="fa fa-columns" aria-hidden="true"></i> Columna',
            className: "btn btn-primary",
            extend: 'colvis',
            postfixButtons: [                                  
                {
                    text: 'Restaurar columnas',
                    extend: 'colvisRestore',     
                }             
                ]
        },
        {
           text: '<i class="fa fa-file-excel-o"></i> Excel',
            className: "btn-success",
            action: function ( e, dt, node, config ) { 
                var data=table.rows( { filter : 'applied'} ).data().toArray(); 
                var json = JSON.stringify( data );
                $.ajax({ 
                    type:'POST', 
                    url:'ajaxtosession/DATA_R003A', 
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: { "_token": "{{ csrf_token() }}", "arr": json }, 
                    success:
                        function(data){ 
                            window.location.href = 'R003AXLS';
                        } 
                }); 
            }     
        }, 
        {
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',           
            className: "btn-danger",            
                    action: function ( e, dt, node, config ) {                                
                         var data=table.rows( { filter : 'applied'} ).data().toArray();               
                         var json = JSON.stringify( data );
                         $.ajax({
                            type:'POST',
                            url:'ajaxtosession/DATA_R003A',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "arr": json
                                },
                                success:function(data){
                                    window.open('R003APDF', '_blank')                                   
                            }
                         });
                     }         
        },
    ],
    ajax: {
                                url: '{!! route('datatables.show003a') !!}',
                                 data: function (d) {
                                    d.sociedad = $('input[name=sociedad]').val();
                                    d.tipo = $('input[name=tipo]').val();
                                }
                            },
    columns: [
                                {data: 'CODIGO'},
                                {data: 'NOMBRE'},
                                {data: 'FAMILIA'},
                                {data: 'SUB_CAT'},
                                {data: 'UDM'},
                                
                                {data: 'EXISTENCIA',
                                render: function(data){
                                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                                    return val;
                                }},
                                {data: 'ESTANDAR',
                                render: function(data){
                                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                                    return val;
                                }},
                                {data: 'PROMEDIO',
                                render: function(data){
                                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                                    return val;
                                }},
                                {data: 'U_COMPRA',
                                render: function(data){
                                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                                    return val;
                                }},
                               
                            ],
    columnDefs: [
    
    ],
      "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
    },   
    
}); //fin datatable
yadcf.init(table,
            [
                {
                    column_number : [5],
                    filter_type: 'range_number',
                    filter_default_label: ["Min", "Max"]
                },
                {
                    column_number : [6],
                    filter_type: 'range_number',
                    filter_default_label: ["Min", "Max"]
                },
                {
                    column_number : [7],
                    filter_type: 'range_number',
                    filter_default_label: ["Min", "Max"]
                },
                {
                    column_number : [8],
                    filter_type: 'range_number',
                    filter_default_label: ["Min", "Max"]
                }
            
            ],
            );

                    @endsection

                    <script>

                       
                    </script>
                   
                   

