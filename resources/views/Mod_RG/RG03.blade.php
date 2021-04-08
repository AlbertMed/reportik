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
                                <small>Sociedad: <b>{{$sociedad}}</b> </small>
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
                                            <div class="col-sm-4">
                                                <input type="hidden" id='sociedad' name="sociedad" value="{{ $sociedad }}">
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
                                            <label class="control-label col-sm-2"><a id="showImg" src="{{asset('images/ec.png')}}">Mano de Obra (MAS)</a></label>
                                            <div class="col-sm-3">
                                                <input type="number" placeholder="" name="mo" id="mo" min="0" step=".01" class="form-control">                                                
                                            </div>                                                                                        
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2">Gtos Indirectos (MENOS)</label>
                                            <div class="col-sm-3">
                                                <input type="number" placeholder="" name="indirectos" id="indirectos" min="0" step=".01" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2">MP en Proceso OT</label>
                                            <div class="col-sm-3">
                                                <input type="number" placeholder="" name="mp_ot" id="mp_ot" min="0" step=".01" class="form-control">
                                            </div>
                                        </div>
                    
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-1">
                                                <button onclick="mostrar();" type="submit" class="btn btn-primary">Generar</button>
                                            </div>
                                            
                                        </div>   

                                    </form>
                                   <div id="hiddendiv" class="progress col-sm-3" style="display: none">
                                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                        aria-valuemax="100" style="width: 100%">
                                        <span>Espere un momento...<span class="dotdotdot"></span></span>
                                    </div>
                                </div>
                                </div>
                            </div>  
                                         
                        </div>            
                    </div>
                   
                </div>   <!-- /.container -->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Cerrar</span></button>
                <img src="" class="imagepreview" style="width: 100%;">
            </div>
        </div>
    </div>
</div>
                    @endsection

                    <script>function js_iniciador() {
    $('.boot-select').selectpicker();
    $('.toggle').bootstrapSwitch();
    $('.dropdown-toggle').dropdown();                                      
                        $("#showImg").click(function(){
                        $('.imagepreview').attr('src', $("#showImg").attr('src'));
                        $('#imagemodal').modal('show');
                        });
                        $("#cbo_periodo").on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                        var val = $("#cbo_periodo").val().split('-');

                        $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: "ajustesfill",
                            type: "POST",
                            data: {
                                'sociedad': $('#sociedad').val(),
                                'ejercicio': val[0],
                                'periodo': val[1]
                            },
                            beforeSend: function () {
                            $.blockUI({
                            message: '<h1>Verificando campos guardados...,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                            css: {
                            border: 'none',
                            padding: '16px',
                            width: '50%',
                            top: '40%',
                            left: '30%',
                            backgroundColor: '#fefefe',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .7,
                            color: '#000000'
                            }
                            });
                            },
                            complete: function(){
                            setTimeout($.unblockUI, 1500);
                            },
                            success: function (data) {
                               options = [];                               
                               $("#mo").val(data.mo);     
                               $("#indirectos").val(data.indirectos);     
                               $("#mp_ot").val(data.mp_ot);     
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == 0) {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un problema con la conexión a la red. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else if (jqXHR.status == 404) {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un problema la URL especificada para la solicitud ajax no se encontro. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto. '+jqXHR.url,
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else if (jqXHR.status == 500) {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un error interno del servidor. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else if (textStatus == 'parsererror') {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un error al parsear json. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else if (textStatus == 'timeout') {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un error Timeout. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else if (textStatus == 'abort') {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un AJAX Abourt Errors. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            } else {
                                bootbox.dialog({
                                    title: "Error",
                                    message: 'Ocurrió un error desconocido. Si el problema persiste, por favor contacte a soporte ' +
                                        'técnico para recibir ayuda al respecto.',
                                    buttons: {
                                        main: {
                                            label: "Cerrar",
                                            className: "btn-primary m-r-5 m-b-5"
                                        }
                                    }
                                });
                            }
                        });                                                           
                    });
}</script>                                  
                <script>
                    function mostrar(){
                            if ($("#cbo_periodo").val() != '') {
                                $("#hiddendiv").show();
                            }
                    };
                    
                </script>
