<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PDF</title>
    
    <style>
        /* Center tables for demo */
        table {
        margin: 0 auto;
        }
        
        /* Default Table Style */
        table {
        color: #333;
        background: white;
        border: 1px solid grey;
        font-size: 12pt;
        border-collapse: collapse;
        }
        table thead th,
        table tfoot th {
        color: #777;
        background: rgba(0,0,0,.1);
        }
        table caption {
        padding:.5em;
        }
        table th,
        table td {
        padding: .5em;
        border: 1px solid lightgrey;
        }
        /* Zebra Table Style */
        [data-table-theme*=zebra] tbody tr:nth-of-type(odd) {
        background: rgba(0,0,0,.05);
        }
        [data-table-theme*=zebra][data-table-theme*=dark] tbody tr:nth-of-type(odd) {
        background: rgba(255,255,255,.05);
        }
        /* Dark Style */
        [data-table-theme*=dark] {
        color: #ddd;
        background: #333;
        font-size: 12pt;
        border-collapse: collapse;
        }
        [data-table-theme*=dark] thead th,
        [data-table-theme*=dark] tfoot th {
        color: #aaa;
        background: rgba(0255,255,255,.15);
        }
        [data-table-theme*=dark] caption {
        padding:.5em;
        }
        [data-table-theme*=dark] th,
        [data-table-theme*=dark] td {
        padding: .5em;
        border: 1px solid grey;
        }
        /*
                Generic Styling, for Desktops/Laptops
                */
        img {
        width: 15%;
        height: 15%;
        position: absolute;
        margin-left: 5px;
        margin-top: -6px;
        }

        table {
            width: 100%;            
            border-collapse: collapse;            
        }

        th {         
            font-weight: bold;
            color: black;
            font-family: 'Helvetica';
            font-size: 60%;
        }

        td {
            font-family: 'Helvetica';
            font-size: 50%;
        }

        h3 {
            font-family: 'Helvetica';
        }

      

        #header {
            position: fixed;
            margin-top: 2px;
        }

        #content {
            position: relative;
            top: 8%;
            margin: 0em;
        }
        body{
            margin: 0em;
        }
        legend {
            font-size: 21px;
            line-height: 28px;
        }
        .table-espacio10{
            margin-top: 10px;
        }
        .page_break { page-break-before: always; }
        small{
            font-size: 9pt;
        }
        th, td {
            padding-top: 8px;
            padding-bottom: 8px;
            padding-left: 2px;
            padding-right: 2px;
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
                    <td  align="center" bgcolor="#fff">
                        <h1><b>Reporte Presupuesto </b></h1>
                    
                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
            <div class="row">                                  
               @include($vista)
            </div>
        </div>


                <footer>
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 

                        $empresa = 'Sociedad: <?php echo $sociedad; ?>';
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'Reporte_Presupuesto.Pdf'; 
                        
                        $pdf->page_text(40, 23, $empresa, $font, 9);
                        $pdf->page_text(580, 23, $date, $font, 9);  

                        $pdf->page_text(35, 580, $text, $font, 9);                         
                        $pdf->page_text(620, 580, $tittle, $font, 9);    
                    </script>
                </footer>
                

</body>

</html>