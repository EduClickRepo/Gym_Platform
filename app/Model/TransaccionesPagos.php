<?php

namespace App\Model;

use App\Category;
use App\PaymentMethod;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TransaccionesPagos extends Model
{
    protected $table = 'transacciones_pagos';
    use SoftDeletes;

    protected $fillable = [
        'ref_payco', 'payment_method_id', 'codigo_respuesta', 'respuesta', 'amount', 'data', 'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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