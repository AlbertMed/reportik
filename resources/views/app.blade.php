<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">      
  
    
    <script data-require="jquery" data-semver="2.0.3" src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/fixedColumns.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
   {!! Html::script('assets/js/jquery.dataTables.yadcf.js') !!}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.bootstrap.min.css" type="text/css">
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
{!! Html::style('assets/css/sb-admin.css') !!}
{!! Html::style('assets/css/responsive.css') !!}
{!! Html::style('assets/css/jquery.datatables.yadcf.css') !!}
<!-- Bootstrap Date-Picker Plugin -->
{!! Html::script('assets/datepicker/js/js/bootstrap-datepicker.min.js') !!}
{!! Html::script('assets/datepicker/js/locales/bootstrap-datepicker.es.min.js') !!}
{!! Html::style('assets/datepicker/js/css/bootstrap-datepicker.min.css') !!}
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
   
    <img src="{{ asset('/images/logoitk.jpg') }}" width="200px" height="55px"></div>
                 
                    </a>
                </div>

                <!-- Top Menu Items -->
                <ul class="nav navbar-left top-nav hidden-xs">
                    <li style="left:130%"><a href="#" style="color: #75BA1F; padding-bottom: 0;

padding-top: 13px;"><h3 style="padding: 0px;
            margin: 0px;">REPORTIK</h3></a></li>
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

<!--<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>-->
{!! Html::script('assets/js/jquery.dataTables.min.js') !!}
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
{!! Html::script('assets/js/bootstrap.min.js') !!}
<!--<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
{!! Html::script('assets/js/moment.min.js') !!}
{!! Html::script('assets/js/shortcut.js') !!}
<!-- Latest compiled and minified JavaScript -->
<script src="{{ URL::asset('bootstrap-select/js/bootstrap-select.min.js')}}"></script>

<script>

    $(document).ready(function (event) {
        $('.boot-select').selectpicker();
        $('.toggle').bootstrapSwitch();
        $('.dropdown-toggle').dropdown();

        @yield('script')

    });


</script>

</html>
