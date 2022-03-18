@extends('home')

@section('homecontent')
<style>
    th,
    td {
        white-space: nowrap;
        vertical-align: middle;
    }
    table.dataTable.nowrap td {
    white-space: nowrap;
    vertical-align: middle;
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
        font-size: 12px;
        border: 0px;
        line-height: 1;
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

    
    .DTFC_LeftHeadWrapper {
        overflow-x: hidden;
    }
    .DTFC_LeftBodyLiner{
        overflow-x: hidden;
    }
    .blue{
        background-color: #87cefad3;
    }
    .ignoreme{
                    background-color: rgba(235, 0, 0, 0.288) !important;       
    }
    .hidden {
        display: none;
    }
</style>

<div class="container">
   <br>
    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Captura de Presupuesto
                <small>Sociedad: <b>{{$sociedad}}</b> </small>

            </h3>
            
        </div>

        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
    </div> <!-- /.row -->
    <div class="row">
            <div class="form-group">
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <label class="control-label">Ejercicio:</label>
                    <input type="text" name="date" id="periodo" 
                    class="form-control" autocomplete="off" >
                                           
                    
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <a style="margin-top:24px" class="btn btn-success btn-sm"
                       id="btn_guardar" ><i class="fa fa-save"></i>
                        Guardar cambios</a>
                        <a style="margin-top:24px" class="btn btn-primary btn-sm" href="{{url('home/reporte/05 PRESUPUESTOS/1')}}">Atras</a>
                </div>
            </div>
                
        </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="table_ctas" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>                        
                        <th>Cuenta</th>
                        <th>Descripción</th>
                        <th>Saldo Inicial</th>
                        <th>Enero</th>   
                        <th>Febrero</th>
                        <th>Marzo</th>
                        <th>Abril</th>
                        <th>Mayo</th>
                        <th>Junio</th>
                        <th>Julio</th>
                        <th>Agosto</th>
                        <th>Septiembre</th>
                        <th>Octubre</th>
                        <th>Noviembre</th>
                        <th>Diciembre</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
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
            
            $('#btn_add_code').prop('disabled', true);
            $('#btn_del_acabado').prop('disabled', true);
         var date_input=$('input[name="date"]'); 

        date_input.datepicker( {
            language: "es",    
            autoclose: true,
            format: "yyyy",
            startView: "years",
            dropupAuto: false,
            minViewMode: "years"
        });  
       
            var data,
            tableName = '#table_ctas',
            table_ctas;
        $(window).on('load', function() {
            var xhrBuscador = null;
            createTable();
            var wrapper = $('#page-wrapper2');
            var resizeStartHeight = wrapper.height();
            var height = (resizeStartHeight * 65)/100;
            if ( height < 200 ) {   
                height = 200;
            }
            console.log('height_datatable' + height)
            console.log('wrapp' + wrapper)
            console.log('resizeStartHeight' + resizeStartHeight)
            console.log('(resizeStartHeight *70)/100' + resizeStartHeight *75)
            function createTable(){
                table_ctas = $(tableName).DataTable({
                    deferRender: true,
                    "paging": true,
                    dom: 'lrftip',
                    "pageLength": 100,
                    "lengthMenu": [[100, 50, 25, 10, -1], [100, 50, 25, 10, "Todo"]],
                    scrollX: true,
                    scrollY: height,
                    
                    ajax: {
                        url: '{!! route('datatables_ctas_presupuesto') !!}',
                        data: function (d) {                            
                            d.periodo = $('#periodo').val()
                                 
                        }              
                    },
                    processing: true,
                    columns: [ 
                        {"data" : "BC_Cuenta_Id"},
                        {"data" : "BC_Cuenta_Nombre"},
                        {"data" : "BC_Saldo_Inicial"},
                        {"data" : "BC_Movimiento_01"},
                        {"data" : "BC_Movimiento_02"},
                        {"data" : "BC_Movimiento_03"},
                        {"data" : "BC_Movimiento_04"},
                        {"data" : "BC_Movimiento_05"},
                        {"data" : "BC_Movimiento_06"},
                        {"data" : "BC_Movimiento_07"},
                        {"data" : "BC_Movimiento_08"},
                        {"data" : "BC_Movimiento_09"},
                        {"data" : "BC_Movimiento_10"},
                        {"data" : "BC_Movimiento_11"},
                        {"data" : "BC_Movimiento_12"}
                        
                    ],
                    "rowCallback": function( row, data, index ) {
                        
                    },
                    "language": {
                        "url": "{{ asset('assets/lang/Spanish.json') }}",
                    },
                    columnDefs: [                    
                        {
                            "targets": [ 2 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "saldoInicial" style="width: 100px" class="form-control input-sm" value="' + number_format(row['BC_Saldo_Inicial'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 3 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_01" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_01'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 4 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_02" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_02'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 5 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_03" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_03'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 6 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_04" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_04'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 7 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_05" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_05'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 8 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_06" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_06'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 9 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_07" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_07'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 10 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_08" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_08'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 11 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_09" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_09'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 12 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_10" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_10'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 13 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_11" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_11'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                        {
                            "targets": [ 14 ],
                            "searchable": false,
                            "orderable": false,
                            'className': "dt-body-center",
                            "render": function ( data, type, row ) {
                                return '<input id= "movimiento_12" style="width: 100px" class="form-control input-sm" value="' 
                                + number_format(row['BC_Movimiento_12'],2,'.','') + '" type="number"  min="0">'                            
                            }
                        },
                    ],
                    "initComplete": function(settings, json) {
                     
                    }
                });
            }
            
            $('input[name="date"]').change( function(e) {
                console.log(this.value)
                e.preventDefault();                
                table_ctas.ajax.reload();
            });
            
$('#btn_guardar').off().on( 'click', function (e) 
{
   
    var datosTablaCtas;
    datosTablaCtas = getdatosTablaCtas();
    datosTablaCtas = JSON.stringify(datosTablaCtas);
      console.log(datosTablaCtas);
         $.ajax({
            url: "{{route('guardar_presupuesto')}}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {
                "datosTablaCtas": datosTablaCtas,
                "periodo" : $('#periodo').val()    
            },
            type: "POST",
            beforeSend: function() {
                $.blockUI({
                    message: '<h1>Guardando Cuentas,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
                    bootbox.dialog({
                        title: "Mensaje",
                        message: "<div class='alert alert-success m-b-0'>Cuentas registradas",
                        buttons: {
                            success: {
                                label: "Ok",
                                className: "btn-success m-r-5 m-b-5"
                            }
                        }
                    }).find('.modal-content').css({'font-size': '14px'} );
                    
                    //$('#tableProgramas').DataTable().ajax.reload();
                    

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

});
function getdatosTablaCtas(){

    var tabla = $('#table_ctas').DataTable();
    var fila = $('#table_ctas tbody tr').length;
    var datos_Tabla = tabla.rows().data();
    var tbl = new Array();
   
    if (datos_Tabla.length != 0){

        var siguiente = 0;
        for (var i = 0; i < fila; i++) {
            tbl[siguiente]={               
                "BC_Cuenta_Id" : datos_Tabla[i]["BC_Cuenta_Id"]
                ,"BC_Cuenta_Nombre" : datos_Tabla[i]["BC_Cuenta_Nombre"]
                //,"BC_Saldo_Inicial" : datos_Tabla[i]["BC_Saldo_Inicial"]
                ,"BC_Saldo_Inicial" : $('input#saldoInicial', tabla.row(i).node()).val()
                //,"BC_Movimiento_01" : datos_Tabla[i]["BC_Movimiento_01"]
                ,"BC_Movimiento_01" : $('input#movimiento_01', tabla.row(i).node()).val()
                //,"BC_Movimiento_02" : datos_Tabla[i]["BC_Movimiento_02"]
                ,"BC_Movimiento_02" : $('input#movimiento_02', tabla.row(i).node()).val()
                //,"BC_Movimiento_03" : datos_Tabla[i]["BC_Movimiento_03"]
                ,"BC_Movimiento_03" : $('input#movimiento_03', tabla.row(i).node()).val()
                //,"BC_Movimiento_04" : datos_Tabla[i]["BC_Movimiento_04"]
                ,"BC_Movimiento_04" : $('input#movimiento_04', tabla.row(i).node()).val()
                //,"BC_Movimiento_05" : datos_Tabla[i]["BC_Movimiento_05"]
                ,"BC_Movimiento_05" : $('input#movimiento_05', tabla.row(i).node()).val()
                //,"BC_Movimiento_06" : datos_Tabla[i]["BC_Movimiento_06"]
                ,"BC_Movimiento_06" : $('input#movimiento_06', tabla.row(i).node()).val()
                //,"BC_Movimiento_07" : datos_Tabla[i]["BC_Movimiento_07"]
                ,"BC_Movimiento_07" : $('input#movimiento_07', tabla.row(i).node()).val()
                //,"BC_Movimiento_08" : datos_Tabla[i]["BC_Movimiento_08"]
                ,"BC_Movimiento_08" : $('input#movimiento_08', tabla.row(i).node()).val()
                //,"BC_Movimiento_09" : datos_Tabla[i]["BC_Movimiento_09"]
                ,"BC_Movimiento_09" : $('input#movimiento_09', tabla.row(i).node()).val()
                //,"BC_Movimiento_10" : datos_Tabla[i]["BC_Movimiento_10"]
                ,"BC_Movimiento_10" : $('input#movimiento_10', tabla.row(i).node()).val()
                //,"BC_Movimiento_11" : datos_Tabla[i]["BC_Movimiento_11"]
                ,"BC_Movimiento_11" : $('input#movimiento_11', tabla.row(i).node()).val()
                //,"BC_Movimiento_12" : datos_Tabla[i]["BC_Movimiento_12"]
                ,"BC_Movimiento_12" : $('input#movimiento_12', tabla.row(i).node()).val()
            }
            //montoTotalPrograma = montoTotalPrograma + parseFloat($('input#saldoFacturaPesos', tabla.row(i).node()).val());
            siguiente++;
        }
        return tbl;

    }
    else{

        return tbl;

    }

}

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
        }); //fin on load
    } //fin js_iniciador               
</script>