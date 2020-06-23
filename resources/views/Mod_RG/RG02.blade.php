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
                               Relacionar PDFs
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                            <div class="panel panel-default">
                                <div class="panel-heading">Agregar archivo</div>
                                <div class="panel-body">
                                    <form class="form-horizontal" method="POST" action="{{url('home/RG02-guardar')}}"
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
                                            <label class="control-label col-sm-2" for="pwd">Reporte:</label>
                                            <div class="col-sm-6">
                                                <select name='cbo_reporte' class = "form-control selectpicker"
                                                data-style = "btn-success btn-sm" required = 'required'
                                                id ='cbo_reporte' placeholder = 'Selecciona una opción'>
                                                    <option hidden selected value>Selecciona una opción</option>
                                                @foreach ($reportes as $value) 
                                                    <option value='{{$value}}'>{{$value}}</option>
                                                @endforeach
                                                </select>
                                                <!--
                                                !! Form::select('cbo_reporte', , null, [
                                                "class" => "form-control selectpicker", "data-style" => "btn-success btn-sm",
                                                'id' => 'cbo_reporte', 'placeholder' => 'Selecciona una opción']) !!*/
                                                -->
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="pwd">Archivo:</label>
                                            <div class="col-sm-6">
                                                <input type="file" name="archivo" id="archivo" accept="application/pdf" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-6">
                                                <button onclick="mostrar();" type="submit" class="btn btn-primary">Guardar</button>
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

                    </div>   <!-- /.container -->

                    @endsection

                    @section('homescript')

                        var date_input=$('input[name="date"]'); 

                        date_input.datepicker( {
                            language: "es",    
                            autoclose: true,
                            format: "yyyy-mm",
                            startView: "months",
                            minViewMode: "months"
                        });

                    @endsection                                      
                <script>
                    function mostrar(){
                        if ($("#cbo_periodo").val() != '' && $('#cbo_reporte').val() != '') {
                            $("#hiddendiv").show();
                        }
                                            
                                        };

                </script>
