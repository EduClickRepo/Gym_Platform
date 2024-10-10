<div class="m-auto text-center w-75">
    <h2>Asistentes:</h2>
    <table class="w-100 table">
        <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Nombre</th>
                <th scope="col">Es cortesía</th>
                @if(strcasecmp($event->classType->type, \App\Utils\PlanTypesEnum::KANGOO->value) == 0)
                    <th scope="col">Peso</th>
                    <th scope="col">Talla</th>
                    <th scope="col">Kangoo</th>
                @endif
                <th scope="col">Asistió</th>
            </tr>
        </thead>
        <tbody>
        @foreach($event->attendees as $clientSession)

            <tr>
                <td><div style="max-height:3rem; overflow:hidden">{{$clientSession->client->usuario_id}}</div></td>
                <td><a class="client-icon"
                       href="{{route('visitarPerfil', ['user'=> $clientSession->client->usuario->slug])}}"><div style="max-height:3rem; overflow:hidden">{{$clientSession->client->usuario->fullName}}</div></a></td>
                <td><div style="max-height:3rem; overflow:hidden">{{$clientSession->is_courtesy ? 'Si' : 'No'}}</div></td>
                @if(strcasecmp($event->classType->type, \App\Utils\PlanTypesEnum::KANGOO->value) == 0 || strcasecmp($event->classType->type, \App\Utils\PlanTypesEnum::KANGOO_KIDS->value) == 0)
                    <td><div style="max-height:3rem; overflow:hidden">{{$clientSession->client->peso() ? $clientSession->client->peso()->peso : ''}}</div></td>
                    <td><div style="max-height:3rem; overflow:hidden">{{$clientSession->client->talla_zapato}}</div></td>
                    <td><div style="max-height:3rem; overflow:hidden">{{$clientSession->kangoo_id ? $clientSession->kangoo->SKU : ''}}</div></td>
                @endif
                <td><div style="max-height:3rem; overflow:hidden">
                    <input class="form-check-input" type="checkbox" name="attended" id="attended_{{$clientSession->id}}" onclick="checkAttendee({{$clientSession->id}}, this)" {{$clientSession->attended ? 'checked' : ''}} required>
                </div></td>
            </tr>

        @endforeach
        <tbody>
    </table>

    <div class="d-flex justify-content-around">
        <button class="btn themed-btn" data-toggle="modal" data-target="#registerAttendee">
            Registrar Asistente
        </button>
        @if(strcasecmp($event->classType->type, \App\Utils\PlanTypesEnum::KANGOO->value) == 0)
            <button type="button" class="btn themed-btn" onclick="reorderKangoos()">Reordenar Kangoos</button>
        @endif
    </div>
</div>

@include('components/modalRegisterAttendee')

@push('scripts')
    <script>
        function checkAttendee(clientSessionId, checkbox){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('checkAttendee') }}",
                method: "POST",
                data: {
                    clientSessionId: clientSessionId,
                    checked: $(checkbox).is(':checked')
                },

                /*if you want to debug you need to uncomment this line and comment reload
                error: function(data) {
                    console.log(data);
                }*/
            });
        }
    </script>

    <script>
        function reorderKangoos(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('reorderKangoos') }}",
                method: "POST",
                data: {
                    eventId: {{$event->id}},
                    startDate: "{{Carbon\Carbon::parse($event->fecha_inicio)->format('d-m-Y')}}",
                    startHour: "{{$event->start_hour}}",
                    endDate: "{{Carbon\Carbon::parse($event->fecha_fin)->format('d-m-Y')}}",
                    endHour: "{{$event->end_hour}}",
                },
                success: function () {
                    $('html, body').animate({ scrollTop: 0 }, 0);
                    location.reload();
                },
                error: function(data) {
                    console.log("Erorr al reordenar los kangoos")
                    //console.log(data); //if you want to debug you need to uncomment this line and comment reload
                }
            });
        }
    </script>
@endpush

