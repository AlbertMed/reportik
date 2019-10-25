<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ '013 pdf' }}</title>
    <style>
        /*
                Generic Styling, for Desktops/Laptops
                */
table { width: 100%; border-collapse: collapse; font-family: arial;
 } th { color: white; font-weight: bold; color: white;
font-family: 'Helvetica'; font-size: 12px; background-color: #333333; } td { font-family: 'Helvetica'; font-size: 11px; }

img { display: block; margin-top: 1%; width: 670; height: 45; position: absolute; right: 2%; } 
h5 { font-family: 'Helvetica';
margin-bottom: 2px; margin-top: 0; } 
     #header { position: fixed; margin-top: 2px; }
        #content {
            position: relative;
            top: 10%
        }
         table,
        th,
        td {
            text-align: center;
            border: 1px solid black;
           
            
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .zrk-silver{
            background-color: #AFB0AE;
            color: black;
        }
        .zrk-dimgray{
            background-color: #514d4a;
            color: white;
        }
        .zrk-gris-claro{
            background-color: white;
            color: black;
        }
        .zrk-silver-w{
            background-color: #656565;
            color: white;
        }
        .table > thead > tr > th, 
        .table > tbody > tr > th, 
        .table > tfoot > tr > th, 
        .table > thead > tr > td, 
        .table > tbody > tr > td,
        .table > tfoot > tr > td { 
            padding-bottom: 2px; padding-top: 2px; padding-left: 4px; padding-right: 0px;
        }
        .total{
            text-align: right; 
            padding-right:4px;
        }
        h2,h3 ,h4{
            
            padding: 0px;
            margin: 0px;
            
        }
    </style>
</head>

<body>

    <div id="header">
        <img src="images/Mod01_Produccion/logoiteknia.png">
        <!--empieza encabezado, continua cuerpo-->
        <table border="1px" class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <td colspan="6" align="center" bgcolor="#fff">
                       
                        <h2><b>013 {{env('EMPRESA_NAME')}}</b></h2>
                        <h2>Reporte de Entradas a Almacén Artículos y Miceláneas (COMPRAS)</h2>
                        <h3><b>Del:</b> {{\AppHelper::instance()->getHumanDate(array_get($fechas_entradas,'fi'))}} <b>al:</b> {{\AppHelper::instance()->getHumanDate(array_get($fechas_entradas,'ff'))}}</h3>

                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
           @if(count($entradas)>0)
            <div class="row">
               
                <div class="col-md-8">                               
                        <?php $totalEntrada = 0;
                            $index = 0; ?>
                            @foreach ($entradas as $rep) 
                           
                            @if($index == 0)                                                               
                            <h4>Entradas {{$rep->MONEDA}}</h4>
                                <?php                                    
                                    $moneda = $rep->MONEDA;
                                    $DocN = $rep->ORDEN; 
                                    $totalEntrada = $rep->IMPORTE;
                                ?>
                                <table class="table table-striped" style="table-layout:fixed;">            
                                <thead  class="table-condensed">
                                    <tr style="width:100%">
                                        <th style="width:7%">ORDEN</th>
                                        <th style="width:8%">F_RECIBO</th>
                                        <th style="width:8%">CLIENTE</th>
                                        <th style="width:27%">RAZON_SOC</th>
                                        <th style="width:30%">PROYECTO</th>
                                        <th style="width:20%" class="zrk-gris">NOTAS</th>
                                    </tr>
                              </thead>  
                            </table>  

                              <table class="table table-striped" style="table-layout:fixed;">                             
                                   <thead class="table-condensed">
                                    <tr style="width:100%">
                                        <th style="width:7%" class="zrk-gris-claro">CODE</th>
                                        <th style="width:33%; text-align: left" class="zrk-gris-claro">ARTICULO</th>
                                        <th style="width:7%" class="zrk-gris-claro">FACT</th>
                                        <th style="width:5%" class="zrk-gris-claro">UMI</th>
                                        <th style="width:8%" class="zrk-gris-claro">CANTIDAD</th>

                                        <th style="width:9%" class="zrk-gris-claro">COSTO_OC</th>
                                        <th style="width:10%" class="zrk-gris-claro">IMPORTE</th>
                                        <th style="width:8%" class="zrk-gris-claro">MONEDA</th>
                                        <th style="width:13%" class="zrk-gris-claro">NOM_EMPL</th>
                                    </tr>
            
                                </thead>
                            </table>

                             <table class="table table-striped" style="table-layout:fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width:7%" class="zrk-silver-w" scope="row">
                                            {{$rep->ORDEN}}
                                        </td>
                                        <td style="width:8%" class="zrk-silver-w" scope="row">
                                            {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                                        </td>
                                        <td style="width:8%" class="zrk-silver-w" scope="row">
                                            {{$rep->CLIENTE}}
                                        </td>
                                        <td style="width:27%" class="zrk-silver-w" scope="row">
                                            {{substr($rep->RAZON_SOC, 0, 37)}}
                                        </td>                                        
                                        <td style="width:30%" class="zrk-silver-w" scope="row">
                                            {{substr($rep->PROYECTO, 0, 39)}}
                                        </td>
                                        <td style="width:20%" class="zrk-silver-w" scope="row">
                                            {{$rep->NOTAS}}
                                        </td>
                                    </tr>
                                   </tbody>
                                   </table>

                                    <table class="table table-striped" style="table-layout:fixed;">
                                        <tbody>
                                    <tr>
                                        <td style="width:7%" class="zrk-gris-claro" scope="row">
                                            {{$rep->CODE_ART}}
                                        </td>
                                        <td style="width:33%; text-align:left" class="zrk-gris-claro" scope="row">
                                            {{substr($rep->ARTICULO,0,45)}}
                                        </td>
                                        <td style="width:7%" class="zrk-gris-claro" scope="row">
                                            {{$rep->FACT}}
                                        </td>
                                        <td style="width:5%" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMI}}
                                        </td>
                                        <td style="width:8%" class="zrk-gris-claro" scope="row">
                                            {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                        </td>
                                        <td style="width:9%" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                        </td>
                                        <td style="width:10%" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->IMPORTE,'2', '.',',')}}
                                        </td>
                                        <td style="width:8%" class="zrk-gris-claro" scope="row">
                                            {{$rep->MONEDA}}
                                        </td>
                                        <?php
                                            $name = explode(' ', $rep->NOM_EMPL);
                                            $name = $name[0].' '.$name[1];
                                        ?>
                                        <td style="width:13%" class="zrk-gris-claro" scope="row" >
                                           {{$name}}
                                        </td>
                                    </tr>
                               
            
                        @elseif($DocN == $rep->ORDEN)

                            <?php
                                $totalEntrada += $rep->IMPORTE;
                                $moneda = $rep->MONEDA;
                            ?>                                        
                       <tr>
                            <td style="width:7%" class="zrk-gris-claro" scope="row">
                                {{$rep->CODE_ART}}
                            </td>
                            <td style="width:33%; text-align: left" class="zrk-gris-claro" scope="row">
                                {{substr($rep->ARTICULO,0,45)}}
                            </td>
                            <td style="width:7%" class="zrk-gris-claro" scope="row">
                                {{$rep->FACT}}
                            </td>
                            <td style="width:5%" class="zrk-gris-claro" scope="row">
                                {{$rep->UMI}}
                            </td>
                            <td style="width:8%" class="zrk-gris-claro" scope="row">
                                {{number_format($rep->CANTIDAD,'0', '.',',')}}
                            </td>
                            <td style="width:9%" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                            </td>
                            <td style="width:10%" class="zrk-gris-claro" scope="row">
                                ${{number_format($rep->IMPORTE,'2', '.',',')}}
                            </td>
                            <td style="width:8%" class="zrk-gris-claro" scope="row">
                                {{$rep->MONEDA}}
                            </td>
                            <?php
                                $name = explode(' ', $rep->NOM_EMPL);
                                $name = $name[0].' '.$name[1];
                            ?>
                            <td style="width:13%" class="zrk-gris-claro" scope="row" >
                               {{$name}}
                            </td>
                        </tr>
                              
                        @else
                                        <tr>

                                                <td colspan="6" class="total zrk-gris-claro">Total:</td>
                                                <td class="zrk-gris-claro">${{number_format($totalEntrada,'2', '.',',')}} </td>
                                                <td  class="zrk-gris-claro">{{$moneda}}</td>
                                                <td  class="zrk-gris-claro"></td>
                                            </tr>
                                        <?php
                        $DocN = $rep->ORDEN;
                        $totalEntrada = $rep->IMPORTE;
                    ?>
             </tbody>
            </table>

            @if($moneda <> $rep->MONEDA)
                <h4>Entradas {{$rep->MONEDA}}</h4>
                <?php                                    
                                                    $moneda = $rep->MONEDA;
                                                    $DocN = $rep->ORDEN; 
                                                    $totalEntrada = $rep->IMPORTE;
                                                ?>
                <table class="table table-striped" style="table-layout:fixed;">
                    <thead class="table-condensed">
                        <tr style="width:100%">
                            <th style="width:7%">ORDEN</th>
                            <th style="width:8%">F_RECIBO</th>
                            <th style="width:8%">CLIENTE</th>
                            <th style="width:27%">RAZON_SOC</th>
                            <th style="width:30%">PROYECTO</th>
                            <th style="width:20%" class="zrk-gris">NOTAS</th>
                        </tr>
                    </thead>
                </table>
                
                <table class="table table-striped" style="table-layout:fixed;">
                    <thead class="table-condensed">
                        <tr style="width:100%">
                            <th style="width:7%" class="zrk-gris-claro">CODE</th>
                            <th style="width:33%; text-align: left" class="zrk-gris-claro">ARTICULO</th>
                            <th style="width:7%" class="zrk-gris-claro">FACT</th>
                            <th style="width:5%" class="zrk-gris-claro">UMI</th>
                            <th style="width:8%" class="zrk-gris-claro">CANTIDAD</th>
                
                            <th style="width:9%" class="zrk-gris-claro">COSTO_OC</th>
                            <th style="width:10%" class="zrk-gris-claro">IMPORTE</th>
                            <th style="width:8%" class="zrk-gris-claro">MONEDA</th>
                            <th style="width:13%" class="zrk-gris-claro">NOM_EMPL</th>
                        </tr>
                
                    </thead>
                </table>
            @else
            @endif

             <table class="table table-striped" style="table-layout:fixed;">
                    <tbody>
                            <tr>
                                    <td style="width:7%" class="zrk-silver-w" scope="row">
                                        {{$rep->ORDEN}}
                                    </td>
                                    <td style="width:8%" class="zrk-silver-w" scope="row">
                                        {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                                    </td>
                                    <td style="width:8%" class="zrk-silver-w" scope="row">
                                        {{$rep->CLIENTE}}
                                    </td>
                                    <td style="width:27%" class="zrk-silver-w" scope="row">
                                        {{substr($rep->RAZON_SOC, 0, 37)}}
                                    </td>
                                    
                                    <td style="width:30%" class="zrk-silver-w" scope="row">
                                        {{substr($rep->PROYECTO, 0, 39)}}
                                    </td>
                                    <td style="width:20%" class="zrk-silver-w" scope="row">
                                        {{$rep->NOTAS}}
                                    </td>
                                </tr>
                               </tbody>
                               </table>

                                <table class="table table-striped" style="table-layout:fixed;">
                                    <tbody>
                                <tr>
                                    <td style="width:7%" class="zrk-gris-claro" scope="row">
                                        {{$rep->CODE_ART}}
                                    </td>
                                    <td style="width:33%; text-align: left" class="zrk-gris-claro" scope="row">
                                        {{substr($rep->ARTICULO,0,45)}}
                                    </td>
                                    <td style="width:7%" class="zrk-gris-claro" scope="row">
                                        {{$rep->FACT}}
                                    </td>
                                    <td style="width:5%" class="zrk-gris-claro" scope="row">
                                        {{$rep->UMI}}
                                    </td>
                                    <td style="width:8%" class="zrk-gris-claro" scope="row">
                                        {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                    </td>
                                    <td style="width:9%" class="zrk-gris-claro" scope="row">
                                        ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                    </td>
                                    <td style="width:10%" class="zrk-gris-claro" scope="row">
                                        ${{number_format($rep->IMPORTE,'2', '.',',')}}
                                    </td>
                                    <td style="width:8%" class="zrk-gris-claro" scope="row">
                                        {{$rep->MONEDA}}
                                    </td>
                                    <?php
                                        $name = explode(' ', $rep->NOM_EMPL);
                                        $name = $name[0].' '.$name[1];
                                    ?>
                                    <td style="width:13%" class="zrk-gris-claro" scope="row" >
                                       {{$name}}
                                    </td>
                                </tr>
               
                                            @endif 
                                            @if($index == count($entradas)-1)
                                            <tr>

                                                    <td colspan="6" class="total zrk-gris-claro">Total:</td>
                                                    <td class="zrk-gris-claro">${{number_format($totalEntrada,'2', '.',',')}}</td>
                                                    <td  class="zrk-gris-claro">{{$moneda}}</td>
                                                    <td class="zrk-gris-claro"></td>  
                                                </tr>
            
                                            @endif
                                            <?php
                                                $index++;
                                            ?>
                                                @endforeach
                                              
                     </tbody>
                    </table>
                </div>
            
            </div>
            @endif 
           
          
        </div>
                <footer>
                    
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 

                        $empresa = 'Sociedad: <?php echo 'ITEKNIA EQUIPAMIENTO S.A. de C.V.'; ?>';
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'Almacen_Reporte_013.Pdf'; 
                        
                        $pdf->page_text(40, 23, $empresa, $font, 9);
                        $pdf->page_text(580, 23, $date, $font, 9);  

                        $pdf->page_text(35, 580, $text, $font, 9);                         
                        $pdf->page_text(630, 580, $tittle, $font, 9);                                                 
                    </script>
                </footer>
                @yield('subcontent-01')
</body>

</html>