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
                                <small></small>
                            </h3>
                                        
                        </div>
                        <div class="col-md-12 ">
                            @include('partials.alertas')
                        </div>
                    </div>
                         <div class="row" id="panel-body-datos">
                            <input type="text" style="display: none" class="form-control input-sm" id="input-cliente-id">
                            <ul class="nav nav-tabs" style="background: #008a8a">
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
