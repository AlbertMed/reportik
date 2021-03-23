<tr>
    <!-- renglon 3 -->

    <td style="{{$rep->RGC_estilo}}" class="row-id" scope="row">
        {{$rep->BC_Cuenta_Id}}
    </td>
    <td class="row-nombre" scope="row">
        {{$rep->BC_Cuenta_Nombre}}
    </td>
    <td>$ {{number_format($acumuladosxcta_hoja1[$rep->BC_Cuenta_Id],'2', '.',',')}}</td>


</tr>
        
  

