<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kardex por OV</title>
    <style>
        
table { width: 100%; border-collapse: collapse; font-family: arial;
 } th { font-weight: bold;
font-family: 'Helvetica'; font-size: 12px; background-color: #b0adad; } 
td { font-family: 'Helvetica'; font-size: 11px; }

img {
width: 13%;
height: 13%;
position: absolute;
margin-left: 5px;
margin-top: -6px;
}
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
            
            border: .5px solid black;
           
            
        }
        .numbers{
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .zrk-silver{
            
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
    
        }
        .table > thead > tr > th, 
        .table > tbody > tr > th, 
        .table > tfoot > tr > th, 
        .table > thead > tr > td, 
        .table > tbody > tr > td,
        .table > tfoot > tr > td { 
            padding-bottom: 2px; padding-top: 2px; padding-left: 4px; 
        }
        .total{
            text-align: right; 
            padding-right:5px !important;
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
                       
                        <h2><b>Kardex de Estado de Cuenta por OV</b></h2>
                       @if(count($ovs)>0)
                        <h3>PROYECTO: {{$info[0]->PROYECTO}}</h3>
                        <h3>CLIENTE: {{$info[0]->CLIENTE}}</h3>
                        <h3>CONTACTO: {{$info[0]->COMPRADOR}}</h3>
                        @endif
                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
           @if(count($ovs)>0)
            <div class="row">
               
                <div class="col-md-8">                               
                        <?php 
                            $index = 1; ?>
                           
                                                                                  
                         
                        <table class="table table-striped" style="table-layout:fixed;">
                            <thead class="table-condensed">
                                <tr style="width:100%">
                                    <th style="width:56%" class="total">{{'DOCUMENTO EXPRESADO EN '. strtoupper($info[0]->MONEDA)}} &nbsp; TOTAL:</th>
                                    
                        
                                    <th style="width:11%" class="numbers">${{number_format($sumOV,'2', '.',',')}}</th>
                                    <th style="width:11%" class="numbers">${{number_format($sumFAC,'2', '.',',')}}</th>
                                    <th style="width:11%" class="numbers">${{number_format($sumEMB,'2', '.',',')}}</th>
                                    <th style="width:11%" class="numbers">${{number_format($sumPAG,'2', '.',',')}}</th>
                                </tr>                        
                            </thead>
                        </table>
                        <table class="table table-striped" style="table-layout:fixed;">
                            <thead class="table-condensed">
                                <tr style="width:100%">
                                    <th style="width:56%" class="total">{{'SALDO:'}}</th>
                                    
                        
                                    <th style="width:11%" class="numbers"></th>
                                    <th style="width:11%" class="numbers">${{number_format($sumOV - $sumFAC,'2', '.',',')}}</th>
                                    <th style="width:11%" class="numbers">${{number_format($sumOV - $sumEMB,'2', '.',',')}}</th>
                                    <th style="width:11%" class="numbers">${{number_format($sumOV - $sumPAG,'2', '.',',')}}</th>
                                </tr>                        
                            </thead>
                        </table>
                           <table class="table table-striped" style="table-layout:fixed;">            
                                <thead  class="table-condensed">
                                    <tr style="width:100%">
                                        <th style="width:3%">#</th>
                                        <th style="width:8%">FECHA</th>
                                        <th style="width:11%">IDENTIFICADOR</th>
                                        <th style="width:16%">DOCUMENTO</th>
                                        <th style="width:18%">REFERENCIA</th>
                                        
                                        <th style="width:11%">IMP. OV</th>
                                        <th style="width:11%">IMP. FACTURA</th>
                                        <th style="width:11%">IMP. EMBARQUE</th>
                                        <th style="width:11%">IMP. PAGO</th>
                                    </tr>
                                </thead>  
                            </table>  

                            
                            
                            @foreach ($ovs as $rep) 

                            <table class="table table-striped" style="table-layout:fixed;">
                                <tbody>
                                    <tr>
                                        <td style="width:3%" class="zrk-silver-w" scope="row">
                                        {{$index}}
                                        </td>
                                        <td style="width:8%" class="zrk-silver-w" scope="row">
                                            {{date_format(date_create($rep->FECHA), 'd/m/Y')}}
                                        </td>
                                        <td style="width:11%" class="zrk-silver-w" scope="row">
                                            {{$rep->IDENTIF}}
                                        </td>
                                        <td style="width:16%" class="zrk-silver-w" scope="row">
                                            {{$rep->DOCUMENT}}
                                        </td>                                        
                                        <td style="width:18%" class="zrk-silver-w" scope="row">
                                            {{$rep->REFERENCIA}}
                                        </td>
                                        <td style="width:11%" class="zrk-silver-w numbers" scope="row">
                                            ${{number_format($rep->IMP_OV,'2', '.',',')}}
                                        </td>
                                        <td style="width:11%" class="zrk-silver-w numbers" scope="row">
                                            ${{number_format($rep->IMP_FAC,'2', '.',',')}}
                                        </td>
                                        <td style="width:11%" class="zrk-silver-w numbers" scope="row">
                                            ${{number_format($rep->IMP_EMB,'2', '.',',')}}
                                        </td>
                                        <td style="width:11%" class="zrk-silver-w numbers" scope="row">
                                            ${{number_format($rep->IMP_PAG,'2', '.',',')}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php 
                                                        $index++; ?>
                            @endforeach    
                 
                </div>
            
            </div>
        @endif 
           
          
        </div>
                <footer>
                    
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 

                       
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'KardexOV.Pdf'; 
                        
                        $pdf->page_text(580, 23, $date, $font, 9);  

                        $pdf->page_text(35, 580, $text, $font, 9);                         
                        $pdf->page_text(630, 580, $tittle, $font, 9);                                                 
                    </script>
                </footer>
              
</body>

</html>