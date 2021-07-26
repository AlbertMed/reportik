<!-- ------------------------------------BEGIN CSS----------------------------------------------- -->

<style>
    .color-input{
        background: transparent;
    }
</style>

<!-- --------------------------------------END CSS------------------------------------------------ -->

<!-- begin panel -->
<div class="col-md-11">
    
    <div class="">

        <div class="row">
            <div class="invoice">
                <div class="invoice-header">

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><strong><font size="2">Codigo</font></strong></label>
                                <input type="text" class="form-control" id="input-codigo" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-4">
                                <label><strong><font size="2">Nombre</font></strong></label>
                                <input type="text" class="form-control" id="input-nombreP" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-3">
                                <label><strong><font size="2">Estado</font></strong></label>
                                <input type="text" class="form-control" id="input-estado" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-2">
                                <label><strong><font size="2">Fecha</font></strong></label>
                                <input type="text" class="form-control" id="input-fechaP" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><strong><font size="2">Banco</font></strong></label>
                                <input type="text" class="form-control" id="input-banco" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-3">
                                <label><strong><font size="2">Cuenta</font></strong></label>
                                <input type="text" class="form-control" id="input-cuentaP" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-3">
                                <label><strong><font size="2">Moneda</font></strong></label>
                                <input type="text" class="form-control" id="input-moneda" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-4">
                                <label><strong><font size="2">Creado Por</font></strong></label>
                                <input type="text" class="form-control" id="input-creado" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><strong><font size="2">Monto</font></strong></label>
                                <input type="text" class="form-control" id="input-monto" placeholder="0" style="font-size: 100%;" disabled/>
                            </div>
                            <div class="col-md-2">

                            </div>
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table id="tableProgramaDetalle" class="table table-striped table-bordered nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Factura Proveedor</th>
                            <th>Codigo Proveedor</th>
                            <th>Nombre Proveedor</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tfoot>

                    </tfoot>
                </table>
            </div>
        </div>

        <div class="invoice-footer text-muted right">
            <div class="pull-right">
                <button type="button" class="btn btn-default m-r-5 m-b-5" id="btn-cerrar">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- end panel -->
