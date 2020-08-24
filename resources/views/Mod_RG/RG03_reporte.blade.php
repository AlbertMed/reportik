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
            </style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Reporte Gerencial
                                <small>ITEKNIA EQUIPAMIENTO, S.A. DE C.V.</small>
                            </h3>
                                        
                        </div>
                    <legend class="pull-left width-full">Periodo: {{$nombrePeriodo}}/{{$ejercicio}}
                    <small>Actualizado: {{date('d-m-Y h:i a', strtotime("now"))}}</small>
                    </legend>
                            
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
                            <ul class="nav nav-tabs" >
                                <li id="lista-tab1" class="active"><a href="#default-tab-1" data-toggle="tab"
                                    aria-expanded="true">Balance General</a></li>
                                <li id="lista-tab2" class=""><a href="#default-tab-2" data-toggle="tab"
                                    aria-expanded="false">Estado de Resultados</a></li>
                                <li id="lista-tab3" class=""><a href="#default-tab-3" data-toggle="tab"
                                    aria-expanded="false">Estado Contable</a></li>
                                <li id="lista-tab4" class=""><a href="#default-tab-4" data-toggle="tab"
                                    aria-expanded="false">Inventarios</a></li>
                                <li id="lista-tab5" class=""><a href="#default-tab-5" data-toggle="tab" 
                                    aria-expanded="false">Gtos Fabricación</a></li>
                                <li id="lista-tab6" class=""><a href="#default-tab-6" data-toggle="tab"
                                    aria-expanded="false">Gtos Administración</a></li>
                                <li id="lista-tab7" class=""><a href="#default-tab-7" data-toggle="tab"
                                    aria-expanded="false">Gtos Ventas</a></li>
                                <li id="lista-tab8" class=""><a href="#default-tab-8" data-toggle="tab"
                                    aria-expanded="false">Gtos Financieros</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="default-tab-1">
                                    <div class="container">  
                                        <a class="btn btn-danger btn-sm" 
                                            href="{!! url('home/ReporteGerencial/1') !!}" target="_blank"><i
                                            class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                                                          
                                        @include('Mod_RG.RG03_reporte_BG01')                                                                                                   
                                    </div> 
                                </div>
                                                          
                                <div class="tab-pane fade " id="default-tab-2">
                                    <div class="container">
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/2') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                        
                                        @include('Mod_RG.RG03_reporte_ER')
                                    </div>
                                </div>

                                <div class="tab-pane fade " id="default-tab-3">
                                    <div class="container">   
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/3') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                     
                                        @include('Mod_RG.RG03_reporte_EC')
                                    </div>
                                </div>                      
                                <div class="tab-pane fade " id="default-tab-4">
                                    <div class="container">
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/4') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                        
                                        @include('Mod_RG.RG03_reporte_Inv')
                                    </div>
                                </div>   
                                <div class="tab-pane fade " id="default-tab-5">
                                    <div class="container">                    
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/5') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                    
                                        @include('Mod_RG.RG03_reporte_GtosFab')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-6">
                                    <div class="container">               
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/6') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                         
                                        @include('Mod_RG.RG03_reporte_GtosAdmon')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-7">
                                    <div class="container">  
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/7') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                      
                                        @include('Mod_RG.RG03_reporte_GtosVentas')
                                    </div>
                                </div>                   
                                <div class="tab-pane fade " id="default-tab-8">
                                    <div class="container">  
                                        <a class="btn btn-danger btn-sm" href="{!! url('home/ReporteGerencial/8') !!}" target="_blank"><i
                                                class="fa fa-file-pdf-o"></i> Reporte PDF</a>                                      
                                        @include('Mod_RG.RG03_reporte_GtosFinanzas')
                                    </div>
                                </div>                   
                                                   
                            </div>  <!-- /.tab-content -->                     
                        </div>  <!-- /.row -->                     
                    </div>   <!-- /.container -->

                    @endsection

                    @section('homescript')

                      

                    @endsection                                      
                <script>
                   

                </script>
