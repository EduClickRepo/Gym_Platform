<?php

namespace App\View\Composers;

use App\HistoricalActiveClient;
use Illuminate\View\View;
use App\Traits\DateRangeTrait;

class HistoricActiveClientsComposer
{

    use DateRangeTrait;

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $request = request();
        [$startDate, $endDate] = $this->getDateRange($request);

        $historicalData = HistoricalActiveClient::
            when($startDate, function ($query, $startDate){
                $query->where('date', '>=', $startDate->startOfDay());
            })
            ->when($endDate, function ($query, $endDate){
                $query->where('date', '<=', $endDate->endOfDay());
            })
            ->orderBy('date')->get();

        $activeClientsData = $historicalData->pluck('active_clients');
        $activeNewClientsData = $historicalData->pluck('active_new_clients');
        $activeOldClientsData = $historicalData->pluck('active_old_clients');

        $activeClientsDatasets = [
            [
                'label' => 'Historico clientes activos',
                'data' => $activeClientsData,
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
            ],
            [
                'label' => 'Historico clientes Nuevos',
                'data' => $activeNewClientsData,
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'borderWidth' => 1,
            ],
            [
                'label' => 'Historico clientes Antiguos',
                'data' => $activeOldClientsData,
                'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                'borderColor' => 'rgba(153, 102, 255, 1)',
                'borderWidth' => 1,
            ],
        ];

        $retainedClientsData = $historicalData->pluck('retained_clients');
        $retainedClientsDataset = [
            [
                'label' => 'Historico clientes retenidos',
                'data' => $retainedClientsData,
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
            ],
        ];

        $percentRetainedClientsData = $historicalData->pluck('percent_retained_clients');
        $percentRetainedClientsDataset = [
            [
                'label' => 'Historico porcentaje clientes retenidos',
                'data' => $percentRetainedClientsData,
                'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                'borderColor' => 'rgba(255, 159, 64, 1)',
                'borderWidth' => 1,
            ],
        ];

        $dates = $historicalData->pluck('date')->toJson();

        $view->with([
            'dates' => $dates,
            'activeClientsDatasets' => $activeClientsDatasets,
            'retainedClientsDataset' => $retainedClientsDataset,
            'percentRetainedClientsDataset' => $percentRetainedClientsDataset,
        ]);
    }
}