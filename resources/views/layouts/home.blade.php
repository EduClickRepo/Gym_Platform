@extends('layouts.app')

@section('title')
    Home
@endsection

@push('head-content')

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{asset('css/home.css')}}">

@endpush

@section('content')

    <!--mostrar direccion-->
    <script>
        $(document).ready(function () {
            $('#mapModal').on('shown.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                document.getElementById('direccion').innerHTML = button.data('direccion');
            });
        });
    </script>
    <!-- Modal maps-->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ubicacion <br/><small id="direccion"></small></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&#215;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height:80vh;"></div>
                </div>
            </div>
        </div>
    </div>

    @include('modalCompletarPerfil')

    @include('crearBlogModal')

    @stack('modals')

    <div class="container-fluid">
        <div class="d-md-flex">
            <div class="perfil-container m-3">
                <img src="{{asset('images/avatars/'.$user->foto)}}" class="user-profile-icon">
                @if(!$visitante)
                    @if((strcasecmp ($user->rol, 'entrenador' ) == 0 && $user->entrenador == null) || (strcasecmp ($user->rol, 'cliente' ) == 0 && $user->cliente == null))
                        <div class="progress bg-dark ml-auto mr-auto" style="width: 80%;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 25%; color: black"
                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%
                            </div>
                        </div>
                        <button class="btn themed-btn d-block ml-auto mr-auto" data-toggle="modal"
                                data-target="#completarPerfilModal">Completar perfil
                        </button>
                    @endif
                @endif
                @if(strcasecmp ($user->rol, 'entrenador' ) == 0)
                    <img style="width: 100px; height: 25px; margin-bottom: 0" alt="rating"
                         src="{{asset('images/empty_rating.png')}}">
                    <div style="margin: 0 auto 3vh auto; width: 100px; height: 1px">
                        <div class="fullRating-container"
                             style="width: calc(100px*{{$user->ratingPorcentage()}}); height: 25px; margin-top: -25px;"></div>
                    </div>
                @endif
                <h4>{{$user->nombre}} {{$user->apellido_1}}</h4>
                <p>@yield('tipoUsuario')</p>
                <p>{{$user->edad}}</p>
            </div>
            <div id="right-div" class="ml-md-auto m-3">
                @stack('cards')

                <div>
                    <h1 class="text-center">
                        Síguenos en nuestras redes sociales
                    </h1>
                    <div
                            loading="lazy"
                            data-mc-src="b0d5ad7d-5646-435f-9f97-9bb2c4e2f014#instagram">
                    </div>
                </div>

                <div class="themed-block p-3 mb-3">
                    <h3 class="mb-5">Reviews ({{$user->reviews->count()}}):</h3>
                    @foreach($user->reviews as $review)
                        <div class="floating-card theme-inverted p-3 mb-3">
                            <div class="float-left">
                                <img class="rounded-circle" height="48px" width="48px" alt="user"
                                     src="{{asset('images/avatars/'.$review->reviewer->foto)}}">
                            </div>

                            <div class="user-info d-inline-block">
                                <h4>{{$review->reviewer->nombre}} {{$review->reviewer->apellido_1}}</h4>
                                <div class="fullRating-container"
                                     style="min-width: calc(64px*{{$review->rating/5}}); max-width: calc(100px*{{$review->rating/5}}); width: calc(10vw*{{$review->rating/5}});"></div>
                                <img id="emptyRating" alt="rating" src="{{asset('images/empty_rating.png')}}">
                            </div>
                            <div style="height: 2.5vw; min-height: 16px; max-height: 25px;"></div>
                            <blockquote class="blockquote">
                                @if($review->review != null)
                                    <p class="mb-0">"{{$review->review}}"</p>
                                @endif
                                <footer class="blockquote-footer">{{$review->tiempo()}}</footer>
                            </blockquote>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $('.counter-count').each(function () {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 3000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    </script>

    <!--Instagram-->
    <script
            src="https://cdn2.woxo.tech/a.js#616af38872a6520016a29c25"
            async data-usrc>
    </script>

    <!--Maps-->
    <script>
        var map;
        var marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 16
            });
        }

        function cargarUbicacion(solicitud) {

            initMap();

            var location = {
                lat: Number(solicitud.latitud),
                lng: Number(solicitud.longitud)
            };

            crearMarker(map, location);

            map.setCenter(location);
        }

        function crearMarker(map, location, draggable) {
            marker = new google.maps.Marker({
                position: location,
                draggable: draggable,
                map: map
            });
        }
    </script>
    <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDjn6uQjll2HhwGi8L5_QTs4bAxAjqh5E0&libraries=geometry&callBack=initMap">
    </script>
@endpush