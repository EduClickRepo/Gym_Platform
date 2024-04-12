<?php

namespace App\View\Composers;

use App\HistoricalActiveClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class HistoricActiveClientsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $activeClients =  HistoricalActiveClient::all();

        //where('date', '>=', )

        $datasets = [
            [
                'label' => 'Historico clientes activos',
                'data' => $activeClients->pluck('active_clients'),
                'backgroundColor' => 'rgba(75, 192, 192, 1)',
                'borderColor' => 'rgba(75, 192, 192, 1)'
            ],
        ];
        $view->with([
            'labels' => $activeClients->pluck('date')->toJson(),
            'dataset' => $datasets,
        ]);
    }
}