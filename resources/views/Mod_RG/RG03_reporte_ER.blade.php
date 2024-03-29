    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0; 
        $totalAnterior = 0;   
        $totalAcumulado = 0;    

        $finalAnterior = 0;        
        $finalEntrada = 0;        
        $finalAcumulado = 0; 
               
    ?>
    <div class="ocultar"><h3>Estado de Resultados<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio}}</b></small></h3></div>
 
@foreach ($hoja2 as $rep)

@if($index == 1) 
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->movimiento;
        if ($periodo == '01'){
            $totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
        }else {            
            $totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
        }
        $totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        
        $moneda = '';
    ?>
    <div class="row">
<div class="col-md-12">
<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
        <tr>
            <th colspan="2"  style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>         
            <th>Anterior</th>
            <th>%</th>
            <th>Movimiento Periodo</th>
            <th>%</th>
            <th>Acumulado</th>
            <th>%</th>   
        </tr>
@include('Mod_RG.fila_ER')
@elseif($llave == $rep->RGC_tabla_titulo)
    <?php                                                                    
        $totalEntrada += $rep->movimiento;
        $totalAcumulado += $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        
        if ($periodo == '01'){
            $totalAnterior += (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
        }else {            
            $totalAnterior += $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
        }
        // $moneda = $rep->MONEDA;
    ?>
    
@include('Mod_RG.fila_ER')    
@else
<!-- ES OTRO, SE CAMBIA LA LLAVE -->
    <tr>
        <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
        </th>
        <th>
            $ {{number_format($totalAnterior,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>{{number_format(($totalAnterior / 
        (($totalesIngresosGastos[0]['anterior'] == 0)? 1 : ($totalesIngresosGastos[0]['anterior']))) * 100 ,'2', '.',',')}}%</th>
        <th>
            $ {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>{{number_format(($totalEntrada / 
        (($totalesIngresosGastos[0]['periodo'] == 0)? 1 : ($totalesIngresosGastos[0]['periodo']))) * 100 ,'2', '.',',')}}%</th>
        <th>$ {{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
        <th>{{number_format(($totalAcumulado / 
        (($totalesIngresosGastos[0]['acumulado'] == 0)? 1 : ($totalesIngresosGastos[0]['acumulado']))) * 100 ,'2', '.',',')}}%</th>
    </tr>
</tbody>
</table>
</div> <!-- /.col-md-6 -->
    

<div class="col-md-12">
<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
       <tr>
            <th colspan="2" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>
            <th>Anterior</th>
            <th>%</th>
            <th>Movimiento Periodo</th>
            <th>%</th>
            <th>Acumulado</th>
            <th>%</th>
        </tr>
    <?php
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;   

        $finalAnterior += $totalAnterior;
        $finalEntrada += $totalEntrada;
        $finalAcumulado += $totalAcumulado;

        $totalEntrada = $rep->movimiento;    
        $totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        if ($periodo == '01'){
            $totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
        }else {
            $totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
        }
                                                     

    ?>
@include('Mod_RG.fila_ER')
@endif
@if($index == count($hoja2))
<tr>
    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
    </th>
    <th>
        $ {{number_format($totalAnterior,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>{{number_format(($totalAnterior / 
    (($totalesIngresosGastos[0]['anterior'] == 0)? 1 : ($totalesIngresosGastos[0]['anterior']))) * 100 ,'2', '.',',')}}%</th>
    <th>
        $ {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>{{number_format(($totalEntrada / 
    (($totalesIngresosGastos[0]['periodo'] == 0)? 1 : ($totalesIngresosGastos[0]['periodo']))) * 100 ,'2', '.',',')}}%</th>
    <th>$ {{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
    <th>{{number_format(($totalAcumulado / 
    (($totalesIngresosGastos[0]['acumulado'] == 0)? 1 : ($totalesIngresosGastos[0]['acumulado']))) * 100 ,'2', '.',',')}}%</th>
</tr>
</tbody>
</table>
<?php
        $finalAnterior += $totalAnterior;
        $finalEntrada += $totalEntrada;
        $finalAcumulado += $totalAcumulado;
    ?>
<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
        <tr>
            <th  style="text-align: center;">
            </th>
            <th>Anterior</th>
            <th>%</th>
            <th>Movimiento Periodo</th>
            <th>%</th>
            <th>Acumulado</th>
            <th>%</th>
        </tr>
        @foreach ( $totalesIngresosGastos as $rep )
        <tr>
           
            <td class="thh row-nombre" scope="row" style="text-align: right; white-space:nowrap; width:25%;">
                {{$rep['titulo']}}
            </td>
            <!-- Anterior = Acumulado - movimiento -->
            @if ($periodo == '01')
                <td style=" width:13%" class="thh">
                    $ {{number_format((array_key_exists($rep['titulo'], $box_anterior)) ? $box_anterior[$rep['titulo']] : 0,'2', '.',',')}}
                </td>
                <td class="thh">{{number_format((((array_key_exists($rep['titulo'], $box_anterior)) ? $box_anterior[$rep['titulo']] : 0) / (($totalesIngresosGastos[0]['anterior']) == 0)? 1 : ($totalesIngresosGastos[0]['anterior'])) * 100,'2', '.',',')}}%</td>
            @else
            <td class="thh" style="width:13%">$ {{number_format($rep['anterior'],'2', '.',',')}}                
            </td>
            <td class="thh">{{number_format(($rep['anterior'] / 
            (($totalesIngresosGastos[0]['anterior'] == 0)? 1 : ($totalesIngresosGastos[0]['anterior']))) * 100 ,'2', '.',',')}}%</td>
            @endif
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($rep['periodo'],'2', '.',',')}}
                <td class="thh">{{number_format(($rep['periodo'] / 
                (($totalesIngresosGastos[0]['periodo'] == 0)? 1 : ($totalesIngresosGastos[0]['periodo']))) * 100 ,'2', '.',',')}}%</td>
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($rep['acumulado'],'2', '.',',')}}</td>
            <td class="thh">{{number_format(($rep['acumulado'] / 
            (($totalesIngresosGastos[0]['acumulado'] == 0)? 1 : ($totalesIngresosGastos[0]['acumulado']))) * 100 ,'2', '.',',')}}%</td>
            <!-- porcentaje -->
           
        </tr>
        @endforeach
    </tbody>
</table>


</div> <!-- /.col-md-12 -->
</div> <!-- /.row -->
@endif
    <?php
        $index++;
    ?>
@endforeach

<br>