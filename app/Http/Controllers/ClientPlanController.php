<?php

namespace App\Http\Controllers;

use App\Category;
use App\Model\Cliente;
use App\Model\ClientPlan;
use App\Model\Plan;
use App\Model\TransaccionesPagos;
use App\PaymentMethod;
use App\RemainingClass;
use App\Utils\CategoriesEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ClientPlanController extends Controller
{
    public function save(int $clientId,int $planId, int $payment_id, $payDay = null, int $accumulativeClasses = 0)
    {
        $payDay = $payDay ?? Carbon::now();

        $clientPlan = new ClientPlan();
        $clientPlan->client_id = $clientId;
        $plan = Plan::find($planId);
        $clientPlan->plan_id = $planId;
        $clientPlan->remaining_shared_classes = $plan-> number_of_shared_classes ?  $plan-> number_of_shared_classes + $accumulativeClasses : null;
        $clientPlan->expiration_date = $payDay->copy()->addDays($plan->duration_days)->endOfDay();
        $clientPlan->payment_id = $payment_id;
        $clientPlan->created_at = $payDay;
        $clientPlan->save();

        /*FIT-57: Uncomment this if you want specific classes*/
        foreach ($plan->allClasses as $class){
            $remainingClass =new RemainingClass();
            $remainingClass->client_plan_id = $clientPlan->id;
            $remainingClass->class_type_id = $class->class_type_id;
            $remainingClass->unlimited = $class->unlimited;
            $remainingClass->remaining_classes = $class->number_of_classes;
            $remainingClass->equipment_included = $class->equipment_included;
            $remainingClass->save();
        }
        /*FIT-57: end block code*/
    }

    public function showLoadClientPlan(){
        $clients = Cliente::all();
        $paymentMethods = PaymentMethod::where('enabled', true)->get();
        $enabledPlans = Plan::all();
        return view('admin.clientPlan.saveClientPlan', compact('clients', 'paymentMethods', 'enabledPlans'));

    }

    public function saveClientPlan(Request $request){
        DB::beginTransaction();

        try {
            $payDay = Carbon::createFromFormat('d/m/Y',$request->payDay);
            $transaction = new TransaccionesPagos();
            $transaction->payment_method_id = $request->paymentMethodId;
            $transaction->ref_payco = "1";
            $transaction->codigo_respuesta = "1";
            $transaction->respuesta = "Aprobado";
            $transaction->amount = $request->amount;
            $transaction->data = $request->data ?? "";
            $transaction->user_id = $request->clientId;
            $transaction->created_at = $payDay;
            $transaction->category_id = Category::where('name', CategoriesEnum::PLANES)->first()->id;
            $transaction->save();

            $lastPlan = ClientPlan::find($request->lastPlanId);
            if($lastPlan && ($request->accumulateClasses === "on" || !$lastPlan->remaining_shared_classes)){
                if($lastPlan->expiration_date->greaterThan($payDay)){
                    $payDay =  $lastPlan->expiration_date;
                }
                $lastPlan->expiration_date = now();
                $lastPlan->save();
            }
            $this->save($request->clientId, $request->planId, $transaction->id,$payDay, $request->accumulateClasses === "on" ? (int)($request->remainingClases) : 0);
            Session::put('msg_level', 'success');
            Session::put('msg', __('general.success_save_client_plan'));
            Session::save();
            DB::commit();
            return redirect()->back();
        }catch (Exception $exception) {
            DB::rollBack();
            Log::error("ERROR ClientPlanController - saveClientPlan: " . $exception->getMessage());
            Session::put('msg_level', 'danger');
            Session::put('msg', __('general.error_save_client_plan'));
            Session::save();
            return redirect()->back();
        }
    }

    public function showFreezeClientPlan(){
        $clients = Cliente::all();
        return view('admin.clientPlan.freezeClientPlan', compact('clients'));
    }

    public function freezePlan(Request $request){
        if(!$request->lastPlanId || !$request->frozenFrom || !$request->frozenTo){
            Session::put('msg_level', 'danger');
            Session::put('msg', __('general.error_no_plan'));
            Session::save();
            return redirect()->back();
        }
        $lastPlan = ClientPlan::find($request->lastPlanId);
        $frozenFrom = Carbon::createFromFormat('d/m/Y',$request->frozenFrom);
        $frozenTo =  Carbon::createFromFormat('d/m/Y',$request->frozenTo);
        $lastPlan->frozen_from = $frozenFrom;
        $lastPlan->frozen_to = $frozenTo;
        $lastPlan->expiration_date = $lastPlan->expiration_date->addDays($frozenFrom->diffInDays($frozenTo, false));
        $lastPlan->save();
        Session::put('msg_level', 'success');
        Session::put('msg', __('general.success_freeze'));
        Session::save();
        return redirect()->back();
   }
}
