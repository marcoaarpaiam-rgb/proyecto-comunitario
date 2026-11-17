<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PeriodoAcademico;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Configuracion', [
            'coordinador'   => auth()->user(),
            'periodoActivo' => PeriodoAcademico::periodoActivo(),
            'periodos'      => PeriodoAcademico::orderBy('id_periodo', 'desc')->get(),
            'lideres'       => \App\Models\Usuario::whereHas('usuarioRoles', fn($q) =>
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
        if (!$usuario->hasRol('lider')) {
            return back()->withErrors(['usu_id' => 'El usuario seleccionado no es un líder.']);
        }
        $usuario->update(['usu_password' => bcrypt($usuario->usu_cedula)]);
        return back()->with('success', "Contraseña de {$usuario->usu_primer_nombre} reseteada a su cédula: {$usuario->usu_cedula}");
    }

    public function guardarLapsoAcademico(Request $request)
    {
        $request->validate([
            'lapso_academico' => 'required|string|max:50',
            'tipo_semestre'   => 'required|in:Impar,Par',
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after_or_equal:fecha_inicio',
        ], [
            'lapso_academico.required' => 'El código o nombre del lapso académico es obligatorio (ej. 2026-I).',
            'tipo_semestre.required'   => 'Debe indicar si el lapso corresponde a Semestre Impar o Par.',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'       => 'La fecha de culminación es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de culminación no puede ser anterior a la de inicio.',
        ]);

        $lapsoNombre = trim($request->lapso_academico);

        DB::transaction(function () use ($request, $lapsoNombre) {
            // Desactivar lapsos anteriores
            PeriodoAcademico::where('activo', true)->update(['activo' => false]);

            // Buscar si ya existe para actualizarlo o crearlo
            $periodo = PeriodoAcademico::where('lapso_academico', $lapsoNombre)->first();
            if ($periodo) {
                $periodo->update([
                    'tipo_semestre' => $request->tipo_semestre,
                    'fecha_inicio'  => $request->fecha_inicio,
                    'fecha_fin'     => $request->fecha_fin,
                    'activo'        => true,
                ]);
            } else {
                PeriodoAcademico::create([
                    'lapso_academico' => $lapsoNombre,
                    'tipo_semestre'   => $request->tipo_semestre,
                    'fecha_inicio'    => $request->fecha_inicio,
                    'fecha_fin'       => $request->fecha_fin,
                    'activo'          => true,
                ]);
            }
        });

        return back()->with('success', "Lapso Académico activo establecido en: {$lapsoNombre}");
    }

    public function activarLapsoAcademico($id)
    {
        $periodo = PeriodoAcademico::findOrFail($id);

        DB::transaction(function () use ($periodo) {
            PeriodoAcademico::where('activo', true)->update(['activo' => false]);
            $periodo->update(['activo' => true]);
        });

        return back()->with('success', "Lapso Académico activo cambiado a: {$periodo->lapso_academico}");
    }
}