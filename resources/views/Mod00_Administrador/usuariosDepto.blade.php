@extends('Mod00_Administrador.admin')

@section('subcontent-01')

<div class="row">
<div class="col-md-3">
{!! Form::open(['url' => 'admin/addUser', 'method' => 'POST']) !!}
<div class="form-group">
    <label for="exampleFormControlInput1">Agregar Usuario</label>
    <input type="number" class="form-control" id="nominauser" name="Nomina_usuario" placeholder="Número de Nómina"require>
</div>
<div class="form-group">
    <input type="text" class="form-control" id="nameuser" name="Nombre_usuario" placeholder="Nombre del Usuario"require>
</div>
<button type="submit" class="btn btn-primary">Enviar</button> 
{!! Form::close() !!}
</div>
</div>
<br>
       <h4>Lista de Usuarios</h4>
   <div class="row">


               <div class="col-md-12">

                   <table class="table table-bordered" id="users-table2">
                       <thead>
                      
                       <tr>
                           <th>Id</th>
                           <th>Nómina</th>
                           <th>Nombre</th>  
                           <th>Status</th>                           
                           <th>Acción</th>
                       </tr>
                       </thead>
                   </table>
               </div>



                <!-- Modal -->

                <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" >
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="pwModalLabel">Cambio de Password</h4>
                            </div>
                            {!! Form::open(['url' => 'cambio.password', 'method' => 'POST']) !!}
                            <div class="modal-body">

                                    <div class="form-group">
                                        <div >
                                            <label for="password" class="col-md-12 control-label">Usuario:</label>
                                            <input type="text" name="userId" class="form-control" id="userId" value="" readonly/>
                                            <label for="password" class="col-md-12 control-label">Ingresa la nueva Contraseña:</label>
                                            <input id="password" type="password" class="form-control" name="password" required maxlength="6">
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
             <!--Aqui termina HTML -->
                
                <script type="text/javascript" >
                    $(document).ready(function (event) {

                        

                        $('#users-table2').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{!! route('datatables.showusers') !!}',
                                data: function () {
                                   
                                }
                            },
                            columns: [
                                { data: 'id', name: 'id'},                               
                                { data: 'nomina', name: 'nomina'},                               
                                { data: 'name', name: 'name'},                                
                                { data: 'status', name: 'status'},
                                { data: 'action', name: 'action', orderable: false, searchable: false}
                            ],
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                            },
                            "columnDefs": [
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },
                                { "width": "20%", "targets":0 },                               

                            ],
                        });

                        $('#mymodal').on('show.bs.modal', function (event) {
                            var button = $(event.relatedTarget) // Button that triggered the modal
                            var recipient = button.data('whatever') // Extract info from data-* attributes
                            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                            var modal = $(this)

                            modal.find('#userId').val(recipient)
                        });

                    });

                </script>

        </div>



@endsection
