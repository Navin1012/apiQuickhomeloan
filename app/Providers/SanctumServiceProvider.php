<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class SanctumServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);

        config([
            'sanctum.database_connection' => 'mongodb'
        ]);
    }

    
}
