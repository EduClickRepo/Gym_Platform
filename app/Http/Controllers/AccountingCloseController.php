<?php

namespace App\Http\Controllers;

use App\Category;
use App\Model\TransaccionesPagos;
use App\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingCloseController extends Controller
{

    public function AccountingClose(Request $request)
    {

        $this->validateDates($request);
        $startDate = Carbon::parse($request->input('startDate'))->startOfDay();
        $endDate = Carbon::parse($request->input('endDate'))->endOfDay();

        $user = Auth::user();

        //Loads with user and payment to see the name of the user that made the transaction and the payment method used for the transaction
        $transactionsForPeriod = TransaccionesPagos
            ::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('codigo_respuesta', '=', 1);

        $transactionsPerPaymentMethod = (clone $transactionsForPeriod)
            ->with( 'payment')
            ->select('payment_method_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('payment_method_id')
            ->get();

        $transactionsPerCategoryMethod = (clone $transactionsForPeriod)
            ->with( 'category')
            ->select('category_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_id')
            ->get();

        $paymentMethods = PaymentMethod::where('enabled', true)->get();

        return view('admin.accounting.accountingClose',
            compact(
                'transactionsPerPaymentMethod',
                'transactionsPerCategoryMethod',
                'paymentMethods',
            ));
    }

    public function AccountingDetails(Request $request)
    {
        $this->validateDates($request);
        $startDate = Carbon::parse($request->input('startDate'))->startOfDay();
        $endDate = Carbon::parse($request->input('endDate'))->endOfDay();
        $categoryId = $request->input('categoryId');
        $paymentMethodId = $request->input('paymentMethodId');

        $transactions = TransaccionesPagos
            ::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('codigo_respuesta', '=', 1)
            ->when($paymentMethodId, function ($query, $paymentMethodId) {
                return $query->where('payment_method_id', $paymentMethodId);
            });

        if ($categoryId != null) {
            if($categoryId == "0"){
                $transactions->whereNull('transacciones_pagos.category_id');
            }else{
                $transactions->where('transacciones_pagos.category_id', $categoryId);
            }
        }
        $transactions = $transactions->get();

        $paymentMethods = PaymentMethod::where('enabled', true)->get();
        $categories = Category::all();

        return view('admin.accounting.accountingDetails',
            compact(
                'transactions',
                'paymentMethods',
                'categories',
            ));
    }

    public function search(Request $request)
    {
        $this->validateDates($request);
        $startDate = Carbon::parse($request->input('startDate'))->startOfDay();
        $endDate = Carbon::parse($request->input('endDate'))->endOfDay();
        
        $id = $request->input('id');
        $paymentMethod = $request->input('paymentMethod');
        $amount = $request->input('amount');
        $category = $request->input('category');
        $data = $request->input('data');
        $user = $request->input('user');
        $filterDate = $request->input('filterDate');

        $query = TransaccionesPagos::with( 'payment')->with( 'category')->with('user')
            ->where('transacciones_pagos.codigo_respuesta', 1);

        if ($id) {
            $query->where('transacciones_pagos.id', $id);
        }
        if ($paymentMethod && $paymentMethod != "all") {
            $query->where('transacciones_pagos.payment_method_id', $paymentMethod);
        }
        if ($category != null && $category != "all") {
            if($category == "0"){
                $query->whereNull('transacciones_pagos.category_id');
            }else{
                $query->where('transacciones_pagos.category_id', $category);
            }
        }
        if ($amount) {
            $query->where('transacciones_pagos.amount', $amount);
        }
        if ($data) {
            $query->where('transacciones_pagos.data', 'LIKE', "%$data%");
        }
        if($user){
            $query->whereHas('user', function ($query) use ($user) {
                $query->where('nombre', 'LIKE', "%$user%")
                    ->orWhere('apellido_1', 'LIKE', "%$user%")
                    ->orWhere('apellido_2', 'LIKE', "%$user%");
            });
        }
        if($filterDate){
            $query->where('created_at', $filterDate);
        }else{
            $query->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        }

        $transactions = $query->get();
        return response()->json($transactions);
    }

    private function validateDates($request){
        $request->validate([
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
        ]);
    }

    public function updateCategory(Request $request): JsonResponse
    {
        if(!Auth::user()->hasFeature(\App\Utils\FeaturesEnum::CHANGE_TRANSACTION_CATEGORY)){
            return response()->json(['success' => true]);
        }
        $transactionId = $request->input('transactionId');
        $categoryId = $request->input('categoryId');
        TransaccionesPagos::where('id', $transactionId)->update(['category_id' => $categoryId]);

        return response()->json(['success' => true]);
    }
}
