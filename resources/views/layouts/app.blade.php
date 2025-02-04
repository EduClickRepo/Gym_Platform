<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title') - @lang('general.AppName')</title>
    <base href="/">

    <!--This is loading before boostrap because a conflit with bootstrap tooltip (https://stackoverflow.com/questions/13731400/jqueryui-tooltips-are-competing-with-twitter-bootstrap)
     but I think is being used in solicitud servicio and complete profile-->
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- bootstrap 4.1.0 -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <!-- End bootstrap -->

    <!--     Fonts and icons     -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('css/general.css')}}">

    <link rel="stylesheet" href="{{asset('css/chats.css')}}">

    <script src="{{asset('js/general.js')}}"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/2ccdb5d1d9.js" crossorigin="anonymous"></script>

    <!--brand-name-->
    <link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/brand/imago_32x32.png') }}">

    <!--Scrollbar -->
    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #555;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: black;
        }


        .dropdown-menu::-webkit-scrollbar {
            width: 0;
        }
    </style>
    <!--Fin Scrollbar -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{env('GTAG')}}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '{{env('GTAG')}}');
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <!--notificaciones-->
        <meta name="userId" content="{{Auth::user()->id}}">
    @endauth

    @stack('head-content')
</head>
<body>
    @if(session('show_terms_modal'))
        <div class="modal fade" id="newTyCModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Actualización terminos y condiciones</h5>
                    </div>
                    <div class="modal-body">
                        Hola Girly, hemos actualizado nuestros términos y condiciones, por favor leelos atentamente.
                    </div>
                    <div class="modal-footer">
                        <form id="signTyC" method="POST" action="{{ route('signTyC') }}" aria-label="{{ __('Register') }}">
                            @csrf
                            @include('termsAndConditionsModal')
                            <button type="submit" class="btn btn-primary" id="botonConfirmar">Confirmar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="loading-spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div id="ajax-alerts" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <div class="modal fade justify-content-center align-items-center" id="msgModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background: none; border: none">
                <div class="modal-body" style="padding: 0 0 3vh 0">
                    <div class="alert bg-{{session('msg_level')}} color-white redondeado">
                        <p>{{session('msg')}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="app">
        <div id="top-navbar" class="navbar themed-navbar fixed-top">
            <a class="navbar-brand position-absolute" style="width: 60px" href="@auth{{route('home', ['user'=> Auth::user()->slug])}}@else # @endauth">
                <img width="120%" alt="logo" src="{{asset('images/brand/Logo showstars_page-0001.jpg')}}">
            </a>
            <!--
            <form id="buscarEntrenadores-form" action="{{ route('buscarEntrenadores') }}" method="GET">
                <div class=" m-auto">
                    <i style="padding-top: 15px" class="color-white mr-1 fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                </div>
            </form>-->
            <!-- Brand/logo -->
            <div class="links ml-auto">
                <!--
                <a class="banner-icon" href="#">
                    <img width="100%" alt="calendar" src="{{asset('images/calendar.png')}}">
                </a>
                <a class="banner-icon" href="#">
                    <img width="100%" alt="stats" src="{{asset('images/stats.png')}}">
                </a>

                <a class="d-none d-md-inline-block" href="{{route('blogs')}}">
                    Blogs
                </a>
                -->
                <a class="d-none d-md-inline-block" href="{{route('plans')}}">
                    Planes
                </a>
                <a class="d-none d-md-inline-block" href="{{route('agreements')}}">
                    Convenios
                </a>
                @auth
                    @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_USERS))
                        <a class="d-none d-md-inline-block" href="{{route('users.index')}}">
                            Users
                        </a>
                    @endif
                    @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SAVE_PETTY_CASH))
                        <a class="d-none d-md-inline-block" href="{{route('pettyCash.index')}}">
                            Caja Menor
                        </a>
                    @endif
                    @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_ACHIEVEMENTS_WEEKS_RANK))
                        <a class="d-none d-md-inline-block" href="{{route('achievementsWeeksRank')}}">
                            Ranking
                        </a>
                    @endif
                    @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_PETTY_CASH) || Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_MAYOR_CASH))
                        <a class="d-none d-md-inline-block" href="{{route('AccountingFlow')}}">
                            Flujo contable
                        </a>
                    @endif
                    {{--<notification class="cursor-pointer" v-bind:unread_notifications="unread_notifications" v-bind:notifications="notifications"></notification>--}}

                    <div class="dropdown d-inline-block">
                        <img class="rounded-circle user-icon ml-3 cursor-pointer" alt="user" src="{{asset('images/avatars/'.Auth::user()->foto)}}" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <div class="dropdown-menu dropdown-menu-right floating-card themed-block" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('home', ['user'=> Auth::user()->slug])}}">Home</a>
                            <a class="dropdown-item d-block d-md-none" href="{{route('plans')}}">
                                Planes
                            </a>
                            <a class="dropdown-item d-block d-md-none" href="{{route('agreements')}}">
                                Convenios
                            </a>
                            <!--
                            <a class="dropdown-item d-block d-md-none" href="{{route('blogs')}}">
                                Blogs
                            </a>
                            -->
                            @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_USERS))
                                <a class="dropdown-item d-block d-md-none" href="{{route('users.index')}}">
                                    Users
                                </a>
                            @endif
                            @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SAVE_PETTY_CASH))
                                <a class="dropdown-item d-block d-md-none" href="{{route('pettyCash.index')}}">
                                    Caja Menor
                                </a>
                            @endif
                            @if(Auth::user()->hasFeature(\App\Utils\FeaturesEnum::SEE_ACHIEVEMENTS_WEEKS_RANK))
                                <a class="dropdown-item d-block d-md-none" href="{{route('achievementsWeeksRank')}}">
                                    Ranking
                                </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <div class="d-none d-md-inline-block links">
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Registro</a>
                    </div>
                    <div class="dropdown d-inline-block d-inline-block d-md-none">
                        <span class="navbar-toggler-icon cursor-pointer" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></span>

                        <div class="dropdown-menu dropdown-menu-right floating-card bg-dark" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('blogs')}}">Blogs</a>
                            <a class="dropdown-item" href="{{ route('login') }}">Login</a>
                            <a class="dropdown-item" href="{{ route('register') }}">Registro</a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    @feature('dark_theme', false)
        <div class="full"></div><!--para el background de la imagen-->
    @endfeature

    <div class="container-fluid w-100">
        @yield('content')

        <div class="floating_button">
            <div class="chats">
                <a href="https://api.whatsapp.com/send/?phone=573123781174<&text=Hola,%20necesito%20ayuda%20con%20la%20plataforma&app_absent=0" class="icon-whatsapp" target=”_blank”>
                    <img class="icon" width="100%" height="100%" alt="whatsapp" src="<?php echo e(asset('images/wathsapp_icon.png')); ?>">
                </a>
            </div>
        </div>
    </div>

    @auth
        <script src="{{asset('js/app.js')}}"></script>
    @endauth

    @stack('modals')

    @stack('scripts')

    @if(session('show_terms_modal'))
        <script>
            $(document).ready(function(){
                $('#newTyCModal').modal({show: true});
            });
        </script>
    @endif

    @if(session('msg'))
        <script>
            $(document).ready(function(){
                $('#msgModal').modal({show: true});
            });
        </script>
        @php(\Illuminate\Support\Facades\Session::forget('msg'))
    @endif
    <!--Navbar color effect
    <script>
        $(window).on("scroll", function() {
            $("#top-navbar").css("backgroundColor", 'rgba(0, 0, 0, '+$(window).scrollTop());
        });
    </script>
    -->

    <!--activación de los tooltip-->
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <script>
        function handleAjaxResponse(data) {
            appendAjaxAlert(data.msg, data.level);
        }

        function appendAjaxAlert(msg, level) {
            const alert = $('<div class="alert alert-' + level + ' alert-dismissible fade show" role="alert">' + msg +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span></button></div>');

            $('#ajax-alerts').append(alert);

            setTimeout(function() {
                alert.alert('close');
            }, 3000);
        }
    </script>

    <!--subscribers
    <script type="text/javascript">
        var subscribersSiteId = 'cd602984-8642-4484-8a8b-431d84a5faf0';
    </script>
    <script type="text/javascript" src="https://cdn.subscribers.com/assets/subscribers.js"></script>
    -->

</body>
</html>
