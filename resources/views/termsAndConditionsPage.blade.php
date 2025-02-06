<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta name="google-site-verification" content="P_yEgFijsmcG-GjA_AgDfW4N384ev7ACxASfQB2n-2I" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@lang('general.AppName')</title>
    <meta name="description" content="Recibe Ofertas de Entrenadores Personales, Compara y Elige al Entrenador Correcto por el Precio que Quieres"/>
    <link rel="canonical" href="{{env('APP_URL')}}/"/>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,900|K2D" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">


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

    <!--footer-->
    <link rel="stylesheet" href="{{asset('css/footer/footer-distributed-with-address-and-phones.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <link rel="stylesheet" href="{{asset('css/welcome.css')}}">


    <link rel="stylesheet" href="{{asset('css/utils.css')}}">

    <link rel="stylesheet" href="{{asset('css/chats.css')}}">

</head>
<body data-spy="scroll" data-target=".navbar">

    <nav id="top-navbar" class="navbar navbar-expand-md navbar- fixed-top">
        <a class="navbar-brand" href="#" style="width: 60px; padding: 0">
            <img src="{{asset('images/brand/Logo showstars_page-0001.jpg')}}" width="70%" height="70%">
        </a>
        <div class="top-right links">
            <a href="{{ route('blogs') }}">Blogs</a>
            @auth
                <a href="{{route('home', ['user'=> Auth::user()->slug])}}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Registro</a>
            @endauth

        </div>
    </nav>

    <div class="section d-flex flex-column" style="height: 100vh; width:100vw">
        <object data="{{asset('pdf/terminos_y_condiciones.pdf')}}" type="application/pdf" frameborder="0" width="100%" height="100%" style="padding: 20px;">
            <embed src="{{asset('pdf/terminos_y_condiciones.pdf')}}" type='application/pdf' width="100%" height="100%" />
        </object>
    </div>

    <footer style="margin: 0" class="footer-distributed">

        <div class="footer-left">

            <img class="mb-3" src="{{asset('images/brand/imago_sin_fondo_oscuro.png')}}" width="70%" height="70%">

            <p class="footer-company-name mt-4 d-none d-md-block">Desarrollado por: Educlick &copy; 2018</p>
            <p class="footer-company-name d-none d-md-block">322 243 42 96</p>
        </div>

        <div class="footer-center">

            <div>
                <a href="https://goo.gl/maps/SZKV84zvwuorBJha8" target="_blank">
                    <i class="fa fa-map-marker m-0"></i>
                    <p><span>Av. Esperanza #75-25</span> Modelia, Bogotá</p>
                </a>
            </div>

            <div>
                <a href="https://api.whatsapp.com/send/?phone=573123781174<&text=Hola,%20quiero%20conocer%20Girl%20Power&app_absent=0" target="_blank">
                    <i class="fa fa-phone m-0"></i>
                    <p>312 378 11 74</p>
                </a>
            </div>

            <div>
                <i class="fa fa-envelope m-0"></i>
                <p><a href="mailto:contacto@girlpower.com.co">contacto@girlpower.com.co</a></p>
            </div>

        </div>

        <div class="footer-right">
            <!--<a href="#"><i class="fab fa-facebook"></i></a>-->
            <a href="https://www.instagram.com/girlpowerstudio/">
                <i class="fab fa-instagram m-0" style="font-size: 25px"></i>
                <p>@girlpowerstudio</p>
            </a>
        </div>

    </footer>

    <div class="floating_button">
        <div class="chats">
            <a href="https://api.whatsapp.com/send/?phone=573123781174<&text=Hola,%20quiero%20conocer%20Girl%20Power&app_absent=0" class="icon-whatsapp" target="_blank">
                <img class="icon" width="100%" height="100%" alt="whatsapp" src="{{asset('images/wathsapp_icon.png')}}">
            </a>
        </div>
    </div>

</body>
</html>



