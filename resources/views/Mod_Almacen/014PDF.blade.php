<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ '014-A pdf' }}</title>
    <style>
        
                
table { width: 100%; border-collapse: collapse; font-family: arial; } th { color: white; font-weight: bold; color: white;
font-family: 'Helvetica'; font-size: 12px; background-color: #333333; } td { font-family: 'Helvetica'; font-size: 11px; }
img { display: block; margin-top: 0%; width: 670; height: 45; position: absolute; right: 2%; } h5 { font-family: 'Helvetica';
margin-bottom: 15; } .fz { font-size: 100%; margin-top: 7px; } #header { position: fixed; margin-top: 2px; }
        #content {
            position: relative;
            top: 8%
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
            background-color: #eeeeee;
            color: black;
        }
        .zrk-silver-w{
            background-color: #656565;
            color: white;
        }
        h2,h3,h4{
            
            padding: 2px;
            margin: 2px;
            
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
                       
                        <h2><b>14-A {{env('EMPRESA_NAME')}}</b></h2>
                        <h2>Reporte de Inventario General</h2>
                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
    <!--Cuerpo o datos de la tabla-->
    <div id="content">
     
        @if(count($data)>0)
        <div class="row">
            
            
            <?php
                        $index = 0;
                        //$totalEntrada = 0;
                        //$moneda = 'Pesos';   
                    ?>
            @foreach ($data as $rep)
            @if($index == 0)
            <?php
                $llave = $rep->LLAVE; 
                //$totalEntrada = $rep->IMPORTE;
            ?>
            <h4>{{$rep->ALMACEN.' / '. $rep->LOCALIDAD.' / '.$rep->NOM_LOCAL}}</h4>
            <table class="table table-striped" style="table-layout:fixed;">
                <thead class="table-condensed">
                    <tr style="width:100%">
                        <th align="center" bgcolor="#474747" style="color:white; width:7%" ;scope="col">CODIGO</th>
                        <th  bgcolor="#474747" style="color:white; width:62%; text-align: left; padding-left:6px" ;scope="col">NOMBRE</th>    
                        <th align="center" bgcolor="#474747" style="color:white; width:7%" ;scope="col">UM_INV</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">EXISTE</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">COS_EST</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">IMPORTE</th>
                    </tr>
                </thead>
            </table>
    
            <table class="table table-striped" style="table-layout:fixed;">
                <tbody>
                    <tr>
                      
                        <td align="center" style="width:7%" scope="row">
                            {{$rep->CODIGO}}
                        </td>
                        <td style="text-align:left; width:62%; padding-left:6px" scope="row">
                            {{$rep->NOMBRE}}
                        </td>
                        
                        <td align="center" style="width:7%" scope="row">
                            {{$rep->UM_Inv}}
                        </td>
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->EXISTE,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->COS_EST,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format(($rep->COS_EST * $rep->EXISTE),'2', '.',',')}}
                        </td>
                    </tr>
    
                    @elseif($llave == $rep->LLAVE)
                    <?php
                               // $totalEntrada += $rep->IMPORTE;
                               // $moneda = $rep->MONEDA;
                            ?>
    
                    <tr>
                      
                        <td align="center" style="width:7%" scope="row">
                            {{$rep->CODIGO}}
                        </td>
                        <td style="text-align:left; width:62%; padding-left:6px" scope="row">
                            {{$rep->NOMBRE}}
                        </td>
                        
                        <td align="center" style="width:7%" scope="row">
                            {{$rep->UM_Inv}}
                        </td>
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->EXISTE,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->COS_EST,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format(($rep->COS_EST * $rep->EXISTE),'2', '.',',')}}
                        </td>
                    </tr>
    
                    @else
                  
                    <?php
                                $llave = $rep->LLAVE;
                            //    $totalEntrada = $rep->IMPORTE;
                     ?>
                </tbody>
            </table>
            <h4>{{$rep->ALMACEN.' / '. $rep->LOCALIDAD.' / '.$rep->NOM_LOCAL}}</h4>
            <table class="table table-striped" style="table-layout:fixed;">
                <thead class="table-condensed">
                    <tr style="width:100%">
                        <th align="center" bgcolor="#474747" style="color:white; width:7%" ;scope="col">CODIGO</th>
                        <th bgcolor="#474747" style="color:white; width:62%; text-align: left; padding-left:6px" ;scope="col">NOMBRE</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:7%" ;scope="col">UM_INV</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">EXISTE</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">COS_EST</th>
                        <th align="center" bgcolor="#474747" style="color:white; width:8%" ;scope="col">IMPORTE</th>
                    </tr>
                </thead>
            </table>
            <table class="table table-striped" style="table-layout:fixed;">
                <tbody>
                    <tr>
                       <td align="center" style="width:7%" scope="row">
                            {{$rep->CODIGO}}
                        </td>
                        <td style="text-align:left; width:62%; padding-left:6px" scope="row">
                            {{$rep->NOMBRE}}
                        </td>
                        
                        <td align="center" style="width:7%" scope="row">
                            {{$rep->UM_Inv}}
                        </td>
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->EXISTE,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format($rep->COS_EST,'2', '.',',')}}
                        </td>
                        
                        <td align="center" style="width:8%" scope="row">
                            {{number_format(($rep->COS_EST * $rep->EXISTE),'2', '.',',')}}
                        </td>
                    </tr>
    
                    @endif
                   
                    <?php
                            $index++;
                        ?>
                    @endforeach
    
                </tbody>
            </table>
        </div>
        @endif
    </div> 
                <footer>
                    
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 

                        $empresa = 'Sociedad: <?php echo 'ITEKNIA EQUIPAMIENTO S.A. de C.V.'; ?>';
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'Inventario_General_14-A.Pdf'; 
                        
                        $pdf->page_text(40, 23, $empresa, $font, 9);
                        $pdf->page_text(580, 23, $date, $font, 9);  

                        $pdf->page_text(35, 580, $text, $font, 9);                         
                        $pdf->page_text(630, 580, $tittle, $font, 9);                                                 
                    </script>
                </footer>
                @yield('subcontent-01')
</body>

</html>