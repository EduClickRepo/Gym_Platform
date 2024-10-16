<?php

namespace App\Model;

use App\Category;
use App\PaymentMethod;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaccionesPagos extends Model
{
    protected $table = 'transacciones_pagos';
    use SoftDeletes;

    protected $fillable = [
        'ref_payco', 'payment_method_id', 'codigo_respuesta', 'respuesta', 'amount', 'data', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentMethod::Class, 'payment_method_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::Class, 'category_id');
    }
}