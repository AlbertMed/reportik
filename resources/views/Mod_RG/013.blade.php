@extends('home')

            @section('homecontent')
<style>
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
    th:first-child {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        z-index: 5;
    }
    table.dataTable thead .sorting_asc{
        position: sticky;
    }
    .DTFC_LeftBodyWrapper{
        margin-top: 84px;
    }
    .DTFC_LeftHeadWrapper {
        display:none;
    }
    .dataTables_filter {
        display: none;
    } 
    div.dt-buttons {
       // float: right;
    }
    .btn-group > .btn{
        float: none;
    }
    .btn{
        border-radius: 4px;
    }
    .btn-group > .btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
        border-radius: 4px;
    }
    .btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    .btn-group > .btn:last-child:not(:first-child), .btn-group > .dropdown-toggle:not(:first-child) {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .dataTables_wrapper .dataTables_length { /*mueve el selector de registros a visualizar*/
     float: right;
    } 

    div.dataTables_wrapper div.dataTables_processing { /*Procesing mas visible*/
    z-index: 10;
    }
  input{
color: black;
}
</style>

                <div class="container" >

                    <!-- Page Heading -->
                    <div class="row">
                        <div class="col-md-11">
                            <h3 class="page-header">
                               Captura de Historicos
                                <small></small>
                            </h3>
                                        
                    </div>
                    </div>
                                          
                    </div> 
                    <br>  
                 <!-- /.row -->
                    
                    </div>
                    <!-- /.container -->

                    @endsection

               

                    <script>

                       
                    </script>
                   
                   

