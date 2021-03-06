<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ '003-A pdf' }}</title>
    <style>
       table { width: 100%; border-collapse: collapse; font-family: arial;
    } th { color: white; font-weight: bold; color: white;
    font-family: 'Helvetica'; font-size: 12px; background-color: #333333; } td { font-family: 'Helvetica'; font-size: 11px;
    }
    
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
        <img src="images/logo.png">
        <!--empieza encabezado, continua cuerpo-->
        <table border="1px" class="table table-striped">
            <thead class="thead-dark">
                <tr>
                   <td colspan="6" align="center" bgcolor="#fff">
                    
                        <h2><b>003-A {{$sociedad}}</b></h2>
                        <h2>Reporte Precios Materias Primas ({{$tipo}})</h2>
                        <h3><b> {{'FECHA DE IMPRESION: ' . \AppHelper::instance()->getHumanDate_format(date( "Y-m-d h:i:s"), 'h:i A')}}</b></h3>
                    
                    </td>
                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
            <div class="row">                
                    <table border="1px" class="table ">
                        <thead class="table  table-bordered table-condensed">
                            <tr>
                                <th align="center" bgcolor="#474747" style="color:white" ;scope="col">CODIGO</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">NOMBRE</th>
                                <th align="center" bgcolor="#474747" style="color:white" ;scope="col">FAMILIA</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">SUB_CAT</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">UDM</th>
                                
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">EXISTENCIA</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">ESTANDAR</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">PROMEDIO</th>
                                <th align="center" bgcolor="#474747" style="color:white" ; scope="col">U_COMPRA</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($data)>0) 
                            @foreach ($data as $rep)
                            <tr>
                                <td  style="width:5%" align="center" scope="row">
                                    {{$rep->CODIGO}}
                                </td>
                                <td style="width:29%; text-align: left" scope="row">
                                    {{$rep->NOMBRE}}
                                </td>
                                <td  style="width:10%" align="center" scope="row">
                                    {{ $rep->FAMILIA }}
                                </td>
                                <td  style="width:7%" align="center" scope="row">
                                    {{ $rep->SUB_CAT }}
                                </td>                        
                                <td  style="width:4%" align="center" scope="row">
                                    {{ $rep->UDM }}
                                </td>                        
                                <td  style="width:6%" align="center" scope="row">                                    
                                    {{number_format($rep->EXISTENCIA,'2', '.',',')}}
                                </td>                        
                                <td  style="width:6%" align="center" scope="row">                                    
                                    {{number_format($rep->ESTANDAR,'2', '.',',')}}
                                </td>                        
                                <td  style="width:6%" align="center" scope="row">                                  
                                    {{number_format($rep->PROMEDIO,'2', '.',',')}}
                                </td>                        
                                <td  style="width:6%" align="center" scope="row">                             
                                    {{number_format($rep->U_COMPRA,'2', '.',',')}}
                                </td>                        
                            </tr>
                            @endforeach 
                            @endif
                        </tbody>
                    </table>



                </div>
                </div>


                <footer>
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 
                        $nombre ='REPORTE PRECIOS MATERIAS PRIMAS';       
                        $empresa = '003-A';
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'REPORTIK '; 
                        
                        $pdf->page_text(40, 23, $empresa, $font, 9);
                        $pdf->page_text(580, 23, $date, $font, 9);  
                        $pdf->page_text(325, 23, $nombre, $font, 9);  

                        $pdf->page_text(365, 580, $text, $font, 9);                         
                        $pdf->page_text(700, 580, $tittle, $font, 9);                                                 
                    </script>
                </footer>
                @yield('subcontent-01')

</body>

</html>