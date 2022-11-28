        <tr>
            <!-- renglon 3 -->
            
            <td style="{{$rep->RGC_estilo}}" class="row-id" scope="row" style="white-space: nowrap; width:18%; text-align:left">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row" style="white-space:nowrap; width:25%; text-align:left">
                {{$rep->BC_Cuenta_Nombre}}
            </td>
            <!-- Anterior = Acumulado - movimiento -->
            @if ($periodo == '01')
                <td style="width:20%;">$ {{number_format(((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] : 0),'2', '.',',')}}</td>
                <!-- porcentaje -->
                <td>{{number_format( ((array_key_exists(($rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'), $box_anterior)) ? $box_anterior[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2.'%'] : 0),'2', '.',',')}}%</td>
            @else
                <td style="width:13%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento,'2', '.',',')}}
                </td>
                <!-- porcentaje -->
                <td>{{number_format((($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] - $rep->movimiento) / 
                (($totalesIngresosGastos[0]['anterior'] == 0)? 1 : ($totalesIngresosGastos[0]['anterior'])))  * 100 ,'2', '.',',')}}%</td>
            @endif
                        
            <!-- movimiento periodo -->
            <td style="width:13%" class="row-movimiento" scope="row">
                $ {{number_format($rep->movimiento,'2', '.',',')}}                
            </td>
            <!-- porcentaje -->
            <td style="width:9%">{{number_format(($rep->movimiento / 
            (($totalesIngresosGastos[0]['periodo'] == 0)? 1 : ($totalesIngresosGastos[0]['periodo']))) * 100 ,'2', '.',',')}}%</td>
            <!-- Acumulado -->
            <td style="width:13%">$ {{number_format($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>
            <!-- porcentaje -->
            <td style="width:9%">{{number_format(($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] / 
            (($totalesIngresosGastos[0]['acumulado'] == 0)? 1 : ($totalesIngresosGastos[0]['acumulado']))) * 100 ,'2', '.',',')}}%</td>
        </tr>
  

