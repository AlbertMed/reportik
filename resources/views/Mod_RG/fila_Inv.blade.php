       
            <!-- renglon 3 -->
            
        <tr>
            <td class="row-id" scope="row">                
                {{$rep->LOC_CodigoLocalidad}}
            </td>
            <td class="row-nombre" scope="row">
                {{$rep->IC_LOC_Nombre}}
            </td>
            <td></td>
            <td class="row-movimiento" scope="row">
                {{number_format($rep->IC_COSTO_TOTAL,'2', '.',',')}}                
            </td>
          
        </tr>
  

