<?php

namespace App\Http\Services;


use Illuminate\Http\Client\Response;

interface ProcessPaymentInterface
{
    public function processPayment($data);

    public function getPaymentInfo(String $request);

    public function makePayment(string $userId, string $payment_source_id, float $amount, string $currency, string $itemId, string $email, int $installments = 12): Response;
}