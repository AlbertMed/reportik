        <tr>
            <!-- renglon 3 -->
            
            <td style="{{$rep->RGC_estilo}}" class="row-id" scope="row" style="white-space: nowrap; width:18%; text-align:left">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row" style="white-space:nowrap; width:25%; text-align:left">
                {{$rep->BC_Cuenta_Nombre}}
            </td>
            <!-- Anterior = Acumulado - movimiento -->
            <td style="width:13%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento,'2', '.',',')}}
            </td>
            <!-- porcentaje -->
            <td>{{(($acumulados_hoja2[$rep->RGC_tabla_titulo] - $totales_hoja2[$rep->RGC_tabla_titulo]) == 0) ? '0' :number_format((($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento) / ($acumulados_hoja2[$rep->RGC_tabla_titulo] - $totales_hoja2[$rep->RGC_tabla_titulo]) ) * 100 ,'2', '.',',')}}%</td>
            <!-- movimiento periodo -->
            <td style="width:13%" class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento,'2', '.',',')}}                
            </td>
            <!-- porcentaje -->
            <td style="width:9%">{{($totales_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($rep->movimiento / $totales_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
            <!-- Acumulado -->
            <td style="width:13%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>
            <!-- porcentaje -->
            <td style="width:9%">{{($acumulados_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] / $acumulados_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
        </tr>
  

