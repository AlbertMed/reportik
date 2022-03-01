@extends('home')
@section('homecontent')
    {!! Html::script('assets/js/SIDInsert.js') !!}
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <div class="container">
        <div class="row">
            <div class="col-md-11">
                <input type="hidden" name="baseURLAlmacen" id="baseURLAlmacen"
                    value="<?= url('/home/AlmacenDigital/') ?>" />
                @if ($dataArray['insert'])
                    <h3 class="page-header">Ingresar nuevo documento {{ $dataArray['module_type'] }}</h3>
                @else
                    <h3 class="page-header">Detalles Almacen de documento : {{ $digRowDetails->DOC_ID }}</h3>
                @endif
            </div>
        </div>
        <div class="panel panel-default">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="panel-body">
                @if ($dataArray['insert'])
                    <div class="row">
                        <button id="selectOV" class="btn btn-success">Seleciona una Orden de Trabajo</button>
                    </div>
                    <div class="row" id="ordenTrabajoID">
                        <table class="table tableFixHead display compact" id="digStoreTable" style="">
                            <thead style="">
                                <tr>
                                    @foreach ($dataArray['orden_trabajo']['columns'] as $columnName => $row)
                                        <th scope="col">{{ $row['title'] }}</th>
                                    @endforeach
                                    <th scope="col">Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody id="digStoreListDivResult">

                            </tbody>
                        </table>
                    </div>
                @endif
                @if ($dataArray['insert'])
                    <form action="<?= url('/home/AlmacenDigital/store') ?>" method="post" enctype="multipart/form-data"
                        id="digStoreUpd">
                    @else
                        <form
                            action="<?= url('/home/AlmacenDigital/update', [$digRowDetails->id, $dataArray['module_type']]) ?>"
                            method="post" enctype="multipart/form-data" id="digStoreUpd">
                @endif

                <input type="hidden" name="user_modified" value="{{ $user->nomina }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="moduleType" name="moduleType" value="{{ $dataArray['module_type'] }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="department">Departamento</label>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="department" id="department">
                            @foreach ($dataArray['data_area'] as $rows)
                                <option value="{{ $rows->DEP_Codigo }}">
                                    {{ $rows->DEP_Codigo }} --
                                    {{ $rows->DEP_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    @if ($dataArray['insert'])
                        <button type="submit" class="btn btn-info">Ingresar Datos</button>
                    @else
                        <button type="submit" class="btn btn-info">Guardar Cambios</button>
                    @endif

                </div>
            </div>
            </form>
        </div>
    </div> <!-- /.container -->
@endsection
<script>
    function js_iniciador() {
        $('#digStoreUpd').submit(function(e) {

            //submitForm = $( this ).serialize()

        });
    }
</script>
