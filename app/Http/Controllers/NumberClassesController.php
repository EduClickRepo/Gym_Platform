<?php

namespace App\Http\Controllers;

use App\ClassType;
use App\Model\Evento;
use Illuminate\Http\Request;
use App\Model\SesionCliente; // Suponiendo que la tabla de clases está en el modelo Clase

class NumberClassesController extends Controller
{
    public function tClasses(Request $request)
    {
        // Obtener todos los tipos de clase para mostrar en el select del formulario
        $tiposDeClase = ClassType::all();

        // Inicializar variables
        $tipoSeleccionado = null;
        $startDate = null;
        $endDate = null;
        $clasesFiltradas = collect();
        $clasesPorTipo = [];

        // Verificar si hay un filtro activo
        if ($request->has(['tipo_clase', 'start_date', 'end_date'])) {
            $tipoSeleccionado = $request->input('tipo_clase');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Buscar el evento por su nombre
            $evento = Evento::where('nombre', $tipoSeleccionado)->first();

            if ($evento) {
                // Filtrar clases por evento_id y rango de fechas
                $clasesFiltradas = SesionCliente::where('evento_id', $evento->id)
                    ->whereBetween('fecha_inicio', [$startDate, $endDate])
                    ->get();

                // Agrupar y contar las clases por tipo
                $clasesPorTipo = $clasesFiltradas->groupBy('evento_id')
                    ->map(fn($clases) => $clases->count())
                    ->toArray();
            }
        }

        return view('timeClasses', compact(
            'tiposDeClase',
            'tipoSeleccionado',
            'startDate',
            'endDate',
            'clasesFiltradas',
            'clasesPorTipo'
        ));
    }
}