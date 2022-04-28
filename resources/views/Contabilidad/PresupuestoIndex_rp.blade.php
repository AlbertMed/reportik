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
   table {
display: block;
overflow-x: auto;
white-space: nowrap;
}
</style>

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Reporte Presupuesto
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
                    <input type="text" name="date" id="periodo" value="{{(isset($ejercicio))?$ejercicio.'-'.$periodo:''}}"
                    class="form-control" autocomplete="off" >
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <a style="margin-top:24px" class="btn btn-success btn-sm"
                        href="#" id="btn_reporte"><i class="fa fa-cogs"></i>
                        Mostrar</a>
                    <a style="margin-top:24px" class="btn btn-success btn-sm"
                        href="{{url('home/reporte/05 PRESUPUESTOS/captura')}}"><i class="fa fa-cogs"></i>
                        Capturar Presupuesto</a>
                    <a id="btn_pdf" disabled="true" style="margin-top:24px" class="btn btn-danger btn-sm" href="#"><i class="fa fa-file-pdf-o"></i>  PDF</a>                                    
                    <a id="btn_xls" disabled="true" style="margin-top:24px" class="btn btn-success btn-sm " href="#"><i class="fa fa-file-excel-o"></i>  XLS</a>
                </div>
            </div>
                
        </div>
        <hr>
        @if (!is_null($periodo))            
            @include('Contabilidad.RG03_reporte_ER')
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
    var data, tableName = '#table_ctas', table_ctas;
        var xhrBuscador = null;
        var wrapper = $('#page-wrapper2');
        var resizeStartHeight = wrapper.height();
        var height = (resizeStartHeight * 65)/100;
        if ( height < 200 ) {   
            height = 200;
        }
        
        $('input[name="date"]').change( function(e) {

            console.log(this.value)
            e.preventDefault();  
            $('#btn_reporte').attr('href', "{!! url('home/reporte/05 PRESUPUESTOS/"+this.value+"') !!}");  
                       
            //table_ctas.ajax.reload();
           
        });
        $('#table_ctas').on('preXhr.dt', function (e, settings, data) {
            if(!ignore_blockUI){
                $.blockUI({
                    baseZ: 2000,
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
            }            
        })
        
        $('#table_ctas').on('xhr.dt', function (e, settings, json, xhr) {
            setTimeout($.unblockUI, 1500);
        })
        $('#btn_reporte').on( 'click', function (e)
        {
          if ($('#periodo').val() != '') {
              $.blockUI({
                    baseZ: 2000,
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
          }
            
        });

}  //fin js_iniciador               
</script>
