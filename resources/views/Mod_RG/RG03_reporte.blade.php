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
                               Reporte Gerencial - ITEKNIA EQUIPAMIENTO, S.A. DE C.V.
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
                            <ul class="nav nav-tabs" >
                                <li id="lista-datos-facturacion" class="active"><a href="#default-tab-1" data-toggle="tab"
                                        aria-expanded="true">BG</a></li>
                                <li id="lista-criterios-administracion" class=""><a href="#default-tab-2" data-toggle="tab"
                                        aria-expanded="false">ER</a></li>
                                <li id="lista-contactos" class=""><a href="#default-tab-3" data-toggle="tab" aria-expanded="false">Gastos Fabricación</a>
                                </li>
                                <li id="lista-sucursales" class=""><a href="#default-tab-4" data-toggle="tab"
                                        aria-expanded="false">Gastos Admon</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="default-tab-1">
                                    <div class="container">
                                        <legend class="pull-left width-full">Posición Financiera, Balance General</legend>                                    
                                        @include('Mod_RG.RG03_reporte_BG01')                                                                                                   
                                        </div> 
                                    </div>
                                                          
                                <div class="tab-pane fade " id="default-tab-2">
                                    <div class="container">
                                        <legend class="pull-left width-full">Estado de Resultados</legend>
                                        @include('Mod_RG.RG03_reporte_ER')
                                    </div>
                                </div>                      
                                <div class="tab-pane fade " id="default-tab-3">
                                    <legend class="pull-left width-full">*****</legend>
                                    <div class="row">
                                        <div class="col-md-6">                                           
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label title="Nombre oficial y legal. Máximo 100 dígitos, campo requerido.">Razón Social
                                                    <strong>(*)</strong></label>
                                                <input type="text" maxlength="100" class="form-control input-sm" id="input-razon-social"
                                                    autocomplete="false">
                                            </div>
                                        </div>                                        
                                    </div>
                                                          
                                    </div>                       
                                <div class="tab-pane fade " id="default-tab-4">
                                    <legend class="pull-left width-full">*****</legend>
                                    <div class="row">
                                        <div class="col-md-6">                                           
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label title="Nombre oficial y legal. Máximo 100 dígitos, campo requerido.">Razón Social
                                                    <strong>(*)</strong></label>
                                                <input type="text" maxlength="100" class="form-control input-sm" id="input-razon-social"
                                                    autocomplete="false">
                                            </div>
                                        </div>                                        
                                    </div>
                                                          
                                    </div>                       
                                    </div>                       
                                    </div>                       
                    </div>   <!-- /.container -->

                    @endsection

                    @section('homescript')

                      

                    @endsection                                      
                <script>
                   

                </script>
