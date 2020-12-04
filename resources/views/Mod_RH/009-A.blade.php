@extends('home')

            @section('homecontent')
<style>
    th {
        font-size: 12px;
    }

    td {
        font-size: 11px;
    }

    th,
    td {
        white-space: nowrap;
    }

    .btn-group {
        padding-bottom: 5px;
    }

    .btn-group>.btn {
        float: none;
    }

    .btn {
        border-radius: 4px;
    }

    .btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
        border-radius: 4px;
    }

    .btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .btn-group>.btn:last-child:not(:first-child),
    .btn-group>.dropdown-toggle:not(:first-child) {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
</style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Catálogo de Recursos Humanos
                                <small>Personal activo</small>
                            </h3>
                    
                            <h5>Fecha & hora: {{date('d-m-Y h:i a', strtotime("now"))}}</h5>                         
                        </div>
                    </div>
                   
                    <!-- modificacion a botones de reportes-->
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="dt-buttons btn-group">                                                                                                     
                                <a class="btn btn-success" href="../R009AXLS"><i class="fa fa-file-excel-o"></i> Excel</a>
                                <a href="../R009APDF" target="_blank" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Pdf</a> 
                            </div>  
                        </div>                      
                    </div> 
                     
                 <!-- /.row -->
                    <div class="row">
                        <div class="col-md-12">
                        <table  border="1px"class="table table-striped">
                    <thead class="table table-striped table-bordered table-condensed" >
                        <tr>                      
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">CODIGO</th>
                        <th align="center" bgcolor="#474747" style="color:white"; scope="col">NOMBRE</th>
                        <th align="center" bgcolor="#474747" style="color:white";scope="col">DEPARTAMENTO</th>
                        <th align="center" bgcolor="#474747" style="color:white"; scope="col">PUESTO</th>                      
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($data)>0)
                    @foreach ($data as $rep)
                        <tr>                            
                            <td scope="row">
                                {{$rep->EMP_CodigoEmpleado}}
                            </td>
                            <td scope="row">
                                {{$rep->Nombre}}
                            </td>
                            <td scope="row">
                                {{ $rep->Departamento }}
                            </td>
                            <td align="center"scope="row">
                                {{ $rep->Puesto }}
                            </td>                        
                        </tr>    
                    @endforeach 
                    @endif
                    </tbody>
                </table>
                        </div>
                    </div>

                    </div>
                    <!-- /.container -->

                    @endsection

       

                    <script>

                        function mostrar(){
                            $("#hiddendiv").show();
                        };

                    </script>