<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Usuario;
use App\Models\EquipoIntegrante;
use App\Models\UsuarioRol;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $esProfesor = $user->esProfesor();

        // Si es profesor solo ve sus secciones
        $seccionesIds = $esProfesor
            ? $user->seccionesProfesor()->where('pse_status', true)->pluck('pse_id_sec')
            : null;

        $equiposQuery = Equipo::with([
            'seccion.trayecto',
            'seccion.turno',
            'trayecto',
            'integrantes.usuario',
            'proyectoComunidad.comunidad',
        ])->where('equ_status', true)->orderBy('equ_codigo');

        if ($esProfesor && $seccionesIds) {
            $equiposQuery->whereIn('equ_id_sec', $seccionesIds);
        }

        $seccionesQuery = Seccion::with(['trayecto','turno'])->where('sec_status', true);
        if ($esProfesor && $seccionesIds) {
            $seccionesQuery->whereIn('sec_id', $seccionesIds);
        }

        return Inertia::render('Shared/Equipos', [
            'equipos'     => $equiposQuery->get(),
            'secciones'   => $seccionesQuery->get(),
            'trayectos'   => Trayecto::where('tra_status', true)->get(),
            'estudiantes' => Usuario::where('usu_status', true)
                ->where(function($q) {
                    $q->where('usu_tiene_acceso', false)
                    ->orWhereHas('usuarioRoles', fn($r) =>
                        $r->where('uro_status', true)
                            ->whereHas('rol', fn($r2) =>
                                $r2->where('rol_nombre', 'lider')
                            )
                    );
                })->get(['usu_id','usu_cedula','usu_primer_nombre',
                        'usu_primer_apellido','usu_tiene_acceso']),
            'puedeCrear' => true,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'equ_codigo'   => 'required|string|max:20|unique:equipo,equ_codigo',
            'equ_titulo'   => 'required|string|max:150',
            'equ_id_sec'   => 'required|exists:seccion,sec_id',
            'equ_id_tra'   => 'required|exists:trayecto,tra_id',
            'integrantes'  => 'required|array|min:1',
            'integrantes.*.usu_id'   => 'required|exists:usuario,usu_id',
            'integrantes.*.es_lider' => 'required|boolean',
            'lider_count'  => 'sometimes',
        ], [
            'equ_codigo.required'  => 'El código es obligatorio.',
            'equ_codigo.unique'    => 'Ya existe un equipo con ese código.',
            'equ_titulo.required'  => 'El título es obligatorio.',
            'equ_id_sec.required'  => 'La sección es obligatoria.',
            'equ_id_tra.required'  => 'El trayecto es obligatorio.',
            'integrantes.required' => 'Debe agregar al menos un integrante.',
        ]);

        // Validar que haya exactamente un líder
        $lideres = collect($request->integrantes)->where('es_lider', true)->count();
        if ($lideres !== 1) {
            return back()->withErrors(['integrantes' => 'Debe designar exactamente un líder.']);
        }

        $equipo = Equipo::create([
            'equ_codigo'         => $request->equ_codigo,
            'equ_titulo'         => $request->equ_titulo,
            'equ_id_sec'         => $request->equ_id_sec,
            'equ_id_tra'         => $request->equ_id_tra,
            'equ_status'         => true,
            'equ_id_usu_created' => auth()->id(),
        ]);

        foreach ($request->integrantes as $integrante) {
            EquipoIntegrante::create([
                'ein_id_equ'      => $equipo->equ_id,
                'ein_id_usu'      => $integrante['usu_id'],
                'ein_es_lider'    => $integrante['es_lider'],
                'ein_fecha_ingreso'=> now()->toDateString(),
                'ein_status'      => true,
                'ein_id_usu_created' => auth()->id(),
            ]);

            // Si es líder darle acceso al sistema
            if ($integrante['es_lider']) {
                $usuario = Usuario::find($integrante['usu_id']);
                if (!$usuario->usu_tiene_acceso) {
                    $rolLider = \App\Models\Rol::where('rol_nombre','lider')->first();
                    $usuario->update([
                        'usu_tiene_acceso' => true,
                        'usu_username'     => 'lider_' . strtolower($usuario->usu_cedula),
                        'usu_password'     => bcrypt($usuario->usu_cedula),
                    ]);
                    UsuarioRol::create([
                        'uro_id_usu'      => $usuario->usu_id,
                        'uro_id_rol'      => $rolLider->rol_id,
                        'uro_fecha_inicio' => now()->toDateString(),
                        'uro_status'      => true,
                        'uro_id_usu_created' => auth()->id(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Equipo creado correctamente. El líder puede ingresar con su cédula como usuario y contraseña.');
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);

        $request->validate([
            'equ_codigo' => "required|string|max:20|unique:equipo,equ_codigo,{$id},equ_id",
            'equ_titulo' => 'required|string|max:150',
            'equ_id_sec' => 'required|exists:seccion,sec_id',
            'equ_id_tra' => 'required|exists:trayecto,tra_id',
        ]);

        $equipo->update([
            'equ_codigo'         => $request->equ_codigo,
            'equ_titulo'         => $request->equ_titulo,
            'equ_id_sec'         => $request->equ_id_sec,
            'equ_id_tra'         => $request->equ_id_tra,
            'equ_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        $equipo->update([
            'equ_status'         => false,
            'equ_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Equipo desactivado.');
    }
}