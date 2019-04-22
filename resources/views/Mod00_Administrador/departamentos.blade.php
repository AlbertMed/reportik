@extends('app')

@section('content')
@include('partials.menu-admin')

    <div>
        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                    <div class= "visible-xs"><br><br></div>
                  <div class= "col-lg-6.5 col-md-12 col-sm-8">
                      <h3 class="page-header">
                        Departamentos
                    </h3>
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
                            <i class="fa fa-archive"></i>  <a href="inventario">Departamentos</a>
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
            <div class="row">
            <div class="col-lg-5.5 col-md-10 col-sm-7">
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
<div class="col-md-3">
{!! Form::open(['url' => 'admin/addDepto', 'method' => 'POST']) !!}
<div class="form-group">
    <label for="exampleFormControlInput1">Agregar Departamento</label>
    <input type="text" class="form-control" id="deptoName" name="Nombre_departamento" placeholder="Nombre de Departamento"require>
</div>
<button type="submit" class="btn btn-primary">Enviar</button>
{!! Form::close() !!}
</div>
</div> 
<br>

            <div class="row">
        <div class="col-md-10">
             <div class="table-responsive">
             <table  class="table table-striped header-fixed">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Nombre</th>                       
                        <th scope="col">Acciones</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($departamentos as $depto)
                        <tr>
                        <th scope="row">{{ $depto->Id }}</th>
                        <td>{{ $depto->Nombre }}</td>
                      
                        <td>
                            <a href="departementos/modificar/{{$depto->Id}}" data-toggle="modal" data-target="#modificaDepto" data-nombre="{{$depto->Nombre}}" data-id="{{$depto->Id}}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
                            <a href="departamentos/borrar/{{$depto->Id}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                        </td>
                       
                        </tr>
                    @endforeach 
                    </tbody>
                    </table>
                  </table>
                </div>
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


   <!-- Modal -->

   <div class="modal fade" id="modificaDepto" tabindex="-1" role="dialog" >
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="pwModalLabel">Modificación Departamento</h4>
                            </div>
                            {!! Form::open(['url' => 'cambio.depto', 'method' => 'POST']) !!}
                            <div class="modal-body">

                                    <div class="form-group">
                                        <div >
                                            <label for="password" class="col-md-12 control-label">Nombre Actual:</label>
                                            <input type="hidden" name="Id" class="form-control" id="Id" value="" />
                                            <input type="text" name="Name" class="form-control" id="nameDepto" value="" readonly/>
                                            <label for="password" class="col-md-12 control-label">Nuevo Nombre:</label>
                                            <input id="nombreDepto" type="text" class="form-control" name="NombreDepto" required >
                                        </div>
                                    </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
    <!-- /#wrapper -->
@endsection

@section('script')




$('#modificaDepto').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget) // Button that triggered the modal
                            var recipient = button.data('nombre') // Extract info from data-* attributes
                            var recipient2 = button.data('id') // Extract info from data-* attributes
                            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                            var modal = $(this)

                            modal.find('#nameDepto').val(recipient)
                            modal.find('#Id').val(recipient2)
                        });

@endsection
