@extends('app')

@section('content')

<?php
$bnd = null;
$bnd2 = null;
$index = 0;
        ?>
        
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul id="sidebar" class="nav navbar-nav side-nav " style="">
            @foreach($actividades as $n1)
                <?php
                 $index = $index + 1;
                ?>

                    @if ($bnd == null)
                        <!-- primer elemento, se crea el primer modulo, el primer menu y la primera tarea, NO se cierran las etiquetas (puede que haya una tarea más) -->
                            <?php
                            $bnd = $n1->depto_Id; //para saber donde acaban
                           // $bnd2 = $n1->id_menu; 
                            ?>

                            <li><a href="javascript:;" data-toggle="collapse"  data-target="#mo{{$n1->depto_Id}}" ><i class="fa fa-fw fa-dashboard"></i> {{$n1->depto}} <i class=""></i></a>
                                <ul id="mo{{$n1->depto_Id}}" class="collapse in">                                   
                                            <li>
                                                <a onclick="cargando()" href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
                                            </li>
                                        
                    @elseif($bnd == $n1->depto_Id)
                            <!-- si es el mismo depto,  -->
                           
                                <!-- agrego la tarea -->
                                    <li>
                                        <a onclick="cargando()" href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
                                    </li>
                              
                                    @if($ultimo == $index)                                                
                                                    </ul>
                                                </li>                                            
                                    @endif
                    @else <!-- si no es el mismo modulo -->
                            <?php
                            $bnd = $n1->depto_Id;
                            //$bnd2 = $n1->id_menu;
                            ?>
                             <!-- cierro el modulo anterior-->
                                          </ul>
                                      </li>
                                   

                           <li><a href="javascript:;" data-toggle="collapse"  data-target="#mo{{$n1->depto_Id}}" ><i class="fa fa-fw fa-dashboard"></i> {{$n1->depto}} <i class=""></i></a>
                                <ul id="mo{{$n1->depto_Id}}" class="collapse in">                                   
                                            <li>
                                                <a onclick="cargando()" href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
                                            </li>

                             @if($ultimo == $index)
                                                <!--cerrar menu y modulo -->
                                    </ul>
                                </li>
                         
                            @endif

                    @endif

@endforeach







                @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>

    <div id="page-wrapper2" style="height: 100%;">
        <style>
            td {
                font-family: 'Helvetica';
                font-size: 12px;
            }
        
            th {
                font-family: 'Helvetica';
                font-size: 12px;
            }
        
            .btn-group>.btn {
                float: none;
            }
        
            .btn {
                //botones redondeados
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
        
            #sidebar {
                min-width: 230px;
                transition: all 0.3s;
            }
        
            #sidebar.active {
                margin-left: -450px;
            }
        
            .navbar-collapse.in {
                background-color: rgb(16, 13, 13);
            }
        
            .content {
                padding: 10px;
                margin-left: -230px;
                transition: all 0.3s;
            }
            .page-header {
                margin: 15px;
                padding-top: 15px;
                padding-bottom:0px;
            }
        
        </style>
        @yield('homecontent')

        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper2 -->
    </div>
    </div>
    <!-- /#wrapper -->
@endsection





