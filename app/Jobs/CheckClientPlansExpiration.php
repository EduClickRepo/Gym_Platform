<?php

namespace App\Jobs;

use App\DTO\ExpirationInfo;
use App\Http\Services\ProcessPaymentInterface;
use App\Model\ClientPlan;
use App\Model\Subscriptions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckClientPlansExpiration
{
    public function __construct()
    {}

    /**
     * Check all the plans that will expire in the next 3 days and sends a message to remind them to renew.
     *
     * @return void
     */
    public function __invoke(): void
    {
        DB::transaction(function () {

            $initialDate = Carbon::now()->startOfDay();
            $finalDate = Carbon::now()->addDays(7)->endOfDay();
            $usersInfo =
                ClientPlan::join('usuarios', 'usuarios.id', 'client_plans.client_id')
                ->where('expiration_date', '>=', $initialDate)
                ->where('expiration_date', '<=', $finalDate)
                ->where('scheduled_renew_msg', '0')
                ->whereNotIn('usuarios.id', function ($query) use ($finalDate) {
                    $query->select('cp.client_id')
                        ->from('client_plans as cp')
                        ->where('cp.expiration_date', '>', $finalDate);
                })
                ->select('usuarios.telefono', 'client_plans.expiration_date', 'client_plans.id as client_plan_id', 'usuarios.id as user_id')
                ->get();

            $usersInfo->each(function ($info) {
                $subscription = Subscriptions::where('user_id', $info->user_id)->first();
                if($subscription){
                    $paymentService = app(ProcessPaymentInterface::class);
                    $response = $paymentService->makePayment($subscription->user_id, $subscription->payment_source_id, $subscription->amount, $subscription->currency, $subscription->plan_id, $subscription->user->email);
                    Log::info('Result of subscription payment: '. $response->body());
                }else{
                    $expirationDate = Carbon::parse($info->expiration_date);
                    $threeDaysFromNow = Carbon::now()->addDays(3)->endOfDay();
                    if ($expirationDate->lte($threeDaysFromNow)) {
                        $expirationInfo = new ExpirationInfo($info->telefono, $info->expiration_date, $info->client_plan_id);
                        dispatch(new SendMessageToRenewPlan($expirationInfo));
                    }
                }
            });
        });
    }
}
