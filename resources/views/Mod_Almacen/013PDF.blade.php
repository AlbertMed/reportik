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
table { width: 100%; border-collapse: collapse; font-family: arial; } th { color: white; font-weight: bold; color: white;
font-family: 'Helvetica'; font-size: 12px; background-color: #333333; } td { font-family: 'Helvetica'; font-size: 11px; }
img { display: block; margin-top: 3.8%; width: 670; height: 45; position: absolute; right: 2%; } h5 { font-family: 'Helvetica';
margin-bottom: 15; } .fz { font-size: 100%; margin-top: 7px; } #header { position: fixed; margin-top: 2px; }
        #content {
            position: relative;
            top: 20%
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
                        <h2>013</h2>
                        <h2><b>{{env('EMPRESA_NAME')}}</b></h2>
                        <h2>Reporte de Entradas a Almacén Artículos y Miceláneas (COMPRAS)</h2>
                        <h3><b>Del:</b> {{\AppHelper::instance()->getHumanDate(array_get($fechas_entradas,'fi'))}} <b>al:</b> {{\AppHelper::instance()->getHumanDate(array_get($fechas_entradas,'ff'))}}</h3>

                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
           @if(count($entradasMXP)>0)
            <div class="row">
                <h4>Entradas (Pesos)</h4>
                <div class="col-md-8">
                    <table class="table table-striped" style="table-layout:fixed;">
            
            
                        <?php
                                        $index = 0;
                                        $totalEntrada = 0;
                                        $moneda = 'Pesos';   
                                    ?>
                            @foreach ($entradasMXP as $rep) @if($index == 0)
                            <?php
                                            $DocN = $rep->ORDEN; 
                                            $totalEntrada = 0;
                                        ?>
                                <thead class="table-condensed">
                                    <tr>
                                        <th style="width:100px" class="zrk-gris" scope="col">ORDEN</th>
                                        <th style="width:120px" class="zrk-gris" scope="col">F_RECIBO</th>
                                        <th style="width:110px" class="zrk-gris" scope="col">CLIENTE</th>
                                        <th style="width:457px" class="zrk-gris" scope="col" colspan="3">RAZON_SOC</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">NOTAS</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">C_PROY</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">PROYECTO</th>
                                    </tr>
                                    <tr>
                                        <th style="width:60px" class="zrk-gris-claro">CODE_ART</th>
                                        <th style="width:450px" class="zrk-gris-claro" colspan="2">ARTICULO</th>
                                        <th class="zrk-gris-claro">UMC</th>
                                        <th style="width:70px" class="zrk-gris-claro">FACT</th>
                                        <th style="width:70px" class="zrk-gris-claro">UMI</th>
                                        <th style="width:70px" class="zrk-gris-claro">CANTIDAD</th>
                                        <th style="width:100px" class="zrk-gris-claro">COSTO</th>
                                        <th style="width:100px" class="zrk-gris-claro">MONEDA</th>
                                        <th style="width:100px" class="zrk-gris-claro">COSTO_OC</th>
                                        <th style="width:100px" class="zrk-gris-claro">C_EMPL</th>
                                        <th style="width:100px" class="zrk-gris-claro">NOM_EMPL</th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
            
                                <tbody>
                                    <tr>
                                        <td style="width:100px" class="zrk-silver-w" scope="row">
                                            {{$rep->ORDEN}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row">
                                            {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                                        </td>
                                        <td style="width:110px" class="zrk-silver-w" scope="row">
                                            {{$rep->CLIENTE}}
                                        </td>
                                        <td style="width:457px" class="zrk-silver-w" scope="row" colspan="3">
                                            {{$rep->RAZON_SOC}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->NOTAS}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->C_PROY}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->PROYECTO}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:60px" class="zrk-gris-claro" scope="row">
                                            {{$rep->CODE_ART}}
                                        </td>
                                        <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                            {{$rep->ARTICULO}}
                                        </td>
                                        <td style="width:57px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMC}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->FACT}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMI}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO,'2', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->MONEDA}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                           {{$rep->C_EMPL}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                           {{$rep->NOM_EMPL}}
                                        </td>
                                    </tr>
            
            
                                    @elseif($DocN == $rep->ORDEN)
                                    <?php
                            $totalEntrada += 0;
                            $moneda = $rep->MONEDA;
                        ?>
                                       <tr>
                                        <td style="width:60px" class="zrk-gris-claro" scope="row">
                                            {{$rep->CODE_ART}}
                                        </td>
                                        <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                            {{$rep->ARTICULO}}
                                        </td>
                                        <td style="width:57px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMC}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->FACT}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMI}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO,'2', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->MONEDA}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                            {{$rep->C_EMPL}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                            {{$rep->NOM_EMPL}}
                                        </td>
                                    </tr>
                                        @else
                                       
                                        <?php
                        $DocN = $rep->ORDEN;
                        $totalEntrada = 0;
                    ?>
            
                      <tr>
                        <td style="width:100px" class="zrk-silver-w" scope="row">
                            {{$rep->ORDEN}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row">
                            {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                        </td>
                        <td style="width:110px" class="zrk-silver-w" scope="row">
                            {{$rep->CLIENTE}}
                        </td>
                        <td style="width:457px" class="zrk-silver-w" scope="row" colspan="3">
                            {{$rep->RAZON_SOC}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                            {{$rep->NOTAS}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                            {{$rep->C_PROY}}
                        </td>
                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                            {{$rep->PROYECTO}}
                        </td>
                    </tr>
                    <tr>
                        <td style="width:60px" class="zrk-gris-claro" scope="row">
                            {{$rep->CODE_ART}}
                        </td>
                        <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                            {{$rep->ARTICULO}}
                        </td>
                        <td style="width:57px" class="zrk-gris-claro" scope="row">
                            {{$rep->UMC}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            {{$rep->FACT}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            {{$rep->UMI}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            {{number_format($rep->CANTIDAD,'0', '.',',')}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->COSTO,'2', '.',',')}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            {{$rep->MONEDA}}
                        </td>
                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                            ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                        </td>
                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                            {{$rep->C_EMPL}}
                        </td>
                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                            {{$rep->NOM_EMPL}}
                        </td>
                    </tr>
                                            @endif @if($index == count($entradasMXP)-1)
                                          
            
                                            @endif
                                            <?php
                    $index++;
                    ?>
                                                @endforeach
                    </table>
                </div>
            
            </div>
            @endif 
            @if(count($entradasUSD)>0)
            <div class="row">
                <h4>Entradas (Dolar)</h4>
                <div class="col-md-8">
                    <table class="table table-striped" style="table-layout:fixed;">
            
            
                        <?php
                                                    $index = 0;
                                                    $totalEntrada = 0;
                                                    $moneda = 'Pesos';   
                                                ?>
                            @foreach ($entradasUSD as $rep) @if($index == 0)
                            <?php
                                                        $DocN = $rep->ORDEN; 
                                                        $totalEntrada = 0;
                                                    ?>
                                <thead class="table-condensed">
                                    <tr>
                                        <th style="width:100px" class="zrk-gris" scope="col">ORDEN</th>
                                        <th style="width:120px" class="zrk-gris" scope="col">F_RECIBO</th>
                                        <th style="width:110px" class="zrk-gris" scope="col">CLIENTE</th>
                                        <th style="width:457px" class="zrk-gris" scope="col" colspan="3">RAZON_SOC</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">NOTAS</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">C_PROY</th>
                                        <th style="width:120px" class="zrk-gris" scope="col" colspan="2">PROYECTO</th>
                                    </tr>
                                    <tr>
                                        <th style="width:60px" class="zrk-gris-claro">CODE_ART</th>
                                        <th style="width:450px" class="zrk-gris-claro" colspan="2">ARTICULO</th>
                                        <th class="zrk-gris-claro">UMC</th>
                                        <th style="width:70px" class="zrk-gris-claro">FACT</th>
                                        <th style="width:70px" class="zrk-gris-claro">UMI</th>
                                        <th style="width:70px" class="zrk-gris-claro">CANTIDAD</th>
                                        <th style="width:100px" class="zrk-gris-claro">COSTO</th>
                                        <th style="width:100px" class="zrk-gris-claro">MONEDA</th>
                                        <th style="width:100px" class="zrk-gris-claro">COSTO_OC</th>
                                        <th style="width:100px" class="zrk-gris-claro">C_EMPL</th>
                                        <th style="width:100px" class="zrk-gris-claro">NOM_EMPL</th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
            
                                <tbody>
                                    <tr>
                                        <td style="width:100px" class="zrk-silver-w" scope="row">
                                            {{$rep->ORDEN}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row">
                                            {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                                        </td>
                                        <td style="width:110px" class="zrk-silver-w" scope="row">
                                            {{$rep->CLIENTE}}
                                        </td>
                                        <td style="width:457px" class="zrk-silver-w" scope="row" colspan="3">
                                            {{$rep->RAZON_SOC}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->NOTAS}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->C_PROY}}
                                        </td>
                                        <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                            {{$rep->PROYECTO}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:60px" class="zrk-gris-claro" scope="row">
                                            {{$rep->CODE_ART}}
                                        </td>
                                        <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                            {{$rep->ARTICULO}}
                                        </td>
                                        <td style="width:57px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMC}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->FACT}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->UMI}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO,'2', '.',',')}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            {{$rep->MONEDA}}
                                        </td>
                                        <td style="width:70px" class="zrk-gris-claro" scope="row">
                                            ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                            {{$rep->C_EMPL}}
                                        </td>
                                        <td style="width:100px" class="zrk-gris-claro" scope="row">
                                            {{$rep->NOM_EMPL}}
                                        </td>
                                    </tr>
            
            
                                    @elseif($DocN == $rep->ORDEN)
                                    <?php
                                        $totalEntrada += 0;
                                        $moneda = $rep->MONEDA;
                                    ?>
                                        <tr>
                                            <td style="width:60px" class="zrk-gris-claro" scope="row">
                                                {{$rep->CODE_ART}}
                                            </td>
                                            <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                                {{$rep->ARTICULO}}
                                            </td>
                                            <td style="width:57px" class="zrk-gris-claro" scope="row">
                                                {{$rep->UMC}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                {{$rep->FACT}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                {{$rep->UMI}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                ${{number_format($rep->COSTO,'2', '.',',')}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                {{$rep->MONEDA}}
                                            </td>
                                            <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                            </td>
                                            <td style="width:100px" class="zrk-gris-claro" scope="row">
                                                {{$rep->C_EMPL}}
                                            </td>
                                            <td style="width:100px" class="zrk-gris-claro" scope="row">
                                                {{$rep->NOM_EMPL}}
                                            </td>
                                        </tr>
                                        @else
            
                                        <?php
                                    $DocN = $rep->ORDEN;
                                    $totalEntrada = 0;
                                ?>
            
                                            <tr>
                                                <td style="width:100px" class="zrk-silver-w" scope="row">
                                                    {{$rep->ORDEN}}
                                                </td>
                                                <td style="width:120px" class="zrk-silver-w" scope="row">
                                                    {{date_format(date_create($rep->F_RECIBO), 'd/m/Y')}}
                                                </td>
                                                <td style="width:110px" class="zrk-silver-w" scope="row">
                                                    {{$rep->CLIENTE}}
                                                </td>
                                                <td style="width:457px" class="zrk-silver-w" scope="row" colspan="3">
                                                    {{$rep->RAZON_SOC}}
                                                </td>
                                                <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                                    {{$rep->NOTAS}}
                                                </td>
                                                <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                                    {{$rep->C_PROY}}
                                                </td>
                                                <td style="width:120px" class="zrk-silver-w" scope="row" colspan="2">
                                                    {{$rep->PROYECTO}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:60px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->CODE_ART}}
                                                </td>
                                                <td style="width:450px" class="zrk-gris-claro" scope="row" colspan="2">
                                                    {{$rep->ARTICULO}}
                                                </td>
                                                <td style="width:57px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->UMC}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->FACT}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->UMI}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    {{number_format($rep->CANTIDAD,'0', '.',',')}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    ${{number_format($rep->COSTO,'2', '.',',')}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->MONEDA}}
                                                </td>
                                                <td style="width:70px" class="zrk-gris-claro" scope="row">
                                                    ${{number_format($rep->COSTO_OC,'2', '.',',')}}
                                                </td>
                                                <td style="width:100px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->C_EMPL}}
                                                </td>
                                                <td style="width:100px" class="zrk-gris-claro" scope="row">
                                                    {{$rep->NOM_EMPL}}
                                                </td>
                                            </tr>
                                            @endif @if($index == count($entradasUSD)-1) @endif
                                            <?php
                                $index++;
                                ?>
                                                @endforeach
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