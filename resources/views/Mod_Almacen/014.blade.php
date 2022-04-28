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
        margin-top: 84px;
        /* margin-top: 85px; */
    }
    .DTFC_LeftHeadWrapper {
        display:none;
    }
    .dataTables_filter {
        display: none;
    } 
    div.dt-buttons {
        float: left;
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
    .dataTables_wrapper .dataTables_length {
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
                                Reporte de Inventario General                                
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
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">ALMACEN</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">LOCALIDAD</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">NOM_LOCAL</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">CODIGO</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">NOMBRE</th>
                                        
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">UM_Inv</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">EXISTE</th>                                        
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">COS_EST</th>                                                                                
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">IMPORTE</th>                                        
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">FAMILIA</th>
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">CATEGORIA</th>
                                        
                                        <th align="center" bgcolor="#474747" style="color:white" ;scope="col">TIPO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                   
                   
                   
                    </div>

                    </div>
                    <!-- /.container -->

                    @endsection

                    <script>function js_iniciador() {
   startjs()
$('#tentradas thead tr').clone(true).appendTo( '#tentradas thead' );

$('#tentradas thead tr:eq(1) th').each( function (i) {
var title = $(this).text();
$(this).html( '<input style="color: black;" type="text" placeholder="Filtro '+title+'" />' );

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
    "order": [[2, "desc"], [1, "asc"], [0, "asc" ]],
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
    "iDisplayLength": 100,
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
                //var data= $("#tentradas").DataTable().$('tr', { filter : 'applied'}); 
                
                var json = JSON.stringify( data );
                $.ajax({ 
                    type:'POST', 
                     url:'/public/home/reporte/ajaxtosession/DATA_R014',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: { "_token": "{{ csrf_token() }}", "arr": json }, 
                    success:
                        function(data){ 
                            window.location.href = 'R014AXLS';
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
                             url:'/public/home/reporte/ajaxtosession/DATA_R014',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},                            
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "arr": json
                                },
                                success:function(data){
                                    window.open('R014APDF', '_blank')                                   
                            }
                         });
                     }         
        },
    ],
   
    
    ajax: {
                                url: '{!! route('datatables.show014') !!}',
                                
                            },
    columns: [
        {data: 'ALMACEN'},
        {data: 'LOCALIDAD'},
        {data: 'NOM_LOCAL'},
        {data: 'CODIGO'},
        {data: 'NOMBRE'},

        {data: 'UM_Inv'},
        {data: 'EXISTE',
        render: function(data){ var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data); return val; }
        },
        {data: 'COS_EST',
        render: function(data){ var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data); return val; }
        },
        {data: 'IMPORTE',
        render: function(data){ var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data); return val; }
        },
        {data: 'FAMILIA'},
        {data: 'CATEGORIA'},
        {data: 'TIPO'}
                            ],
    columnDefs: [
    
    ],
      "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
    },   
   "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
        };

        // Total over this page for VS
        pageTotalCant = api
            .column( 6, { filter: 'applied'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
        
        pageTotalImporte = api
            .column( 8, { filter: 'applied'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );
        // Update footer for VS
        //.toLocaleString("es-MX",{style:"currency", currency:"MXN"}) //example to format a number to Mexican Pesos
        //var n = 1234567.22
        //alert(n.toLocaleString("es-MX",{style:"currency", currency:"MXN"}))

        var pageTCant = pageTotalCant.toLocaleString("es-MX", {minimumFractionDigits:2})
       // var pageTCosto = pageTotalCosto.toLocaleString("es-MX", {minimumFractionDigits:2})
        var pageTImporte = pageTotalImporte.toLocaleString("es-MX", {minimumFractionDigits:2})
        


        $( api.column( 6 ).footer() ).html(
            pageTCant
        );
       
        $( api.column( 8 ).footer() ).html(
            '$ '+pageTImporte
        );


    }
}); //fin datatable
 yadcf.init(table,
            [
           
            //{
              //  column_number : [6],
                //filter_type: 'range_number',
                //filter_default_label: ["Min", "Max"]
            //}
            
            ],
            );
}</script>

                    <script>

                       
                    </script>
                   
                   

