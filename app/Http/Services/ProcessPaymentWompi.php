<?php

namespace App\Http\Services;


use Illuminate\Http\Request;;

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
}