<?php

namespace App\Http\Controllers;

use App\Category;
use App\Model\Cliente;
use App\Model\ClientPlan;
use App\Model\Plan;
use App\Model\TransaccionesPagos;
use App\PaymentMethod;
use App\RemainingClass;
use App\Repositories\ClientPlanRepository;
use App\Utils\CategoriesEnum;
use App\Utils\DurationTypesEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ClientPlanController extends Controller
{
    public function save(int $clientId, int $planId, int $payment_id, $creationDay = null, int $accumulativeClasses = 0)
    {
        $creationDay = $creationDay ?? Carbon::now();
        $expirationDate = $creationDay;
        $clientPlanRepository = new ClientPlanRepository();
        $lastPlan = $clientPlanRepository->findValidClientPlan(clientId: $clientId, frozenPlans: true);
        if($lastPlan){
            //TODO si pasa de un plan que le quedan clases a un plan ilimitado, el plan ilimitado quedaría con mayor extensión del debido, porque la extensión del plan por clases se convierte en ilimitado
            //no se necesita chequear que la fecha sea mayor, porque eso ya está dado por el    findValidClientPlan
            $expirationDate =  $lastPlan->expiration_date;
            $accumulativeClasses = $lastPlan->remaining_shared_classes;
            $lastPlan->expiration_date = now();
            $lastPlan->save();
        }

        $clientPlan = new ClientPlan();
        $clientPlan->client_id = $clientId;
        $plan = Plan::find($planId);
        $clientPlan->plan_id = $planId;
        $clientPlan->remaining_shared_classes = $plan-> number_of_shared_classes ?  $plan-> number_of_shared_classes + $accumulativeClasses : null;
        $durationType = DurationTypesEnum::from($plan->duration_type);
        $clientPlan->expiration_date = match ($durationType) {
            DurationTypesEnum::day => $expirationDate->copy()->addDays($plan->duration)->endOfDay(),
            DurationTypesEnum::month => $expirationDate->copy()->addMonths($plan->duration)->endOfDay(),
            DurationTypesEnum::year => $expirationDate->copy()->addYears($plan->duration)->endOfDay(),
        };
        $clientPlan->payment_id = $payment_id;
        $clientPlan->created_at = $creationDay;
        $clientPlan->updated_at = $expirationDate;
        $clientPlan->save();

        /*FIT-57: Uncomment this if you want specific classes*/
        foreach ($plan->allClasses as $class){
            $remainingClasses =new RemainingClass();
            $remainingClasses->client_plan_id = $clientPlan->id;
            $remainingClasses->class_type_id = $class->class_type_id;
            $remainingClasses->unlimited = $class->unlimited;
            $remainingClasses->remaining_classes = $class->number_of_classes;
            $remainingClasses->equipment_included = $class->equipment_included;
            $remainingClasses->save();
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
