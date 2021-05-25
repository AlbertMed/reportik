<tr>
    <!-- renglon 3 -->

    <td style="white-space: nowrap; {{$rep->RGC_estilo}}" class="row-id" scope="row">
        {{$rep->BC_Cuenta_Id}}
    </td>
    <td style="white-space: nowrap;" class="row-nombre" scope="row">
        {{$rep->BC_Cuenta_Nombre}}
    </td>
    <td>$ {{number_format($acumuladosxcta_hoja1[$rep->BC_Cuenta_Id.$rep->RGC_BC_Cuenta_Id2],'2', '.',',')}}</td>


</tr>
        
  

