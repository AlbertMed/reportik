<!-- Styles -->
<!-- Material Design fonts -->
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
{!! Html::style('assets/css/bootstrap.css') !!}


{!! Html::style('assets/css/font-awesome.css') !!}

{!! Html::style('assets/css/util.css') !!}
{!! Html::style('assets/css/main.css') !!}


<script>  
    window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
</script>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-form-title" style="background-image: url(../images/login.png);">
					<span class="login100-form-title-1">
						ITEKNIA
					<img src="{{url("images/logo.png")}}" style="margin-left:-120px" alt="" width="200px" height="75px">
					</span>
                </div>
                @if (count($errors) > 0)
                <div class="alert alert-danger text-center" style="border-radius: 15px;" role="alert">
                    @foreach($errors->getMessages() as $this_error)
                        <strong>Error  &nbsp; {{$this_error[0]}}</strong><br>
                    @endforeach
                </div>
            @elseif(Session::has('mensaje'))
                <div class="alert alert-success text-center"style="border-radius: 15px; " role="alert">
                    {{ Session::get('mensaje') }}
                </div>
            @endif

				<form class="login100-form validate-form" method="post" action="{{url('/auth/login')}}">
                        {{ csrf_field() }}
					<div class="wrap-input100 validate-input m-b-26" data-validate="Id es obligatorio">
						<span class="label-input100">Usuario</span>
						<input class="input100" type="text" name="id" placeholder="Escribe tu Usuario">
						<span class="focus-input100"></span>
					</div>

					<div class="wrap-input100 validate-input m-b-18" data-validate = "Password es obligatorio">
						<span class="label-input100">Contraseña</span>
						<input class="input100" type="password" name="password" placeholder="Escribe tu contraseña">
						<span class="focus-input100"></span>
					</div>

					

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>
</html>

