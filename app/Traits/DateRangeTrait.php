<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait DateRangeTrait
{
    public function getDateRange(Request $request): array
    {
        $startDate = null;
        $endDate = null;

        if ($request->input('start_date') || $request->input('end_date')) {
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
        }

        return [$startDate, $endDate];
    }
}
