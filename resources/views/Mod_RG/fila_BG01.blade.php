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
           
        </tr>
  
