    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $totalAcumulado = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>
<h3>Gastos de Administración<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}}
@if (!isset($fecha_actualizado) || $fecha_actualizado == true)
{{$fechaA}}
@endif
</b></small></h3>
@foreach ($hoja6 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->movimiento;
        $totalAcumulado = $acumuladosxcta_hoja6[trim($rep->BC_Cuenta_Id)];
        $moneda = '';
    ?>
    <div class="row">
<div class="col-md-11">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">
    <tbody>
        <tr>
            <th>CC</th>
            <th style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>         
            <th>{{$nombrePeriodo}}/{{$ejercicio}}</th>
            <th>Acumulado</th>
        </tr>
@include('Mod_RG.fila_GtosAdmon')
@elseif($llave == $rep->RGC_tabla_titulo)
    <?php                                                                    
        $totalEntrada += $rep->movimiento;
        $totalAcumulado += $acumuladosxcta_hoja6[trim($rep->BC_Cuenta_Id)];
       // $moneda = $rep->MONEDA;
    ?>
    
@include('Mod_RG.fila_GtosAdmon')    
@else
<!-- ES OTRO, SE CAMBIA LA LLAVE -->
    <tr>
        <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
        </th>
        <th>
            {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
        </th>   
               
        <th>{{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
       
    </tr>
</tbody>
</table>
</div> <!-- /.col-md-6 -->
    

<div class="col-md-11">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">
    <tbody>
      <tr>
        <th>CC</th>
        <th style="text-align: center;">{{$rep->RGC_tabla_titulo}}
        </th>
        <th>{{$nombrePeriodo}}/{{$ejercicio}}</th>
        <th>Acumulado</th>
    </tr>
    <?php
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;   
        $totalEntrada = $rep->movimiento;    
        $totalAcumulado = $acumuladosxcta_hoja6[trim($rep->BC_Cuenta_Id)];                                              
    ?>
@include('Mod_RG.fila_GtosAdmon')
@endif
@if($index == count($hoja6))
<tr>
    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
    </th>
    <th>
        {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
    </th>
   
    <th>{{number_format($totalAcumulado,'2', '.',',')}}{{' '.$moneda}} </th>
    
</tr>
</tbody>
</table>
</div> <!-- /.col-md-6 -->
</div> <!-- /.row -->
@endif
    <?php
        $index++;
    ?>
@endforeach

<br>