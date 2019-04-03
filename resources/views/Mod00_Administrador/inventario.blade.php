@extends('app')

@section('content')
@include('partials.menu-admin')

    <div>
        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <div class= "visible"><br><br></div>
                    <h3 class="page-header">
                       Gestiòn de Inventario
                    </h3>
                </div>
                  <div class= "col-lg-6.5 col-md-9 col-sm-8">
                    <div class="hidden-xs">
                        <div class= "hidden-ms">
                    <ol class="breadcrumb">
                    <li>
                            <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Administrador</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">Gestiòn de Inventarios</a>
                        </li>
                    </ol>
                        </div>
                    </div>
                  </div>
            </div>
            <style>
         td{
        font-family: 'Helvetica';
        font-size:80%;
    }
    th{
       font-family: 'Helvetica';
        font-size:90%;
    }
            </style>
            <!-- /.row -->
            <div class="row" scrolling="yes">
                <div class="col-lg-5 col-md-9 col-sm-8">
                    <div class="well">
                    <a href="altaInventario" class="btn btn-success"data-toggle="tooltip" title="Nuevo Registro"><i class="glyphicon glyphicon-plus-sign"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
        <div class="col-md-12">
             <div class="table-responsive">
             <table  class="table table-striped header-fixed">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">No.Equipo</th>
                        <th scope="col">Nombre Equipo</th>
                        <th scope="col">Nombre Usuario</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Monitor</th>
                        <th scope="col">Fecha de Mantenimiento</th>
                        <th scope="col">PDF</th>
                        <th scope="col">Marcar Obsoleto</th>
                        <th scope="col">Modificar</th>
                        <th scope="col">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($inventario as $inventario)
                        <tr>
                        <th scope="row">{{ $inventario->numero_equipo }}</th>
                        <td>{{ $inventario->nombre_equipo }}</td>
                        <td>{{ $inventario->nombre_usuario }}</td>
                        <td>{{ $inventario->correo}}</td>
                        <td>{{ $inventario->nombre_monitor }}</td>
                        <td>{{ $inventario->Fecha_mantenimiento }}</td>
                        <td>
                            <a href="generarPdf/{{$inventario->id_inv}}" class="btn btn-default"><i class="fa fa-file-pdf-o"data-toggle="tooltip" data-placement="top" title="Tooltip on top"></i</a>
                        </td>
                        <td>
                            <a href="mark_obs/{{$inventario->id_inv}}" class="btn btn-default"><i class="fa fa-recycle"></i</a>
                        </td>
                        <td>
                            <a href="mod_inv/{{$inventario->id_inv}}/{{0}}" class="btn btn-warning"><i class="fa fa-pencil-square"></i</a>
                        </td>
                        <td>
                            <a href="delete_inv/{{$inventario->id_inv}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i</a>
                        </td>
                        </tr>
                    @endforeach 
                    </tbody>
                    </table>
                  </table>
                </div>
              </div>
                 <div class="col-lg-5.5 col-md-8 col-sm-7">
                     @if (count($errors) > 0)
                         <div class="alert alert-danger text-center" role="alert">
                             @foreach($errors->getMessages() as $this_error)
                                 <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br>
                             @endforeach
                         </div>
                     @elseif(Session::has('mensaje'))
                         <div class="row">
                             <div class="alert alert-success text-center" role="alert">
                                 {{ Session::get('mensaje') }}
                             </div>
                         </div>
                     @endif

                 </div>
             </div>
             <div class="col-lg-5.5 col-md-8 col-sm-7">
             </div>
             </div>
             @yield('subcontent-01')
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>



    <!-- /#wrapper -->
@endsection

@section('script')





@endsection
