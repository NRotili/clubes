<?php

namespace App\Providers;

use App\Models\ClubConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        try {
            View::share('club', ClubConfig::todos());
        } catch (\Throwable) {
            View::share('club', [
                'nombre'    => null,
                'logo_url'  => null,
                'direccion' => '',
                'telefono'  => '',
                'email'     => '',
                'web'       => '',
            ]);
        }

        RateLimiter::for('api-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email', '') . '|' . $request->ip())
                ->response(fn() => response()->json(
                    ['message' => 'Demasiados intentos. Esperá un minuto e intentá de nuevo.'], 429
                ));
        });

        RateLimiter::for('api-register', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(fn() => response()->json(
                    ['message' => 'Demasiadas solicitudes. Esperá un minuto e intentá de nuevo.'], 429
                ));
        });
    }
}
