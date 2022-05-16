<?php 
    $helper = AppHelper::instance();
    $cols = (int) $periodo + 3;
    $filas = 22;
?>
<div class="ocultar">
    <h3>Estado de Costos<small> Periodo: <b>{{$nombrePeriodo}}/{{$ejercicio.' '}}
    </b></small></h3>
</div>

<div class="row">
<div class="table-responsive">

<table id="tableCosto" class="display" style="width:100%">
    <thead>
                <tr >
                    <th style="width-disable:20%; text-align: center;">Concepto</th>
                    <th style="width-disable:10%;">Anterior</th>
                    @for ($i = 1; $i <= $periodo; $i++) 
                        <th style="width-disable:20%;">{{$helper->getNombrePeriodo($i)}}</th>
                    @endfor
                    <th style="width-disable:20%;">Acumulado</th>
                </tr>
    </thead>
    <tbody>
        
                    @for ($i = 0; $i < $filas; $i++) 
                        <tr>
                            @for ($j = 0; $j < $cols ; $j++) 
                            <td style="width-disable:20%;">{{$datosEstadoCostos[$j][$i]}}</td>
                        
                            @endfor
                        </tr>
            
                    @endfor
                
    </tbody>
</table>
</div>
</div>
<br>