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
                                <option value="{{ $rows->DEP_Codigo }}_{{ $rows->DEP_Nombre }}">
                                    {{ $rows->DEP_Codigo }} -- {{ $rows->DEP_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($dataArray['insert'])
                    <div class="row">
                        <div class="col-md-3">
                            <label for="searchOT">Buscar OT</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchOT" placeholder="Ingresa una OT">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" id="btnSearchOT">Buscar</button>
                        </div>
                    </div>
                    <div class="row" id="OTInputFormDiv">
                        <div class="col-md-4 col-sm-8 col-lg-4">
                            @foreach ($dataArray['insertColumns'] as $column)
                                <div class="row">
                                    <div class="col-md-6 col-sm-8 col-lg-6">
                                        <label for="{{ $column['label'] }}"> {{ $column['label'] }}</label>
                                    </div>
                                    <div class="col-md-6 col-sm-8 col-lg-6">
                                        <input class="form-control" type="{{ $column['type'] }}"
                                            name="{{ $column['name'] }}" id="{{ $column['id'] }}"
                                            placeholder="{{ $column['label'] }}" {{ $column['readonly'] }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-1 col-sm-8 col-lg-1">
                        </div>
                        <div class="col-md-4 col-sm-8 col-lg-4">
                            <table class="table">
                                <thead>
                                    <tr id="searchOTThead">
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="searchOTTbody" scope="row">

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" id="submitButtonDiv">
                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Guardar Datos</button>
                        </div>
                    </div>
                @endif
            </div>
            </form>
        </div>
    </div> <!-- /.container -->
@endsection
