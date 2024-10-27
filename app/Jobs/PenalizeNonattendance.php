<?php

namespace App\Jobs;

use App\Http\Services\PenalizeService;
use Carbon\Carbon;

class PenalizeNonattendance
{
    private PenalizeService $penalizeService;

    public function __construct(PenalizeService $penalizeService)
    {
        $this->penalizeService = $penalizeService;
    }

    /**
     * Calculates the number of active clients for certain day to update or create the registry in historical_active_clients table.
     *
     * @return void
     */
    public function __invoke(): void
    {
        $this->penalizeService->penalizeEvent(Carbon::now());
    }
}
