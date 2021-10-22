    @extends('home')

    @section('homecontent')
        {!! Html::script('assets/js/digitalStorage.js') !!}
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
        <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <div class="container">
            <!-- Page Heading -->
            <div class="row FixHeaders">
                <div class="col-md-9">
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
                            <button class="btn btn-info" type="submit" id="syncTables">Sincronizar Tablas</button>
                        </form>
                    @endif
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
                <div class="panel-body" id="digStoreListDiv" class="">

                    <table class="table display compact tablefixHead" id="digStoreTable">
                        <thead style="height: 10px !important; overflow: scroll;">
                            <tr>
                                {{-- <th scope="col">Llave ID</th> --}}
                                @if ($moduleType == 'SAC')
                                    <th scope="col">OV</th>
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
            </div>

        </div>
        <style>
            .FixHeaders {
                overflow: auto !important;
                height: 110px !important;
                position: sticky !important;
                top: 1 !important;
                z-index: 3 !important;
                background: white;
            }

            #dataTables_length,
            .dataTables_length {
                overflow: auto !important;
                height: 70px !important;
                position: sticky !important;
                top: 110 !important;
                z-index: 3 !important;
                background: white;
                width: 62.4116vw;
            }

            #dataTables_filter,
            .dataTables_filter {
                overflow: auto !important;
                height: 70px !important;
                position: sticky !important;
                top: 110 !important;
                z-index: 3 !important;
                background: white;
            }

            .tableDivResultOverhead {
                overflow: auto !important;
                /* height: 30px !important; */
                position: sticky !important;
                /* top: 250 !important; */
                /* z-index: -1 !important; */
                /* background: white;*/
            }

            .tableFixHead {
                overflow: auto !important;
                /* height: 180px !important; */
            }

            .tableFixHead thead th {
                position: sticky !important;
                top: 180 !important;
                z-index: 3 !important;
            }

            .tableFixHead tbody tr {
                max-height: 26px !important;
            }

            .page-header {
                padding-bottom: 9px;
                margin: 40px 0 20px;
                border-bottom: 0px solid #eee;
            }

        </style>
    @endsection
