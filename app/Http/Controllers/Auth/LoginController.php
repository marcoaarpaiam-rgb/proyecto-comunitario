<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usu_username' => 'required|string',
            'usu_password' => 'required|string',
        ], [
            'usu_username.required' => 'La cédula o usuario es obligatorio.',
            'usu_password.required' => 'La contraseña es obligatoria.',
        ]);

        $loginInput = trim($request->usu_username);

        // Buscar por usu_username o usu_cedula
        $usuario = Usuario::where('usu_username', $loginInput)
            ->orWhere('usu_cedula', $loginInput)
            ->first();

        if (!$usuario || !\Illuminate\Support\Facades\Hash::check($request->usu_password, $usuario->usu_password)) {
            return back()->withErrors([
                'usu_username' => 'Cédula o contraseña incorrectas. Intenta de nuevo.',
            ]);
        }

        if (!$usuario->usu_tiene_acceso) {
            return back()->withErrors([
                'usu_username' => 'No tienes permiso para acceder al sistema.',
            ]);
        }

        Auth::login($usuario);

        $request->session()->regenerate();

        // Redirigir según el rol
        if ($usuario->esCoordinador()) {
            return redirect()->route('coordinador.dashboard');
        }

        if ($usuario->esProfesor()) {
            return redirect()->route('profesor.dashboard');
        }

        if ($usuario->esLider()) {
            return redirect()->route('lider.dashboard');
        }

        Auth::logout();
        return back()->withErrors(['usu_username' => 'Rol no reconocido.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}