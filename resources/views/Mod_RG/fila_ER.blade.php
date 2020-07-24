        <tr>
            <!-- renglon 3 -->
            
            <td class="row-id" scope="row">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row">
                {{$rep->BC_Cuenta_Nombre}}
            </td>
            <td class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento,'2', '.',',')}}                
            </td>
            <td>{{($totales_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($rep->movimiento / $totales_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
            <td>$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id],'2', '.',',')}}</td>
            <td>{{($acumulados_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($acumuladosxcta[$rep->BC_Cuenta_Id] / $acumulados_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
        </tr>
  

