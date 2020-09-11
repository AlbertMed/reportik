   <legend class="pull-left width-full">Estado de Costos</legend>
    <div class="row">
<div class="col-md-6">
<table class="table table-condensed table-espacio10" style="table-layout:fixed;">
    <tbody>
        <tr>
            <th colspan="2"  style="text-align: center;">{{$ejercicio.' - '.$nombrePeriodo}}
            </th>                    
        </tr>
        @foreach ($data_formulas_33 as $item)
            <tr>
            <td style="font-weight: bold; {{$item->RGC_estilo}}">{{$item->RGC_tabla_titulo}}</td>
                <td style="font-weight: bold;">{{eval("echo number_format((".$item->RGC_descripcion_cuenta. ")*".$item->RGC_multiplica.",'2', '.',',');")}}</td>               
            </tr> 
        @endforeach   
        <tr>
            <th>TOTAL INVENTARIO</th>
            <th>{{number_format($total_inventarios,'2', '.',',')}}</th>
            
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