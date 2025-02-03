@extends('layouts.app')

@section('title')

    Asistencia de cliente

@endsection

@section('content')
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Formulario de Clases</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

        </head>
        <body class="container mt-5">
        <h2 class="text-center">Formulario de Clases</h2>
        <form action="{{ url('/userClasses') }}" method="GET" class="text-center mb-5">
            <label for="cliente_id">ID del Cliente:</label>
            <input type="number" name="cliente_id" id="cliente_id" required>

            <label for="start_date">Fecha de inicio:</label>
            <input type="date" name="start_date" id="start_date" required>

            <label for="end_date">Fecha de fin:</label>
            <input type="date" name="end_date" id="end_date" required>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>

        @if(isset($assistedClasses) && $assistedClasses->count() > 0)
            <h3 class="text-center">Resultados de la búsqueda:</h3>
            <div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
                <p><strong>ID del Cliente:</strong> {{ $clienteId }}</p>
                <p><strong>Fecha de Inicio:</strong> {{ $startDate }}</p>
                <p><strong>Fecha de Fin:</strong> {{ $endDate }}</p>
                <p><strong>Total de Clases Asistidas:</strong> {{ $assistedClasses->count() }}</p>

                <h4>Totales por Evento:</h4>
                <ul>
                    @foreach ($eventosContados as $evento => $total)
                        <li><strong>Total {{ $evento }}:</strong> {{ $total }}</li>
                    @endforeach
                </ul>

                <h4>Detalle de Clases:</h4>
                <ul>
                    @foreach ($clasesAsistidas as $clase)
                        <li>
                            <strong>Fecha:</strong> {{ $clase->fecha_inicio }} |
                            <strong>Evento:</strong> {{ $clase->event->nombre ?? 'Evento desconocido' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-center"><strong>No hay clases que mostrar para este rango de fechas.</strong></p>
        @endif
        </body>
        </html>
@endsection