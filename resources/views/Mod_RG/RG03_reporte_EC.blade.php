    <?php
            $mermas = 0;
            $val1 = $mp_ini + $ctas_hoja3['COMPRAS NETAS'] + $mermas;
            $mp_utilizada = $val1 - $mp_fin;
            $mo = $input_mo + $ctas_hoja3['MO'];
            $indirectos = $input_indirectos - $ctas_hoja3['GASTOS IND'];
            $prod_proceso = $mp_utilizada + $mo + $indirectos;
            $val2 = $prod_proceso + $pp_ini;
            $producto_terminado = $val2 - $pp_fin;
            $val3 = $pt_ini + $producto_terminado;
            $costo_vendido = $val3 - $pt_fin;
    ?>
<legend class="pull-left width-full">Estado de Costos</legend>
    <div class="row">
<div class="col-md-6">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">
    <tbody>
        <tr>
            <th colspan="3"  style="text-align: center;">{{$ejercicio.' - '.$nombrePeriodo}}
            </th>                    
        </tr>
        <tr>
            <td style="font-weight: bold;">INV. INICIAL MP.</td>
            <td style="font-weight: bold;">{{number_format($mp_ini,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td>COMPRAS NETAS</td>
           <td>{{number_format($ctas_hoja3['COMPRAS NETAS'],'2', '.',',')}}</td>
           <td></td>
        </tr>
        <tr>
            <td>MERMAS</td>
            <td>{{number_format($mermas ,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>{{number_format($val1 ,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td style="color:teal">INV. FINAL MP.</td>
            <td style="color:teal">{{number_format($mp_fin,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td>MP UTILIZADA</td>
            <td style="color:red">{{number_format($mp_utilizada ,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td>M.O.</td>
            <td>{{number_format($mo,'2', '.',',')}}</td>
            <td style="background:yellow">{{number_format($input_mo * 1,'2', '.',',')}} (MAS)</td>
        </tr>
        <tr>
            <td>GASTOS INDIRECTOS</td>
             <td>{{number_format($indirectos ,'2', '.',',')}}</td>
             <td style="background:yellow">{{number_format($input_indirectos * 1,'2', '.',',')}} (MENOS)</td>
        </tr>
        <tr>
            <td>PROD EN PROCESO</td>
             <td>{{number_format($prod_proceso,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">INV. INICIAL PP.</td>
             <td style="font-weight: bold;">{{number_format($pp_ini,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td></td>
             <td>{{number_format($val2 ,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td style="color:teal">INV. FINAL PP.</td>
             <td style="color:teal">{{number_format($pp_fin,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td>PRODUCTO TERMINADO</td>
            <td>{{number_format($producto_terminado ,'2', '.',',')}}</td>
            <td></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">INV. INICIAL PT.</td>
             <td style="font-weight: bold;">{{number_format($pt_ini,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td></td>
             <td>{{number_format($val3,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td style="color:teal">INV. FINAL PT.</td>
             <td style="color:teal">{{number_format($pt_fin,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <td>COSTO DE LO VENDIDO</td>
             <td>{{number_format($costo_vendido ,'2', '.',',')}}</td>
             <td></td>
        </tr>
        <tr>
            <th>TOTAL INVENTARIO</th>
             <th>{{number_format($mp_fin + $pp_fin + $pt_fin,'2', '.',',')}}</th>
             <th></th>
        </tr>
        
    </tbody>
</table>
</div>
</div>
<br>