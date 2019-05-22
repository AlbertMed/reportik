<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ '-----' }}</title>

</head>

<body>
    <div id="header">  
    </div>
    <!--Cuerpo o datos de la tabla-->
    <div id="content">
        @if(count($data)>0)
            <div class="row">
                <h4>TITULO</h4>
               
                <?php
                    $index = 0;
                    //$totalEntrada = 0;
                    //$moneda = 'Pesos';   
                ?>
                @foreach ($data as $rep) 
                    @if($index == 0)
                        <?php
                            $llave = $rep->ORDEN; 
                            //$totalEntrada = $rep->IMPORTE;
                        ?>
                        <table class="table table-striped" style="table-layout:fixed;">
                            <thead  class="table-condensed">
                                <tr style="width:100%">
                                    <th style="width:7%">ORDEN</th>
                                </tr>
                            </thead>  
                        </table>  

                        <table class="table table-striped" style="table-layout:fixed;">
                            <tbody>
                                <tr>
                                    <td style="width:7%" class="zrk-gris-claro" scope="row">
                                        {{$rep->CODE_ART}}
                                    </td>
                                </tr>
                                           
                    @elseif($llave == $rep->ORDEN)
                        <?php
                           // $totalEntrada += $rep->IMPORTE;
                           // $moneda = $rep->MONEDA;
                        ?>
                       
                       <tr>
                            <td style="width:7%" class="zrk-gris-claro" scope="row">
                                {{$rep->CODE_ART}}
                            </td>
                        </tr>
                              
                     @else
                       /* <tr>
                            <td colspan="6" class="total zrk-gris-claro">Total:</td>
                            <td class="zrk-gris-claro">${{number_format($totalEntrada,'2', '.',',')}} </td>
                            <td  class="zrk-gris-claro">{{$moneda}}</td>
                            <td  class="zrk-gris-claro"></td>
                        </tr>*/
                        <?php
                            $llave = $rep->ORDEN;
                        //    $totalEntrada = $rep->IMPORTE;
                         ?>
                        </tbody>
                        </table>
                        <table class="table table-striped" style="table-layout:fixed;">
                            <thead class="table-condensed">
                                <tr style="width:100%">
                                    <th style="width:7%">ORDEN</th>
                                </tr>
                            </thead>
                        </table> 
                        <table class="table table-striped" style="table-layout:fixed;">
                            <tbody>
                                <tr>
                                    <td style="width:7%" class="zrk-gris-claro" scope="row">
                                        {{$rep->CODE_ART}}
                                    </td>
                                </tr>
               
                    @endif 
                    /*@if($index == count($entradasMXP)-1)
                        <tr>
                            <td colspan="6" class="total zrk-gris-claro">Total:</td>
                            <td class="zrk-gris-claro">${{number_format($totalEntrada,'2', '.',',')}}</td>
                            <td  class="zrk-gris-claro">{{$moneda}}</td>
                            <td class="zrk-gris-claro"></td>  
                        </tr>
                    @endif*/
                    <?php
                        $index++;
                    ?>
                    @endforeach
                                              
                    </tbody>
                    </table>
                </div>
            @endif 
           </div>/* endContent*/
            
        
                <footer>
                 
                </footer>
                @yield('subcontent-01')
</body>

</html>