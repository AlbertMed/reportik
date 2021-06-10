@extends('home')

@section('homecontent')
    {!! Html::script('assets/js/digitalStorage.js') !!}
    <div class="container">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-11">
                <h3 class="page-header">Almacen Digital</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <input type="hidden" id="baseURLAlmacen" value="<?= url('/home/AlmacenDigital/') ?>"/>
                <input type="hidden" id="baseURL" value="<?= url('/') ?>"/>
        </div>
        <div class="col-md-3">
            <form id="almacenDigitalSync" method="POST" action="<?= url('/home/AlmacenDigital/syncOrdersWithDigitalStorage/') ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button class="btn btn-info" type="submit" id="syncTables">Sincronizar Tablas</button>
            </form>
        </div>
        <div class="col-md-3">
            <form id="almacenDigitalCreate" method="POST" action="<?= url('/home/AlmacenDigital/crear/') ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <button class="btn btn-info" type="submit" id="newDigStore">Ingresar Datos</button>
            </form>
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
        <div class="panel panel-default">
            <form id="DigStorSalesForm">
                <div class="row panel-heading" style="margin:0px">
                    <h3 class="col-md-3 panel-title">Almacen Digital Lista</h3>
                    <h3 class="col-md-9 panel-title">
                        {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="document_id" placeholder="Documento" aria-label="Documento" aria-describedby="basic-addon2">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" name="group_id" placeholder="Grupo" aria-label="Grupo" aria-describedby="basic-addon2">
                        </div>
                        <div class="col-md-2">
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
            <div class="panel-body " id="digStoreListDiv" >
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Llave ID</th>
                        <th scope="col">GROUPO ID</th>
                        <th scope="col">DOC ID</th>
                        <th scope="col">ARCHIVO 1</th>
                        <th scope="col">ARCHIVO 2</th>
                        <th scope="col">ARCHIVO 3</th>
                        <th scope="col">ARCHIVO 4</th>
                        <th scope="col">ARCHIVO XML</th>
                        <th scope="col">Ver/Editar</th>
                    </tr>
                    </thead>
                    <tbody id="digStoreListDivResult">
                    </tbody>
                </table>
            </div>
        </div>
                    
    </div>
@endsection
