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
    font-size: 12px;
    border: 0px;
    line-height: 1;
    color: #111;

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
   table {
display: block;
overflow-x: auto;
white-space: nowrap;
}
</style>

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-12" style="margin-top: -20px">
            <h3 class="page-header">
                Reporte Estado de Costos {{$ejercicio}}
                <small>Sociedad: <b>{{$sociedad}}</b> </small>
            
            </h3>                                        
        </div>
        
    </div> <!-- /.row -->     
    @if (!is_null($periodo))            
        @include('Contabilidad.RG03_reporte_EC')
    @endif
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
    $("#sidebar").toggleClass("active"); 
    $("#page-wrapper2").toggleClass("content"); 
    $(this).toggleClass("active");
    ignore_blockUI = true;
    //$("#flujoEfectivoDetalle").hide();
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
    var date_input=$('input[name="date"]'); 

    date_input.datepicker( {
        language: "es",    
        autoclose: true,
        format: "yyyy-mm",
        startView: "months",
            dropupAuto: false,
        minViewMode: "months"
    }); 
    console.log(date_input.val())
    if (date_input.val() != '') {
        $('#btn_pdf').attr('href', "{{url('ReportePresupuestoPDF')}}");  
        $('#btn_pdf').attr('target', "_blank");  
        $('#btn_xls').attr('href', "{{url('ReportePresupuestoXLS')}}");  
            
        $( "#btn_pdf" ).removeAttr('disabled');            
        $( "#btn_xls" ).removeAttr('disabled');
    }    
    var data; 
        var xhrBuscador = null;
        var wrapper = $('#page-wrapper2');
        var resizeStartHeight = wrapper.height();
        var height = (resizeStartHeight * 85)/100;
        if ( height < 200 ) {   
            height = 200;
        }
        
        $('input[name="date"]').change( function(e) {

            console.log(this.value)
            e.preventDefault();  
            $('#btn_reporte').attr('href', "{!! url('home/reporte/05 PRESUPUESTOS/"+this.value+"') !!}");  
            
           
        });
        var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        var diasSemana = new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
        var f=new Date();
        var hours = f.getHours();
        var ampm = hours >= 12 ? 'pm' : 'am';
        var fecha = 'ACTUALIZADO: '+ diasSemana[f.getDay()] + ', ' + f.getDate() + ' de ' + meses[f.getMonth()] + ' del ' + f.getFullYear()+', A LAS '+hours+":"+f.getMinutes()+ ' ' + ampm; 
        var f = fecha.toUpperCase();

        var table = $('#tableCosto').DataTable({
            language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            searching: true,
            iDisplayLength: 5000,
            aaSorting: [],
            deferRender: true,
            dom: 'Brti',
            bInfo: false,
            fixedColumns: false,
            paging: false,
            buttons:[
                {
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: "btn-success",
                 extend: 'excelHtml5',
                title: 'ESTADO DE COSTOS '+"{{$ejercicio}}",
                message: "{{$sociedad}}",
                messagethree: f
            },
            {
                text: '<i class="fa fa-file-pdf-o"></i> Pdf',
                className: "btn-danger",
                action: function (e, dt, node, config) {
                    var datos = table.rows().data().toArray();
                    var json = JSON.stringify(datos);
                    $.ajax({
                        type: 'POST',
                        url: routeapp + 'home/reporte/ajaxtosession/estadoCostoPDF',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "arr": [json,"{{$sociedad}}",{{$ejercicio}}, {{$periodo}}],

                        },
                        success: function (data) {
                            window.open(routeapp + 'estadoCostoPDF', '_blank')
                        }
                    });
                }
            }
            ]
        });

}  //fin js_iniciador               
</script>
