@extends('home')

@section('homecontent')

    <div class="container" >

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5">
            <div class="hidden-lg"><br><br></div>
                <h3 class="page-header">
                    {{env('EMPRESA_NAME')}}
                    <!-- NOMBREAPP-->
                    <small>Reportik</small>
                </h3>
 
            </div>
        </div>
        <style>
.div{
    font-family:arial;
}
        </style>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-6.5 col-md-8 col-sm-5 ">

                @include('partials.alertas')
              
               
<!--<a href="Mod01_Produccion/Noticias" button class="btn btn-primary" type="button">
 <div class="glyphicon glyphicon-envelope">  </div> Notificaciones   <span class="badge badge-danger"> </span>
</button></a>-->
@endsection
 