<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PersonalAccessToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';
    protected $guarded = [];

    // FIX: add this method
    public static function findToken($token)
    {
        return static::where('token', hash('sha256', $token))->first();
    }

    public function tokenable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
