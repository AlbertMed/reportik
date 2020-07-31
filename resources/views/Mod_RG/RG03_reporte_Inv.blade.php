<?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $totalAcumulado = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>

@foreach ($data_inventarios as $rep)

@if($index == 1)
<?php
        $llave = $rep->RGC_tabla_titulo;                         
        $totalEntrada = $rep->IC_COSTO_TOTAL;
       // $totalAcumulado = $acumuladosxcta[$rep->BC_Cuenta_Id];
      
    ?>
<div class="row">
    <div class="col-md-11">
        <table class="table table-condensed " style="table-layout:fixed;">
            <tbody>
                <tr>
                    <th colspan="2" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
                    </th>
                    <th></th>
                    <th>
                        @if (strpos($rep->RGC_tabla_titulo, 'M.P.') !== false)
                            {{$mp_fin}}
                        @endif
                        @if (strpos($rep->RGC_tabla_titulo, 'P.P.') !== false)
                            {{$pp_fin}}
                        @endif
                        @if (strpos($rep->RGC_tabla_titulo, 'P.T.') !== false)
                            {{$pt_fin}}
                        @endif
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
        <table class="table table-condensed" style="table-layout:fixed;">
            <tbody>
             <tr>
                <th colspan="2" style="text-align: center;">{{$rep->RGC_tabla_titulo}}
                </th>
                <th></th>
                <th>
                    @if (strpos($rep->RGC_tabla_titulo, 'M.P.') !== false)
                    {{$mp_fin}}
                    @endif
                    @if (strpos($rep->RGC_tabla_titulo, 'P.P.') !== false)
                    {{$pp_fin}}
                    @endif
                    @if (strpos($rep->RGC_tabla_titulo, 'P.T.') !== false)
                    {{$pt_fin}}
                    @endif
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
    </div> <!-- /.col-md-6 -->
</div> <!-- /.row -->
@endif
<?php
        $index++;
    ?>
@endforeach

<br>