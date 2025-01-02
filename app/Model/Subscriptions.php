<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id', 'payment_source_id', 'plan_id', 'amount', 'currency'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}