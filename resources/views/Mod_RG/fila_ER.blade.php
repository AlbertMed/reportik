        <tr>
            <!-- renglon 3 -->
            
            <td style="{{$rep->RGC_estilo}}" class="row-id" scope="row" style="width:10%">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row" style="white-space:nowrap; width:25%; text-align:left">
                {{$rep->BC_Cuenta_Nombre}}
            </td>
            <td style="width:15%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento,'2', '.',',')}}
            </td>
            <td>{{($totales_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format((($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento) / ($acumulados_hoja2[$rep->RGC_tabla_titulo] - $totales_hoja2[$rep->RGC_tabla_titulo]) ) * 100 ,'2', '.',',')}}%</td>
            <td style="width:15%" class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento,'2', '.',',')}}                
            </td>
            <td style="width:10%">{{($totales_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($rep->movimiento / $totales_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
            <td style="width:15%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>
            <td style="width:10%">{{($acumulados_hoja2[$rep->RGC_tabla_titulo] == 0) ? '0' :number_format(($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] / $acumulados_hoja2[$rep->RGC_tabla_titulo]) * 100 ,'2', '.',',')}}%</td>
        </tr>
  

