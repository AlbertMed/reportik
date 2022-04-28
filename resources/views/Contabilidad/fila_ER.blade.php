        <tr>
           
            <td nowrap style="{{$rep->RGC_estilo}}" class="row-id" scope="row" style="white-space: nowrap; width:6%; text-align:left">                
                {{$rep->BC_Cuenta_Id}}
            </td>
            <td class="row-nombre" scope="row" style="white-space:nowrap; width:12%; text-align:left">
                {{substr($rep->BC_Cuenta_Nombre, 0, 22)}}
            </td>
<!-- REAL -->
        @if ($periodo == '01')
      
            <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
      
        @else
            <td style="width:9%">$ {{number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]) -
                abs($rep->movimiento),'2', '.',',')}}
            </td>
        @endif
        <td style="width:9%" class="row-movimiento" scope="row">
            $ {{number_format(abs($rep->movimiento),'2', '.',',')}}
        </td>
      
        <td style="width:9%">$ {{number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]),'2', '.',',')}}</td>
        
<!-- PRESUPUESTO -->          
           
            @if ($periodo == '01')
          
                <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
          
            @else
                <td style="width:9%">$ {{number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]) -
                    abs($rep->movimiento_p),'2', '.',',')}}
                </td>
              
            @endif
            <td style="width:9%" class="row-movimiento" scope="row">
                $ {{number_format(abs($rep->movimiento_p),'2', '.',',')}}                
            </td>
            
            <td style="width:9%">$ {{number_format(abs($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]),'2', '.',',')}}</td>
           
<!-- DIFERIENCIA -->
         
            @if ($periodo == '01')
            <td style="width:9%;">$ {{number_format(0,'2', '.',',')}}</td>
      
            @else
            <td style="width:9%">$ {{number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2] -
            $rep->movimiento) - abs(($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]) -
            $rep->movimiento_p),'2', '.',',')}}
            </td>            
            @endif            
           
            <td style="width:9%" class="row-movimiento" scope="row">
                $ {{number_format(abs($rep->movimiento) - abs($rep->movimiento_p),'2', '.',',')}}
            </td>
            
            <td style="width:9%">$ {{number_format(abs($acumuladosxcta[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]) - 
            abs($acumuladosxcta_p[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2]),'2', '.',',')}}
            </td>
           
        </tr>
  

