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
            'usu_username.required' => 'El usuario es obligatorio.',
            'usu_password.required' => 'La contraseña es obligatoria.',
        ]);

        $credentials = [
            'usu_username' => $request->usu_username,
            'password'     => $request->usu_password,
        ];

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'usu_username' => 'Usuario o contraseña incorrectos.',
            ]);
        }

        $usuario = Auth::user();

        if (!$usuario->usu_tiene_acceso) {
            Auth::logout();
            return back()->withErrors([
                'usu_username' => 'No tienes permiso para acceder al sistema.',
            ]);
        }

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