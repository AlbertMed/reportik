        <tr>
            <!-- renglon 3 -->
            
            <td nowrap style="{{$rep->RGC_estilo}}" class="row-id" scope="row" style="white-space: nowrap; width:6%; text-align:left">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row" style="white-space:nowrap; width:12%; text-align:left">
                {{substr($rep->BC_Cuenta_Nombre, 0, 22)}}
            </td>
           <!-- Anterior = Acumulado - movimiento -->
        @if ($periodo == '01')
       <!-- <td style="width:20%;">$ {{number_format(((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2),
            $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0),'2', '.',',')}}</td>
         porcentaje 
        <td>{{number_format( ((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'), $box_anterior)) ?
            $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'] : 0),'2', '.',',')}}%</td>
       -->
        <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
        <!--<td>0.00 %</td>-->
       @else
        <td style="width:9%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] -
            $rep->movimiento,'2', '.',',')}}
        </td>

        <!-- porcentaje 
        <td>{{(($acumulados_hoja2[$rep->RGC_tabla_titulo] - $totales_hoja2[$rep->RGC_tabla_titulo]) == 0) ? '0'
            :number_format((($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento) /
            ($acumulados_hoja2[$rep->RGC_tabla_titulo] - $totales_hoja2[$rep->RGC_tabla_titulo]) ) * 100 ,'2', '.',',')}}%</td>
        -->
        @endif
        
        <!-- movimiento periodo -->
        <td style="width:9%" class="row-movimiento" scope="row">
            $ {{number_format($rep->movimiento,'2', '.',',')}}
        </td>
        <!-- porcentaje 
        <td style="width:9%">{{($totales_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($rep->movimiento /
            $totales_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
        -->
        <!-- Acumulado -->
        <td style="width:9%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>
        <!-- porcentaje 
        <td style="width:9%">{{($acumulados_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0'
            :number_format(($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] /
            $acumulados_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
        -->
<!--  -->          
            
            <!-- Anterior = Acumulado - movimiento -->
            @if ($periodo == '01')
           <!-- porcentaje      <td style="width:20%;">$ {{number_format(((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior_p)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0),'2', '.',',')}}</td>
                
                <td>{{number_format( ((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'), $box_anterior_p)) ? $box_anterior_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'] : 0),'2', '.',',')}}%</td>
           --> 
           <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
           <!--<td>0.00 %</td>-->
           @else
                <td style="width:9%">$ {{number_format($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] -
                    $rep->movimiento_p,'2', '.',',')}}
                </td>
                <!-- porcentaje 
                <td>{{(($acumulados_hoja2_p[$rep->RGC_tabla_titulo] - $totales_hoja2_p[$rep->RGC_tabla_titulo]) == 0) ? '0' :number_format((($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento_p) / ($acumulados_hoja2_p[$rep->RGC_tabla_titulo] - $totales_hoja2_p[$rep->RGC_tabla_titulo]) ) * 100 ,'2', '.',',')}}%</td>
                -->
            @endif
                        
            <!-- movimiento periodo -->
            <td style="width:9%" class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento_p,'2', '.',',')}}                
            </td>
            <!-- porcentaje 
            <td style="width:9%">{{($totales_hoja2_p[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($rep->movimiento_p / $totales_hoja2_p[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
           --> <!-- Acumulado -->
            <td style="width:9%">$ {{number_format($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>
            <!-- porcentaje 
            <td style="width:9%">{{($acumulados_hoja2_p[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] / $acumulados_hoja2_p[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
           -->
<!-- -->
            <!-- Anterior = Acumulado - movimiento -->
            @if ($periodo == '01')
            <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
      
            @else
            <td style="width:9%">$ {{number_format(($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] -
                $rep->movimiento_p) - ($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] -
            $rep->movimiento),'2', '.',',')}}
            </td>            
            @endif            
            <!-- movimiento periodo -->
            <td style="width:9%" class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento_p - $rep->movimiento,'2', '.',',')}}
            </td>
            
            <!-- Acumulado -->
            <td style="width:9%">$ {{number_format($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}
            </td>
           
        </tr>
  

