    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0; 
        $totalAnterior = 0;   
        $totalAcumulado = 0;    

        $totalEntrada_p = 0; 
        $totalAnterior_p = 0;   
        $totalAcumulado_p = 0;   
        
        $totalGeneralEntrada = 0; 
        $totalGeneralAnterior = 0;   
        $totalGeneralAcumulado = 0;    

        $totalGeneralEntrada_p = 0; 
        $totalGeneralAnterior_p = 0;   
        $totalGeneralAcumulado_p = 0;   
        $moneda = '';
     
    ?>
<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}}
@if (count($hoja2) == 0)
    <div class="alert alert-danger" role="alert">
            No hay Cuentas, Captura Presupuesto.
        </div>
@endif
</b></small>
@foreach ($hoja2 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = abs($rep->movimiento);
        $totalEntrada_p = abs($rep->movimiento_p);
     
        if ($periodo == '01'){
            $totalAnterior = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
            $totalAnterior_p = (array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0;
             
        }else {            
            $totalAnterior = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - abs($rep->movimiento);
            $totalAnterior_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)] - abs($rep->movimiento_p);
        }
       
        $totalAcumulado = $acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        $totalAcumulado_p = $acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)];
        $totalAcumulado = abs($totalAcumulado);
        $totalAcumulado_p = abs($totalAcumulado_p);
        $totalAnterior = abs($totalAnterior);
        $totalAnterior_p = abs($totalAnterior_p);    
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
        $totalEntrada += abs($rep->movimiento);
        $totalEntrada_p += abs($rep->movimiento_p);
     
        $totalAcumulado += abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);
        $totalAcumulado_p += abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);
       
        if ($periodo == '01'){
            $totalAnterior += abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
            $totalAnterior_p += abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
        }else {            
            $totalAnterior += abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento);
            $totalAnterior_p += abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento_p);
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
            $ {{number_format($totalAnterior - $totalAnterior_p,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>
            $ {{number_format($totalEntrada - $totalEntrada_p,'2', '.',',')}}{{' '.$moneda}}
        </th>
        <th>$ {{number_format($totalAcumulado - $totalAcumulado_p,'2', '.',',')}}{{' '.$moneda}} </th>
       
        
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
        $totalGeneralEntrada += $totalEntrada;
        $totalGeneralAnterior += $totalAnterior;
        $totalGeneralAcumulado += $totalAcumulado;
        $totalGeneralEntrada_p += $totalEntrada_p;
        $totalGeneralAnterior_p += $totalAnterior_p;
        $totalGeneralAcumulado_p += $totalAcumulado_p;
        
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;       

        $totalEntrada = abs($rep->movimiento);    
        $totalEntrada_p = abs($rep->movimiento_p);    
        
        $totalAcumulado = abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);
        $totalAcumulado_p = abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]);          
        
        if ($periodo == '01'){
            $totalAnterior = abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
            $totalAnterior_p = abs((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0);
        }else {
            $totalAnterior = abs($acumuladosxcta[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento);
            $totalAnterior_p = abs($acumuladosxcta_p[trim($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2)]) - abs($rep->movimiento_p);
        }

    ?>
@include('Contabilidad.fila_ER')
@endif
@if($index == count($hoja2))
<?php
    $totalGeneralEntrada += $totalEntrada;
    $totalGeneralAnterior += $totalAnterior;
    $totalGeneralAcumulado += $totalAcumulado;
    $totalGeneralEntrada_p += $totalEntrada_p;
    $totalGeneralAnterior_p += $totalAnterior_p;
    $totalGeneralAcumulado_p += $totalAcumulado_p;
?>
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
        $ {{number_format($totalAnterior - $totalAnterior_p,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>
        $ {{number_format($totalEntrada - $totalEntrada_p,'2', '.',',')}}{{' '.$moneda}}
    </th>
    <th>$ {{number_format($totalAcumulado - $totalAcumulado_p,'2', '.',',')}}{{' '.$moneda}} </th>
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
       
        <tr>
<!-- REAL -->           
            <td class="thh row-nombre" scope="row" style="text-align: right; white-space:nowrap; width:25%;">
                TOTAL GENERAL
            </td>
            
            <td class="thh" style="width:13%">$ {{number_format($totalGeneralAnterior,'2', '.',',')}}                
            </td>
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($totalGeneralEntrada,'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($totalGeneralAcumulado,'2', '.',',')}}</td>
<!-- PRESUPUESTO -->                        
            <td class="thh" style="width:13%">$ {{number_format($totalGeneralAnterior_p,'2', '.',',')}}                
            </td>
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($totalGeneralEntrada_p,'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($totalGeneralAcumulado_p,'2', '.',',')}}</td>
           
<!-- DIFERIENCIA -->           
           
            <td class="thh" style="width:13%">$ {{number_format($totalGeneralAnterior - $totalGeneralAnterior_p,'2', '.',',')}}                
            </td>
           
            <!-- movimiento periodo -->
            <td style=" width:13%" class="thh row-movimiento" scope="row">
                $ {{number_format($totalGeneralEntrada - $totalGeneralEntrada_p ,'2', '.',',')}}
            </td>
         
            <!-- Acumulado -->
            <td class="thh" style=" width:13%">$ {{number_format($totalGeneralAcumulado - $totalGeneralAcumulado_p,'2', '.',',')}}</td>
           
        </tr>
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