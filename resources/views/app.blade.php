<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('EMPRESA_NAME')}}</title>
    <!-- Styles -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Material Design fonts -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Jquery -->
    <script data-require="jquery" data-semver="2.0.3" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <!-- JS dataTables -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
   
 <!--RESPONSIVE DATATABLES
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js"></script>
    
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/features/scrollResize/dataTables.scrollResize.min.js"></script>
-->
    <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/fixedColumns.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
    
    <script src="https://cdn.datatables.net/fixedheader/3.2.0/js/dataTables.fixedHeader.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

   {!! Html::script('assets/js/jquery.dataTables.yadcf.js') !!}
   

    <!-- CSS dataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">      
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap.min.css" type="text/css">
   <!-- RESPONSIVE DATATABLES
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">      
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css">      
    -->                                                       
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.bootstrap.min.css" type="text/css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.dataTables.min.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.0/css/fixedHeader.bootstrap.min.css" type="text/css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap.min.css" type="text/css">
    <link href="{{ URL::asset('bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" />
    
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>    
    webshims.setOptions('waitReady', false);
    webshims.setOptions('forms-ext', {type: 'date'});
    webshims.setOptions('forms-ext', {type: 'time'});
    webshims.polyfill('forms forms-ext');
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>

    <![endif]-->
{!! Html::style('assets/css/bootstrap.css') !!}
{!! Html::style('assets/css/bootstrap-switch.min.css') !!}
{!! Html::style('assets/css/bootstrap-switch.css') !!}
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.bootstrap.min.css" type="text/css">
{!! Html::style('assets/css/sbadmin.css') !!}
{!! Html::style('assets/css/responsive.css') !!}
{!! Html::style('assets/css/jquery.datatables.yadcf.css') !!}
<!-- Bootstrap Date-Picker Plugin -->
{!! Html::script('assets/datepicker/js/js/bootstrap-datepicker.min.js') !!}
{!! Html::script('assets/datepicker/js/locales/bootstrap-datepicker.es.min.js') !!}
{!! Html::style('assets/datepicker/js/css/bootstrap-datepicker.min.css') !!}
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/colreorder/1.3.3/css/colReorder.dataTables.min.css">

<script type="text/javascript" src="https://cdn.datatables.net/colreorder/1.3.3/js/dataTables.colReorder.min.js">
</script>
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
    <style>
        
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .side-nav>li>ul>li>ul>li>a {
            display: block;
            color: #e8e8e8;
            padding: 8px 26px 0% 25%;
            text-decoration: none;
        }

        /* Change the link color on hover */
        .side-nav>li>ul>li>ul>li>a:hover {
            background-color: black;
            color: white;
        }
        
    </style>


    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>


</head>
<body>
        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top"  style="" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="{!! url('home') !!}" style="color: white">
                      <div  style=" display: inline-block;
                    
  position: absolute;
  top:  0px; 
  left: 0px;
    ">
   
    <img src="{{ asset('/images/logo.png') }}" width="200px" height="55px"></div>
                 
                    </a>
                </div>

                <!-- Top Menu Items -->
                <ul class="nav navbar-left top-nav hidden-xs">
                    <li style="left:110%">
                        <a href="#"> 
                            <div id="sidebarCollapse" style="font-size: 14pt; color: #75BA1F; padding-bottom: 0;
                            padding-top: 0px;">
                                <i class="glyphicon glyphicon-align-left"> REPORTIK
                                </i>
                            </div>                            
                        </a>
                    </li>
                </ul>
                <ul class="nav navbar-right top-nav hidden-xs">
                    
                    <li class="dropdown">
                    
                    @if (Auth::guest())
                     <a href="{{ url('/auth/login') }}" style="color: white">Login</a>
                        <!--  <li><a href="url('/register') ">Register</a></li>  -->
                    @else
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" ><i class="fa fa-user"></i>
                                &nbsp;{{ Auth::user()->firstName.' '.Auth::user()->name }} &nbsp;
                                <b class="caret"></b></a>


                        <ul class="dropdown-menu">
                        @if(isset($isAdmin))
                            @if ($isAdmin)
                            <li>
                                <a href="{!! url('/MOD00-ADMINISTRADOR') !!}"><i class="fa fa-fw fa-gear"></i> Configuración</a>
                            </li>
                            @endif
                        @endif
                            <li class="divider"></li>
                            <li>
                                <a href="{!! url('/auth/logout') !!}"><i class="fa fa-fw fa-power-off"></i> Cerrar Sesión</a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>


            @yield('content')


            </nav>
        </div>
           



</body>




 

{!! Html::script('assets/js/bootstrap-switch.js') !!}
<script async src="{{ URL::asset('plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{ URL::asset('plugins/blockui/jquery.blockUI.js')}}"></script>

<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
{!! Html::script('assets/js/bootstrap.min.js') !!}
<!--<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
{!! Html::script('assets/js/moment.min.js') !!}
{!! Html::script('assets/js/shortcut.js') !!}
<!-- Latest compiled and minified JavaScript -->
<script src="{{ URL::asset('bootstrap-select/js/bootstrap-select.min.js')}}"></script>
<script>
    let routeapp = "{{url().'/'}}";
    $(document).ready(js_iniciador);
    $( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
        //alert("Session expired. You'll be take to the login page");
        if (jqxhr.status === 403) {
            bootbox.alert({
                    title: "Sesión terminada",
                    message: "<div class='alert alert-danger m-b-0'>Tiene que volver a iniciar Sesión.</div>",
                    callback: function(){ location.href = '{!! route('auth/login') !!}'; }
                });     
        }
        
    });
    function startjs(params) {
        $('.toggle').bootstrapSwitch();
        $('[data-toggle="tooltip"]').tooltip();
        $('.boot-select').selectpicker();
        $('.dropdown-toggle').dropdown();
        setTimeout(function() {
        $('#infoMessage').fadeOut('fast');
        }, 5000); // <-- time in milliseconds
        $("#sidebarCollapse").on("click", function() {
            $("#sidebar").toggleClass("active"); 
            $("#page-wrapper2").toggleClass("content"); 
            $(this).toggleClass("active"); 
        });
        $("#sidebar").toggleClass("active"); 
        $("#page-wrapper2").toggleClass("content"); 
        $(this).toggleClass("active");
    }
    function cargando() 
        {
            console.log('test');
            $.blockUI({
            message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
            css: {
                border: 'none',
                padding: '16px',
                width: '50%',
                top: '40%',
                left: '30%',
                backgroundColor: '#fefefe',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .7,
                color: '#000000'
            }  
            });
        }
</script>

</html>
