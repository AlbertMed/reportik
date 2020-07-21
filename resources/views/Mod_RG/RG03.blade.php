@extends('home')

            @section('homecontent')
            <style>
                .btn{
                    border-radius: 4px;
                }
               
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Reporte Gerencial
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">Opciones</div>
                                <div class="panel-body">
                                    <form class="form-horizontal" method="POST" action="{{url('home/RG03-reporte')}}"
                                        accept-charset="UTF-8" enctype="multipart/form-data">
                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Ejercicio - Periodo:</label>
                                            <div class="col-sm-6">
                                                <select name='cbo_periodo' class="form-control selectpicker"  data-style="btn-success btn-sm"  required='required' 
                                                    id='cbo_periodo'  placeholder='Selecciona una opción' data-live-search="true">
                                                    <option hidden selected value>Selecciona una opción</option>
                                                    @foreach ($cbo_periodos as $value)
                                                    <option value='{{$value}}'>{{$value}}</option>
                                                    @endforeach
                                                </select>
                                                <!--
                                                !! Form::select('cbo_periodo', $cbo_periodos, null, ['id' => 'cbo_periodo',
                                                'class' => 'form-control selectpicker', 'required' => 'required',
                                                "data-style"=>"btn-default", "data-live-search"=>"true", "title"=>"No has seleccionado nada"]) !!}
                                                -->
                                            </div>
                                        </div>
                    
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-6">
                                                <button onclick="mostrar();" type="submit" class="btn btn-primary">Generar</button>
                                            </div>
                                        </div>            
                                    </form>
                                </div>
                            </div>  
                            <div id="hiddendiv" class="progress" style="display: none">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 50%">
                                    <span>Espere un momento...<span class="dotdotdot"></span></span>
                                </div>
                            </div>             
                        </div>            
                    </div>
                   
                </div>   <!-- /.container -->

                    @endsection

                    @section('homescript')

                    @endsection                                      
                <script>
                    function mostrar(){
                        if ($("#cbo_periodo").val() != '') {
                            $("#hiddendiv").show();
                        }
                                            
                                        };

                </script>
