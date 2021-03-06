

@extends('home')

@section('homecontent')

        <div class="container" >

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-8 col-md-9 col-xs-10">
                <div class="hidden-lg"><br><br></div>
                    <h3 class="page-header">
                       Notificaciones
                        <small>Retrocesos</small>
                    </h3>
                    <div class="visible-lg">
                    <ol class="breadcrumb">
                        <li>
                            <i class="fa fa-dashboard">  <a href="{!! url('home') !!}">Inicio</a></i>
                        </li>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            @foreach ($noticias as $noticia)
           
            
<div class="col-md-8"   >
    <div class="alert alert-info alert-dismissible fade in">  
             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
             <strong>• Retroceso:</strong> Se esta llevando a cabo el reproceso de la orden <strong>"{{$noticia->No_Orden}}"</strong> de la estación {{$noticia->Estacion_Act}} a la estacion de destino{{$noticia->Estacion_Destino}} 
             por motivo de <strong> {{$noticia->Descripcion}}</strong>.
             <br>            
             <strong> Nota:</strong>  {{$noticia->Nota}}

             <div align="right">
             <strong> Autorizo :</strong>  {{$noticia->Reproceso_Autorizado}}
            </div>
<a href="../leido/{{$noticia->Id}}" class="btn btn-primary btn-sm">Aceptar</a></div>
@endforeach
        <!-- /.container-fluid -->

@endsection
