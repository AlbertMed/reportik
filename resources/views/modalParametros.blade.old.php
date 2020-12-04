@extends('home')

@section('homecontent')


<div class="container">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="page-header">
                {{$nombre }}
                <small>Producción</small>
            </h3>

        </div>

    </div>


    <!-- Modal -->

    <div class="modal fade" id="pass" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="pwModalLabel">{{$nombre}}</h4>
                </div>
                {!! Form::open(['url' => 'home/reporte/'.$nombre, 'method' => 'POST']) !!}

                <div class="modal-body">
                    <input type="text" hidden name="send" value="send">
                    <div class="form-group">
                        @include('partials.alertas')
                    </div>
                    @if($fechas == true)
                    <div class="form-group">
                        <label for="date_range" class="control-label">Rango de Fechas:</label><br>
                        Desde:<input type="date" id="FechIn" name="FechIn" class="form-control" autofocus required>
                        Hasta:<input type="date" id="FechaFa" name="FechaFa" class="form-control" required>
                    </div>
                    @endif
                    @if($fieldOtroNumber <> '')
                        <div class="form-group">
                            Escribe {{$fieldOtroNumber}}:<input type="number" id="fieldOtroNumber" name="fieldOtroNumber" class="form-control" autofocus required>
                        </div>
                        @endif
                        @if($fieldOtroText <> '')
                            <div class="form-group">
                                Escribe {{$fieldOtroText}}:<input type="text" id="fieldOtroText" name="fieldOtroText" class="form-control" autofocus required>
                            </div>
                            @endif

                </div>


                <div class="modal-footer">
                    <div id="hiddendiv" class="progress" style="display: none">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span>Espere un momento...<span class="dotdotdot"></span></span>
                        </div>
                    </div>
                    &nbsp;&nbsp;&nbsp;
                    <input id="submit" name="submit" type="submit" value="Generar" onclick="mostrar();" class="btn btn-primary" />

                    <a type="button" class="btn btn-default" href="{!!url('home')!!}">Cancelar</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div><!-- /modal -->

</div>
<!-- /.container -->

@endsection
<script>function js_iniciador() {
    $('.boot-select').selectpicker();
    $('.toggle').bootstrapSwitch();
    $('.dropdown-toggle').dropdown();
$('#pass').modal(
{
show: true,
backdrop: 'static',
keyboard: false
}
);
}</script>
<script>
    function mostrar() {
        $("#hiddendiv").show();
    };
</script>