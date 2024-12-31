<?php

namespace App\Model;

use App\Category;
use App\PaymentMethod;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Subscriptions extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id', 'payment_source_id', 'plan_id', 'amount', 'currency',
    ];

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