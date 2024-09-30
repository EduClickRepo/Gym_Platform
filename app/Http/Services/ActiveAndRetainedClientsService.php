<?php

namespace App\Http\Services;

use App\HistoricalActiveClient;
use App\Model\ClientPlan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActiveAndRetainedClientsService
{
    /**
     * @param $date
     * Los $countActiveOldClients: son clientes que renovaron su plan o que tienen planes activos hace más de un mes. Pueden ser clientes que estuvieron activos hace 1 año y volvieron
     * Los $countRetainedClients: son clientes que se les vencía su plan en el mes pasado y que renovaron
     */
    public function saveActiveClients($date):void
    {
        $currentDate = Carbon::parse($date);
        $thirtyDaysAgo = $currentDate->copy()->subDays(30);
        $startOfLastMonth = $currentDate->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $currentDate->copy()->subMonth()->endOfMonth();

        $countActiveClients = ClientPlan::where('created_at', '<=', $date)
            ->where('expiration_date', '>=', $date)
            ->where(function ($query)  {
                $query->where('remaining_shared_classes', '>', 0)
                    ->orWhereNull('remaining_shared_classes');
            })
            ->distinct('client_id')
            ->count('client_id');

        //The active clients are those who acquired a plan in the last 30 days, and don't have a plan before
        $countActiveNewClients = ClientPlan::where('created_at', '<=', $date)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('expiration_date', '>=', $date)
            ->where(function ($query)  {
                $query->where('remaining_shared_classes', '>', 0)
                    ->orWhereNull('remaining_shared_classes');
            })
            ->whereNotExists(function ($query) use ($date) {
                $query->selectRaw(1)
                    ->from('client_plans as cp2')
                    ->whereRaw('cp2.client_id = client_plans.client_id')
                    ->whereRaw('cp2.id != client_plans.id')
                    ->whereRaw('cp2.expiration_date <= client_plans.expiration_date')
                    ->where('cp2.created_at', '<=', $date);
            })
            ->distinct('client_id')
            ->count('client_id');

        //The active old clients are those who have a plan before the current plan, or have plans created more than 30 day ago (ie: annual or promos)
        $countActiveOldClients = ClientPlan::where('created_at', '<=', $date)
            ->where('expiration_date', '>=', $date)
            ->where(function ($query)  {
                $query->where('remaining_shared_classes', '>', 0)
                    ->orWhereNull('remaining_shared_classes');
            })
            ->where(function($q) use ($date, $thirtyDaysAgo) {
                $q->whereExists(function ($query) use ($date) {
                    $query->selectRaw(1)
                        ->from('client_plans as cp2')
                        ->whereRaw('cp2.client_id = client_plans.client_id')
                        ->whereRaw('cp2.id != client_plans.id')
                        ->whereRaw('cp2.expiration_date <= client_plans.expiration_date')
                        ->where('cp2.created_at', '<=', $date);
                })
                ->orWhere('created_at', '<=', $thirtyDaysAgo);
            })
            ->distinct('client_id')
            ->count('client_id');

        Log::info('Active clients at: ' . $date . ' - ' . $countActiveClients);

        $totalExpiredLastMonth = ClientPlan::where('expiration_date', '<=', $endOfLastMonth)
            ->where('expiration_date', '>=', $startOfLastMonth)
            ->distinct('client_id')
            ->count('client_id');

        $countRetainedClients = ClientPlan::where('expiration_date', '<=', $endOfLastMonth)
            ->where('expiration_date', '>=', $startOfLastMonth)
            ->whereExists(function ($query) use ($currentDate) {
                $query->selectRaw(1)
                    ->from('client_plans as cp2')
                    ->whereRaw('cp2.client_id = client_plans.client_id')
                    ->whereRaw('cp2.id != client_plans.id')
                    ->where('cp2.created_at', '<=', $currentDate)
                    ->where('cp2.expiration_date', '>=' ,$currentDate);
            })
            ->distinct('client_id')
            ->count('client_id');

        $percentageRetainedClients = ($totalExpiredLastMonth > 0) ? ($countRetainedClients / $totalExpiredLastMonth) * 100 : 0;


        HistoricalActiveClient::updateOrCreate(
            ['date' => $currentDate],
            ['active_clients' => $countActiveClients,
             'active_new_clients' => $countActiveNewClients,
             'active_old_clients' => $countActiveOldClients,
             'retained_clients' => $countRetainedClients,
             'percent_retained_clients' => $percentageRetainedClients]
        );
    }
}