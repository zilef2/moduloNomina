<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdjustSessionLifetime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
     public function handle($request, Closure $next)
    {
        if (app()->environment('test')) {
            config(['session.lifetime' => (4 * 24 * 60)]); // 4 dias para pruebas
        } else {
            config(['session.lifetime' =>  2 * 60]); // 2 horas para producción
        }

        return $next($request);
    }
}
