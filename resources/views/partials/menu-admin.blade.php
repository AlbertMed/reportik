    <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">

            <li>
                <a href="javascript:;" data-toggle="collapse" data-target="#demo">Administrador<i class="fa fa-fw fa-caret-down"></i></a>
                <ul id="demo" class="">

                     
                    <li>
                        <a href="{!! url('admin/users') !!}"><i class="fa fa-fw fa-user"></i> Usuarios Reportik</a>
                    </li>
                    <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#inventario">Catálogos  <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="inventario" class="">
                            <li>
                                <a href="{!! url('admin/departamentos') !!}"><i class="fa fa-archive"></i> Departamentos</a>
                            </li>
                            <li>
                                <a href="{!! url('admin/reportes') !!}"><i class="fa fa-file-text-o"></i> Reportes</a>
                            </li>
                           
                        </ul>   
                    </li>
                   
                </ul>
            </li>
            @include('partials.section-navbar')
        </ul>
    </div>
    <!-- /.navbar-collapse -->
    </nav>