   <legend class="pull-left width-full">Estado de Costos</legend>
    <div class="row">
<div class="col-md-6">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">
    <tbody>
        <tr>
            <th colspan="3"  style="text-align: center;">{{$ejercicio.' - '.$nombrePeriodo}}
            </th>                    
        </tr>
        @for ($i = 0; $i < count($data_formulas_33); $i++)
        <tr>
            <?php 
                $llave = trim($data_formulas_33[$i]->RGC_valor_default); 
            ?>
            <td style="font-weight: bold; {{$data_formulas_33[$i]->RGC_estilo}}">{{$data_formulas_33[$i]->RGC_tabla_titulo." (".$data_formulas_33[$i]->RGC_BC_Cuenta_Id.")"}}</td>
            <td style="font-weight: bold;">{{$box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id]}}</td>               
            @if ($i+1 < count($data_formulas_33))
                    @if (is_numeric(strpos($llave, trim($data_formulas_33[$i + 1]->RGC_valor_default))))
                        <?php 
                            $i++;                          
                        ?>
                        <td style="font-weight: bold; {{$data_formulas_33[$i]->RGC_estilo}}">{{$box[$data_formulas_33[$i]->RGC_BC_Cuenta_Id]}} ({{$data_formulas_33[$i]->RGC_descripcion_cuenta}})</td>
                    @else                
                        <td></td>               
                    @endif
            @else
                <td></td>
            @endif
            </tr>
        @endfor
       
        <tr>
            <th>TOTAL INVENTARIO</th>
            <th>{{number_format($total_inventarios,'2', '.',',')}}</th>
        <th></th>
        </tr>               
    </tbody>
</table>
</div>


<div class="col-md-6">
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