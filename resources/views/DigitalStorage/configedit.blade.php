@extends('home')

@section('homecontent')
    {!! Html::script('assets/js/digitalStorageConfig.js') !!}
    <div class="container">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-11">
                <h3 class="page-header">Nueva ruta para Almacen Digital</h3>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($values)
            <form id="insertConfigRow" method="POST" action="<?= url('/home/ALMACENDIGITAL/config/updateConfig') ?>">
                {!! Form::hidden('id', $values->ID) !!}
            @else
                <form id="insertConfigRow" method="POST" action="<?= url('/home/ALMACENDIGITAL/config/insertConfig') ?>">
        @endif
        <div class="row">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="col-md-3">
                {!! Form::label('group_name', 'Nombre Grupo', ['class' => '']) !!}
            </div>
            <div class="col-md-3">
                @if ($values)
                    <input type="text" name="group_name" placeholder="Nombre Grupo" value="<?= $values->GROUP_NAME ?>" id=""
                        class="form-control" required>
                @else <input type="text" name="group_name" placeholder="Nombre Grupo" value="" id=""
                        class="form-control" required>
                @endif

            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                {!! Form::label('url', 'URL Prefijo', ['class' => '']) !!}
            </div>
            <div class="col-md-3">
                @if ($values)
                    <input type="url" name="url" id="url" placeholder="URL" value="<?= $values->URL ?>" class="form-control"
                        required>
                @else <input type="url" name="url" id="url" placeholder="URL" value="" class="form-control" required>
                @endif

            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                {!! Form::label('enabled', 'Habilitado', ['class' => '']) !!}
            </div>
            <div class="col-md-3">
                @if ($values)
                    <input type="checkbox" name="enabled" <?= $values->ENABLED == '1' ? 'checked' : '' ?> id=""
                        class="form-check-label">
                @else <input type="checkbox" name="enabled" id="" class="form-check-label">
                @endif

            </div>
        </div>
        <button class="btn btn-info" type="submit" id="createConfig">Guardar Configuracion</button>
        </form>
        <div class="row">
        </div>

    </div>
@endsection
