@extends('home')

@section('homecontent')
<style>
    th, td { white-space: nowrap; }
    .btn{
        border-radius: 4px;
    }
    th {
        background: #dadada;
        color: black;
        font-weight: bold;
        font-style: italic; 
        font-family: 'Helvetica';
        font-size: 12px;
        border: 0px;
    }
    
    td {
    font-family: 'Helvetica';
    font-size: 11px;
    border: 0px;
    line-height: 1;
    }
    tr:nth-of-type(odd) {
    background: white;
    }
    .row-id {
    width: 15%;
    }
    .row-nombre {
    width: 60%;
    }
    .row-movimiento {
    width: 25%;
    }
    table{
        table-layout: auto;
    }
    .width-full{
        margin: 5px;
    }
    .dataTables_wrapper.no-footer .dataTables_scrollBody {
    border-bottom: 1px solid #111;
    max-height: 250px;
    }
    .dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: right;
    visibility: visible;
    }
    .ignoreme{
        background-color: hsla(0, 100%, 46%, 0.10) !important;       
    }
    .dataTables_scrollHeadInner th:first-child {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 5;
    }
    
    .segundoth {
    position: -webkit-sticky;
    position: sticky !important;
    left: 0px;
    z-index: 5;
    }
    
    table.dataTable thead .sorting {
    position: sticky;
    }
    
    .DTFC_LeftBodyWrapper {
    margin-top: 80px;
    }
    
    .DTFC_LeftHeadWrapper {
    display: none;
    }
    
    .DTFC_LeftBodyLiner {
    overflow: hidden;
    overflow-y: hidden;
    }
</style>

<div class="container" >

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11" style="margin-top: -20px">
            <h3 class="page-header">
                Programas
                <small><b>Flujo Efectivo</b></small>
            
            </h3>                                        
        </div>
            
        <div class="col-md-12 ">
            @include('partials.alertas')
        </div>
        </div> <!-- /.row -->
        
        <div class="col-md-12">
            <div class="row">
                    <a onclick="cargando()" href="{{ url('home/FINANZAS/01 FLUJO EFECTIVO') }}" class="btn btn-primary">Atras</a>    
                    <a onclick="cargando()" class="btn btn-success" href="{{url('home/FINANZAS/nuevoPrograma')}}"><i class="fa fa-plus"></i> Nuevo</a> 
                
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                
                <div class="table-responsive">
                    <table id="tableProgramas" class="table table-striped table-bordered nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>Ver</th>
                                <th>Acción</th>
                                <th>Eliminar</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Banco</th>
                                <th>Cuenta</th>
                                <th>Moneda</th>
                                <th>F. Programa</th>
                                <th>F. Pago</th>
                                <th>Creado Por</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            
        </div>
        

</div> <!-- /.container -->  

@endsection

