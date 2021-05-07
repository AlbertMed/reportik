@extends('home')

@section('homecontent')


    <div class="container">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-11">
                <h3 class="page-header">Almacen Digital</h3>
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

            <div class="panel-heading">Lista de Archivos</div>
            <div class="panel-body">
            <div class="row">
                <h3>Filters</h3>
                <div class="row">
                    <form action="AlmacenDigital/find/" >
                        
                            {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">     --}}
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="document_id" placeholder="OV Numero" aria-label="OV Numero" aria-describedby="basic-addon2">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-info" type="submit">Button</button>
                            </div>
                        
                    </form>
                    <div class="col-md-3">
                    </div>    
                    <div class="col-md-3">
                        <form action="AlmacenDigital/crear" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button class="btn btn-info" type="submit">Ingresar Datos</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($digStoreList)
                
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
                    <tbody>
                        
                        @foreach ( $digStoreList as $digStoreRow )
                        <tr>
                            <th scope="row">{{$digStoreRow->LLAVE_ID}}</th>
                            <td>{{$digStoreRow->GRUPO_ID}}</td>
                            <td>{{$digStoreRow->DOC_ID}}</td>
                            @if ($digStoreRow->ARCHIVO_1 != "")
                            <td><a href="../{{$digStoreRow->ARCHIVO_1}}" target="blank">Ver Documento</a></td>    
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
                            <td><a href="edit/{{$digStoreRow->id}}">Editar</a></td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @else
                  <h4>No se encontro ninguna informacion!</h4>
                  @endif
            </div>
            </div>
        </div>
    </div> <!-- /.container -->
@endsection
<script>
</script>
