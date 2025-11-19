<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles Los roles permitidos, pasados como argumentos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si el usuario no está autenticado, Laravel Sanctum lo maneja con 401.
        // Aquí solo verificamos el perfil (rol) si está autenticado.
        if (!$request->user()) {
            // Esto realmente no debería ejecutarse si está detrás de auth:sanctum
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        // Obtener el perfil del usuario autenticado
        $userProfile = $request->user()->perfil;

        // Comprobar si el perfil del usuario está en la lista de perfiles permitidos ($roles)
        if (!in_array($userProfile, $roles)) {
            // El usuario no tiene el perfil requerido
            return response()->json([
                'message' => 'Acceso Denegado. Se requiere uno de los siguientes perfiles: ' . implode(', ', $roles)
            ], 403);
        }

        // El perfil es correcto, permitir que la solicitud continúe
        return $next($request);
    }
}
