
@foreach ($hoja1_temp as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id];
        $moneda = '';
    ?>
    <div class="row">
<div class="col-md-6">
<table class="table table-condensed table-espacio10">
    <tbody>
        <tr>
            <th colspan="3"  style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>            
        </tr>
@include('Mod_RG.fila_BG01')

@elseif($llave == $rep->RGC_tabla_titulo)
    <?php                                                                    
        $totalEntrada += $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id];
       // $moneda = $rep->MONEDA;
    ?>
    
@include('Mod_RG.fila_BG01')
    
@else
<!-- ES OTRO, SE CAMBIA LA LLAVE -->
        
    <tr>
        <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
        </th>
        <th>
            $ {{number_format($totalEntrada,'2', '.',',')}}{{' '.$moneda}}
        </th>
    </tr>
</tbody>
</table>
</div> <!-- /.col-md-6 -->
  <!-- ////// -->

<div class="col-md-6">
<table class="table table-condensed table-espacio10" >
    <tbody>
        <tr>
            <th colspan="3" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
            </th>
        </tr>
    <?php
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;   
        $totalEntrada = $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id];                                                  
    ?>
@include('Mod_RG.fila_BG01')
@endif
@if($index == count($hoja1_temp))
@if ($utilidadEjercicio !== 0 && $pasivos == true)
    <tr>
        <!-- EN LA TABLA DE RESULTADOS - Este concepto se mete a mano aqui por que no tiene cuenta -->
        <td class="row-id" scope="row">
    
        </td>
        <td class="row-nombre" scope="row">
            UTILIDAD O PERDIDA DEL EJERCICIO
        </td>
        <td>$ {{number_format($utilidadEjercicio,'2', '.',',')}}</td>
    </tr>
@endif

<tr>
    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL {{$llave}}:
    </th>
    <th>
        $ {{number_format($totalEntrada + $utilidadEjercicio,'2', '.',',')}}{{' '.$moneda}}
    </th>
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