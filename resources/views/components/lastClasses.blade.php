@push('head-content')
    <link href="{{asset('css/lastClasses.css')}}" rel="stylesheet"/>
@endpush

<div class="py-3 mb-3">
    <h3>Últimas Clases:</h3>
</div>
<div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
    <table class="m-auto text-center">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Evento</th>
            <th>Asistió</th>
        </tr>

        </thead>
        <tbody>
        @foreach($lastSessions as $session)
            <tr>
                <td>{{ $session->fecha_inicio }}</td>
                <td>{{ $session->event->nombre }}</td>
                <td>{{ $session->attended == 1 ? 'Si' : 'No' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
