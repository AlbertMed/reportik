@if (count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        @foreach($errors->getMessages() as $this_error)
            <strong>¡Error!  &nbsp; {{$this_error[0]}}</strong><br>
        @endforeach
    </div>

@endif