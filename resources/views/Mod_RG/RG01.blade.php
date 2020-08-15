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
                               Captura de Histórico
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                            <div class="panel panel-default">
                                <div class="panel-heading">Captura de Archivo</div>
                                <div class="panel-body">
                                    <form id="form_archivo" class="form-horizontal" method="POST" action="{{url('home/RG01-guardar')}}"
                                        accept-charset="UTF-8" enctype="multipart/form-data">
                    
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Periodo:</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="date" id="periodo" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2" for="pwd">Balanza comprobación:</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="archivo" id="archivo" accept="application/vnd.ms-excel" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button disabled='disabled' id="boton_confirma" type="button" class="btn btn-success">Guardar</button>
                                            </div>
                                        </div>            
                                    </form>
                                </div>
                            </div>  
                                                   

                    </div>   <!-- /.container -->
<div class="modal fade" id="confirma" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pwModalLabel">Agregar</h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <div>
                        <h4>¿Desea continuar?</h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div id="hiddendiv" class="progress" style="display: none">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                        aria-valuemax="100" style="width: 100%">
                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                    </div>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="submitBtn" onclick="mostrar();" class="btn btn-primary">Guardar</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="confirma_actualiza" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="pwModalLabel">Actualizar</h4>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <div>
                        <h4>Hay Información de ese Periodo ¿desea actualizar?</h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div id="hiddendiv2" class="progress" style="display: none">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                        aria-valuemax="100" style="width: 100%">
                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                    </div>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="submitBtn2" onclick="mostrar();" class="btn btn-primary">Guardar</button>
            </div>

        </div>
    </div>
</div>

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
                       
                        $('#boton_confirma').on('click', function (e) {
                        e.preventDefault();
                            $.ajax({
                            type: 'POST',                         
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: { "_token": "{{ csrf_token() }}", 
                            date: $('#periodo').val() },
                            url: "checkctas",
                            success: function(data){                           
                            if (data.respuesta) {
                            $('#confirma_actualiza').modal('show');
                            } else {
                            $('#confirma').modal('show');
                            }
                            }
                            });
                        });

                        $("#submitBtn").click(function(){
                        
                        $("#form_archivo").submit(); // Submit the form
                        
                        });
                        $("#submitBtn2").click(function(){
                        
                        $("#form_archivo").submit(); // Submit the form
                        
                        });

                        $("#archivo").change(function(){
                            if($('#periodo').val() !== ''){
                                $("#boton_confirma").prop("disabled", this.files.length == 0);
                            }
                        });
                    @endsection                                      
                <script>
                    function mostrar(){
                                            $("#hiddendiv").show();
                                            $("#hiddendiv2").show();
                                        };
                                                                                                           

                </script>
