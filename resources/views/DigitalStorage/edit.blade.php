@extends('home')
@section('homecontent')
    {!! Html::script('assets/js/digitalStorageInsert.js') !!}
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <div class="container">
        <div class="row">
            <div class="col-md-11">
                <input type="hidden" name="baseURLAlmacen" id="baseURLAlmacen"
                    value="<?= url('/home/AlmacenDigital/') ?>" />
                @if ($insert)
                    <h3 class="page-header">Ingresar nuevo documento {{ $moduleType }}</h3>
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
                @if ($insert)
                    <div class="row">
                        <button id="selectOV" class="btn btn-success">Seleciona una Orden de Trabajo</button>
                    </div>
                    <div class="row">
                        <table class="table tableFixHead display compact" id="digStoreTable" style="display:none">
                            <thead style="">
                                <tr>
                                    {{-- <th scope="col">Llave ID</th> --}}
                                    <th scope="col">GROUPO COM</th>
                                    <th scope="col">DOC ID</th>
                                    <th scope="col">ARCHIVO 1</th>
                                    <th scope="col">ARCHIVO 2</th>
                                    <th scope="col">ARCHIVO 3</th>
                                    <th scope="col">ARCHIVO 4</th>
                                    <th scope="col">ARCHIVO XML</th>
                                    <th scope="col">Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody id="digStoreListDivResult">
                            </tbody>
                        </table>
                    </div>
                @endif
                @if ($insert)
                    <form action="<?= url('/home/AlmacenDigital/store') ?>" method="post" enctype="multipart/form-data"
                        id="digStoreUpd">
                    @else
                        <form action="<?= url('/home/AlmacenDigital/update', [$digRowDetails->id, $moduleType]) ?>"
                            method="post" enctype="multipart/form-data" id="digStoreUpd">
                @endif

                <input type="hidden" name="user_modified" value="{{ $user->nomina }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="moduleType" name="moduleType" value="{{ $moduleType }}">
                @if (in_array($moduleType, $deptIds) && $insert)
                    <div class="row">
                        <div class="col-md-3">
                            <label for="department">Departamento</label>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="department" id="department">
                                @foreach ($deptRows as $rows)
                                    <option value="{{ $rows->DEP_Codigo }}">{{ $rows->DEP_Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                @foreach ($inputType as $name => $rows)
                    <div class="row">
                        <div class="col-md-3">
                            <label for="{{ $name }}">{{ $rows['title'] }}</label>
                        </div>
                        @if ($rows['type'] == 'file' and !empty($rows['value']))
                            <div class="col-md-3">
                                <a href="{{ $rows['value'] }}" target="_blank"> Ver {{ $rows['title'] }}</a>
                            </div>
                        @endif
                        <div class="col-md-3">
                            @if ($rows['readonly'])
                                <span>{{ $rows['value'] }}</span>
                            @else
                                <input type="{{ $rows['type'] }}" name="{{ $name }}"
                                    class="{{ $rows['class'] }}" id="{{ $name }}"
                                    value="{{ $rows['value'] }}">
                            @endif

                        </div>
                    </div>
                @endforeach
                <div class="row">
                    @if ($insert)
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
