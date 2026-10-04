<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Usuario;
use App\Models\EquipoIntegrante;
use App\Models\UsuarioRol;
use App\Models\EstudianteSeccion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipoController extends Controller
{
    public function index()
    {
        $user       = auth()->user();
        $esProfesor = $user->esProfesor();

        $seccionesIds = $esProfesor
            ? $user->seccionesProfesor()
                   ->where('pse_status', true)
                   ->pluck('pse_id_sec')
            : null;

        // ── Equipos ──────────────────────────────────────────────────
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

        // ── Secciones ────────────────────────────────────────────────
        $seccionesQuery = Seccion::with(['trayecto.pnf', 'turno'])
            ->where('sec_status', true);

        if ($esProfesor && $seccionesIds) {
            $seccionesQuery->whereIn('sec_id', $seccionesIds);
        }

        // ── Estudiantes filtrados por sección del profesor ────────────
        // Coordinador: ve todos los disponibles
        // Profesor: ve solo los de sus secciones que no están en equipo activo
        if ($esProfesor && $seccionesIds && $seccionesIds->isNotEmpty()) {
            $estudiantesIds = EstudianteSeccion::whereIn('ese_id_sec', $seccionesIds)
                ->where('ese_status', true)
                ->pluck('ese_id_usu');

            $estudiantes = Usuario::whereIn('usu_id', $estudiantesIds)
                ->where('usu_status', true)
                ->whereDoesntHave('equiposIntegrante', fn($q) =>
                    $q->where('ein_status', true)
                      ->whereHas('equipo', fn($q2) =>
                          $q2->where('equ_status', true)
                      )
                )
                ->get(['usu_id','usu_cedula','usu_primer_nombre',
                       'usu_primer_apellido','usu_tiene_acceso']);
        } else {
            // Coordinador: todos los disponibles sin equipo activo
            $estudiantes = Usuario::where('usu_status', true)
                ->where(function ($q) {
                    $q->where('usu_tiene_acceso', false)
                      ->orWhereHas('usuarioRoles', fn($r) =>
                          $r->where('uro_status', true)
                            ->whereHas('rol', fn($r2) =>
                                $r2->where('rol_nombre', 'lider')
                            )
                      );
                })
                ->whereDoesntHave('equiposIntegrante', fn($q) =>
                    $q->where('ein_status', true)
                      ->whereHas('equipo', fn($q2) =>
                          $q2->where('equ_status', true)
                      )
                )
                ->get(['usu_id','usu_cedula','usu_primer_nombre',
                       'usu_primer_apellido','usu_tiene_acceso']);
        }

        return Inertia::render('Shared/Equipos', [
            'equipos'          => $equiposQuery->get(),
            'secciones'        => $seccionesQuery->get(),
            'trayectos'        => Trayecto::where('tra_status', true)->get(),
            'equiposDisueltos' => Equipo::with([
                                    'seccion','trayecto','integrantes.usuario'
                                  ])->where('equ_status', false)
                                    ->orderBy('equ_codigo')->get(),
            'estudiantes'      => $estudiantes,
            'comunidades'      => \App\Models\Comunidad::where('com_status', true)
                                    ->orderBy('com_nombre')->get(),
            'tiposProyecto'    => \App\Models\TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'      => \App\Models\ModalidadProyecto::where('mpr_status', true)->get(),
            'tipoBeneficiario' => \App\Models\TipoBeneficiario::where('tbe_status', true)->get(),
            'puedeCrear'       => true,
        ]);
    }

    // ── El resto de métodos se mantienen igual ────────────────────────
    // store(), update(), destroy(), disolver(), removerIntegrante(),
    // cambiarLider(), historial(), reactivar() — no cambia nada en ellos

    public function store(Request $request)
    {
        $request->validate([
            'equ_id_sec'            => 'required|exists:seccion,sec_id',
            'equ_numero_grupo'      => 'required|integer|min:1',
            'equ_titulo'            => 'required|string|max:150',
            'integrantes'           => 'required|array|min:1|max:6',
            'integrantes.*.usu_id'  => 'required|exists:usuario,usu_id',
            'integrantes.*.es_lider'=> 'required|boolean',
        ], [
            'equ_id_sec.required'      => 'La sección es obligatoria.',
            'equ_numero_grupo.required'=> 'El número de grupo es obligatorio.',
            'equ_titulo.required'      => 'El título es obligatorio.',
            'integrantes.required'     => 'Debe agregar al menos un integrante.',
            'integrantes.max'          => 'El equipo no puede tener más de 6 integrantes.',
        ]);

        $lideres = collect($request->integrantes)->where('es_lider', true)->count();
        if ($lideres !== 1) {
            return back()->withErrors(['integrantes' => 'Debe designar exactamente un líder.']);
        }

        $seccion  = Seccion::with('trayecto')->findOrFail($request->equ_id_sec);
        $trayecto = $seccion->trayecto;

        $pnfSiglas = strtoupper($trayecto->pnf?->pnf_siglas ?? 'PNFI');
        $codigo    = "{$pnfSiglas}-{$seccion->sec_codigo}-G{$request->equ_numero_grupo}-T{$trayecto->tra_numero}";

        if (Equipo::where('equ_codigo', $codigo)->exists()) {
            return back()->withErrors([
                'equ_numero_grupo' => "Ya existe un equipo con el código {$codigo} en esta sección.",
            ]);
        }

        foreach ($request->integrantes as $integrante) {
            $yaEnEquipo = EquipoIntegrante::where('ein_id_usu', $integrante['usu_id'])
                ->where('ein_status', true)
                ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
                ->exists();

            if ($yaEnEquipo) {
                $usuario = Usuario::find($integrante['usu_id']);
                $nombre  = $usuario->usu_primer_nombre . ' ' . $usuario->usu_primer_apellido;
                return back()->withErrors([
                    'integrantes' => "El estudiante {$nombre} ya pertenece a otro equipo activo.",
                ]);
            }
        }

        $equipo = Equipo::create([
            'equ_codigo'         => $codigo,
            'equ_titulo'         => $request->equ_titulo,
            'equ_id_sec'         => $request->equ_id_sec,
            'equ_id_tra'         => $trayecto->tra_id,
            'equ_status'         => true,
            'equ_id_usu_created' => auth()->id(),
        ]);

        foreach ($request->integrantes as $integrante) {
            EquipoIntegrante::create([
                'ein_id_equ'         => $equipo->equ_id,
                'ein_id_usu'         => $integrante['usu_id'],
                'ein_es_lider'       => $integrante['es_lider'],
                'ein_fecha_ingreso'  => now()->toDateString(),
                'ein_status'         => true,
                'ein_id_usu_created' => auth()->id(),
            ]);

            // Busca el integrante designado como líder
            if ($integrante['es_lider']) {
                $usuario = \App\Models\Usuario::find($integrante['usu_id']);
                if ($usuario && !$usuario->usu_tiene_acceso) {
                    $usuario->update([
                        'usu_tiene_acceso' => true,
                        'usu_username'     => $usuario->usu_cedula,
                        'usu_password'     => bcrypt($usuario->usu_cedula),
                    ]);
                    // Asignar rol lider en usuario_rol
                    $rolLider = \App\Models\Rol::where('rol_nombre', 'lider')->first();
                    if ($rolLider) {
                        \App\Models\UsuarioRol::updateOrCreate(
                            ['uro_id_usu' => $usuario->usu_id, 'uro_id_rol' => $rolLider->rol_id],
                            ['uro_fecha_inicio' => now()->toDateString(), 'uro_status' => true, 'uro_id_usu_created' => auth()->id()]
                        );
                    }
                }
            }
        }

        if ($request->has('proyecto') && $request->proyecto) {
            $proy = $request->proyecto;
            if (!empty($proy['pco_id_com']) && !empty($proy['pco_id_tpr'])) {
                $proyecto = \App\Models\ProyectoComunidad::create([
                    'pco_id_equ'         => $equipo->equ_id,
                    'pco_id_com'         => $proy['pco_id_com'],
                    'pco_id_tpr'         => $proy['pco_id_tpr'],
                    'pco_id_mpr'         => $proy['pco_id_mpr'] ?? null,
                    'pco_resumen'        => $proy['pco_resumen'] ?? '',
                    'pco_fecha_limite'   => $proy['pco_fecha_limite'] ?? null,
                    'pco_status'         => true,
                    'pco_id_usu_created' => auth()->id(),
                ]);

                foreach ($proy['beneficiarios'] ?? [] as $ben) {
                    if (!empty($ben['tbe_id']) && !empty($ben['cantidad'])) {
                        \App\Models\ProyectoBeneficiario::create([
                            'pbe_id_pco'         => $proyecto->pco_id,
                            'pbe_id_tbe'         => $ben['tbe_id'],
                            'pbe_cantidad'        => $ben['cantidad'],
                            'pbe_id_usu_created' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        return back()->with('success', "Equipo {$codigo} creado correctamente. El líder puede ingresar con su cédula.");
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);
        $request->validate([
            'equ_titulo' => 'required|string|max:150',
        ], [
            'equ_titulo.required' => 'El título es obligatorio.',
        ]);
        $equipo->update([
            'equ_titulo'         => $request->equ_titulo,
            'equ_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        $equipo->update(['equ_status' => false, 'equ_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Equipo desactivado.');
    }

    public function disolver(Request $request, $id)
    {
        $request->validate(['motivo' => 'required|string'], [
            'motivo.required' => 'El motivo de la disolución es obligatorio.',
        ]);
        $equipo = Equipo::findOrFail($id);
        \App\Models\HistorialEquipo::create([
            'heq_id_equ'         => $equipo->equ_id,
            'heq_id_tee'         => \App\Models\TipoEventoEquipo::where('tee_nombre','Disuelto')->first()?->tee_id,
            'heq_motivo'         => $request->motivo,
            'heq_fecha_evento'   => now()->toDateString(),
            'heq_id_usu_created' => auth()->id(),
        ]);
        $equipo->integrantes()->where('ein_status', true)->update([
            'ein_status'         => false,
            'ein_fecha_salida'   => now()->toDateString(),
            'ein_id_usu_updated' => auth()->id(),
        ]);
        $equipo->update(['equ_status' => false, 'equ_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Equipo disuelto correctamente.');
    }

    public function removerIntegrante(Request $request, $equipoId, $integranteId)
    {
        $integrante = EquipoIntegrante::where('ein_id', $integranteId)
            ->where('ein_id_equ', $equipoId)
            ->where('ein_status', true)
            ->firstOrFail();
        if ($integrante->ein_es_lider) {
            return back()->withErrors(['integrante' => 'No puedes remover al líder. Primero asigna un nuevo líder.']);
        }
        $integrante->update([
            'ein_status'         => false,
            'ein_fecha_salida'   => now()->toDateString(),
            'ein_id_usu_updated' => auth()->id(),
        ]);
        \App\Models\HistorialEquipo::create([
            'heq_id_equ'         => $equipoId,
            'heq_id_tee'         => \App\Models\TipoEventoEquipo::where('tee_nombre','Integrante Removido')->first()?->tee_id,
            'heq_motivo'         => 'Integrante '.$integrante->usuario?->usu_primer_nombre.' '.$integrante->usuario?->usu_primer_apellido.' removido del equipo.',
            'heq_fecha_evento'   => now()->toDateString(),
            'heq_id_usu_created' => auth()->id(),
        ]);
        return back()->with('success', 'Integrante removido correctamente.');
    }

    public function cambiarLider(Request $request, $equipoId)
    {
        $request->validate(['nuevo_lider_id' => 'required|exists:equipo_integrante,ein_id'], [
            'nuevo_lider_id.required' => 'Debes seleccionar el nuevo líder.',
        ]);
        $equipo      = Equipo::findOrFail($equipoId);
        $liderActual = EquipoIntegrante::where('ein_id_equ', $equipoId)
            ->where('ein_es_lider', true)->where('ein_status', true)->first();
        if ($liderActual) {
            $liderActual->update(['ein_es_lider' => false, 'ein_id_usu_updated' => auth()->id()]);
            $usuarioAnterior = Usuario::find($liderActual->ein_id_usu);
            $usuarioAnterior?->update(['usu_tiene_acceso' => false]);
            UsuarioRol::where('uro_id_usu', $liderActual->ein_id_usu)
                ->where('uro_status', true)
                ->whereHas('rol', fn($q) => $q->where('rol_nombre', 'lider'))
                ->update(['uro_status' => false]);
        }
        $nuevoIntegrante = EquipoIntegrante::findOrFail($request->nuevo_lider_id);
        $nuevoIntegrante->update(['ein_es_lider' => true, 'ein_id_usu_updated' => auth()->id()]);
        $nuevoLider = Usuario::find($nuevoIntegrante->ein_id_usu);
        if ($nuevoLider && !$nuevoLider->usu_tiene_acceso) {
            $rolLider = \App\Models\Rol::where('rol_nombre', 'lider')->first();
            $nuevoLider->update([
                'usu_tiene_acceso' => true,
                'usu_username'     => $nuevoLider->usu_cedula,
                'usu_password'     => bcrypt($nuevoLider->usu_cedula),
            ]);
            UsuarioRol::create([
                'uro_id_usu'         => $nuevoLider->usu_id,
                'uro_id_rol'         => $rolLider->rol_id,
                'uro_fecha_inicio'   => now()->toDateString(),
                'uro_status'         => true,
                'uro_id_usu_created' => auth()->id(),
            ]);
        }
        \App\Models\HistorialEquipo::create([
            'heq_id_equ'         => $equipoId,
            'heq_id_tee'         => \App\Models\TipoEventoEquipo::where('tee_nombre','Líder Cambiado')->first()?->tee_id,
            'heq_motivo'         => 'Nuevo líder: '.$nuevoLider?->usu_primer_nombre.' '.$nuevoLider?->usu_primer_apellido,
            'heq_fecha_evento'   => now()->toDateString(),
            'heq_id_usu_created' => auth()->id(),
        ]);
        return back()->with('success', 'Líder cambiado correctamente. El nuevo líder puede ingresar con su cédula.');
    }

    public function historial($equipoId)
    {
        $equipo = Equipo::with([
            'historial.tipoEvento',
            'historial' => fn($q) => $q->orderBy('heq_fecha_evento', 'desc'),
        ])->findOrFail($equipoId);
        return response()->json($equipo->historial);
    }

    public function reactivar(Request $request, $id)
    {
        $equipo = Equipo::where('equ_status', false)->findOrFail($id);
        $equipo->update(['equ_status' => true]);
        $equipo->integrantes()->where('ein_status', false)
            ->whereDoesntHave('historialRemociones')
            ->update(['ein_status' => true]);
        \App\Models\HistorialEquipo::create([
            'heq_id_equ'          => $equipo->equ_id,
            'heq_id_tee'          => \App\Models\TipoEventoEquipo::where('tee_nombre','Reactivado')->value('tee_id'),
            'heq_motivo'          => $request->input('motivo', 'Reactivación del equipo por el coordinador.'),
            'heq_fecha_evento'    => now(),
            'heq_id_usu_registro' => auth()->id(),
        ]);
        return back()->with('success', "Equipo {$equipo->equ_codigo} reactivado correctamente.");
    }
    
    // Vista de secciones del profesor
    public function misSecciones()
    {
        $profesor = auth()->user();
        $secciones = \App\Models\Seccion::whereHas('profesores', fn($q) =>
            $q->where('pse_id_usu', $profesor->usu_id)
            ->where('pse_status', true)
        )->with(['trayecto.pnf', 'turno'])->get();

        return \Inertia\Inertia::render('Profesor/MisSecciones', [
            'secciones' => $secciones->map(fn($s) => [
                'sec_id'        => $s->sec_id,
                'sec_codigo'    => $s->sec_codigo,
                'trayecto'      => $s->trayecto?->tra_nombre,
                'pnf'           => $s->trayecto?->pnf?->pnf_siglas,
                'turno'         => $s->turno?->tur_nombre,
                'total_equipos' => \App\Models\Equipo::where('equ_id_sec', $s->sec_id)
                                    ->where('equ_status', true)->count(),
                'total_estudiantes' => \App\Models\EstudianteSeccion::where('ese_id_sec', $s->sec_id)
                                        ->where('ese_status', true)->count(),
            ]),
        ]);
    }

    // Vista de equipos de una sección específica (para el profesor)
    public function seccionEquipos($id)
    {
        $profesor   = auth()->user();
        $seccion    = \App\Models\Seccion::with(['trayecto.pnf', 'turno'])->findOrFail($id);

        // Verificar que el profesor pertenece a esa sección
        $perteneceASeccion = \App\Models\ProfesorSeccion::where('pse_id_usu', $profesor->usu_id)
            ->where('pse_id_sec', $id)
            ->where('pse_status', true)
            ->exists();

        if (!$perteneceASeccion) {
            abort(403);
        }

        $seccionesIds = collect([$id]);

        $estudiantesIds = \App\Models\EstudianteSeccion::where('ese_id_sec', $id)
            ->where('ese_status', true)
            ->pluck('ese_id_usu');

        $estudiantes = \App\Models\Usuario::whereIn('usu_id', $estudiantesIds)
            ->where('usu_status', true)
            ->whereDoesntHave('equiposIntegrante', fn($q) =>
                $q->where('ein_status', true)
                ->whereHas('equipo', fn($q2) => $q2->where('equ_status', true))
            )
            ->get(['usu_id','usu_cedula','usu_primer_nombre','usu_primer_apellido','usu_tiene_acceso']);

        return \Inertia\Inertia::render('Shared/Equipos', [
            'seccion'          => [
                'sec_id'     => $seccion->sec_id,
                'sec_codigo' => $seccion->sec_codigo,
                'trayecto'   => $seccion->trayecto?->tra_nombre,
                'turno'      => $seccion->turno?->tur_nombre,
            ],
            'equipos'          => \App\Models\Equipo::with([
                                    'seccion.trayecto','integrantes.usuario',
                                    'proyectoComunidad.comunidad',
                                ])->where('equ_id_sec', $id)
                                ->where('equ_status', true)
                                ->orderBy('equ_codigo')->get(),
            'equiposDisueltos' => \App\Models\Equipo::with([
                                    'seccion','trayecto','integrantes.usuario'
                                ])->where('equ_id_sec', $id)
                                ->where('equ_status', false)
                                ->orderBy('equ_codigo')->get(),
            'secciones'        => \App\Models\Seccion::where('sec_id', $id)->with(['trayecto','turno'])->get(),
            'trayectos'        => \App\Models\Trayecto::where('tra_status', true)->get(),
            'estudiantes'      => $estudiantes,
            'comunidades'      => \App\Models\Comunidad::where('com_status', true)->orderBy('com_nombre')->get(),
            'tiposProyecto'    => \App\Models\TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'      => \App\Models\ModalidadProyecto::where('mpr_status', true)->get(),
            'tipoBeneficiario' => \App\Models\TipoBeneficiario::where('tbe_status', true)->get(),
            'puedeCrear'       => true,
            'modoProfesor'     => true,
        ]);
    }
}