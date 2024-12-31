<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Services\ProcessPaymentInterface;
use App\Model\SesionCliente;
use App\Model\Subscriptions;
use App\Model\TransaccionesPagos;
use App\Model\TransaccionesPendientes;
use App\PaymentMethod;
use App\Utils\CategoriesEnum;
use App\Utils\PayStatusEnum;
use App\Utils\PaymentMethodsEnum;
use App\Utils\PayStatusMapper;
use App\Utils\PayTypesEnum;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PagosController extends Controller
{
    protected ProcessPaymentInterface $paymentService;

    public function __construct(ProcessPaymentInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    public function responsePayment(Request $request)
    {
        $signature = $request->header('X-Event-Checksum');
        $status = $request->data['transaction']['status'];
        $amount = $request->data['transaction']['amount_in_cents'];
        $timestamp = $request->timestamp;
        $reference = $request->data['transaction']['reference'];
        $this->verifySignature($signature, $reference, $status, $amount, $timestamp);
        $paymentInfo = $this->paymentService->getPaymentInfo($reference);
        $response_code = PayStatusMapper::getMappedValue($status);
        if($this->verifyProcessing($reference, $response_code)){
            return response()->json(['message' => 'Transacción ya procesada anteriormente'], 200);
        }
        DB::transaction(function () use ($paymentInfo, $status, $amount, $response_code, $reference, $request) { //If the plan is not loaded, wompi will try again
            $payment_id = $this->guardarRespuestaTx($reference, $response_code, $status, $amount, $request->data, $paymentInfo['userId']);
            switch ($paymentInfo['type']) {
                case PayTypesEnum::Plan->value:
                    $this->processPlanPayment($response_code, $paymentInfo['paidObjectId'], $paymentInfo['userId'], $payment_id);
                    break;
                case PayTypesEnum::Session->value:
                    //$this->processSessionPayment($response_code, $paymentInfo['paidObjectId'], $paymentInfo['userId'], $data->x_extra4, $data->x_extra5, $data->x_extra6);
                    break;
                default:
                    return response()->json(['message' => 'Artículo pagado desconocido'], 400);
            }
            return response()->json(['message' => 'Pago procesado con éxito'], 200);
        });
    }

    private function verifySignature(string $signature,string $reference,string $status,string $amount,string $timestamp)
    {
        $integrity =env('INTEGRITY_SIGNATURE');
        $expectedSignature = hash('sha256', "{$reference}{$status}{$amount}{$timestamp}{$integrity}");
        if (!$signature || $signature !== $expectedSignature) {
            abort(403, 'Invalid Firm');
        }
    }

    private function verifyProcessing($platformRef, $response_code)
    {
        $transaction = TransaccionesPagos::where('ref_payco', $platformRef)->first();
        if (!$transaction) {
            return false;
        }
        return $transaction->codigo_respuesta === $response_code;
    }

    private function procesarPago($data)
    {
        $p_cust_id_cliente = env('EPAYCO_P_CUST_ID_CLIENTE');
        $p_key = env('EPAYCO_P_KEY');
        $x_ref_payco = $data->x_ref_payco;
        $x_transaction_id = $data->x_transaction_id;
        $x_amount = $data->x_amount;
        $x_currency_code = $data->x_currency_code;
        $x_signature = $data->x_signature;
        $signature = hash('sha256', $p_cust_id_cliente . '^' . $p_key . '^' . $x_ref_payco . '^' . $x_transaction_id . '^' . $x_amount . '^' . $x_currency_code);

        //Validamos la firma
        if ($x_signature == $signature) {
            $payment_id = $this->guardarRespuestaTx($data);
            /*Si la firma esta bien podemos verificar el estado de la transacción*/
            $x_cod_response = $data->x_cod_response;
            switch ((string) $data->x_extra1) {
                case PayTypesEnum::Plan->value:
                    $this->processPlanPayment($x_cod_response,$data->x_extra3, $data->x_extra2, $payment_id);
                    break;
                case PayTypesEnum::Session->value:
                    $this->processSessionPayment($x_cod_response,$data->x_extra3, $data->x_extra2, $data->x_extra4, $data->x_extra5, $data->x_extra6);
                    break;
                default:
                    die("Tipo de pago desconocido");
            }
        } else {
            die("Firma no valida");
        };



        return response()->json([
            'success' => true,
            'message' => 'transacción procesada'
        ], 200);
    }

    private function processPlanPayment($x_cod_response, $planId, $clientId, $payment_id ){
        switch ((int)$x_cod_response) {
            case 1:
                # code transacción aceptada
                (new ClientPlanController())->save($clientId, $planId, $payment_id);
                Session::put('msg_level', 'success');
                Session::put('msg', __('general.success_purchase'));
                Session::save();
                break;
            case 2:# code transacción rechazada
            case 4:# code transacción fallida
                Session::put('msg_level', 'danger');
                Session::put('msg', __('general.failed_purchase'));
                Session::save();
                break;
            case 3:# code transacción pendiente //ESTO SE MANEJA EN EL GUARDAR TRANSACCIÓN
                Session::put('msg_level', 'info');
                Session::put('msg', __('general.pending_purchase'));
                Session::save();
                break;
        }
    }
    private function processSessionPayment($x_cod_response, $eventId, $clientId, $sessionClientId, $startDate, $endDate ){
        switch ((int)$x_cod_response) {
            case 1:
                # code transacción aceptada
                (new SesionClienteController())->save($eventId, $clientId, $sessionClientId, $startDate, $endDate);
                Session::put('msg_level', 'success');
                Session::put('msg', __('general.success_purchase'));
                Session::save();
                break;
            case 2:# code transacción rechazada
            case 4:# code transacción fallida
                Session::put('msg_level', 'danger');
                Session::put('msg', __('general.failed_purchase'));
                Session::save();
                if($sessionClientId != "null") {//La sesion fue creada con reserva de kangoo, así que se debe eliminar
                    $sesionCLiente = SesionCliente::find($sessionClientId);
                    $sesionCLiente->delete();
                }
                break;
            case 3:# code transacción pendiente //ESTO SE MANEJA EN EL GUARDAR TRANSACCIÓN
                Session::put('msg_level', 'info');
                Session::put('msg', __('general.pending_purchase'));
                Session::save();
                break;
        }
    }
    private function guardarRespuestaTx($ref_payco, $cod_response, $response_reason_text, $amount, $data, $user_id): int
    {
        $id = TransaccionesPagos::updateOrCreate(
            ['ref_payco' => $ref_payco], // Condición para buscar el registro
            [   // Datos a crear o actualizar
                'payment_method_id' => 1,
                'codigo_respuesta' => $cod_response,
                'respuesta' => $response_reason_text,
                'amount' => $amount,
                'data' => json_encode($data),
                'user_id' => $user_id,
                'category_id' => Category::where('name', CategoriesEnum::PLANES)->first()->id,
            ]
        )->id;
        return $id;
    }

    public function savePettyCash(Request $request){

        $payDay = Carbon::createFromFormat('d/m/Y',$request->payDay);
        $paymentMethod = PaymentMethod::find($request->paymentMethodId);
        $transaction = new TransaccionesPagos();
        $transaction->payment_method_id = $request->paymentMethodId;
        $transaction->ref_payco = "1";
        $transaction->codigo_respuesta = "1";
        $transaction->respuesta = strcasecmp( $paymentMethod->name, PaymentMethodsEnum::ACCOUNT_PAYABLE->value) == 0 ? $request->person : "Aprobado";
        $transaction->data = $request->data ?? '';
        $transaction->user_id = $request->clientId ?? null;
        $transaction->created_at = $payDay;
        $transaction->amount = $request->transactionType == 1 ? $request->amount : -1*abs($request->amount);//transaction_type == 1 is an income, 0 is a expense
        $transaction->is_petty_cash = 1;
        if($request->categoryId != 0){
            $transaction->category_id =  $request->categoryId;
        }
        $transaction->save();

        Session::put('msg_level', 'success');
        Session::put('msg', __('general.success_save_petty_cash'));
        Session::save();
        return redirect()->back();
    }

    public function paymentIntegritySignature(float $amount, string $currency, string $planId, string $expirationTime = null){
        $userId = auth()->id();
        $prefix = "GP";
        $timestamp = now()->timestamp;
        $payType = PayTypesEnum::Plan->value;
        $reference = "{$prefix}-{$userId}-{$payType}-{$planId}-{$timestamp}";
        $expirationTime = $expirationTime ?? '';
        $integrity =env('INTEGRITY_SIGNATURE');
        $signature = hash('sha256', "{$reference}{$amount}{$currency}{$expirationTime}{$integrity}");
        return [
            'reference' => $reference,
            'signature' => $signature,
            'currency' => $currency,
        ];
    }

    public function paymentSubscription(Request $request){
        list($acceptanceToken, $personalDataAuth) = $this->getAcceptanceTokens();
        $id = $this->createPaymentSource($request->token, $acceptanceToken, $personalDataAuth);
        Subscriptions::create([
            'user_id' => auth()->user()->id,
            'payment_source_id' => $id,
            'plan_id' => $request->planId,
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);
        $this->makePayment($id, $request->amount, $request->currency, $request->planId);
    }

    private function getAcceptanceTokens()
    {
        $url = env('WOMPI_URL').'v1/merchants/'.env('WOMPI_PUBLIC_KEY');
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $acceptanceToken = $data['data']['presigned_acceptance']['acceptance_token'];
            $personalDataAuthToken = $data['data']['presigned_personal_data_auth']['acceptance_token'];
            return [$acceptanceToken, $personalDataAuthToken];

        } else {
            return [
                'error' => $response->status(),
                'message' => $response->body(),
            ];
        }
    }

    private function createPaymentSource(string $token, string $acceptanceToken, string $personalDataAuth)
    {
        $url = env('WOMPI_URL', 'https://sandbox.wompi.co/').'v1/payment_sources';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WOMPI_PRIVATE_KEY'),
        ])->post($url, [
            'customer_email' => auth()->user()->email,
            'type' => 'CARD',
            'token' => $token,
            'acceptance_token' => $acceptanceToken,
            'accept_personal_auth' => $personalDataAuth
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $id = $data['data']['id'];
            return $id;
        } else {
            return [
                'error' => $response->status(),
                'message' => $response->body(),
            ];
        }
    }

    private function makePayment(string $id, float $amount, string $currency, string $planId)
    {
        $signature = $this->paymentIntegritySignature($amount, $currency, $planId);
        $url = env('WOMPI_URL', 'https://sandbox.wompi.co/').'v1/transactions';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WOMPI_PRIVATE_KEY'),
        ])->post($url, [
            'amount_in_cents' => $amount,
            'currency' => $currency,
            'signature' => $signature['signature'],
            'customer_email' => auth()->user()->email,//TODO get email from user when second automatic pay
            'payment_method' => [
                "installments" => 12
            ],
            'reference' => $signature['reference'],
            'payment_source_id' => $id
        ]);

        if ($response->successful()) {
            $data = $response->json();
            switch($data['data']['status']){
                case PayStatusEnum::APPROVED->value:
                    //When successful the event URL webbhook will be used to load the plan
                    Session::put('msg_level', 'success');
                    Session::put('msg', __('general.success_purchase'));
                    Session::save();
                    break;
                case PayStatusEnum::PENDING->value:
                    Session::put('msg_level', 'info');
                    Session::put('msg', __('general.pending_purchase'));
                    Session::save();
                    break;
                case PayStatusEnum::ERROR->value:
                case PayStatusEnum::DECLINED->value:
                    Session::put('msg_level', 'danger');
                    Session::put('msg', __('general.failed_purchase'));
                    Session::save();
                    break;
                default:
                    Session::put('msg_level', 'danger');
                    Session::put('msg', __('general.error_purchase'));
                    Session::save();
                    break;
            }
        } else {
            return [
                'error' => $response->status(),
                'message' => $response->body(),
            ];
        }

    }
}
