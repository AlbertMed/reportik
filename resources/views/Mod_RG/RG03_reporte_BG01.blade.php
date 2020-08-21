    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>    

<legend class="pull-left width-full">Posición Financiera, Balance General</legend>

@foreach ($hoja1 as $rep)

@if($index == 1)
    <?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $acumuladosxcta_hoja1[$rep->BC_Cuenta_Id];
        $moneda = '';
    ?>
    <div class="row">
<div class="col-md-6">
<table class="table table-condensed">
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
<table class="table table-condensed" >
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
@if($index == count($hoja1))
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
</div> <!-- /.row -->
@endif
    <?php
        $index++;
    ?>
@endforeach
<div class="row">
<div class="col-md-6"> 
<table>
    <tbody>
        <tr>
            <th style="text-align:right">TOTAL ACTIVOS:  $ {{number_format($izquierdo,'2', '.',',')}}</th>   
        </tr>
    </tbody>
</table>
</div> <!-- /.col-md-6 -->
<div class="col-md-6"> 
<table>
    <tbody>
        <tr>
            <th style="text-align:right">TOTAL PASIVOS Y CAPITAL:  $ {{number_format($derecho,'2', '.',',')}}</th>   
        </tr>
    </tbody>
</table>
</div> <!-- /.col-md-6 -->
</div> <!-- /.row -->
<br>