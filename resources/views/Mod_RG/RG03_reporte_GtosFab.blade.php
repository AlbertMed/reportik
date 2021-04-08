    <?php
        $index = 1;       
        $totalEntrada = 0;    
        $totalAcumulado = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>
<h3>Gastos de Fabricación<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}} {{$fechaA}}</b></small></h3>
@foreach ($hoja5 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->movimiento;
        $totalAcumulado = $acumuladosxcta_hoja5[trim($rep->BC_Cuenta_Id)];
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
@include('Mod_RG.fila_GtosFab')
@elseif($llave == $rep->RGC_tabla_titulo)
    <?php                                                                    
        $totalEntrada += $rep->movimiento;
        $totalAcumulado += $acumuladosxcta_hoja5[trim($rep->BC_Cuenta_Id)];
       // $moneda = $rep->MONEDA;
    ?>
    
@include('Mod_RG.fila_GtosFab')    
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
 </div>
 
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
    <?php     
        $llave = $rep->RGC_tabla_titulo;   
        $totalEntrada = $rep->movimiento;    
        $totalAcumulado = $acumuladosxcta_hoja5[trim($rep->BC_Cuenta_Id)];                                              
    ?>
@include('Mod_RG.fila_GtosFab')
@endif
@if($index == count($hoja5))
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