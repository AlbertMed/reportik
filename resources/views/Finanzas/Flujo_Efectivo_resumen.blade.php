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
                Resumen
                <small><b>Flujo Efectivo</b></small>
            
            </h3>                                        
        </div>
            
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
        </div> <!-- /.row -->
        <?php 
        $saldo = $total_bancos[0]->SaldoDisponibleMN;
        $semanas = 5;
        $saldos_bancos = [];
        ?>
        <div class="col-md-12">
            <div class="row">
                    <a class="btn btn-success" href="{{url('home/FINANZAS/flujoefectivo-programas')}}"><i class="fa fa-cogs"></i>
                        Programar Pagos</a>
                    <a class="btn btn-success" href="{{url('home/FINANZAS/flujoefectivo-resumen-cliente-proveedor')}}"><i class="fa fa-eye"></i>
                        Resumen x Cliente/ Proveedor</a>
                    <a class="btn btn-success" href="{{url('home/FINANZAS/flujoefectivo-detalle-cliente-proveedor')}}"><i class="fa fa-eye"></i>
                        Detalle x Factura/ Orden Venta</a>
            </div>
        </div>
        
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="table_resumen" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>MXN</th>
                                <th>SALDO</th>
                                <th>VENCIDO</th>
                                @for ($i = 0; $i <= $semanas; $i++)
                                    @if ($i == 0)
                                        <th>SEM ACTUAL {{$sem}}</th>
                                    @else
                                        <th>SEM {{$sem +$i}}</th>
                                    @endif
                                @endfor
                                <th>RESTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BANCOS</td>
                                <td>$ {{number_format($saldo,'2', '.',',')}}</td>
                                <td>$ {{number_format($saldo,'2', '.',',')}}</td>
                                <?php $ba = $saldo; ?>
                                 @for ($i = 0; $i <= $semanas; $i++)
                                    @if ($i == 0)
                                        <?php 
                                        $ba = ($ba - $cxp_xsemana['VENCIDO']) + $cxc_xsemana['VENCIDO'];
                                        $saldos_bancos [$sem] = $ba;
                                        ?>
                                        <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                    @else
                                        <?php 
                                        $index = $sem + $i;
                                        $ba = ($ba - $cxp_xsemana[$index - 1]) + $cxc_xsemana[$index - 1];
                                        $saldos_bancos [$index] = $ba;
                                        ?>
                                        @if ($ba > 0)
                                            <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endif
                                @endfor
                                  <?php 
                                    $index = $sem + $semanas;
                                    $ba = ($ba - $cxp_xsemana[$index]) + + $cxc_xsemana[$index];
                                    $saldos_bancos ['RESTO'] = $ba;
                                    ?>
                                @if ($ba > 0)
                                    <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                @else
                                    <td></td>
                                @endif
                            </tr>
                            <tr>
                                <td>CXP</td>
                                <td>$ {{number_format($total_cxp,'2', '.',',')}}</td>
                                <td>$ {{number_format($cxp_xsemana['VENCIDO'],'2', '.',',')}}</td>
                                
                                @for ($i = 0; $i <= $semanas; $i++)
                                    <td>$ {{number_format($cxp_xsemana[$sem + $i],'2', '.',',')}}</td>
                                @endfor
                                        
                                <td>$ {{number_format($cxp_xsemana['RESTO'],'2', '.',',')}}</td>
                                
                            </tr>
                            <tr>
                                <td>DIFERIENCIA FLUJO</td>
                                <td>$ {{number_format($saldo - $total_cxp,'2', '.',',')}}</td>
                                <td>$ {{number_format($saldo - $cxp_xsemana['VENCIDO'],'2', '.',',')}}</td>
                                 @for ($i = 0; $i <= $semanas; $i++)
                                    
                                        <?php 
                                        $index = $sem + $i;
                                        $ba = $saldos_bancos[$index] - $cxp_xsemana[$index];
                                        
                                        ?>
                                    <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                        
                                @endfor
                                  <?php 
                                    $ba = $saldos_bancos['RESTO'] - $cxp_xsemana['RESTO'];
                                    ?>
                                <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                
                            </tr>
                            <tr>
                                @for ($i = 0; $i < $semanas + 4; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                             <tr>
                                <td>COMPROMETIDO</td>
                                <td>$ {{number_format($total_comprometido,'2', '.',',')}}</td>
                                <td>$ {{number_format($cxc_xsemana['VENCIDO'],'2', '.',',')}}</td>
                                 @for ($i = 0; $i <= $semanas; $i++)
                                    
                                        <?php 
                                        $index = $sem + $i;
                                        $ba = $cxc_xsemana[$index];
                                        
                                        ?>
                                    <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                        
                                @endfor
                                  <?php 
                                    $ba = $cxc_xsemana['RESTO'];
                                    ?>
                                <td>$ {{number_format($ba,'2', '.',',')}}</td>
                            </tr>
                             <tr>
                                <td>DIFERIENCIA TOTAL</td>
                                <td>$ {{number_format(($saldo - $total_cxp) + $total_comprometido,'2', '.',',')}}</td>
                                <td>$ {{number_format(($saldo - $cxp_xsemana['VENCIDO']) + $cxc_xsemana['VENCIDO'],'2', '.',',')}}</td>
                                 @for ($i = 0; $i <= $semanas; $i++)
                                    
                                        <?php 
                                        $index = $sem + $i;
                                        $ba = ($saldos_bancos[$index] - $cxp_xsemana[$index]) + $cxc_xsemana[$index];
                                        
                                        ?>
                                    <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                        
                                @endfor
                                  <?php 
                                    $ba = ($saldos_bancos['RESTO'] - $cxp_xsemana['RESTO']) + $cxc_xsemana['RESTO'];
                                    ?>
                                <td>$ {{number_format($ba,'2', '.',',')}}</td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="table-responsive">
                    <table id="table_cxc" class="table table-striped table-bordered nowrap" width="40%">
                       <tbody>
                           <tr><th colspan="3">CXC  (MXN)</th></tr>
                           <tr>
                               <td>OV</td>
                               <td>$ {{number_format($total_ov,'2', '.',',')}}</td>
                               <td>100%</td>
                           </tr>
                           <tr>
                               <td>COBRADO</td>
                               <td>$ {{number_format($total_cobrado,'2', '.',',')}}</td>
                               <td>{{number_format(($total_cobrado * 100) / $total_ov,'2', '.',',')}}%</td>
                           </tr>
                           <tr>
                               <td>COMPROMETIDO</td>
                               <td>$ {{number_format($total_comprometido,'2', '.',',')}}</td>
                               <td>{{number_format(($total_comprometido * 100) / $total_ov,'2', '.',',')}}%</td>
                           </tr>
                           <tr>
                               <td>ESTIMADO</td>
                               <td>$ {{number_format($total_estimado,'2', '.',',')}}</td>
                               <td>{{number_format(($total_estimado * 100) / $total_ov,'2', '.',',')}}%</td>
                           </tr>
                           <tr>
                               <td>NO PROGRAMADO</td>
                               <td>$ {{number_format($no_programado,'2', '.',',')}}</td>
                               <td>{{number_format(($no_programado * 100) / $total_ov,'2', '.',',')}}%</td>
                           </tr>
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
            $("#page-wrapper2").toggleClass("content"); 
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
