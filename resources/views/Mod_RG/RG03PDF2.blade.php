<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
   
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>>
    <title>PDF</title>
    
    <style>
       
        /*
                Generic Styling, for Desktops/Laptops
                */

        img {
            display: block;
            margin-left: 50px;
            width: 700%;
            margin-top: 0%;
        }

        table {
            width: 100%;            
            border-collapse: collapse;            
        }

        th {         
            font-weight: bold;
            color: black;
            font-family: 'Helvetica';
            font-size: 70%;
        }

        td {
            font-family: 'Helvetica';
            font-size: 60%;
        }

        img {
            width: 20%;
            height: 20%;
            position: absolute;
            margin-top: -7;
            align-content: ;
        }

        h3 {
            font-family: 'Helvetica';
        }

        b {
            font-size: 100%;
        }

        #header {
            position: fixed;
            margin-top: 2px;
        }

        #content {
            position: relative;
            top: 6%
        }
        legend {
            font-size: 21px;
            line-height: 28px;
        }
        .table-espacio10{
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div id="header">
       
        <!--empieza encabezado, continua cuerpo-->
        <table border="1px" class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <td colspan="6" align="center" bgcolor="#fff">
                        <b></b><br>

                        <b>Reporte Gerencial</b>
                       
                      
                    </td>

                </tr>
            </thead>
        </table>
       
    </div>
        <!--Cuerpo o datos de la tabla-->
        <div id="content">
            <div class="row">                                  
               
            </div>
        </div>


                <footer>
                    <script type="text/php">
                        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif","normal"); 

                        $empresa = 'Sociedad: <?php echo 'ITEKNIA EQUIPAMIENTO S.A. de C.V.'; ?>';
                        $date = 'Fecha de impresion:  <?php echo $hoy = date("d-m-Y H:i:s"); ?>';
                        $text = 'Pagina: {PAGE_NUM} / {PAGE_COUNT}'; 
                        $tittle = 'Reporte_Gerencial.Pdf'; 
                        
                        $pdf->page_text(40, 23, $empresa, $font, 9);
                        $pdf->page_text(395, 23, $date, $font, 9);  

                        $pdf->page_text(35, 755, $text, $font, 9);                         
                        $pdf->page_text(450, 755, $tittle, $font, 9);                                                 
                    </script>
                </footer>
                

</body>

</html>