<script>
function js_iniciador() {
        $('.toggle').bootstrapSwitch();
        $('[data-toggle="tooltip"]').tooltip();
        $('.boot-select').selectpicker();
        $('.dropdown-toggle').dropdown();
        setTimeout(function() {
        $('#infoMessage').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active"); 
            $("#page-wrapper2").toggleClass("content"); 
            $(this).toggleClass("active"); 
        });
//$("#flujoEfectivoDetalle").hide();
document.onkeyup = function(e) {
    if (e.shiftKey && e.which == 112) {
        var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
        console.log(namefile)
        $.ajax({
        url:"{{ URL::asset('ayudas_pdf') }}"+"/"+namefile,
        type:'HEAD',
        error: function()
        {
            //file not exists
            window.open("{{ URL::asset('ayudas_pdf') }}"+"/AY_00.pdf","_blank");
        },
        success: function()
        {
            //file exists
            var pathfile = "{{ URL::asset('ayudas_pdf') }}"+"/"+namefile;
            window.open(pathfile,"_blank");
        }
        });

        
    }
}
//$(window).on('load',function(){            
/*PROGRAMAS*/
function consultaProgramaPorId(programaId){

    $.ajax({

        cache: false,
        async: false,
        url: "consultaProgramaPorId",
        data: {

            "programaId": programaId

        },
        type: "POST",
        success: function( datos ) {

            $("#tableProgramaDetalle").DataTable().clear().draw();
            if(JSON.parse(datos.consulta).programaDetalle != ''){

                $("#tableProgramaDetalle").dataTable().fnAddData(JSON.parse(datos.consulta).programaDetalle);

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

        }

    });

}
    var PRECIOS_DECIMALES = 2;
$('#tableProgramas').on( 'click', 'button#btnVerPrograma', function (e) {

    e.preventDefault();

    var tblProgramas = $('#tableProgramas').DataTable();
    var fila = $(this).closest('tr');
    var datos = tblProgramas.row(fila).data();
    var programaId = datos['DT_RowId'];

    $("#input-codigo").val(datos['PPCXP_Codigo']);
    $("#input-nombreP").val(datos['PPCXP_Nombre']);
    $("#input-estado").val(datos['PPCXP_Estado']);
    $("#input-fechaP").val(datos['PPCXP_FechaPrograma']);
    $("#input-banco").val(datos['BAN_NombreBanco']);
    $("#input-cuentaP").val(datos['BCS_Cuenta']);
    $("#input-moneda").val(datos['MON_Nombre']);
    $("#input-creado").val(datos['PPCXP_CreadoPor']);
    $("#input-monto").val('$ ' + number_format(datos['PPCXP_Monto'],PRECIOS_DECIMALES,'.',','));

    //consultaProgramaPorId(programaId);
    window.location.href = "{{url().'/home/FINANZAS/consultaProgramaPorId/'}}"+programaId;   
    /*$("#flujoEfectivo").hide();
    $("#btnBuscadorFlujoEfectivo").hide();
    $("#flujoEfectivoDetalle").show();
*/

});
    $("#tableProgramas").DataTable({

            language:{
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "iDisplayLength": 10,
            "aaSorting": [],
            dom: 'T<"clear">lfrtip',
            deferRender: true,
            
            columns: [
                {data: "BTN_Ver"},
                {data: "BTN_Autorizar"},
                {data: "BTN_Eliminar"},
                {data: "PPCXP_Codigo"},
                {data: "PPCXP_Nombre"},
                {data: "PPCXP_Estado"},
                {data: "PPCXP_Monto"},
                {data: "BAN_NombreBanco"},
                {data: "BCS_Cuenta"},
                {data: "MON_Nombre"},
                {data: "PPCXP_FechaPrograma"},
                {data: "PPCXP_FechaPago"},
                {data: "PPCXP_CreadoPor"}

            ],
            "columnDefs": [

                {

                    "targets": [ 0 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {
                        if(row['PPCXP_Estado'] == 'Abierto'){

                         return '<button type="button" class="btn btn-primary" id="btnVerPrograma"> <span class="fa fa-pencil-square-o"></span></button>';

                        } else{
                            return '<button type="button" class="btn btn-primary" id="btnVerPrograma"> <span class="fa fa-eye"></span></button>';
                        }
                    }

                },
                {

                    "targets": [ 1 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        if(row['PPCXP_Estado'] == 'Abierto'){

                          return '<button type="button" class="btn btn-success" id="btnAutorizarPrograma"> <span class="fa fa-check-square-o"></span> </button>';

                        } else if(row['PPCXP_Estado'] == 'Autorizado'){
													return '<button type="button" class="btn btn-success" id="btnShowFile"> <span class="fa fa-file-text-o"></span> </button>';
                        } else if(row['PPCXP_Estado'] == 'Aplicado'){
                            return '<span class="fa fa-check-square"></span>';
                        } 
                        else{

                            return '';

                        }

                    }

                },
                {

                    "targets": [ 2 ],
                    "searchable": false,
                    "orderable": false,
                    'className': "dt-body-center",
                    "render": function ( data, type, row ) {

                        return '<button type="button" class="btn btn-danger" id="btnEliminarPrograma"> <span class="fa fa-trash-o"></span> </button>';

                    }

                },
                {

                "targets": [ 6 ],
                "searchable": false,
                "orderable": false,
                'className': "dt-body-center",
                "render": function ( data, type, row ) {

                    if(row['PPCXP_Monto'] != ''){

                        return '$ ' + number_format(row['PPCXP_Monto'],PRECIOS_DECIMALES,'.',',');

                    }
                    else{

                        return '';

                    }

                }

            }
            ],
            fixedColumns: false,
            tableTools: {sSwfPath: "plugins/DataTables/swf/copy_csv_xls_pdf.swf"},
            'order': [[3, 'DESC']]

    });

    function consultarDatosInicio(){
        $.ajax({

            type: 'GET',
            async: true,
            url: "programas-registros",
            /*data:{

                "fechaDesde": $('#input-fechaInicio').val(),
                "fechaHasta": $('#input-fechaFinal').val(),
                "cuentaId": cuentaId

            },*/
            beforeSend: function() {
                $.blockUI({
                    message: '<h1>Actualizando tabla Programas,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
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
            complete: function() {
                setTimeout($.unblockUI, 1500);
            },
            success: function(data){
                console.log(data)
                $("#tableProgramas").DataTable().clear().draw();

                if(JSON.parse(data).consulta != ''){

                    $("#tableProgramas").dataTable().fnAddData(JSON.parse(data).consulta);
                }
                //tabla_resumen_cxc();
            },
            error: function (xhr, ajaxOptions, thrownError) {

                $.unblockUI();
                var error = JSON.parse(xhr.responseText);
                bootbox.alert({

                    size: "large",
                    title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                    message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                    ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                    ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                    ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

                });

            }

        });
    }
    consultarDatosInicio();

    function number_format(number, decimals, dec_point, thousands_sep) 
    {
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function (n, prec) {
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                var k = Math.pow(10, prec);
                return Math.round(n * k) / k;
            },
            s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

   $('#tableProgramas').on( 'click', 'button#btnEliminarPrograma', function (e) {

			e.preventDefault();

			var tblProgramas = $('#tableProgramas').DataTable();
			var fila = $(this).closest('tr');
			var datos = tblProgramas.row(fila).data();
			var programaId = datos['DT_RowId'];
			var codigo = datos['PPCXP_Codigo'];
			var nombre = datos['PPCXP_Nombre'];
			var programa = codigo + " - " + nombre;

			bootbox.dialog({

					title: "Flujo de Efectivo",
					message: "¿Estás seguro de cancelar el programa "+ programa +"?, No podrás deshacer el cambio.",
					buttons: {

							success: {

									label: "Si",
									className: "btn-success m-r-5 m-b-5",
									callback: function () {

											$.blockUI({ css: {

													border: 'none',
													padding: '15px',
													backgroundColor: '#000',
													'-webkit-border-radius': '10px',
													'-moz-border-radius': '10px',
													opacity: .5,
													color: '#fff'

											} });

											$.ajax({

													type: "GET",
													async: false,
													data: {

															programaId: programaId

													},
													dataType: "json",
													url: "cancelarPorgramaCXP",
													success: function (data) {

															setTimeout($.unblockUI, 2000);
															setTimeout(function () {

																	var respuesta = JSON.parse(JSON.stringify(data));
																	if(respuesta.codigo == 200){

																			bootbox.dialog({

																					message: "Se ha cancelo el programa " + programa + " con exito.",
																					title: "Flujo de Efectivo",
																					buttons: {

																							success: {

																									label: "Ok",
																									className: "btn-success",
																									callback: function () {

																										consultarDatosInicio();

																									}

																							}

																					}

																			});

																	}
																	else{

																			setTimeout($.unblockUI, 2000);
																			bootbox.dialog({

																					message: respuesta.respuesta,
																					title: "Flujo de Efectivo",
																					buttons: {

																							success: {

																									label: "ok",
																									className: "btn-success"

																							}

																					}

																			});

																	}

															}, 2000);

													},
													error: function (xhr, ajaxOptions, thrownError) {

															$.unblockUI();
															var error = JSON.parse(xhr.responseText);
															bootbox.alert({

																	size: "large",
																	title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
																	message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
																	( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
																	( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
																	( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

															});

													}

											});

									}

							},
							default: {

									label: "No",
									className: "btn-default m-r-5 m-b-5"

							}

							}

			});

    });
$('#tableProgramas').on( 'click', 'button#btnAutorizarPrograma', function (e) {

	e.preventDefault();

	var tblProgramas = $('#tableProgramas').DataTable();
	var fila = $(this).closest('tr');
	var datos = tblProgramas.row(fila).data();
	var programaId = datos['DT_RowId'];
	var codigo = datos['PPCXP_Codigo'];
	var nombre = datos['PPCXP_Nombre'];
	var programa = codigo + " - " + nombre;

	bootbox.dialog({
		title: "Flujo de Efectivo",
		message: "¿Estás seguro de autorizar el programa "+ programa +"?",
		buttons: {
				success: {
						label: "Si",
						className: "btn-success m-r-5 m-b-5",
						callback: function () {
								autorizarProgramaPorId(programaId);
						}
				},
				default: {
						label: "No",
						className: "btn-default m-r-5 m-b-5"
				}

		}

	});
    

});
$('#tableProgramas').on( 'click', 'button#btnShowFile', function (e) {

	e.preventDefault();

	var tblProgramas = $('#tableProgramas').DataTable();
	var fila = $(this).closest('tr');
	var datos = tblProgramas.row(fila).data();
	var programaId = datos['DT_RowId'];
	var codigo = datos['PPCXP_Codigo'];
	var nombre = datos['PPCXP_Nombre'];
	var programa = codigo + " - " + nombre;
	window.location.href = "{{url().'/home/FINANZAS/consultaLayout/'}}"+programaId;

});

function autorizarProgramaPorId(programaId){

    $.ajax({

        cache: false,
        async: false,
        url: "autorizaProgramaPorId",
        data: {

            "programaId": programaId

        },
        type: "GET",
        success: function( datos ) {

            if(datos["Status"] == "Error"){
                BootstrapDialog.show({
                    title: 'Error',
                    type: BootstrapDialog.TYPE_DANGER,
                    message: datos["Mensaje"],
                    cssClass: 'login-dialog',
                    buttons: [{
                        label: 'Aceptar',
                        cssClass: 'btn-default',
                        action: function(dialog){
                            dialog.close();
                        }
                    }]
                });
            }
            else{

                BootstrapDialog.show({
                    title: 'Éxito',
                    type: BootstrapDialog.TYPE_PRIMARY,
                    message: "Se ha Autorizado el programa con éxito.",
                    cssClass: 'login-dialog',
                    buttons: [{
                        label: 'Aceptar',
                        cssClass: 'btn-default',
                        action: function(dialog){
                            dialog.close();
                            $('#tableProgramas').DataTable().ajax.reload();
                        }
                    }]
                });

            }

        },
        error: function (xhr, ajaxOptions, thrownError) {

            $.unblockUI();
            var error = JSON.parse(xhr.responseText);
            bootbox.alert({

                size: "large",
                title: "<h4><i class='fa fa-info-circle'></i> Alerta</h4>",
                message: "<div class='alert alert-danger m-b-0'> Mensaje : " + error['mensaje'] + "<br>" +
                ( error['codigo'] != '' ? "Código : " + error['codigo'] + "<br>" : '' ) +
                ( error['clase'] != '' ? "Clase : " + error['clase'] + "<br>" : '' ) +
                ( error['linea'] != '' ? "Línea : " + error['linea'] + "<br>" : '' ) + '</div>'

            });

            }

        });

}

//});//fin on load

}  //fin js_iniciador               
</script>
