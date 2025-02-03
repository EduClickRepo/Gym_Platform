<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SesionCliente;

class UserClassesController extends Controller
{
    public function userClasses(Request $request)
    {
        if ($request->has(['cliente_id', 'start_date', 'end_date'])) {
            $request->validate([
                'cliente_id' => 'required|integer|exists:clientes,usuario_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            $clienteId = $request->cliente_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $assistedClasses = SesionCliente::where('cliente_id', $clienteId)
                ->whereBetween('fecha_inicio', [$startDate, $endDate])
                ->with('event')
                ->get();

            $eventsCounts = $assistedClasses->groupBy('event.nombre')->map->count();

            // Devolver la vista con los resultados
            return view('clientClasses', compact('assistedClasses', 'clienteId', 'startDate', 'endDate', 'eventsCounts'));
        }

        // Si no hay parámetros, mostrar el formulario
        return view('clientClasses');
    }
}