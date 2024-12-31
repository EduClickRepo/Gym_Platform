<?php

namespace App\Http\Services;


use Illuminate\Http\Request;

interface ProcessPaymentInterface
{
    public function processPayment($data);

    public function getPaymentInfo(String $request);
}