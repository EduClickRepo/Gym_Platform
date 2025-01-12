<?php

namespace App\Http\Services;


use App\Utils\PayTypesEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ProcessPaymentWompi implements ProcessPaymentInterface
{

    public function processPayment($data)
    {
        // TODO: Implement processPayment() method.
    }

    public function getPaymentInfo(String $reference)
    {
        $parts = explode('-', $reference);
        if (count($parts) < 5) {
            throw new \InvalidArgumentException("La referencia no tiene el formato esperado.");
        }

        return [
            'userId' => $parts[1],
            'type' => $parts[2],
            'paidObjectId' => $parts[3]
        ];
    }

    public function makePayment(string $userId, string $payment_source_id, float $amount, string $currency, string $itemId, string $email, int $installments = 12): Response
    {
        $amount = $amount*100;//multiplicado por 100 por los centavos
        $signature = $this->paymentIntegritySignature($userId, $amount, $currency, $itemId);
        $url = env('WOMPI_URL').'v1/transactions';

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . env('WOMPI_PRIVATE_KEY'),
        ])->post($url, [
            'amount_in_cents' => $amount,
            'currency' => $currency,
            'signature' => $signature['signature'],
            'customer_email' => $email,
            'payment_method' => [
                "installments" => $installments
            ],
            'recurrent'=> true,
            'reference' => $signature['reference'],
            'payment_source_id' => $payment_source_id
        ]);
    }

    private function paymentIntegritySignature($userId, float $amount, string $currency, string $planId, string $expirationTime = null){
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
}