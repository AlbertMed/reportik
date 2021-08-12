
<style>
    .btn {
        border-radius: 4px;
    }

    th {
        font-size: 12px;
    }

    td {
        font-size: 11px;
    }

    th,
    td {
        white-space: nowrap;
    }

    div.container {
        min-width: 980px;
        margin: 0 auto;
    }

    .dataTables_scrollHeadInner th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .segundoth {
        position: -webkit-sticky;
        position: sticky;
        left: 0px;
        z-index: 5;
    }

    table.dataTable thead .sorting {
        position: sticky;
    }

    .DTFC_LeftBodyWrapper {
        margin-top: 80px;
    }

    .DTFC_LeftHeadWrapper {
        display: none;
    }

    .DTFC_LeftBodyLiner {
        overflow: hidden;
        overflow-y: hidden;
    }

    .dataTables_filter {
        display: none;
    }

    div.dt-buttons {
        float: right;
        margin-bottom: 6px;
        margin-top: 0px;
    }

    .btn-group>.btn {
        float: none;
    }

    .btn {
        border-radius: 4px;
    }

    .btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
        border-radius: 4px;
    }

    .btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    .btn-group>.btn:last-child:not(:first-child),
    .btn-group>.dropdown-toggle:not(:first-child) {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length {
        /*mueve el selector de registros a visualizar*/
        float: right;
    }

    div.dataTables_wrapper div.dataTables_processing {
        /*Procesing mas visible*/
        z-index: 10;
    }

    input {
        color: black;
    }

    .bootbox.modal {
        z-index: 9999 !important;
    }
</style>

<div class="">

    <!-- Page Heading -->
    <div class="row">
        <div class="col-md-11">
            <h3 class="">
                CXC por Cliente
                <small></small>
            </h3>

        </div>
    </div>
    <div class="row">
    
        <div class="col-md-12">
            <div class="table-scroll" id="registros-ordenes-proyeccion">
                <table id="t_ordenes_proyeccion" class="table table-striped table-bordered hover" width="100%">
                    <thead>
                        <tr></tr>
                    </thead>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div> <!-- /.container -->
