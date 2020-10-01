    
<div class="container">
    <br>
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">Reportes Adicionales</div>
                <div class="panel-body">
                    <div >
                        @if (count($docs) == 0) 
                            <div class="alert alert-info" role="alert">
                                No hay ningun reporte adicional
                            </div>
                        @endif
                    </div>
            
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd">Reporte:</label>
                            <div class="col-sm-6">
                                <select name='cbo_reporte' class="form-control selectpicker" data-style="btn-success btn-sm"
                                    required='required' id='cbo_reporte' placeholder='Selecciona una opción'>
                                    <option hidden selected value>Selecciona una opción</option>
                                    @foreach ($docs as $value)
                                    <option value='{{$value->DOC_nombre}}'>{{$value->DOC_tipo}}</option>
                                    @endforeach
                                </select>                              
                            </div>
                        </div>
            
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-6">
                                <button onclick="mostrara();" class="btn btn-primary">Mostrar</button>
                            </div>
                        </div>
                    
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
</div>

<script>
   
</script>