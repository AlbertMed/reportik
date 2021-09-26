@extends('home')

@section('homecontent')
    {!! Html::script('assets/js/digitalStorageConfig.js') !!}
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <div class="container">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-11">
                <h3 class="page-header">Configuracion para Almacen Digital</h3>
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
        <div class="row">
            <form id="insertConfigRow" method="POST" action="<?= url('/home/ALMACENDIGITAL/config/new') ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button class="btn btn-info" type="submit" id="newConfig">Nueva Configuracion</button>
            </form>
        </div>
        <div class="row">
            <table class="table display compact" id="digStoreConfigTable">
                <thead>
                    <tr>
                        @foreach ($configurationHeaders as $header)
                            <td>{{ $header }}</td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($configurationValues as $row)
                        <tr>
                            <td> <a href="<?= url('/home/ALMACENDIGITAL/config/edit/' . $row->ID) ?>"
                                    class="btn btn-primary">Editar</a></td>
                            <td>{{ $row->CREATED_AT }}</td>
                            <td>{{ $row->MENU_NAME }}</td>
                            <td>{{ $row->GROUP_NAME }}</td>
                            <td>{{ $row->URL }}</td>
                            <td>{{ $row->ENABLED }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row">
        </div>
        <div class="row">
        </div>

    </div>
@endsection
