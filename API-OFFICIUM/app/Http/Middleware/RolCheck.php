<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class RolCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // Convertimos los roles separados por comas en un array
        $rolesArray = explode('|', $roles);

        Log::info("Roles: " . implode('|', $rolesArray));
        Log::info("Roles: " .$roles);
        Log::info("User rol: ".Auth::user()->rol);

        if(!Auth::check() || !in_array(Auth::user()->rol, $rolesArray)){
            return response()->json([
                'StatusCode' => 403,
                'ReasonPhrase' => 'Acceso denegado.',
                'Message' => 'No tienes el rol necesario para acceder a este recurso.',
            ], 403);
        }
        return $next($request);
    }
}
