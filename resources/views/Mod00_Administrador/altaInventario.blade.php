@extends('app')

@section('content')
@include('partials.menu-admin')

    <div >

        <div class="container" >
            <!-- Page Heading -->
                         <div class="row">
                           <div class="visible-xs"><br><br></div>
                                <h3 class="page-header">
                                    Alta de Inventarios
                                 </h3>
                         </div>
                    <div class= "col-lg-6.5 col-md-8 col-sm-7">
                        <div class="hidden-xs">
                        <div class="hidden-sm">
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
                    </div>
                </div>
            </div>
            <!-- /.row -->
         <div class="container">
        <div class="row">
        <div class= "col-lg-6.5 col-md8 col-sm-7">   
        {{--este form tiene que enviar la informacion para crear un modulo--}}
             {!! Form::open(['url' => 'admin/altaInventario', 'method' => 'POST']) !!}
                <div class="form-group">
                    <label for="NombreEquipo">Nombre Equipo</label>
                    <input type="text" name="nombre_equipo" class="form-control" placeholder="Ej. HP Probook 4520s" required>
                </div>
                <div class="form-group">
                    <label for="NombreEquipo">Nombre Usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre Apellido" required>
                </div>
                <div class="form-group">
                    <label for="Correo">Correo</label>
                    <input type="email" name="correo" class="form-control" id="exampleFormControlInput1" placeholder="nombre.apellido@zarkin.com" required>
                </div>
                <div class="form-group">
                    <label for="NumeroEquipo">Número de Equipo</label>
                    <input type="number" name="numero_equipo" class="form-control" id="exampleFormControlInput1" placeholder="Ej 77" required>
                </div>
                <div class="form-group">
                    <label for="TipoEquipo">Tipo de Equipo</label>
                    <select class="form-control" name="tipo_equipo">
                    <option>ESCRITORIO</option>
                    <option>LAPTOP</option>
                    <option>ALL IN ONE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tiempo_vida">Tiempo de Vida Estimado (Años)</label>
                    <input type="number" min="0" max="10" maxlength="2" name="tiempo_vida" class="form-control" id="tiempo_vida" placeholder="Ej: 5" required>
                </div>
                <div class="form-group">
                    <label for="Monitor">Monitor</label>
                    <select class="form-control" name="monitor">
                    <option value="1">N/A</option>
                    @foreach ($monitores as $monitor)
                        <option value="{{ $monitor->id_mon }}">{{ $monitor->nombre_monitor }}</option>
                    @endforeach 
                    </select>
                    <br>
                    <div class="form-group">
                    <label for="mantenimiento">Ultima fecha de mantenimiento</label>
                    <input type="Date" name="mantenimiento" class="form-control" placeholder="10/Junio/2018" required>
                </div>
                    <br>
                    
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
                {!! Form::close() !!} 
                </div>
                <div class="col-md-6.5">
                    
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