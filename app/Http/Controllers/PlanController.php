<?php

namespace App\Http\Controllers;

use App\Model\Plan;
use App\Repositories\ClientPlanRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Validator;

class PlanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(Auth::check()){
            $clientPlanRepository = new ClientPlanRepository();
            $lastPlan = $clientPlanRepository->findValidClientPlan(clientId: Auth::id(), frozenPlans: true);
            if($lastPlan && $lastPlan->old) {
                $plans = $this->enabledPlans(true);
                return view('plans', ['plans' => $plans]);
            }
        }
        $plans = $this->enabledPlans(false);
        return view('plans', ['plans' => $plans]);
    }

    public function enabledPlans(bool $old)
    {
        return Plan::where(function($q) {
            $q->where('available_plans', '>', 0)
                ->orWhereNull('available_plans');
            })
            ->where('old', $old)
            ->whereNull('deleted_at')
            ->get();
    }

   public function show(Plan $plan){
       return view('plan', [
           'plan' => $plan,
       ]);
   }
}
