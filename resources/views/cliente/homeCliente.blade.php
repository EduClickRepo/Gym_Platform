@extends('layouts.home')

@section('title')
    Mi Perfil
@endsection

@include('cliente.completeProfileClient')

@section('modals')

    <!--solicitud modal ELIMINAR-->
    <!--Solo se utiliza para clientes ya que los entrenadores pueder ir a la info de la solictud y eliminar su oferta-->
    <div class="modal fade" id="solicitudModalEliminar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="eliminarSolicitudForm" method="post"
                      action="{{route('eliminarSolicitud', ['user'=> Auth::user()->slug])}}"
                      autocomplete="off">
                    @method('DELETE')
                    @csrf

                    <input type="hidden" name="solicitudIDEliminar" id="solicitudIDEliminar">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro que desea eliminar su solicitud?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn themed-btn" data-dismiss="modal"
                                aria-label="Close">Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('tipoUsuario')
    Atleta
@endsection

@section('medidasCliente')
    @if($user->cliente != null && $user->cliente->estatura())
        <p>{{number_format($user->cliente->estatura()->estatura, 2)}} {{$user->cliente->estatura()->unidadMedidaAbreviatura}}</p>
    @endif
    @if($user->cliente != null &&  $user->cliente->peso())
        <p>{{number_format($user->cliente->peso()->peso, 2)}} {{$user->cliente->peso()->unidadMedidaAbreviatura}}</p>
    @endif
@endsection

@push('cards')
    @if(!$visitante)
        @include('entrenamientosAgendados')
        @if(isset($reviewFor))
            @include('modalDarReviewEntrenamiento')
        @endif
        @include('highlightSection')
        @include('cliente/clientPlan')
        <div class="p-3 mb-3">
            <div class="mb-3 d-flex justify-content-between">
                <h3>Próximas sesiones:</h3>
            </div>
            @include('proximasSesiones')
        </div>
    @endif
@endpush

@push('scripts')
    <!--Pop-up Review Session-->
    <script type="text/javascript">
        $(document).ready(function(){
            if({{session('msg') ? 0 : 1}} && !sessionStorage.getItem('training-review-showed') && {{isset($reviewFor) ? 1 : 0}}) {
                sessionStorage.setItem('training-review-showed', 'true');//at the beginning, it won't be present in the session, and if we get it, it will be false
                setTimeout(function() {$('#reviewEntrenamiento').modal('show');},
                    3000);
            }
        });
    </script>
@endpush
