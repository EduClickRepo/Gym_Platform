<?php

namespace App\Http\Controllers;

use App\Model\TransaccionesPagos;
use App\Utils\FeaturesEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingFlowController extends Controller
{
    public function AccountingFlow(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));

        $positiveValuesMayorCash = collect();
        $positiveMayorCashSum = 0;
        $negativeValuesMayorCash = collect();
        $negativeMayorCashSum = 0;
        $positiveValuesPettyCash = collect();
        $positivePettyCashSum = 0;
        $negativeValuesPettyCash = collect();
        $negativePettyCashSum = 0;

        $user = Auth::user();

        if ($user->hasFeature(FeaturesEnum::SEE_PETTY_CASH)) {
            //Loads with user and payment to see the name of the user that made the transaction and the payment method used for the transaction
            $positiveValuesPettyCash = TransaccionesPagos::with(['user', 'payment'])
                ->where('amount', '>', 0)
                ->where('is_petty_cash', '=', 1)
                ->where('created_at', '>=', $startDate->startOfDay())
                ->where('created_at', '<=', $endDate->endOfDay())
                ->where('codigo_respuesta', '=', 1)
                ->get();

            $positivePettyCashSum = $positiveValuesPettyCash->sum('amount');

            $negativeValuesPettyCash = TransaccionesPagos::with(['user', 'createdBy', 'payment'])
                ->where('amount', '<', 0)
                ->where('is_petty_cash', '=', 1)
                ->where('created_at', '>=', $startDate->startOfDay())
                ->where('created_at', '<=', $endDate->endOfDay())
                ->where('codigo_respuesta', '=', 1)
                ->get();

            $negativePettyCashSum = $negativeValuesPettyCash->sum('amount');
        }

        if ($user->hasFeature(FeaturesEnum::SEE_MAYOR_CASH)) {
            //Loads with user and payment to see the name of the user that made the transaction and the payment method used for the transaction
            $positiveValuesMayorCash = TransaccionesPagos::with(['user', 'payment'])
                ->where('amount', '>', 0)
                ->where('is_petty_cash', '=', 0)
                ->where('created_at', '>=', $startDate->startOfDay())
                ->where('created_at', '<=', $endDate->endOfDay())
                ->where('codigo_respuesta', '=', 1)
                ->get();

            $positiveMayorCashSum = $positiveValuesMayorCash->sum('amount');

            $negativeValuesMayorCash = TransaccionesPagos::with(['user', 'payment'])
                ->where('amount', '<', 0)
                ->where('is_petty_cash', '=', 0)
                ->where('created_at', '>=', $startDate->startOfDay())
                ->where('created_at', '<=', $endDate->endOfDay())
                ->where('codigo_respuesta', '=', 1)
                ->get();

            $negativeMayorCashSum = $negativeValuesMayorCash->sum('amount');
        }
        return view('admin.accounting.accountingFlow',
            compact(
                'positiveValuesPettyCash',
                'positivePettyCashSum',
                'negativeValuesPettyCash',
                'negativePettyCashSum',
                'positiveValuesMayorCash',
                'positiveMayorCashSum',
                'negativeValuesMayorCash',
                'negativeMayorCashSum'));
    }
}
