<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta name="google-site-verification" content="P_yEgFijsmcG-GjA_AgDfW4N384ev7ACxASfQB2n-2I" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>@lang('general.AppName') - Academia de Baile</title>
        <meta name="description" content="Únete a nuestra academia de baile y descubre el ritmo en cada paso."/>
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

        <link rel="stylesheet" href="{{asset('css/carousel.css')}}">

        <link rel="stylesheet" href="{{asset('css/utils.css')}}">

        <link rel="stylesheet" href="{{asset('css/chats.css')}}">

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{env('GTAG')}}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{env('GTAG')}}');
        </script>

        <script src="{{ asset('js/moment.min.js') }}"></script>
    </head>
    <body data-spy="scroll" data-target=".navbar">

    <!--Navbar color effect
    <script>
        $(window).on("scroll", function() {
            $("#top-navbar").css("backgroundColor", 'rgba(0, 0, 0, '+$(window).scrollTop());
        });
    </script>
    -->

    <nav id="top-navbar" class="navbar navbar-expand-md navbar-dark fixed-top">
        <a class="navbar-brand" href="#" style="width: 60px; padding: 0">
            <img src="{{asset('images/brand/logoshowstars.png')}}" width="70%" height="70%">
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

    <div id="welcome" class="flex-center position-ref full-height">
        <div class="content">
            <div class="title">
                <video autoplay muted class="portrait-video landingVideo" src="{{asset('video/landing_page_portrait.mp4')}}" preload="auto"></video>
                <video muted class="landscape-video landingVideo" src="{{asset('video/landing_page_portrait.mp4')}}" preload="none"></video>
                <button id="schedule-courtesy" style="background: linear-gradient(90deg, rgba(67,0,36,1) 0%, rgba(67,5,68,1) 72%, rgba(87,6,88,1) 100%);" class="btn ml-auto mr-auto" data-toggle="modal"
                        data-target="#scheduleCourtesyModal">¡Baila con nosotros!
                </button>
                <button id="sound" class="btn position-absolute bg-dark color-white" style="bottom:5vh; left: 5vh; height: 40px; line-height: 0">
                    <span id="volume_off" class="material-icons" style="font-size: smaller">volume_off</span>
                    <span id="volume_on" class="material-icons" style="font-size: smaller; display: none">volume_up</span>
                </button>

            </div>
        </div>
    </div>

    <div class="w-100 text-center p-5 bg-brand-primary-gradient color-white">
        <h3>BIENVENIDOS A NUESTRA ACADEMIA DE BAILE</h3>
    </div>

    <div class="section text-center p-5">
        <h1>¿Por qué elegir nuestra academia?</h1>
        <p>Ofrecemos clases para todos los niveles con instructores profesionales en un ambiente amigable y motivador.</p>
    </div>

    <x-type-section title="Danza Contemporánea" description="Explora la libertad de movimiento y la expresión corporal en una fusión de estilos." img="{{asset('images/brand/danzacontemporeanea.png')}}" background="linear-gradient(90deg, rgba(67,0,36,1) 0%, rgba(67,5,68,1) 72%, rgba(87,6,88,1) 100%)" color="white"></x-type-section>

    <x-type-section title="Hip Hop" description="Ritmo urbano, energía y actitud en coreografías llenas de estilo y creatividad." img="{{asset('images/brand/hiphop.png')}}" background="linear-gradient(90deg, rgba(67,0,36,1) 0%, rgba(67,5,68,1) 72%, rgba(87,6,88,1) 100%)" color="white"></x-type-section>

    <x-type-section title="Salsa y Bachata" description="Aprende los movimientos y la pasión de los ritmos latinos más populares." img="{{asset('images/brand/salsaybachata.png')}}" background="white" color="black"></x-type-section>

    <x-type-section title="Twerk y Dancehall" description="Explora la potencia del movimiento con estos estilos urbanos llenos de energía." img="{{asset('images/brand/dancehall.png')}}" background="white" color="black"></x-type-section>

    <x-type-section title="Danza Urbana" description="Un mix de estilos urbanos para coreografías enérgicas y modernas." img="{{asset('images/brand/danzaurbana.png')}}" background="white" color="black"></x-type-section>

    <div class="section d-flex flex-column">
        <div class="mx-auto mb-4">
            <h1 class="w-75 text-center mx-auto mb-5">Vive la experiencia de la danza</h1>
            <p class="w-50 m-auto text-justify">
                En nuestra academia de baile, creamos un espacio donde la pasión por la danza cobra vida. Fusionamos técnica, expresión y disfrute, brindando un ambiente donde cada persona puede descubrir su propio ritmo y desarrollar su talento con confianza.
            </p>
        </div>
    </div>
        <div class="flex-grow-1 w-100">
            <div class="m-auto floating-card" style="height: 400px; width: 400px; background-image: url('{{asset('images/brand/logoshowstars.png')}}'); background-size: cover; background-position: center"></div>
        </div>
    </div>

    <div class="section text-center d-block">
        <h1 class="text-center">
            Síguenos en redes
        </h1>
        <div
            loading="lazy"
            data-mc-src="b0d5ad7d-5646-435f-9f97-9bb2c4e2f014#instagram">
        </div>
    </div>

    <!--
    <div class="section text-center d-block color-white">
        <h1 class="mb-5">Experiencias</h1>
        <div class="floating-card bg-dark p-3 mb-3 text-left col-8 col-md-6 mx-auto color-white">
            <div class="float-left">
                <img class="rounded-circle" height="48px" width="48px" alt="user"
                     src="{{asset('images/avatars/1')}}">
            </div>
            <div class="user-info d-inline-block w-auto">
                <h6>Camilo Hernandez</h6>
                <div class="fullRating-container"
                     style="min-width: calc(64px); max-width: calc(100px); width: calc(10vw);"></div>
                <img id="emptyRating" alt="rating" src="{{asset('images/empty_rating.png')}}">
            </div>
            <div style="height: 2.5vw; min-height: 16px; max-height: 25px;"></div>
            <blockquote class="blockquote" style="font-size: 1rem">
                    <h5 class="mb-0">"Me encanta"</h5>
                    <footer class="blockquote-footer">{{now()}}</footer>
            </blockquote>
        </div>

        <div class="floating-card bg-dark p-3 mb-3 text-left col-8 col-md-6 mx-auto color-white">
            <div class="float-left">
                <img class="rounded-circle" height="48px" width="48px" alt="user"
                     src="{{asset('images/avatars/2')}}">
            </div>
            <div class="user-info d-inline-block w-auto">
                <h6>Camilo Hernandez</h6>
                <div class="fullRating-container"
                     style="min-width: calc(64px); max-width: calc(100px); width: calc(10vw);"></div>
                <img id="emptyRating" alt="rating" src="{{asset('images/empty_rating.png')}}">
            </div>
            <div style="height: 2.5vw; min-height: 16px; max-height: 25px;"></div>
            <blockquote class="blockquote" style="font-size: 1rem">
                <h5 class="mb-0">"Me encanta"</h5>
                <footer class="blockquote-footer">{{now()}}</footer>
            </blockquote>
        </div>
    </div>
    -->

    <!--Pure Chat component
    <div class="draggable purechat-button-expand" style="background-image: url(&quot;https://app.purechat.com/assets/spanish.c1fabaeb81f83ee3f3db.png&quot;) !important; background-size: cover; position: fixed; right: 0; bottom: 0; margin: 3vh; height: 80.55px !important; width: 90px !important; z-index: 9 !important;"></div>
    -->

    <footer style="margin: 0" class="footer-distributed">

        <div class="footer-left">

            <img class="mb-3" src="{{asset('images/brand/logoshowstars.png')}}" width="70%" height="70%">

            <!--<p class="footer-links">
                <a href="#quienesSomos">Quienes somos</a>
                ·
                <a href="#nuestoCompromiso">Nuestro compromiso</a>
                ·
                <a href="#mision">Mision</a>
                ·
                <a href="#valores">Valores</a>
                ·
                <a href="#equipo">Equipo de trabajo</a>
            </p>-->


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

    @include('cliente.scheduleCourtesyModal')

    @stack('modals')

    @if(session('msg'))
        <script>
            $(document).ready(function(){
                $('#msgModal').modal({show: true});
            });
        </script>
        @php(\Illuminate\Support\Facades\Session::forget('msg'))
    @endif

    <script>
        $(document).ready(function() {
            let options = {
                root: null,    // browser viewport
                rootMargin: '0px',
                threshold: 0.5 // target element visible 50%
            }

            let observer = new IntersectionObserver(fadeOnFocus, options);
            let targets = document.querySelectorAll('.fade-in-section');
            targets.forEach(target => {    // adding observer for all videos
                observer.observe(target);
            });
        });

        let compo = null
        const fadeOnFocus = (entries, observer) => {    // callback
            entries.forEach((entry) => {
                if(entry.isIntersecting) {
                    entry.target.className += entry.target.classList.contains("is-visible") ? "" : " is-visible";
                }
            });
        };
    </script>
    <script>
        $(document).ready(function() {
            $("#schedule-courtesy").delay(2000).fadeIn()
        });
    </script>

    <script>
        var button = document.getElementById('sound');
        var videos = document.querySelectorAll('.landingVideo');
        var volume_off = document.getElementById("volume_off")
        var volume_on = document.getElementById("volume_on")

        button.onclick = function (){

            volume_off.style.display=window.getComputedStyle(volume_off).display === "none" ? "block" : "none";
            volume_on.style.display=window.getComputedStyle(volume_on).display === "none" ? "block" : "none";


            videos.forEach(video => {    // adding observer for all videos
                if(window.getComputedStyle(video).display !== "none")
                    video.muted = !video.muted;
            });
        };

        $(document).ready(function() {
            let options = {
                root: null,    // browser viewport
                rootMargin: '0px',
                threshold: 0.5 // target element visible 50%
            }

            let observer = new IntersectionObserver(playOnFocus, options);
            let targets = document.querySelectorAll('.landingVideo');
            targets.forEach(target => {    // adding observer for all videos
                observer.observe(target);
            });
        });

        const playOnFocus = (entries, observer) => {    // callback
            entries.forEach((entry) => {
                if(entry.isIntersecting) {
                    entry.target.play();    // play target video
                } else {
                    entry.target.pause();    // pause video
                }
            });
        };
    </script>
    <!--Instagram-->
    <script
            src="https://cdn2.woxo.tech/a.js#616af38872a6520016a29c25"
            async data-usrc>
    </script>

    <script src="{{asset('js/bootstrap-swipe-carousel.min.js')}}" type="text/javascript"></script>

    <!--Script para carousel con items que vienen de backend-->
    <script>
        $(document).ready(function() {
            $(".carousel-item:first-child").addClass('active');
        });
    </script>

    <!--Scripts para que vuelvan a la primer card cuando llegue a la última (slider ciclico)-->
    <script>
        $(document).ready(function() {
            var totalItems = $(".itemDivAlmostReady").length;
            //console.log('totalItems: '+totalItems);//for debug purpose

            $("#almostReadyCarousel").on("slide.bs.carousel", function(e) {
                var $e = $(e.relatedTarget);
                var idx = $e.index();
                //console.log('index: '+idx);//for debug purpose
                if (idx == totalItems) {
                    var it = (totalItems - idx);
                    //console.log('otro: '+it);//for debug purpose
                    for (var i = 0; i < it; i++) {
                        // append slides to end
                        if (e.direction == "left") {//forward
                            $(".itemDivAlmostReady")
                                .eq(i)
                                .appendTo("#innerAlmostReady");
                        }
                    }
                    /*$(".media-slider").css('width', '100');*/
                }
                if (e.direction == "right") {
                    //console.log('index: '+idx);//for debug purpose
                    if(idx == 0){
                        $(".itemDivAlmostReady")
                            .eq(totalItems-1)
                            .prependTo("#innerAlmostReady");
                    }

                }
            });
            /*$("#myCarousel").on("slid.bs.carousel", function(e) {
                $(".media-slider").css('width', '100%');
            });*/
        });
    </script>

    @stack('scripts')

    </body>
</html>
