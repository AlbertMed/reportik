   <h3 >Estado de Costos<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.''}}
@if (!isset($fecha_actualizado) || $fecha_actualizado == true)
    {{', '.$fechaA}}
@endif
</b></small></h3>
    <div class="row">
<div class="col-md-10">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">

    <tbody>
        <tr>
            <th style="width:40%; text-align: center;">Título</th>
            <th style="width:20%;">Anterior</th>
            <th style="width:20%;">{{$ejercicio.' - '.$nombrePeriodo}}</th>       
            <th style="width:20%;">Acumulado</th>             
        </tr>
        <?php $bnd = 0; ?>
        @for ($i = 0; $i < count($data_formulas_33); $i++)
        @if ($data_formulas_33[$i]->RGC_tipo_renglon == 'INPUT' && $bnd == 0)
            <?php $bnd = 1; ?>
            <tr style="color:teal">
                <td style="width:40%; font-weight: bold;">TOTAL INVENTARIO</td>
                <td style="width:20%;">{{number_format($total_inventarios_acum - $total_inventarios,'2', '.',',')}}</td>
                <td style="width:20%; font-weight: bold;">{{number_format($total_inventarios,'2', '.',',')}}</td>
                <td style="width:20%;">{{number_format($total_inventarios_acum,'2', '.',',')}}</td>
            
            </tr>
            <tr class="blank_row">
                <td colspan="4"></td>
            </tr>
            <tr class="blank_row">
                <td colspan="4"></td>
            </tr>
        @endif
        <tr style="{{$data_formulas_33[$i]->RGC_estilo}}" >
            <?php 
                $llave = trim($data_formulas_33[$i]->RGC_valor_default); 
            ?>
            <td style="width:40%; white-space: nowrap; font-weight: bold;">{{$data_formulas_33[$i]->RGC_tabla_titulo." (".$data_formulas_33[$i]->RGC_BC_Cuenta_Id.")"}}</td>
            <td style="width:20%;">{{number_format(($box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id.$data_formulas_33[$i]->RGC_BC_Cuenta_Id]) - ($box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id]),'2', '.',',')}}</td>
            <td style="width:20%; font-weight: bold;">{{number_format($box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id],'2', '.',',')}}</td>               
            <td style="width:20%;">{{number_format($box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id.$data_formulas_33[$i]->RGC_BC_Cuenta_Id],'2', '.',',')}}</td>
           
        </tr>
        @endfor
       
                       
    </tbody>
</table>
</div>


<div class="col-md-10">
 @if (count($llaves_invFinal) > 0)        
   <table class="table table-condensed table-espacio10" style="table-layout:fixed;">
        <tbody>
            <tr>
                <th colspan="2">INVENTARIOS</th>
            </tr>
@foreach ($llaves_invFinal as $key)
    
            <tr>
            <td style="font-weight: bold;">{{$key}}</td>
            <td style="font-weight: bold;">{{number_format($inv_Final[$key],'2', '.',',')}}</td>            
            </tr>
      
@endforeach  
</tbody>
    </table>
@endif
</div>
</div>
<br>