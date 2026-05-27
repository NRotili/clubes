<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->rol, $roles)) {
            if ($request->expectsJson()) {
                abort(403);
            }

            // Socios autenticados: redirigir a su propio perfil
            if ($user && $user->rol === 'socio' && $user->socio_id) {
                return redirect()->route('socios.show', $user->socio_id)
                    ->with('error', 'No tenés permiso para acceder a esa sección.');
            }

            abort(403, 'No tenés permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
