<!--Cancelar entrenamiento modal-->
<div class="modal fade" id="cancelarEntrenamientoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="cancelarEntrenamientoForm" method="post" action="{{route('cancelarEntrenamiento')}}" autocomplete="off">
                @method('DELETE')
                @csrf

                <input type="hidden" name="entrenamientoCancelar" id="entrenamientoCancelar">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmar cancelación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas cancelar el entrenamiento?</p>
                    <br/>
                    <p id="advertenciaPenalidad">Recuerda que si cancelas con menos de {{ HOURS_TO_CANCEL_TRAINING }} horas de anticipación, serás penalizada y no se te devolverá la clase.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Seguro</button>
                    <button type="button" class="btn themed-btn" data-dismiss="modal" aria-label="Close">Volver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="p-3 mb-3">
    <div class="mb-5">
        <h3>Entrenamientos agendados:</h3>
    </div>

    @if(!$entrenamientosAgendados->isEmpty())
        @foreach($entrenamientosAgendados as $entrenamiento)
            <div class="solicitud-container  text-center text-md-left d-md-flex themed-block mb-3">
                <div>
                    <h3 class="d-block my-2">{{$entrenamiento->nombre}}</h3>
                    <p class="d-block my-1"><strong>Día:</strong> {{$entrenamiento->fecha_inicio->isoFormat('dddd D MMMM')}}</p>
                    <p class="d-block my-1"><strong>Hora:</strong> {{$entrenamiento->fecha_inicio->format('g:i A')}}</p>
                    <p class="d-block my-1"><strong>Lugar: </strong>{{$entrenamiento->lugar}}</p>
                    @isset($entrenamiento->SKU)
                        <p class="d-block my-1"><strong>Kangoo: </strong>{{$entrenamiento->SKU}}</p>
                    @endisset
                </div>
                <div class="ml-auto my-3">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelarEntrenamientoModal" onclick="prepararCancelarEntrenamiento({{$entrenamiento->id}})">Cancelar</button>
                </div>
            </div>
        @endforeach
    @else
        <div class="solicitud-container d-md-flex themed-block mb-3">
            <p>Aún no tienes entrenamientos agendados</p>
        </div>
    @endif
</div>

@push('scripts')

    <script>
        <!---->
        function prepararCancelarEntrenamiento(sessionId) {
            document.getElementById('entrenamientoCancelar').value= sessionId;
        }
        <!--Review-->
        function asignarReview(sessionId) {
            document.getElementById('reviewFor').value= sessionId;
            document.getElementById('rating').value= null;
            $('#ratingSeleccionado').css('width', '0');
        }
    </script>
@endpush

