<!DOCTYPE html>
<html>
<head>
    <title> @if( ! empty($title)){{$title}}@endif</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <!-- toastr notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <!-- CSFR token for ajax call -->
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- awesome font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
</head>
<body>
<div class="container-fluid" style="background: white;">

    <nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top" style="border-radius: 5px;">
        <a class="navbar-brand" href="{{url('')}}"><img id="Brand" src="{{url('images/Fiege1.png')}}" alt="Logo" style="max-height: 40px;"></a>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav w-100 nav-justified">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                        Törzsadatok
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{url('Sites')}}">Sites</a>
                        <a class="dropdown-item" href="{{url('Users')}}">IT Users</a>
                        <a class="dropdown-item" href="{{url('Equipments')}}">Equipment types</a>
                        <a class="dropdown-item" href="{{url('Employees')}}">Employees</a>
                        <a class="dropdown-item" href="{{url('Notifications')}}">E-mail settings</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                        IT Inventory
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{url('Inventory')}}">Devices</a>
                        <a class="dropdown-item" href="{{url('Softwares')}}">Softwares</a>
                    </div>
                </li>
                <li class="nav-item">
                @if (Auth::guest())
                    <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Log In</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a class="dropdown-item" href="{{url('changePassword')}}">Change Password</a></li>
                            <li><a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Log out</a></li>
                        </ul>
                    </li>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <div class="jumbotron text-center" style="background-color: #C1252D; color: #ffffff; margin-bottom:0; padding: 40px;">
        <h1>IT Inventory Manager</h1>
        <p>IT Asset Management System</p>
    </div>

    <div style="margin-top: 50px;">
            @yield('content')
    </div>

    <p>&nbsp;</p>
    <div class="jumbotron text-center" style="padding: 15px; background-color: #C1252D; color: #ffffff; margin-bottom:0">
        <p><small>Created By Daniel Posztos 2016-2018</small></p>
    </div>

</div>
</body>
</html>
