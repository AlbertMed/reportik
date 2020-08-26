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
                               Provision CXC
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                        
                           <!-- begin row -->
                            <div  id="btnBuscadorOrdenVenta">
   
                                            <div class="row" style="margin-bottom: 40px">
                                                <div class="form-group">
                                                    <div class="col-md-4">
                                                        <label><strong>
                                                                <font size="2">Estado (Activo / Eliminado)</font>
                                                            </strong></label>
                                                        {!! Form::select("CMM_ControlId[]", $estado, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"cboEstado", "data-size" => "8", "data-style"=>"btn-success"])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label><strong>
                                                                <font size="2">Cliente</font>
                                                            </strong></label>
                                                        {!! Form::select("CMM_ControlId[]", $estado, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"cboEstado", "data-size" => "8", "data-style"=>"btn-success"])
                                                        !!}
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label><strong>
                                                                <font size="2">Comprador</font>
                                                            </strong></label>
                                                        {!! Form::select("CMM_ControlId[]", $estado, null, [
                                                        "data-selected-text-format"=>"count", "class" => "form-control selectpicker","id"
                                                        =>"cboEstado", "data-size" => "8", "data-style"=>"btn-success"])
                                                        !!}
                                                    </div>
                                                   
                                                    <div class="col-md-4">
                                                        <p style="margin-bottom: 23px"></p>
                                                        <button type="button" class="form-control btn btn-success m-r-5 m-b-5" id="boton-mostrar"><i
                                                                class="fa fa-cogs"></i> Mostrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="row">
                                            <div class="table-responsive" id="registros-ordenes-venta">
                                                <table id="ordenes-venta" class="table table-striped table-bordered nowrap" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Eliminar</th>
                                                            <th>Editar</th>
                                                            <th>Subir Archivos</th>
                                                            <th>Ver PDF1</th>
                                                            <th>Ver PDF2</th>
                                                            <th>Ver PDF3</th>
                                                            <th>Estado</th>
                                                            <th>Orden de Venta</th>
                                                            <th>Cliente</th>
                                                            <th>Sucursal</th>
                                                            <th>Ante-Proyecto</th>
                                                            <th>Fecha OV</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                   
                                
                            </div>
                            <!-- end row -->
                                                   

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
