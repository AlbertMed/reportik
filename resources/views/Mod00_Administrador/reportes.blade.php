@extends('app') 
@section('content')
    @include('partials.menu-admin')

<div>
    <div class="container">

        <!-- Page Heading -->
        <div class="row">
                <div class="visible-xs"><br><br></div>
            <div class="col-lg-6.5 col-md-12 col-sm-8">
                <h3 class="page-header">
                    Reportes
                </h3>
                <div class="hidden-xs">
                    <div class="hidden-ms">
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                            </li>
                            <li>
                                <i class="fa fa-archive"></i> <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Administrador</a>
                            </li>
                            <li>
                                <i class="fa fa-archive"></i> <a href="inventario">Reportes</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <style>
            td {
                font-family: 'Helvetica';
                font-size: 80%;
            }

            th {
                font-family: 'Helvetica';
                font-size: 90%;
            }
        </style>
        <!-- /.row -->

        <div class="row">
            <div class="col-lg-5.5 col-md-10 col-sm-7">
                @if (count($errors) > 0)
                <div class="alert alert-danger text-center" role="alert">
                    @foreach($errors->getMessages() as $this_error)
                    <strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br> @endforeach
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
        <div class="row">
            <div class="col-md-3">
                <label for="">Agregar Reporte</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                {!! Form::open(['url' => 'admin/addReporte', 'method' => 'POST']) !!}

                <div class="form-group">
                    <label for="exampleFormControlInput1"></label>
                    <input type="text" class="form-control" id="RepName" name="Nombre_reporte" placeholder="Nombre del reporte" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="exampleFormControlInput1"></label>
                    <input type="text" class="form-control" id="RepDescr" name="Descripcion_reporte" placeholder="Descripción" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="exampleFormControlInput1"></label>
                    <select class="form-control" id="seldep" name="Departamento" required>
    <option value="">Seleccione un Departamento</option>
    @foreach ($departamentos as $depto)    
    <option value="{{$depto->Id}}">{{$depto->Nombre}}</option>
    @endforeach
    </select></div>
                <p align="right">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </p>

                {!! Form::close() !!}
            </div>
        </div>

    

    <div class="row">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-striped header-fixed">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Departamento</th>
                            <th scope="col">Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportes as $rep)
                        <tr>
                            <th scope="row">{{ $rep->Id }}</th>
                            <td>{{ $rep->Nombre }}</td>
                            <td>{{ $rep->Descripcion }}</td>
                            <td>{{ $rep->depto }}</td>
                            <td>
                                <a  data-toggle="modal" data-target="#modificaDepto"    data-nombre="{{$rep->Nombre}}"
                                                                                        data-descripcion="{{$rep->Descripcion}}"                                                                                           
                                                                                        data-deptoid="{{$rep->deptoId}}"                                                                                    
                                                                                        data-id="{{$rep->Id}}" class="btn btn-default"><i class="fa fa-pencil"></i></a>
                                <a href="reportes/borrar/{{$rep->Id}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</div>
@yield('subcontent-01')
</div>
<!-- /.container-fluid -->

</div>
<!-- /#page-wrapper2 -->
</div>
</div>


<!-- Modal -->

<div class="modal fade" id="modificaDepto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pwModalLabel">Modificación Reporte</h4>
            </div>
            {!! Form::open(['url' => 'cambio.reporte', 'method' => 'POST']) !!}
            <div class="modal-body">

                <div class="form-group">
                    <div>
                        <label for="password" class="col-md-12 control-label">Id:</label>
                        <input type="number" name="Id" class="form-control" id="Id" value="" readonly/>                       

                        <div class="form-group">
                            <label for="exampleFormControlInput1"></label>
                            <input type="text" class="form-control" id="RepName" name="Nombre_reporte" placeholder="Nombre del reporte" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput1"></label>
                            <input type="text" class="form-control" id="RepDescr" name="Descripcion_reporte" placeholder="Descripción" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput1"></label>
                            <select class="form-control" id="seldep" name="Departamento" required>                                    
                                    @foreach ($departamentos as $depto)    
                                    <option value="{{$depto->Id}}">{{$depto->Nombre}}</option>
                                    @endforeach
                            </select></div>

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
 <script>
     function js_iniciador() {
        $('.boot-select').selectpicker();
        $('.toggle').bootstrapSwitch();
        $('#modificaDepto').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var recipient = button.data('nombre') // Extract info from data-* attributes
        var recipient2 = button.data('descripcion') // Extract info from data-* attributes // If necessary, you could initiate
        //an AJAX request here (and then do the updating in a callback).
        var recipient3 = button.data('deptoid')
        var recipient4 = button.data('id') // Update the modal's content. We'll use jQuery here, but you could use a data
        //binding library or other methods instead.
        var modal = $(this)
        modal.find('#RepName').val(recipient)
        modal.find('#RepDescr').val(recipient2)
        modal.find('#seldep').val(recipient3)
        modal.find('#Id').val(recipient4)
        });
     }
 </script>