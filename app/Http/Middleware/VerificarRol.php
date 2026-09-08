<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $rolUsuario = $request->user()
            ->usuarioRoles()
            ->where('uro_status', true)
            ->with('rol')
            ->first()
            ?->rol
            ?->rol_nombre;

        if (!in_array($rolUsuario, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}