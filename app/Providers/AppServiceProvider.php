<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS scheme whenever the request arrives over HTTPS.
        // Works with any reverse proxy (ngrok, Railway, Cloudflare, VPS nginx)
        // because trustProxies in bootstrap/app.php makes isSecure() reliable.
        // On plain HTTP (localhost dev) this condition is false — no side-effects.
        if (request()->isSecure()) {
            URL::forceScheme('https');
        }
    }
}