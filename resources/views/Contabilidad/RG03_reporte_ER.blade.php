    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0; 
        $totalAnterior = 0;   
        $totalAcumulado = 0;    

        $totalEntrada_p = 0; 
        $totalAnterior_p = 0;   
        $totalAcumulado_p = 0;     
     
    ?>
<h3>Estado de Resultados<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}}
@if (!isset($fecha_actualizado) || $fecha_actualizado == true)
{{$fechaA}}
@endif
</b></small></h3>
@foreach ($hoja2 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->movimiento;
        $totalEntrada_p = $rep->movimiento_p;
     
        if ($periodo == '01'){
            $totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
            $totalAnterior_p = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
             
        }else {            
            $totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
            $totalAnterior_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento_p;
        }
       
        $totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        $totalAcumulado_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        
        $moneda = '';
    ?>
    <div class="row">
<div class="col-md-12">
<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
        <tr>
            <th style="text-align: center;">#Cuenta
            </th>
            <th style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>         
            <th>Anterior</th>
            <!--<th>%</th>-->
            <th>Mes</th>
            <!--<th>%</th>-->
            <th>Acumulado</th>
            <!--<th>%</th>-->   
            <th>Presupuesto Anterior</th>
            <!--<th>%</th>-->
            <th>Presupuesto Mes</th>
            <!--<th>%</th>-->
            <th>Presupuesto Acumulado</th>
            <!--<th>%</th>-->   
            <th>Diferiencia Anterior</th>
            <th>Diferiencia Mes</th>
            <th>Diferiencia Acumulado</th>
        </tr>
@include('Contabilidad.fila_ER')
@elseif($llave == $rep->RGC_tabla_titulo)
    <?php                                                                    
        $totalEntrada += $rep->movimiento;
        $totalEntrada_p += $rep->movimiento_p;
     

        $totalAcumulado += $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        $totalAcumulado_p += $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
       
        if ($periodo == '01'){
            $totalAnterior += (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
            $totalAnterior_p += (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
        }else {            
            $totalAnterior += $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
            $totalAnterior_p += $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento_p;
        }
       
    ?>
    
@include('Contabilidad.fila_ER')    
@else
<!-- ES OTRO, SE CAMBIA LA LLAVE -->
    <tr>
        <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
        </th>
        <th>
            $ {{number_format($totalAnterior,'2', '.',',')}}{{' '.$moneda}}
        </th> 
        <!--<th>100%</th>  -->
        <th>
            $ {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
        </th>   
        <!--<th>100%</th>        -->
        <th>$ {{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
        <!--<th>100%</th>-->
        <th>
            $ {{number_format($totalAnterior_p,'2', '.',',')}}{{' '.$moneda}}
        </th> 
        <!--<th>100%</th>  -->
        <th>
            $ {{number_format($totalEntrada_p,'2', '.',',')}}{{' '.$moneda}}
        </th>   
        <!--<th>100%</th>        -->
        <th>$ {{number_format($totalAcumulado_p,'2', '.',',')}}{{' '.$moneda}} </th>
        <!--<th>100%</th>-->
        <th>
            $ {{number_format($totalAnterior_p - $totalAnterior,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>
            $ {{number_format($totalEntrada_p - $totalEntrada,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>$ {{number_format($totalAcumulado_p - $totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
       
        
    </tr>
</tbody>
</table>
</div> <!-- /.col-md-6 -->
    

<div class="col-md-12">
<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
       <tr>
            <th style="text-align: center;">#Cuenta
            </th>
            <th style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>
            <th>Anterior</th>
            <!--<th>%</th>-->
            <th>Mes</th>
            <!--<th>%</th>-->
            <th>Acumulado</th>
            <!--<th>%</th>   -->
            <th>Presupuesto Anterior</th>
            <!--<th>%</th>-->
            <th>Presupuesto Mes</th>
            <!--<th>%</th>-->
            <th>Presupuesto Acumulado</th>
            <!--<th>%</th>   -->
            <th>Diferiencia Anterior</th>
            <th>Diferiencia Mes</th>
            <th>Diferiencia Acumulado</th>
        </tr>
    <?php
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;       

        $totalEntrada = $rep->movimiento;    
        $totalEntrada_p = $rep->movimiento_p;    
        
        $totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        $totalAcumulado_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];  
        
        if ($periodo == '01'){
            $totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
            $totalAnterior_p = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
        }else {
            $totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento;
            $totalAnterior_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - $rep->movimiento_p;
        }
      
    ?>
@include('Contabilidad.fila_ER')
@endif
@if($index == count($hoja2))
<tr>
    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
    </th>
    <th>
        $ {{number_format($totalAnterior,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <!--<th>100%</th>-->
    <th>
        $ {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <!--<th>100%</th>-->
    <th>$ {{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
    <!--<th>100%</th>-->
    <th>
        $ {{number_format($totalAnterior_p,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <!--<th>100%</th>-->
    <th>
        $ {{number_format($totalEntrada_p,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <!--<th>100%</th>-->
    <th>$ {{number_format($totalAcumulado_p,'2', '.',',')}}{{' '.$moneda}} </th>
    <!--<th>100%</th>-->
    <th>
        $ {{number_format($totalAnterior_p - $totalAnterior,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>
        $ {{number_format($totalEntrada_p - $totalEntrada,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>$ {{number_format($totalAcumulado_p - $totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
</tr>
</tbody>
</table>

<table class="table table-condensed table-espacio10" style="table-layout:auto;">
    <tbody>
        <tr>
           
            <th style="text-align: center;">
            </th>
            <th>Anterior</th>            
            <th>Mes</th>            
            <th>Acumulado</th>

            <th>Presupuesto Anterior</th>            
            <th>Presupuesto Mes</th>            
            <th>Presupuesto Acumulado</th>

            <th>Diferiencia Anterior</th>            
            <th>Diferiencia Mes</th>            
            <th>Diferiencia Acumulado</th>
           
        </tr>
        @foreach ( $totalesIngresosGastos as $rep )
        <tr>
           
            <td class="thh row-nombre" scope="row" style="text-align: right; white-space:nowrap; width:25%;">
                {{$rep['titulo']}}
            </td>
            <!-- Anterior = Acumulado - movimiento -->
            @if ($periodo == '01')
                <!--<td style=" width:13%" class="thh">
                    $ {{number_format((array_key_exists($rep['titulo'], $box_anterior)) ? $box_anterior[$rep['titulo']] : 0,'2', '.',',')}}
                </td>-->
                <td style=" width:13%" class="thh">
                    $ {{number_format(0,'2', '.',',')}}
                </td>
            @else
            <td class="thh" style="width:13%">$ {{number_format($rep['anterior'],'2', '.',',')}}                
            </td>
            @endif
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($rep['periodo'],'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($rep['acumulado'],'2', '.',',')}}</td>
            
            @if ($periodo == '01')
                <!--<td style=" width:13%" class="thh">
                    $ {{number_format((array_key_exists($rep['titulo'], $box_anterior)) ? $box_anterior[$rep['titulo']] : 0,'2', '.',',')}}
                </td>-->
                <td style=" width:13%" class="thh">
                    $ {{number_format(0,'2', '.',',')}}
                </td>
            @else
            <td class="thh" style="width:13%">$ {{number_format($rep['anterior_p'],'2', '.',',')}}                
            </td>
            @endif
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($rep['periodo_p'],'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($rep['acumulado_p'],'2', '.',',')}}</td>
           
           
            @if ($periodo == '01')
                <!--<td style=" width:13%" class="thh">
                    $ {{number_format((array_key_exists($rep['titulo'], $box_anterior)) ? $box_anterior[$rep['titulo']] : 0,'2', '.',',')}}
                </td>-->
                <td style=" width:13%" class="thh">
                    $ {{number_format(0,'2', '.',',')}}
                </td>
            @else
            <td class="thh" style="width:13%">$ {{number_format($rep['anterior_p'] - $rep['anterior'],'2', '.',',')}}                
            </td>
            @endif
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($rep['periodo_p'] - $rep['periodo'],'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($rep['acumulado_p'] - $rep['acumulado'],'2', '.',',')}}</td>
           
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