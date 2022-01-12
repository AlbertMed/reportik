    @extends('home')

    @section('homecontent')
        {!! Html::script('assets/js/digitalStorage.js') !!}
        <link rel="stylesheet" type="text/css"
            href="https://cdn.datatables.net/v/dt/dt-1.11.3/af-2.3.7/b-2.0.1/cr-1.5.5/date-1.1.1/fc-4.0.1/fh-3.2.0/kt-2.6.4/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.5/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.css" />

        <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
        <script type="text/javascript"
                src="https://cdn.datatables.net/v/dt/dt-1.11.3/af-2.3.7/b-2.0.1/cr-1.5.5/date-1.1.1/fc-4.0.1/fh-3.2.0/kt-2.6.4/r-2.2.9/rg-1.1.3/rr-1.2.8/sc-2.0.5/sb-1.3.0/sp-1.4.0/sl-1.3.3/datatables.min.js">
        </script>
        <div class="container">
            <!-- Page Heading -->
            <div class="row FixHeaders">
                <div class="col-md-8">
                    <h3 class="page-header">Almacén Digital {{ $titlePage }}</h3>
                </div>
                <?php if($editable): ?>
                <div class="col-md-1 page-header">
                    <form id="almacenDigitalCreate" method="POST"
                        action="<?= url('/home/AlmacenDigital/crear', [$moduleType]) ?>">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <button class="btn btn-info" type="submit" id="newDigStore">Ingresar Datos</button>
                    </form>
                </div>
                <?php endif; ?>
                <div class="col-md-1 page-header">
                    @if ($moduleType != 'SID')
                        <form id="almacenDigitalSync" method="POST"
                            action="<?= url('/home/AlmacenDigital/syncOrdersWithDigitalStorage/') ?>">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="moduleType" id="moduleType" value="<?= $moduleType ?>" />
                            <button class="btn btn-info btn-sm" type="submit" id="syncTables" data-toggle="modal"
                                data-target="#syncAlertModal">Sincronizar Tablas</button>
                        </form>
                    @endif
                </div>
                <div class="col-md-1 page-header">
                    <input type="button" class="btn btn-info btn-sm" value="Reiniciar Busqueda" id="resetSearchBtn">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" id="baseURLAlmacen" value="<?= url('/home/AlmacenDigital/') ?>" />
                    <input type="hidden" id="baseURL" value="<?= url('/') ?>" />
                </div>
            </div>
            <div class="row">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="panel panel-default hidden">
                    <form id="DigStorSalesForm">
                        <div class="row panel-heading" style="margin:0px">
                            <h3 class="col-md-3 panel-title">Almacén Digital Lista {{ $moduleType }}</h3>
                            <h3 class="col-md-9 panel-title">
                                {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="document_id" placeholder="Documento"
                                        aria-label="Documento" aria-describedby="basic-addon2">
                                </div>display compact
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="group_id" placeholder="Grupo"
                                        aria-label="Grupo" aria-describedby="basic-addon2">
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" id="moduleType" value="<?= $moduleType ?>" />
                                    <input type="hidden" id="editable" name="editable" value="<?= $editable ?>" />
                                    <button class="btn btn-info" type="submit">Buscar</button>
                                </div>
                                <div class="col-md-3">

                                </div>
                            </h3>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <input type="hidden" name="GROUP_ID" value="{{ $request->input('GROUP_ID') }}" id="GROUP_ID">
            </div>
            <div class="row">
                <div class="panel-body" id="digStoreListDiv1" class="">

                    <table class="table display compact tablefixHead" id="digStoreTable1">
                        <!--thead style="height: 10px !important; overflow: scroll;"-->
                        <thead style="">
                            <tr>
                                <!--th scope="col">Llave ID</th-->
                                @if ($moduleType == 'SAC')
                                    <th scope="col">OV</th>
                                @elseif($moduleType == 'COM')
                                    <th scope="col">OC</th>
                                @else
                                    <th scope="col">GRUPO {{ $moduleType }}</th>
                                @endif
                                <th scope="col">DOC ID</th>
                                <th scope="col">ARCHIVO 1</th>
                                <th scope="col">ARCHIVO 2</th>
                                <th scope="col">ARCHIVO 3</th>
                                <th scope="col">ARCHIVO 4</th>
                                <th scope="col">ARCHIVO XML</th>
                                <?php if($editable): ?>
                                <th scope="col">Ver/Editar</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="digStoreListDivResult" class="tableDivResultOverhead">
                        </tbody>
                    </table>
                </div>
                <div class="panel-body" id="digStoreListDiv2" class="">
                    <table class="table display compact tablefixHead" id="digStoreTable2">
                        <!--thead style="height: 10px !important; overflow: scroll;"-->
                        <thead style="">
                            <tr>
                                <!--th scope="col">Llave ID</th-->
                                @if ($moduleType == 'SAC')
                                    <th scope="col">OV</th>
                                @elseif($moduleType == 'COM')
                                    <th scope="col">OC</th>
                                @else
                                    <th scope="col">GRUPO {{ $moduleType }}</th>
                                @endif
                                <th scope="col">DOC ID</th>
                                <th scope="col">ARCHIVO 1</th>
                                <th scope="col">ARCHIVO 2</th>
                                <th scope="col">ARCHIVO 3</th>
                                <th scope="col">ARCHIVO 4</th>
                                <th scope="col">ARCHIVO XML</th>
                                <?php if($editable): ?>
                                <th scope="col">Ver/Editar</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="digStoreListDivResult2" class="tableDivResultOverhead">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="modal fade" id="syncAlertModal" tabindex="-1" role="dialog" aria-labelledby="syncAlertModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="syncAlertModalLabel">Sincronizando</h5>
                    </div>
                    <div class="modal-body">

                        <div class="loader">Cargando...</div>
                        Su peticion esta siendo procesada.
                        Porfavor esperare un momento.
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <style>
            table thead tr th {
                background-color: black;
            }

            .page-header {
                padding-bottom: 9px;
                margin: 40px 0 20px;
                border-bottom: 0px solid #eee;
            }

            .loader {
                color: #000000;
                font-size: 50px;
                text-indent: -9999em;
                overflow: hidden;
                width: 1em;
                height: 1em;
                border-radius: 50%;
                margin: 10px auto;
                position: relative;
                -webkit-transform: translateZ(0);
                -ms-transform: translateZ(0);
                transform: translateZ(0);
                -webkit-animation: load6 1.7s infinite ease, round 1.7s infinite ease;
                animation: load6 1.7s infinite ease, round 1.7s infinite ease;
            }

            @-webkit-keyframes load6 {
                0% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }

                5%,
                95% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }

                10%,
                59% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
                }

                20% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
                }

                38% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
                }

                100% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }
            }

            @keyframes load6 {
                0% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }

                5%,
                95% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }

                10%,
                59% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
                }

                20% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
                }

                38% {
                    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
                }

                100% {
                    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
                }
            }

            @-webkit-keyframes round {
                0% {
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }

            @keyframes round {
                0% {
                    -webkit-transform: rotate(0deg);
                    transform: rotate(0deg);
                }

                100% {
                    -webkit-transform: rotate(360deg);
                    transform: rotate(360deg);
                }
            }

        </style>
    @endsection
