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
    <script src="//cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- js -->
    <script src="{{ asset('js/app.js') }}" defer></script>


</head>

<body>
    <div class="container-nav">
        <div class="navigation">
            <ul class="sidebar-nav">
                <li>
                    <a href="/AdminTool/dashboard">
                        <img class="logo-sidebar" src="{{ asset('images/logo.svg') }}" />
                    </a>
                </li>
                <li>
                    <a href="/AdminTool/dashboard">
                        <span class="icon"><i class="fas fa-table"></i></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/AdminTool/categories">
                        <span class="icon"><i class="fas fa-book" aria-hidden="true"></i></span>
                        <span class="title">Categories</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span class="title dropdown-toggle">Agencies</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="/AdminTool/agencies">
                                <span class="icon"></span>
                                <span class="title">Agencies</span>
                            </a>
                        </li>
                        <li>
                            <a href="/AdminTool/freelancers">
                                <span class="icon"></span>
                                <span class="title">Agency Members</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        <span class="title dropdown-toggle">Clients</span>
                    </a>
                    <ul class="sub-menu">
                        <li>
                            <a href="/AdminTool/companies">
                                <span class="icon"></span>
                                <span class="title">Companies</span>
                            </a>
                        </li>
                        <li>
                            <a href="/AdminTool/clients">
                                <span class="icon"></span>
                                <span class="title">Clients</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/AdminTool/users">
                        <span class="icon"><i class="fas fa-user-cog"></i></span>
                        <span class="title">Other Users</span>
                    </a>
                </li>
                <li>
                    <a href="/AdminTool/announcements">
                        <span class="icon"><i class="fas fa-scroll"></i></span>
                        <span class="title">Announcement</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="main">
        <div class="topbar">
            <div class="toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
            {{-- <div class="search">
                <label>
                    <input type="text" placeholder="Search">
                    <i class="fas fa-search"></i>
                </label>
            </div> --}}
            <div class="user">
                @if (Route::has('login'))
                    @auth
                        {{-- <a href="{{ url('/home') }}">Home</a> --}}
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @else
                        <a href="/AdminTool/login">Login</a>
                    @endauth
                @endif
                {{-- <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png"> --}}
            </div>
        </div>

        @yield('content')


    </div>
    {{-- <main class="container">

    </main> --}}
    <script>
        function toggleMenu() {
            let toggle = document.querySelector('.toggle');
            let navigation = document.querySelector('.navigation');
            let main = document.querySelector('.main');
            toggle.classList.toggle('active');
            navigation.classList.toggle('active');
            main.classList.toggle('active');
        }
        $('.sidebar-nav li a').click(function() {
            $(this).parent().toggleClass('active')
        })
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
