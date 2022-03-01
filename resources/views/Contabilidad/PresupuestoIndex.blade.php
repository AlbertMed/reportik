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
                Reporte de Presupuesto
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
                    <label class="control-label">Ejercicio - Periodo:</label>
                    
                    <select name='cbo_periodo' class="form-control selectpicker" data-style="btn-success btn-sm" required='required'
                        id='cbo_periodo' title='Selecciona una opción' data-live-search="true">
                       
                        @foreach ($cbo_periodos as $value)
                        <option value='{{$value}}'>{{$value}}</option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-md-4 col-sm-6 col-xs-6">
                    <a  style="margin-top:24px" class="btn btn-success btn-sm" href="{{url('home/PRESUPUESTOS/presupuesto_agregar_cta/')}}"><i class="fa fa-plus"></i>
                            Cuenta a Periodo</a>                    
                </div>
            </div>
                
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="table_resumen" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th># CUENTA</th>
                                <th>DESCRIPCION</th>
                                <th>PRESUPUESTO</th>
                                <th>ANTERIOR</th>
                                <th>MOVIMIENTO PERIODO</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
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
$(window).on('load',function(){            


});//fin on load

}  //fin js_iniciador               
</script>
