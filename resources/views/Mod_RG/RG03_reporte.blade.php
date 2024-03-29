@extends('home')

            @section('homecontent')
            <style>
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
                .thh {
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
                .blank_row
                {
                    height: 10px !important; /* overwrites any other rules */
                    background-color: #FFFFFF;
                }
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Reporte Gerencial
                               <small>Sociedad: <b>{{$sociedad}}</b> </small>
                            <div class="pull-right width-full">
                                <a id="btn_pdf" class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/1') !!}" target="_blank" ayudapdf="1"><i
                                        class="fa fa-file-pdf-o"></i> Reporte PDF</a>
                            </div>
                            </h3>
                                        
                        </div>
                          
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
                            <ul class="nav nav-tabs" >
                                <li id="lista-tab1" class="active"><a onclick = "val_btn(1)" href="#default-tab-1" data-toggle="tab"
                                    aria-expanded="true">Balance General</a></li>
                                <li id="lista-tab2" class=""><a onclick = "val_btn(2)" href="#default-tab-2" data-toggle="tab"
                                    aria-expanded="false">Estado de Resultados</a></li>
                                <li id="lista-tab3" class=""><a onclick = "val_btn(3)" href="#default-tab-3" data-toggle="tab"
                                    aria-expanded="false">Estado de Costos</a></li>
                                <li id="lista-tab4" class=""><a onclick = "val_btn(4)" href="#default-tab-4" data-toggle="tab"
                                    aria-expanded="false">Inventarios</a></li>
                                <li id="lista-tab5" class=""><a onclick = "val_btn(5)" href="#default-tab-5" data-toggle="tab" 
                                    aria-expanded="false">Gtos Fabricación</a></li>
                                <li id="lista-tab6" class=""><a onclick = "val_btn(6)" href="#default-tab-6" data-toggle="tab"
                                    aria-expanded="false">Gtos Administración</a></li>
                                <li id="lista-tab7" class=""><a onclick = "val_btn(7)" href="#default-tab-7" data-toggle="tab"
                                    aria-expanded="false">Gtos Ventas</a></li>
                                <li id="lista-tab8" class=""><a onclick = "val_btn(8)" href="#default-tab-8" data-toggle="tab"
                                    aria-expanded="false">Gtos Financieros</a></li>
                                <li id="lista-tab8" class=""><a onclick = "hidebtn()" href="#default-tab-9" data-toggle="tab"
                                    aria-expanded="false">Reportes Adicionales</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="default-tab-1">
                                    <div class="container">                                                                                                                  
                                        @include('Mod_RG.RG03_reporte_BG01')                                                                                                   
                                    </div> 
                                </div>
                                                          
                                <div class="tab-pane fade " id="default-tab-2">
                                    <div class="container">                                                     
                                        @include('Mod_RG.RG03_reporte_ER')
                                    </div>
                                </div>

                                <div class="tab-pane fade " id="default-tab-3">
                                    <div class="container">                                                        
                                        @include('Mod_RG.RG03_reporte_EC')
                                    </div>
                                </div>                      
                                <div class="tab-pane fade " id="default-tab-4">
                                    <div class="container">                         
                                        @include('Mod_RG.RG03_reporte_Inv')
                                    </div>
                                </div>   
                                <div class="tab-pane fade " id="default-tab-5">
                                    <div class="container">                                                            
                                        @include('Mod_RG.RG03_reporte_GtosFab')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-6">
                                    <div class="container">                                     
                                        @include('Mod_RG.RG03_reporte_GtosAdmon')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-7">
                                    <div class="container">                                                 
                                        @include('Mod_RG.RG03_reporte_GtosVentas')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-8">
                                    <div class="container">                                                                            
                                        @include('Mod_RG.RG03_reporte_GtosFinanzas')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-9">
                                    <div class="container">                                                                            
                                        @include('Mod_RG.RG03_reporte_Adicionales')
                                    </div>
                                </div>                   
                                                   
                            </div>  <!-- /.tab-content -->                     
                        </div>  <!-- /.row -->                     
                    </div>   <!-- /.container -->

                    @endsection

<script>
                    
    function js_iniciador() {
        startjs()                    
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

                {{-- window.open("{{ URL::asset('ayudas_pdf') }}"+"/AY_00.pdf","_blank"); --}}
                // var namefile= 'RG_'+$('#btn_pdf').attr('ayudapdf')+'.pdf';
                //var pathfile = "{{ URL::asset('ayudas_pdf') }}"+"/"+namefile;                           
                // window.open(pathfile,"_blank");
                }
        }

        var data;
        var xhrBuscador = null;
        var wrapper = $('#page-wrapper2');
        var resizeStartHeight = wrapper.height();
        var height = (resizeStartHeight * 85)/100;
        if ( height < 200 ) { height=200; } $('input[name="date" ]').change( function(e) { console.log(this.value)
            e.preventDefault(); $('#btn_reporte').attr('href', "{!! url('home/reporte/05 PRESUPUESTOS/" +this.value+"') !!}");
            }); var meses=new Array
            ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            var diasSemana=new Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"); var f=new Date(); var
            hours=f.getHours(); var ampm=hours>= 12 ? 'pm' : 'am';
            var fecha = 'ACTUALIZADO: '+ diasSemana[f.getDay()] + ', ' + f.getDate() + ' de ' + meses[f.getMonth()] + ' del ' +
            f.getFullYear()+', A LAS '+hours+":"+f.getMinutes()+ ' ' + ampm;
            var f = fecha.toUpperCase();
        
            var table = $('#tableCosto').DataTable({
            language:{
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            responsive: true,
            searching: true,
            iDisplayLength: 5000,
            aaSorting: [],
            deferRender: true,
            dom: 'Brti',
            bInfo: false,
            fixedColumns: false,
            paging: false,
            buttons:[
            {
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            className: "btn-success",
            extend: 'excelHtml5',
            title: 'ESTADO DE COSTOS '+"{{$ejercicio}}",
            message: "{{$sociedad}}",
            messagethree: f
            },
            {
            text: '<i class="fa fa-file-pdf-o"></i> Pdf',
            className: "btn-danger",
            action: function (e, dt, node, config) {
            var datos = table.rows().data().toArray();
            var json = JSON.stringify(datos);
            $.ajax({
                type: 'POST',
                url: routeapp + 'home/reporte/ajaxtosession/estadoCostoPDF',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                "_token": "{{ csrf_token() }}",
                "arr": [json,"{{$sociedad}}","{{$ejercicio}}", "{{$periodo}}",  "{{$leyenda_fecha_corte_estados}}"]
            
                },
                success: function (data) {
                window.open(routeapp + 'estadoCostoPDF', '_blank')
                }
            });
            }
            }
            ]
            });
    }
</script>                             
                <script>
                   function val_btn(val) {
                       $('#btn_pdf').show();
                       if (val == 3) {
                           $('#btn_pdf').hide();
                       } else {
                            $('#btn_pdf').show();
                            $('#btn_pdf').attr('href', "{!! url('home/ReporteGerencial/"+val+"') !!}");
                            $('#btn_pdf').attr('ayudapdf', val);   
                       }
                                                 
                    }
                   function mostrara(){
                       //estp es para los reportes adicionales que se muestran PDF
                        var name = $('#cbo_reporte option:selected').val();
                        if (name.length > 0 && name != '') {                            
                            window.open("{{ URL::asset('PDF _ReporteGerencial') }}"+"/"+name,"_blank");
                        }
                    }
                   function hidebtn(){
                       $('#btn_pdf').hide();
                    }
                   
                </script>
