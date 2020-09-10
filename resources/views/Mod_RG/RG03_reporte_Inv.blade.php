<?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $totalAcumulado = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>
<legend class="pull-left width-full">Inventarios</legend>
@foreach ($data_inventarios as $rep)

@if($index == 1)
<?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->IC_COSTO_TOTAL;
       // $totalAcumulado = $acumuladosxcta[$rep->BC_Cuenta_Id];
      
    ?>
<div class="row">
    <div class="col-md-11">
        <table class="table table-condensed table-espacio10" style="table-layout:fixed;">
            <tbody>
                <tr>
                    <th colspan="2" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
                    </th>
                    <th></th>
                    <th>
                       
                    </th>
                </tr>
                @include('Mod_RG.fila_Inv')
                @elseif($llave == $rep->RGC_tabla_titulo)
                <?php                                                                    
        $totalEntrada += $rep->IC_COSTO_TOTAL;
       // $totalAcumulado += $acumuladosxcta[$rep->BC_Cuenta_Id];
     
    ?>

                @include('Mod_RG.fila_Inv')
                @else
                <!-- ES OTRO, SE CAMBIA LA LLAVE -->
                <tr>
                    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL:
                    </th>
                    <th></th>
                    <th>
                        {{number_format($totalEntrada,'2', '.',',')}}
                    </th>
                    
                    
                </tr>
            </tbody>
        </table>
    </div> <!-- /.col-md-6 -->


    <div class="col-md-11">
        <table class="table table-condensed table-espacio10" style="table-layout:fixed;">
            <tbody>
             <tr>
                <th colspan="2" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
                </th>
                <th></th>
                <th>
                   
                </th>
            </tr>
                <?php
        $count_tabla++;
        $llave = $rep->RGC_tabla_titulo;   
        $totalEntrada = $rep->IC_COSTO_TOTAL;    
       // $totalAcumulado = $acumuladosxcta[$rep->BC_Cuenta_Id];                                              
    ?>
                @include('Mod_RG.fila_Inv')
                @endif
                @if($index == count($data_inventarios))
                <tr>
                    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">TOTAL:
                    </th>
                    <th> </th>
                    <th>
                        {{number_format($totalEntrada,'2', '.',',')}}
                    </th>                    
                    
                </tr>
            </tbody>
        </table>
        <table class="table-espacio10">
            <tbody>
                <tr>
                    <th style="text-align:right">GRAN TOTAL: $
                        {{number_format($total_inventarios,'2', '.',',')}}</th>
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