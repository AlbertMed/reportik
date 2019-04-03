@extends('app')

@section('content')

@include('partials.menu-admin')


    <div >

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div >
                    <div class="visible-xs"><br><br></div>
                    <h3 class="page-header">
                        Usuarios
                    </h3>
                    
                       <div class= "col-lg-6.5 col-md-8 col-sm-7">
                        <div class="hidden-xs">
                        <div class="hidden-sm">
                        <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="{!! url('home') !!}">Inicio</a>
                        </li>
                        <li>
                            <i class="fa fa-archive"></i> <a href="users">Usuarios</a>
                       </li>
                       <li>
                            <i class="fa fa-bell"></i>  <a href="{!! url('/admin/Notificaciones') !!}">Notificaciones</a>
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            {!! Form::open(['url' => 'admin/Nueva', 'method' => 'POST']) !!}
  <div class="form-group">
    <label for="exampleFormControlInput1">Autor</label>
    <input type="text" class="form-control" id="Autor" name="Autor" placeholder="Nombre del Autor"require>
  </div>
  <div class="form-group">
    <label for="exampleFormControlInput1">Dirigida a:     </label>
    <input type="text" class="form-control" id="Destinatario" name="Destinatario" placeholder="Destinatario" require >
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Noticia</label>
    <textarea class="form-control" id="Nota"name="Nota" rows="3" require></textarea>
  </div>
  <button type="submit" class="btn btn-primary">Enviar</button> 
  {!! Form::close() !!} 
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>



    <!-- /#wrapper -->

@endsection

