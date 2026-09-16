<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Configuracion', [
            'coordinador' => auth()->user(),
            'lideres' => \App\Models\Usuario::whereHas('usuarioRoles', fn($q) =>
                $q->where('uro_status', true)
                ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'lider'))
            )->get(['usu_id','usu_cedula','usu_primer_nombre','usu_primer_apellido']),
        ]);
    }

    public function actualizarFirma(Request $request)
    {
        $request->validate([
            'firma' => 'required|image|max:2048',
        ], [
            'firma.required' => 'Debes seleccionar una imagen.',
            'firma.image'    => 'El archivo debe ser una imagen.',
            'firma.max'      => 'La imagen no puede superar 2MB.',
        ]);

        $ruta = $request->file('firma')->store('firmas', 'public');
        auth()->user()->update(['usu_firma_digital' => $ruta]);

        return back()->with('success', 'Firma actualizada correctamente.');
    }

    public function actualizarPerfil(Request $request)
    {
        $request->validate([
            'usu_primer_nombre'    => 'required|string|max:50',
            'usu_segundo_nombre'   => 'nullable|string|max:50',
            'usu_primer_apellido'  => 'required|string|max:50',
            'usu_segundo_apellido' => 'nullable|string|max:50',
            'usu_email'            => 'nullable|email|max:150',
            'usu_telefono'         => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only([
            'usu_primer_nombre', 'usu_segundo_nombre',
            'usu_primer_apellido', 'usu_segundo_apellido',
            'usu_email', 'usu_telefono',
        ]));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nuevo'  => 'required|min:8|confirmed',
        ], [
            'password_actual.required' => 'La contraseña actual es obligatoria.',
            'password_nuevo.required'  => 'La nueva contraseña es obligatoria.',
            'password_nuevo.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password_nuevo.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if (!\Hash::check($request->password_actual, auth()->user()->usu_password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta.']);
        }

        auth()->user()->update(['usu_password' => bcrypt($request->password_nuevo)]);
        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function resetPasswordLider(Request $request)
    {
        $request->validate(['usu_id' => 'required|exists:usuario,usu_id']);
        $usuario = \App\Models\Usuario::findOrFail($request->usu_id);
        $usuario->update(['usu_password' => bcrypt($usuario->usu_cedula)]);
        return back()->with('success', "Contraseña de {$usuario->usu_primer_nombre} reseteada a su cédula: {$usuario->usu_cedula}");
    }
}