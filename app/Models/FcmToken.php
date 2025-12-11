<?php

namespace App\Models;

use  MongoDB\Laravel\Eloquent\Model as Eloquent;

class FcmToken extends Eloquent
{
    protected $connection = 'mongodb';      
    protected $collection = 'fcm_tokens';   

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'last_used_at',
    ];
}
