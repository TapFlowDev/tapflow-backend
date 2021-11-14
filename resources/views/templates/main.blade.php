<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tapflow Admin Tool') }}</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- js -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    

</head>

<body>
    <div class="page">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-container">
                    <div class="logo-container">
                        <img class="logo-sidebar" src="{{ asset('images/logo.svg') }}" />
                    </div>
                    {{-- <div class="brand-name-container">
                        <p class="brand-name">
                            {{ config('app.name', 'Tapflow Admin Tool') }}
                        </p>
                    </div> --}}
                </div>
            </div>
            <div class="sidebar-body">
                <ul class="navigation-list">
                    <li class="navigation-list-item">
                        <a class="navigation-link" href="/">
                            <div class="row">
                                <div class="col-2">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div class="col-9">
                                    Dashboard
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="navigation-list-item">
                        <div class="dropdown">
                            <a class="navigation-link dropdown-toggle" id="users-dropdown" 
                            data-bs-toggle='dropdown' role="button" href="#">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="col-9">
                                        Users
                                    </div>
                                </div>
                            </a>
                                <ul class="dropdwon-menu" aria-labelledby="users-dropdown" aria-expanded="false">
                                    <li>
                                        <a href="/AdminTool/users" class="dropdown-item">All users</a>
                                        <a href="/AdminTool/freelancers" class="dropdown-item">FreeLancers</a>
                                        <a href="/AdminTool/clients" class="dropdown-item">Clients</a>
                                    </li>
                                </ul>
                        </div>
                    </li>
                    <li class="navigation-list-item">
                                  <div class="dropdown">
                            <a class="navigation-link dropdown-toggle" id="users-dropdown" 
                            data-bs-toggle='dropdown' role="button" href="#">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="col-9">
                                        Groups
                                    </div>
                                </div>
                            </a>
                                <ul class="dropdwon-menu" aria-labelledby="users-dropdown" aria-expanded="false">
                                    <li>
                                        <a href="/AdminTool/agencies" class="dropdown-item">Teams/Agencies</a>
                                        <a href="/AdminTool/companies" class="dropdown-item">Companies</a>
                                    </li>
                                </ul>
                        </div>
                    </li>
                    <li class="navigation-list-item active">
                        <a class="navigation-link" href="/AdminTool/categories">
                            <div class="row">
                                <div class="col-2">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="col-9">
                                    Categories
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                <hr style="color:rgb(255, 255, 255);margin-top:30px;">
                <div class="teams-title-container">
                </div>
                <ul class="teams-list">
                    <li class="teams-item">
                        <div class="row">
                            <div class="col-9">
                                @if (Route::has('login'))
                                    @auth
                                        {{-- <a href="{{ url('/home') }}">Home</a> --}}
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">Logout</a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}">Log in</a>

                                        {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}">Register</a>
                            @endif --}}
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content">
            <div class="navigationBar">
                <button id="sidebarToggle" class="btn sidebarToggle">
                    <i class="fas fa-bars"></i>
                    </a>
                </button>
            </div>
            <div class="content-container">
                @yield('content')
            </div>
            {{-- <div class="navigationBar">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/">Home </a>
                        </li>
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="/AdminTool/users">Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/AdminTool/categories">Categories</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="form-inline my-2 my-lg-0">
                    @if (Route::has('login'))
                        <div>
                            @auth
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">log
                                    out</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    class="d-none">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('login') }}">Log in</a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div> --}}
        </div>
        {{-- <div class="content">
           

        </div> --}}
    </div>

    {{-- <main class="container">

    </main> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
