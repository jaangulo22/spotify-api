<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // id 3 es del administrador en el seeder
        if ($request->user()->role_id !== 3) {
            return response()->json(['message' => 'Acceso denegado. Se requiere rol de administrador.'], 403);
        }

        return $next($request);
        
    }
}
