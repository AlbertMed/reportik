@extends('app')
<style>
    td {
        font-family: 'Helvetica';
        font-size: 70%;
    }

    th {
        font-family: 'Helvetica';
        font-size: 90%;
    }
</style>
@section('content')

<?php
$bnd = null;
$bnd2 = null;
$index = 0;
        ?>
        
    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav ">
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
                                                <a href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
                                            </li>
                                        
                    @elseif($bnd == $n1->depto_Id)
                            <!-- si es el mismo depto,  -->
                           
                                <!-- agrego la tarea -->
                                    <li>
                                        <a href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
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
                                                <a href="{!! url('home/'.$n1->depto.'/'.$n1->reporte) !!}">{{$n1->reporte}}</a>
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

    <div id="page-wrapper2">
        @yield('homecontent')

        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->
    </div>
    </div>
    <!-- /#wrapper -->
@endsection

@section('script')
@yield('homescript')
@endsection



