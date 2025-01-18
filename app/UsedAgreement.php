<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsedAgreement extends Model
{
    use HasFactory;
    protected $table = 'used_agreements';

    protected $fillable = [
        'user_id','agreement_id'
    ];
}
