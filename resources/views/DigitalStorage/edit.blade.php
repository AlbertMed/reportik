@extends('home')
@section('homecontent')
    <div class="container">
        <div class="row">
            <div class="col-md-11">
                @if ($insert)
                <h3 class="page-header">Ingresar nuevo documento</h3>
                @else
                <h3 class="page-header">Detalles Almacen de documento : {{$digRowDetails->DOC_ID}}</h3>
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
            @if ($insert)
            <form action="store" method="post" enctype="multipart/form-data" id="digStoreUpd">
            @else
            <form action="../update/{{$digRowDetails->id}}" method="post" enctype="multipart/form-data" id="digStoreUpd">    
            @endif
            
                <input type="hidden" name="user_modified" value="{{$user->nomina}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="panel-body">
                    @foreach ($inputType as $name => $rows)
                        <div class="row">
                            <div class="col-md-3">
                                <label for="{{$name}}">{{$rows["title"]}}</label>
                            </div>
                            @if ($rows["type"] == "file" and !empty($rows["value"]))
                                <div class="col-md-3">
                                    <a href="/{{$rows["value"]}}" > Ver {{$rows["title"]}}</a>
                                </div>
                            @endif
                            <div class="col-md-3">
                                @if ($rows["readonly"])
                                    <span>{{$rows["value"]}}</span>
                                @else
                                    <input type="{{$rows["type"]}}" name="{{$name}}" class="{{$rows["class"]}}" id="{{$name}}" value="{{$rows["value"]}}">
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
        $('#digStoreUpd').submit(function(e){
            
            //submitForm = $( this ).serialize()
            
        });
    }
</script>
