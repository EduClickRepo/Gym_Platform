@extends('layouts.app')

@section('title', 'Asistencia por Tipo de Clase')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center">Formulario de Clases por Tipo</h2>

        <form action="{{ url('/timeClasses') }}" method="GET" class="text-center mb-5">
            <label for="tipo_clase">Tipo de Clase:</label>
            <select name="tipo_clase" id="tipo_clase" required>
                @foreach ($tiposDeClase as $tipo)
                    <option value="{{ $tipo->type }}" {{ old('type', $tipoSeleccionado) == $tipo->type ? 'selected' : '' }}>
                        {{ $tipo->type }}
                    </option>
                @endforeach
            </select>

            <label for="start_date">Fecha de inicio:</label>
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $startDate) }}" required>

            <label for="end_date">Fecha de fin:</label>
            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $endDate) }}" required>

            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        @if(isset($clasesFiltradas) && $clasesFiltradas->count() > 0)
            <h3 class="text-center">Resultados de la búsqueda:</h3>
            <div class="themed-block col-12 col-md-10 mx-auto mt-4 p-2">
                <p><strong>Tipo de Clase:</strong> {{ $tipoSeleccionado }}</p>
                <p><strong>Fecha de Inicio:</strong> {{ $startDate }}</p>
                <p><strong>Fecha de Fin:</strong> {{ $endDate }}</p>
                <p><strong>Total de Clases:</strong> {{ $clasesFiltradas->count() }}</p>

                <h4>Totales por Tipo de Clase:</h4>
                <ul>
                    @foreach ($clasesPorTipo as $eventoId => $total)
                        @php
                            $eventoNombre = \App\Model\Evento::find($eventoId)?->nombre ?? 'Desconocido';
                        @endphp
                        <li><strong>Total {{ $eventoNombre }}:</strong> {{ $total }}</li>
                    @endforeach
                </ul>

                <h4>Detalle de Clases:</h4>
                <ul>
                    @foreach ($clasesFiltradas as $clase)
                        <li>
                            <strong>Fecha:</strong> {{ $clase->fecha_inicio }} |
                            <strong>Tipo de Clase:</strong> {{ $clase->evento->nombre ?? 'Desconocido' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <p class="text-center"><strong>No hay clases que mostrar para este rango de fechas.</strong></p>
        @endif
    </div>
@endsection