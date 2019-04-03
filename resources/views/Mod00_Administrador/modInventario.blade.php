@extends('app')

@section('content')
@include('partials.menu-admin')

    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <h3 class="page-header">
                        Alta de Inventarios
                    
                        {{Route::current()->getName()}}
                    </h3>
                </div>
                    <ol class="breadcrumb">
                    <li>
                            <i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Asministrador</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="inventario">Gestiòn de Inventarios</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i>  <a href="altaInventario">Alta Inventarios</a>
                        </li>

                    </ol>
                </div>
            </div>
           
            <!-- /.row -->
         <div class="container">
         @if ($mensaje !='0')
        <div class="alert alert-success">
            <strong>Modificado</strong> Se ha modificado el inventario correctamente
        </div>
        @endif
        <div class="row">
             {{--este form tiene que enviar la informacion para crear un modulo--}}
             <div class="col-md-6">
             {!! Form::open(['url' => 'admin/mod_inv2', 'method' => 'POST']) !!}
                <div class="form-group">
                    <label for="NombreEquipo">Nombre Equipo</label>
                    <input type="text" name="nombre_equipo" class="form-control" value="{{ $inventario[0]->nombre_equipo }}" required>
                </div>
                <div class="form-group">
                    <label for="NombreUsuario">Nombre Usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control"value="{{ $inventario[0]->nombre_usuario }}" required>
                </div>
                <div class="form-group">
                    <label for="Correo">Correo</label>
                    <input type="email" name="correo" class="form-control" value="{{$inventario[0]->correo}}"required>
                </div>
                <div class="form-group">
                    <label for="NumeroEquipo">Número de Equipo</label>
                    <input type="number" name="numero_equipo" class="form-control" value="{{$inventario[0]->numero_equipo}}" required>
                </div>
                <div class="form-group">
                    <label for="TipoEquipo">Tipo de Equipo</label>
                    <select class="form-control" name="tipo_equipo">
                        <option value="ESCRITORIO">ESCRITORIO</option>
                        <option value="LAPTOP">LAPTOP</option>
                        <option value="ALL IN ONE">ALL IN ONE</option>
                        <option value="{{$inventario[0]->tipo_equipo}}" selected>{{$inventario[0]->tipo_equipo}} (*)</option>    
                    </select>
                </div>
                @if ($inventario[0]->fecha_alta == NULL)
                <div class="form-group">
                    <label for="tiempo_vida">Fecha Alta</label>
                    <input type="date" name="fecha_alta" class="form-control" id="fecha_alta" placeholder="Ej: 5" value="{{$inventario[0]->fecha_alta}}" required>
                </div>
                @endif
                <div class="form-group">
                    <label for="tiempo_vida">Fecha de baja Estimada</label>
                    <input type="date" name="fecha_baja" class="form-control" id="fecha_baja" placeholder="Ej: 5" value="{{$inventario[0]->fecha_baja}}">
                </div>
                <div class="form-group">
                <label for="tiempo_vida">Fecha de Mantenimiento</label>
                    <input type="date" name="Mtto" class="form-control" id="Mtto" placeholder="Ej: 5" value="{{$inventario[0]->Fecha_mantenimiento}}" required>
                </div>
                <div class="form-group">
                    <label for="Monitor">Monitor</label>
                    <select class="form-control" name="monitor">
                    <option value="{{ $inventario[0]->id_mon }}">{{ $inventario[0]->nombre_monitor }} (*)</option>
                    <option value="1">N/A</option>
                    @foreach ($monitores as $monitor)
                        <option value="{{ $monitor->id_mon }}">{{ $monitor->nombre_monitor }}</option>
                    @endforeach 
                    </select>
                    <br>
                    <br>
                    <input type="hidden" name="id_inv" class="form-control" value="{{$inventario[0]->id_inv}}" required>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                {!! Form::close() !!} 
                </div>
                <div class="col-md-6">
                    
                </div>
        </div>
             @yield('subcontent-01')
         </div>



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