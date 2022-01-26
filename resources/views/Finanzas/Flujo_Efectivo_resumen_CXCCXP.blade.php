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

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                RESUMEN CXC & CXP
                <small><b>Flujo Efectivo</b></small>
            
            </h3>                                        
        </div>
        
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
        </div> <!-- /.row -->
        <div class="col-md-12">
            <div class="row">
                <a href="{{ url('home/FINANZAS/01 FLUJO EFECTIVO') }}" class="btn btn-primary">Atras</a>                
            </div>        
        </div>
        <div class="" id="resumen_cxc">
            @include('Finanzas.Resumen_CXC_Cliente')
        </div>
        <div class="" id="resumen_cxc">
            @include('Finanzas.Resumen_CXP_Proveedor')
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
        var namefile= 'FE_resumen_clienteProveedor.pdf';
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
///RESUMEN CXC
var xhrBuscador = null;

var data,
tableName= '#t_ordenes_proyeccion',
tableproy,
str, strfoot, contth,
jqxhr =  $.ajax({
    //cache: false,
        async: false,
        dataType:'json',
        type: 'GET',
        data:  {
             
            },
        url: '{!! route('datatables.resumen_cxc') !!}',
        beforeSend: function () {
           
        },
        success: function(data, textStatus, jqXHR) {
           createTable(jqXHR,data);           
        },
        
        complete: function(){
          
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

function createTable(jqXHR,data){
     data = JSON.parse(jqXHR.responseText);
            // Iterate each column and print table headers for Datatables
            contth = 1;
            $.each(data.columns, function (k, colObj) {
                if (contth <= 2) {
                    str = '<th class="segundoth">' + colObj.name + '</th>';
                    strfoot = '<th class="segundoth"></th>';
                }else{
                    str = '<th>' + colObj.name + '</th>';
                    strfoot = '<th></th>';
                }
                contth ++;
                $(str).appendTo(tableName+'>thead>tr');
                $(strfoot).appendTo(tableName+'>tfoot>tr');
                console.log("adding col "+ colObj.name);
            });
            
            for (let index = 2; index < Object.keys(data.columns).length; index++) {
                data.columns[index].render = function (data, type, row) {            
                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                    return val;
                }
            }
                    // Debug? console.log(data.columns[0]);
                   $('#t_ordenes_proyeccion thead tr').clone().appendTo( $("#t_ordenes_proyeccion thead") );   
            
         tableproy = $(tableName).DataTable({
                
                deferRender: true,
               "paging":   false,
                dom: 'frti',
                scrollX: true,
                scrollCollapse: true,
                scrollY: "200px",
                fixedColumns: {
                leftColumns: 2
                },
                aaSorting: [[2, "desc" ]],
                processing: true,
                columns: data.columns,
                data:data.data,
                
                "language": {
                    "url": "{{ asset('assets/lang/Spanish.json') }}",                    
                },
                columnDefs: [
                    {
                    "targets": 0,
                    "visible": false
                    },
                   
                ],
                "footerCallback": function ( tfoot, data, start, end, display ) {
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
                };
                
                //
                for (let index = 2; index < (contth-1); index++) {
                
                    pageTotal = api
                    .column( index, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                    }, 0 );
                    var pageT = pageTotal.toLocaleString("es-MX", {minimumFractionDigits:2})                
                    $( api.column( index ).footer() ).html(pageT);
                }
               
                }, 
            });
            $('#t_ordenes_proyeccion thead tr:eq(0) th').each( function (i) {
            var title = $(this).text();
            //console.log($(this).text());
            $(this).html( '<input style="color:black" type="text" placeholder="Filtro '+title+'" />' );
            $( 'input', this ).on( 'keyup change', function () {
            
            if ( tableproy.column(i).search() !== this.value ) {
            tableproy
            .column(i)
            .search(this.value, true, false)
            .draw();
            
            }
            
            } );
            
            } );
}
////FIN RESUMEN CXC
// INICIO RESUMEN CXP
var
tableName_cxp= '#t_cxp',
table_cxp

jqxhr2 = $.ajax({
    //cache: false,
    async: false,
        dataType:'json',
        type: 'GET',
        data:  {
             
            },
        url: '{!! route('datatables.resumen_cxp') !!}',
        beforeSend: function () {
          
        },
        success: function(data, textStatus, jqXHR) {
           createTable_cxp(jqXHR,data);           
        },
        
        complete: function(){
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
function recargar_cxp(){
$.ajax({
        dataType:'json',
        type: 'GET',
        data:  {
             
            },
        url: '{!! route('datatables.resumen_cxp') !!}',
        beforeSend: function () {
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
            $("#t_cxp").DataTable().clear().draw();
            $("#t_cxp").dataTable().fnAddData(JSON.parse(data).data);
                          
        },
        
        complete: function(){
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
   

}
function createTable_cxp(jqXHR,data){
     data = JSON.parse(jqXHR.responseText);
            // Iterate each column and print table headers for Datatables
            contth = 1;
            $.each(data.columns, function (k, colObj) {
                if (contth <= 2) {
                    str = '<th class="segundoth">' + colObj.name + '</th>';
                    strfoot = '<th class="segundoth"></th>';
                }else{
                    str = '<th>' + colObj.name + '</th>';
                    strfoot = '<th></th>';
                }
                contth ++;
                $(str).appendTo(tableName_cxp+'>thead>tr');
                $(strfoot).appendTo(tableName_cxp+'>tfoot>tr');
                console.log("adding col "+ colObj.name);
            });
            
            for (let index = 2; index < Object.keys(data.columns).length; index++) {
                data.columns[index].render = function (data, type, row) {            
                    var val = new Intl.NumberFormat("es-MX", {minimumFractionDigits:2}).format(data);
                    return val;
                }
            }
                    // Debug? console.log(data.columns[0]);
                   $('#t_cxp thead tr').clone().appendTo( $("#t_cxp thead") );   
            
         table_cxp = $(tableName_cxp).DataTable({
                
                deferRender: true,
               "paging":   false,
                dom: 'frti',
                scrollX: true,
                scrollCollapse: true,
                scrollY: "200px",
                fixedColumns: {
                leftColumns: 2
                },
                aaSorting: [[2, "desc" ]],
                processing: true,
                columns: data.columns,
                data:data.data,
                buttons: {
                    buttons: [
                        {
                            text: 'Recargar',
                            action: function ( e, dt, node, config ) {
                              recargar_cxp();
                            }
                        }
                    ]
                },
                "language": {
                    "url": "{{ asset('assets/lang/Spanish.json') }}",                    
                },
                columnDefs: [
                    {
                    "targets": 0,
                    "visible": false
                    },
                   
                ],
                "footerCallback": function ( tfoot, data, start, end, display ) {
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                i : 0;
                };
                
                //
                for (let index = 2; index < (contth-1); index++) {
                
                    pageTotal = api
                    .column( index, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                    }, 0 );
                    var pageT = pageTotal.toLocaleString("es-MX", {minimumFractionDigits:2})                
                    $( api.column( index ).footer() ).html(pageT);
                }
               
                }, 
            });
            $('#t_cxp thead tr:eq(0) th').each( function (i) {
            var title = $(this).text();
            //console.log($(this).text());
            $(this).html( '<input style="color:black" type="text" placeholder="Filtro '+title+'" />' );
            $( 'input', this ).on( 'keyup change', function () {
            
            if ( table_cxp.column(i).search() !== this.value ) {
                console.log(table_cxp.column(i).search())
            table_cxp
            .column(i)
            .search(this.value, true, false)
            .draw();
            
            }
            
            } );
            
            } );
}
////FIN RESUMEN CXP

$(window).on('load',function(){            
    setTimeout($.unblockUI, 1500);
});//fin on load

}  //fin js_iniciador               
</script>
