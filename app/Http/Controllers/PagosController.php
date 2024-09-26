<?php

namespace App\Http\Controllers;

use App\Model\SesionCliente;
use App\Model\TransaccionesPagos;
use App\Model\TransaccionesPendientes;
use App\PaymentMethod;
use App\Utils\PayTypesEnum;
use App\Utils\PaymentMethodsEnum;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PagosController extends Controller
{
    public function responsePayment()
    {
        $client = new Client();
        $response = $client->get('https://secure.epayco.co/validation/v1/reference/' . request('ref_payco'));
        $data = json_decode($response->getBody()->getContents())->data;//Transforma a array porque en el response tambien se usa array y ambos usan el mismo método de procesar el pago;
        if (!$this->verificarProcesamiento($data)) {//si no ha sido procesado el pago
            $this->procesarPago($data);
        }
        return redirect()->route('home', ['user' => Auth::user()]);
    }

    private function verificarProcesamiento($data)
    {
        $transaccion = TransaccionesPagos::where('ref_payco', $data->x_ref_payco);
        return $transaccion->exists();
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
    private function guardarRespuestaTx($data): int
    {
        $id = TransaccionesPagos::create([
            'ref_payco' => $data->x_ref_payco,
            'payment_method_id' => 1,
            'codigo_respuesta' => $data->x_cod_response,
            'respuesta' => $data->x_response_reason_text,
            'amount' => $data->x_amount,
            'data' => json_encode($data),
            'user_id' => $data->x_extra2,
        ])->id;
        // Cuando está pendiente
        if ($data->x_cod_response == 3){
            TransaccionesPendientes::create([
                'id_transaccion' => $id,
                'verificada' => false,
            ]);
        }
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
        $transaction->data = $request->data;
        $transaction->user_id = Auth::id();
        $transaction->created_at = $payDay;
        $transaction->amount = $request->transactionType == 1 ? $request->amount : -1*abs($request->amount);//transaction_type == 1 is an income, 0 is a expense
        $transaction->is_petty_cash = 1;
        $transaction->save();

        Session::put('msg_level', 'success');
        Session::put('msg', __('general.success_save_petty_cash'));
        Session::save();
        return redirect()->back();
    }
}
