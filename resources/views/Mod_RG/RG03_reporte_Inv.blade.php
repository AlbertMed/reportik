<?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;    
        $totalAcumulado = 0;    
        $derecho = 0;        
        $izquierdo = 0;        
    ?>
    <div class="ocultar"><h3>Inventarios<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}} 
</b></small></h3></div>


@foreach ($data_inventarios_4 as $rep)

@if($index == 1)
<?php
        $llave = trim($rep->RGC_tabla_titulo);                         
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
                @elseif($llave == trim($rep->RGC_tabla_titulo))
                <?php                                                                    
        $totalEntrada += $rep->IC_COSTO_TOTAL;
       // $totalAcumulado += $acumuladosxcta[$rep->BC_Cuenta_Id];
     
    ?>

                @include('Mod_RG.fila_Inv')
                @else
                <!-- ES OTRO, SE CAMBIA LA LLAVE -->
                <?php                
                    $formulas = array_where($data_formulas_33, function ($key, $value) use ($llave){
                            return $value->RGC_tabla_titulo == $llave;
                    });                                                                   
                ?>
                @if (count($formulas) > 0)
                    @foreach (collect($formulas) as $formula)
                        <tr>
                            <td></td>
                            <td>{{$formula->RGC_descripcion_cuenta}}</td>
                            <td></td>
                            <td style="font-weight: bold; {{$formula->RGC_estilo}}">
                                {{eval("echo number_format((".$formula->RGC_valor_default. ")*".$formula->RGC_multiplica.",'2', '.',',');")}}
                            </td>
                        </tr>
                        <?php
                            eval('$total_inventarios_4 += (('.$formula->RGC_valor_default. ') *'.$formula->RGC_multiplica.');');
                            eval('$totalEntrada += (('.$formula->RGC_valor_default. ') *'.$formula->RGC_multiplica.');');
                        ?>
                    @endforeach
                @endif
                <tr>
                <th colspan="2" class="total enfasis encabezado" style="text-align: right;">Total @if (array_key_exists (str_replace (' ', '', $llave), $personalizacion)){{$personalizacion[str_replace (' ', '', $llave)]}} @endif:
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
        $llave = trim($rep->RGC_tabla_titulo);   
        $totalEntrada = $rep->IC_COSTO_TOTAL;    
       // $totalAcumulado = $acumuladosxcta[$rep->BC_Cuenta_Id];                                              
    ?>
                @include('Mod_RG.fila_Inv')
                @endif
                @if($index == count($data_inventarios_4))
                 <?php
                    $formulas = array_where($data_formulas_33, function ($key, $value) use ($llave){
                            return $value->RGC_tabla_titulo == $llave;
                    });                                                                   
                ?>
                @if (count($formulas) > 0)
                    @foreach (collect($formulas) as $formula)
                        <tr>
                            <td></td>
                            <td>{{$formula->RGC_descripcion_cuenta}}</td>
                            <td></td>
                            <td style="font-weight: bold; {{$formula->RGC_estilo}}">
                                {{eval("echo number_format((".$formula->RGC_valor_default. ")*".$formula->RGC_multiplica.",'2', '.',',');")}}
                            </td>
                        </tr>
                        <?php
                            eval('$total_inventarios_4 += (('.$formula->RGC_valor_default. ') *'.$formula->RGC_multiplica.');');
                            eval('$totalEntrada += (('.$formula->RGC_valor_default. ') *'.$formula->RGC_multiplica.');');
                        ?>
                    @endforeach
                @endif
                <tr>
                    <th colspan="2" class="total enfasis encabezado" style="text-align: right;">Total @if (array_key_exists (str_replace (' ', '', $llave), $personalizacion)){{$personalizacion[str_replace (' ', '', $llave)]}} @endif:
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
                        {{number_format($total_inventarios_4,'2', '.',',')}}</th>
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