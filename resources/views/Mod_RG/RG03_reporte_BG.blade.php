    <?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0;          
        $pasivos = false;   
        $hoja1_temp = $hoja1_activos;
        $ctas = array_column($hoja1_temp, 'BC_Cuenta_Id');    
        $total = array_sum(array_map(function ($key) use ($acumuladosxcta_hoja1) {
        return $acumuladosxcta_hoja1[$key];
        }, $ctas));
        
    ?>    

<legend class="pull-left width-full">Posición Financiera, Balance General (Activos)</legend>

@include('Mod_RG.RG03_reporte_BG02')
<div class="row">

    <table class="table-espacio10">
        <tbody>
            <tr>
                <th style="text-align:right">TOTAL ACTIVOS: $ {{number_format($total,'2', '.',',')}}</th>
            </tr>
        </tbody>
    </table>
</div> <!-- /.row -->

<div class="page_break"></div>
<?php
        $index = 1;
        $count_tabla = 1;
        $totalEntrada = 0; 
        $total = 0; 
        $pasivos = true;  
        $hoja1_temp = $hoja1_pasivos;  
        
        $ctas = array_column($hoja1_temp, 'BC_Cuenta_Id');
        $total = array_sum(array_map(function ($key) use ($acumuladosxcta_hoja1) {
        return $acumuladosxcta_hoja1[$key];
        }, $ctas));  
?>

<legend class="pull-left width-full">Posición Financiera, Balance General (Pasivos)</legend>

@include('Mod_RG.RG03_reporte_BG02')
  
<div class="row">  
        <table class="table-espacio10">
            <tbody>
                <tr>
                    <th style="text-align:right">TOTAL PASIVOS: $
                        {{number_format($total + $utilidadEjercicio,'2', '.',',')}}</th>
                </tr>
            </tbody>
        </table>
    
</div> <!-- /.row -->