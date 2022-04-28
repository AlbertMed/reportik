@extends('app')

@section('content')
@include('partials.menu-admin')

	<div class="container" >
		
		<!-- Page Heading -->
		<div class="row">
				<div class= "visible-xs"><br><br></div>
			<div class= "col-lg-6.5 col-md-10 col-sm-8">
				<h3 class="page-header">
					Edición del Usuario "{{$usuario->name}}"
				</h3>
				<div class="hidden-xs">
					<div class= "hidden-ms">
						<ol class="breadcrumb">
							<li>
								<i class="fa fa-dashboard"></i> <a href="{!! url('home') !!}">Inicio</a>
							</li>
							<li>
								<i class="fa fa-archive"></i>  <a href="{!! url('MOD00-ADMINISTRADOR') !!}">MOD-Administrador</a>
							</li>
							<li>
								<i class="fa fa-archive"></i>  <a href="#">Usuario</a>
							</li>
						</ol>
					</div>
				</div>
			</div>
		</div><!--/.row-->
		<style>
			td{
				font-family: 'Helvetica';
				font-size:80%;
			}
			th{
				font-family: 'Helvetica';
				font-size:90%;
			}
		</style>
		
		
		<div class="row">
			<div class="col-lg-5.5 col-md-10 col-sm-7">
				@if (count($errors) > 0)
				<div class="alert alert-danger text-center" role="alert">
					@foreach($errors->getMessages() as $this_error)
					<strong>¡Lo sentimos!  &nbsp; {{$this_error[0]}}</strong><br>
					@endforeach
				</div>
				@elseif(Session::has('mensaje'))
				<div class="row">
					<div class="alert alert-success text-center" role="alert">
						{{ Session::get('mensaje') }}
					</div>
				</div>
				@endif
				
			</div>
		</div><!-- /.row -->
		
		<div class="row">
			<div class="col-md-3">
				{!! Form::open(['url' => 'admin/modificar/usuario', 'method' => 'POST']) !!}				
				<div class="form-group">
					<label for="exampleFormControlInput1">Nombre</label>
					<input type="text" class="form-control" id="nombre" name="Nombre" value="{{$usuario->name}}" >
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleFormControlInput1"># Nómina</label>
					<input type="number" class="form-control" id="nomina" name="Nomina" value="{{$usuario->nomina}}" >
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleFormControlInput1">Status</label>
					<select class="form-control" id="selstatus" name="Status" value="{{$usuario->status}}">
						<option value="1">Activo</option>   
						<option value="0">No Activo</option> 
					</select>					
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
						<label for="exampleFormControlInput1"></label> 					
						<button type="submit" class="btn btn-primary form-control" style="margin-top:4px">Actualizar</button>								
					{!! Form::close() !!}
				</div>
			</div>
		</div><!-- /.row -->
			
			<div class="row">				
				{!! Form::open(['url' => 'admin/addReporte/usuario', 'method' => 'POST']) !!}
				
				<div class="form-group">    
					<input type="hidden" class="form-control"  name="userid" value="{{$usuario->id}}" >
				</div>		
				<div class="col-md-8">
						<label for="exampleFormControlInput1">Autorizar Reporte</label>
					<div class="input-group">
						
						<select class="form-control" id="selrep" name="selrep">
							<option value="">Seleccione un Reporte</option>
							@foreach ($reportes as $rep)    
							<option value="{{$rep->Id}}">{{$rep->depto}}  -  {{$rep->Nombre}}  -  {{$rep->Descripcion}}</option>
							@endforeach
						</select>
						<span class="input-group-btn">
							<button class="btn btn-primary" type="submit">Autorizar</button>
						</span>
					</div><!-- /input-group -->
					{!! Form::close() !!}
				</div>
			</div><!-- /.row -->				
	
	<br>
	
	<div class="row">
		<div class="col-md-10">
				<label for="exampleFormControlInput1">Reportes autorizados</label>
			<div class="table-responsive">
				<table  class="table table-striped header-fixed">
					<thead class="thead-dark">
						<tr>
							<th scope="col">Id</th>
							<th scope="col">Departamento</th>                       
							<th scope="col">Nombre</th>
							<th scope="col">Descripción</th>                       
							<th scope="col">Acciones</th>
							
						</tr>
					</thead>
					<tbody>
						@if(count($accesos) == 0)                    
						<tr>
							<td></td>
							<td></td>
							<td>Sin Reportes</td>
							<td></td>
							<td></td>
						</tr>
						@endif
						@foreach ($accesos as $rep)
						<tr>
							<th scope="row">{{ $rep->IdR }}</th>
							<td>{{ $rep->Depto }}</td>                      
							<td>{{ $rep->Nombre }}</td>
							<td>{{ $rep->Descripcion }}</td>
							<td>                            
								<a href="accesos/borrar/{{$rep->Id}}/{{$usuario->nomina}}/{{$rep->Descripcion}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
							</td>
							
						</tr>
						@endforeach 
					</tbody>
				</table>
			</table>
		</div>
	</div>
	
</div>

</div>
@yield('subcontent-01')
</div>
<!-- /.container-fluid -->

</div>
<!-- /#page-wrapper2 -->
</div>
</div>


<!-- Modal -->

<div class="modal fade" id="modificaDepto" tabindex="-1" role="dialog" >
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="pwModalLabel">Modificación Departamento</h4>
			</div>
			{!! Form::open(['url' => 'cambio.reporte', 'method' => 'POST']) !!}
			<div class="modal-body">
				
				<div class="form-group">
					<div >
						<label for="password" class="col-md-12 control-label">Nombre Actual:</label>
						<input type="hidden" name="Id" class="form-control" id="Id" value="" />
						<input type="text" name="Name" class="form-control" id="nameDepto" value="" readonly/>
						<label for="password" class="col-md-12 control-label">Nuevo Nombre:</label>
						<input id="nombreDepto" type="text" class="form-control" name="NombreDepto" required >
					</div>
				</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<!-- /#wrapper -->
@endsection
<script>
function js_iniciador() {
	$('.boot-select').selectpicker();
	$('.toggle').bootstrapSwitch();
	$('#modificaDepto').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Button that triggered the modal
		var recipient = button.data('nombre') // Extract info from data-* attributes
		var recipient2 = button.data('id') // Extract info from data-* attributes
		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
		var modal = $(this)
		
		modal.find('#nameDepto').val(recipient)
		modal.find('#Id').val(recipient2)
	});
}
</script>
