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
        <input type="hidden" id="baseURLAlmacen" value="<?=url("/home/AlmacenDigital/")?>">
        <input type="hidden" id="baseURL" value="<?=url("/")?>">
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <form id="almacenDigitalCreate" method="POST" action="<?= url("/home/AlmacenDigital/crear/") ?>">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button class="btn btn-info" type="submit" id="newDigStore">Ingresar Datos</button>
                </form>
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
                    <div class="panel panel-default">
                        <form id="DigStorSalesForm">
                            <div class="row panel-heading" style="margin:0px">
                                <h3 class="col-md-3 panel-title">Almacen Digital Lista</h3>
                                <h3 class="col-md-9 panel-title">
                                    {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">     --}}
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
                                    
                                    @foreach ( $digStoreList as $digStoreRow )
                                    <tr>
                                        <th scope="row">{{$digStoreRow->LLAVE_ID}}</th>
                                        <td>{{$digStoreRow->GRUPO_ID}}</td>
                                        <td>{{$digStoreRow->DOC_ID}}</td>
                                        @if ($digStoreRow->ARCHIVO_1 != "")
                                        <td><a href="../../{{$digStoreRow->ARCHIVO_1}}" target="blank">Ver Documento</a></td>    
                                        @else
                                        <td></td>
                                        @endif
                                        @if ($digStoreRow->ARCHIVO_2 != "")
                                        <td><a href="../{{$digStoreRow->ARCHIVO_2}}" target="blank">Ver Documento</a></td>    
                                        @else
                                        <td></td>
                                        @endif
                                        @if ($digStoreRow->ARCHIVO_3 != "")
                                        <td><a href="../{{$digStoreRow->ARCHIVO_3}}" target="blank">Ver Documento</a></td>    
                                        @else
                                        <td></td>
                                        @endif
                                        @if ($digStoreRow->ARCHIVO_4 != "")
                                        <td><a href="../{{$digStoreRow->ARCHIVO_4}}" target="blank">Ver Documento</a></td>    
                                        @else
                                        <td></td>
                                        @endif
                                        @if ($digStoreRow->ARCHIVO_XML != "")
                                        <td><a href="../{{$digStoreRow->ARCHIVO_XML}}" target="blank">Ver Documento</a></td>    
                                        @else
                                        <td></td>
                                        @endif
                                        <td><a href="<?=url("/home/AlmacenDigital/edit/" . $digStoreRow->id)?>">Editar</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </form>
    </div> <!-- /.container -->
@endsection
