<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TyCUser extends Model
{
    use HasFactory;

    protected $table = 'tyc_user';

    protected $fillable = ['user_id', 'tyc_id'];

    public $timestamps = false;
}
