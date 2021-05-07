    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>    

<h3>Posición Financiera, Balance General <small>Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}} {{$fechaA}}</b></small></h3>

@foreach ($hoja1 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2];
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
        $totalEntrada += $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2];
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
    @if($count_tabla % 2 == 0)
    </div> <!-- /.row -->
    <div class="row">
        <?php
                $derecho +=  $totalEntrada                                                         
                        ?>
        @else
            <?php
                  $izquierdo += $totalEntrada                                                
                ?>
    @endif
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
        $totalEntrada = $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2];                                                  
    ?>
@include('Mod_RG.fila_BG01')
@endif
@if($index == count($hoja1))
<tr>
    <!-- EN LA TABLA DE RESULTADOS - Este concepto se mete a mano aqui por que no tiene cuenta -->
    <td class="row-id" scope="row">
       
    </td>
    <td class="row-nombre" scope="row">
        UTILIDAD O PERDIDA DEL EJERCICIO
    </td>
    <td>$ {{number_format($utilidadEjercicio,'2', '.',',')}}</td>
</tr>
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
    @if($count_tabla % 2 == 0)

        <?php
                    $derecho +=  $totalEntrada                                                         
        ?>
    @else
        <?php
                    $izquierdo += $totalEntrada                                                
        ?>
    @endif
@endif
    <?php
        $index++;
    ?>
@endforeach
<div class="row">
<div class="col-md-6"> 
<table class="table-espacio10">
    <tbody>
        <tr>
            <th style="text-align:right">TOTAL ACTIVOS:  $ {{number_format($izquierdo,'2', '.',',')}}</th>   
        </tr>
    </tbody>
</table>
</div> <!-- /.col-md-6 -->
<div class="col-md-6"> 
<table class="table-espacio10">
    <tbody>
        <tr>
            <th style="text-align:right">TOTAL PASIVOS Y CAPITAL:  $ {{number_format($derecho + $utilidadEjercicio,'2', '.',',')}}</th>   
        </tr>
    </tbody>
</table>
</div> <!-- /.col-md-6 -->
</div> <!-- /.row -->
<br>