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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;
use App\Models\Rol;
use App\Models\HistorialEquipo;
use App\Models\TipoEventoEquipo;
use App\Models\ProyectoComunidad;
use App\Models\ProyectoBeneficiario;
use App\Models\Comunidad;
use App\Models\CartaPresentacion;
use App\Models\Notificacion;
use App\Models\TipoNotificacion;

class EquipoController extends Controller
{
    use AutorizaPorSeccion;
    private function eventoId(string $nombre): int
    {
        return TipoEventoEquipo::firstOrCreate(
            ['tee_nombre' => $nombre],
            ['tee_status' => true]
        )->tee_id;
    }

    private function otorgarAccesoLider(Usuario $usuario): void
    {
        if (!$usuario->usu_tiene_acceso) {
            $usuario->update([
                'usu_tiene_acceso' => true,
                'usu_username'     => $usuario->usu_cedula,
                'usu_password'     => bcrypt($usuario->usu_cedula),
            ]);
        }
        $rol = Rol::where('rol_nombre', 'lider')->first();
        if ($rol) {
            UsuarioRol::updateOrCreate(
                ['uro_id_usu' => $usuario->usu_id, 'uro_id_rol' => $rol->rol_id],
                ['uro_fecha_inicio' => now()->toDateString(), 'uro_status' => true, 'uro_id_usu_created' => auth()->id()]
            );
        }
    }

    private function revocarAccesoLider($usuarioId): void
    {
        Usuario::where('usu_id', $usuarioId)->update(['usu_tiene_acceso' => false]);
        UsuarioRol::where('uro_id_usu', $usuarioId)
            ->where('uro_status', true)
            ->whereHas('rol', fn($q) => $q->where('rol_nombre', 'lider'))
            ->update(['uro_status' => false, 'uro_fecha_fin' => now()->toDateString()]);
    }

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
                                    ->with(['proyectos.equipo'])
                                    ->orderBy('com_nombre')->get(),
            'tiposProyecto'    => \App\Models\TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'      => \App\Models\ModalidadProyecto::where('mpr_status', true)->get(),
            'tiposOrganizacion'=> \App\Models\TipoOrganizacion::where('tor_status', true)->orderBy('tor_nombre')->get(),
            'tipoBeneficiario' => \App\Models\TipoBeneficiario::where('tbe_status', true)->get(),
            'puedeCrear'       => true,
            'sede' => config('siscon.sede', 'ACA'),
        ]);
    }

    // ── El resto de métodos se mantienen igual ────────────────────────
    // store(), update(), destroy(), disolver(), removerIntegrante(),
    // cambiarLider(), historial(), reactivar() — no cambia nada en ellos

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'equ_id_sec'                        => 'required|exists:seccion,sec_id',
            'equ_numero_grupo'                  => 'required|integer|min:1|max:99',
            'equ_total_seccion' => 'required|integer|min:1|max:99|gte:equ_numero_grupo',
            'equ_semestre'      => 'required|integer|min:1|max:9',
            'equ_titulo'                        => 'required|string|max:150',
            'integrantes'                       => 'required|array|min:1|max:6',
            'integrantes.*.usu_id'              => 'required|distinct|exists:usuario,usu_id',
            'integrantes.*.es_lider'            => 'required|boolean',
            'justificacion_integrantes'         => 'nullable|string|max:500',
            'proyecto.pco_id_com'               => 'nullable|exists:comunidad,com_id',
            'proyecto.pco_id_tpr'               => 'nullable|exists:tipo_proyecto,tpr_id',
            'proyecto.pco_id_mpr'               => 'nullable|exists:modalidad_proyecto,mpr_id',
            'proyecto.pco_id_pco_previo'        => 'nullable|exists:proyecto_comunidad,pco_id',
            'proyecto.pco_resumen'              => 'nullable|string|max:150',
            'proyecto.pco_fecha_limite'         => 'nullable|date|after_or_equal:today|before_or_equal:' . now()->addYear()->toDateString(),
            'proyecto.beneficiarios'            => 'nullable|array',
            'proyecto.beneficiarios.*.tbe_id'   => 'nullable|exists:tipo_beneficiario,tbe_id',
            'proyecto.beneficiarios.*.cantidad' => 'nullable|integer|min:1|max:1000000',
        ], [
            'equ_id_sec.required'                       => 'La sección es obligatoria.',
            'equ_numero_grupo.required'                 => 'El número de grupo es obligatorio.',
            'equ_total_seccion.required' => 'Indica cuántos equipos tiene la sección.',
            'equ_total_seccion.gte'      => 'El total de equipos no puede ser menor que el N° de este equipo.',
            'equ_semestre.required'      => 'El semestre es obligatorio.',
            'equ_titulo.required'                       => 'El título es obligatorio.',
            'integrantes.required'                      => 'Debe agregar al menos un integrante.',
            'integrantes.max'                           => 'El equipo no puede tener más de 6 integrantes.',
            'integrantes.*.usu_id.distinct'             => 'Un estudiante no puede repetirse en el equipo.',
            'proyecto.pco_fecha_limite.after_or_equal'  => 'La fecha límite no puede ser anterior a hoy.',
            'proyecto.pco_fecha_limite.before_or_equal' => 'La fecha límite no puede superar un año desde hoy.',
            'proyecto.beneficiarios.*.cantidad.min'     => 'La cantidad de beneficiarios debe ser mayor a 0.',
        ]);

        $seccion = Seccion::with('trayecto.pnf')->findOrFail($request->equ_id_sec);

        if ($user->esProfesor()) {
            abort_unless(
                $user->seccionesProfesor()->where('pse_status', true)
                    ->where('pse_id_sec', $seccion->sec_id)->exists(),
                403
            );
        }

        $integrantes = collect($request->integrantes);

        if ($integrantes->where('es_lider', true)->count() !== 1) {
            return back()->withErrors(['integrantes' => 'Debe designar exactamente un líder.']);
        }

        // Todos deben pertenecer a la sección elegida
        $enSeccion = EstudianteSeccion::where('ese_id_sec', $seccion->sec_id)
            ->where('ese_status', true)
            ->whereIn('ese_id_usu', $integrantes->pluck('usu_id'))
            ->count();
        if ($enSeccion !== $integrantes->count()) {
            return back()->withErrors(['integrantes' => 'Todos los integrantes deben pertenecer a la sección seleccionada.']);
        }

        // Mínimo 3, o excepción justificada
        $total = $integrantes->count();
        if ($total < 3 && !filled($request->justificacion_integrantes)) {
            return back()->withErrors([
                'justificacion_integrantes' => "El equipo tiene solo {$total} integrante(s). Escribe una justificación para continuar con menos de 3.",
            ]);
        }

        // Código oficial del PST: PNFI-ACA11-131-1/7
        $trayecto = $seccion->trayecto;
        $numero   = (int) $request->equ_numero_grupo;
        $total    = (int) $request->equ_total_seccion;
        $semestre = (int) $request->equ_semestre;
        $pnf      = strtoupper($trayecto->pnf?->pnf_siglas ?? 'PNFI');
        $codigo   = "{$pnf}-" . config('siscon.sede', 'ACA')
                . "{$trayecto->tra_numero}{$semestre}-{$seccion->sec_codigo}-{$numero}/{$total}";

        $existentes = Equipo::where('equ_id_sec', $seccion->sec_id)->get(['equ_codigo', 'equ_status'])
            ->map(function ($e) {
                if (!preg_match('#^[A-Z]+-[A-Z]+(\d)(\d)-[^-]+-(\d+)/(\d+)$#', $e->equ_codigo, $m)) {
                    return null;
                }
                return ['activo' => (bool) $e->equ_status, 'semestre' => (int) $m[2], 'n' => (int) $m[3], 'total' => (int) $m[4]];
            })->filter();

        if ($existentes->contains('n', $numero)) {
            return back()->withErrors(['equ_numero_grupo' => "Ya existe el equipo N° {$numero} en esta sección (puede estar disuelto)."]);
        }

        $ref = $existentes->where('activo', true)->last();
        if ($ref) {
            if ($ref['semestre'] !== $semestre) {
                return back()->withErrors([
                    'equ_semestre' => "Los demás equipos de la sección usan semestre {$ref['semestre']}. Usa el mismo valor.",
                ]);
            }
            if ($total < $ref['total'] && $total < count($existentes)) {
                return back()->withErrors([
                    'equ_total_seccion' => "El total de equipos ({$total}) no puede ser menor a los equipos ya registrados en esta sección ({$ref['total']}).",
                ]);
            }
        }

        if (Equipo::where('equ_codigo', $codigo)->exists()) {
            return back()->withErrors(['equ_numero_grupo' => "Ya existe un equipo con el código {$codigo}."]);
        }

        $ocupado = EquipoIntegrante::whereIn('ein_id_usu', $integrantes->pluck('usu_id'))
            ->where('ein_status', true)
            ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
            ->with('usuario')->first();
        if ($ocupado) {
            $n = $ocupado->usuario->usu_primer_nombre . ' ' . $ocupado->usuario->usu_primer_apellido;
            return back()->withErrors(['integrantes' => "El estudiante {$n} ya pertenece a otro equipo activo."]);
        }

        // Proyecto opcional: se valida ANTES de crear nada
        $proy          = $request->input('proyecto', []);
        $crearProyecto = !empty($proy['pco_id_com']) && !empty($proy['pco_id_tpr']);
        $beneficiarios = collect($proy['beneficiarios'] ?? [])
            ->filter(fn($b) => !empty($b['tbe_id']) && (int) ($b['cantidad'] ?? 0) > 0)->values();

        if ($crearProyecto && $beneficiarios->isEmpty()) {
            return back()->withErrors(['proyecto.beneficiarios' => 'Debe agregar al menos un tipo de beneficiario con cantidad mayor a 0.']);
        }

        DB::transaction(function () use ($request, $seccion, $trayecto, $codigo, $integrantes, $total, $crearProyecto, $proy, $beneficiarios) {
            $equipo = Equipo::create([
                'equ_codigo'         => $codigo,
                'equ_titulo'         => $request->equ_titulo,
                'equ_id_sec'         => $seccion->sec_id,
                'equ_id_tra'         => $trayecto->tra_id,
                'equ_status'         => true,
                'equ_id_usu_created' => auth()->id(),
            ]);

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Creado'),
                'heq_motivo'         => $total < 3
                    ? "EXCEPCIÓN: equipo creado con {$total} integrante(s). Justificación: {$request->justificacion_integrantes}"
                    : 'Equipo creado.',
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            foreach ($integrantes as $i) {
                EquipoIntegrante::create([
                    'ein_id_equ'         => $equipo->equ_id,
                    'ein_id_usu'         => $i['usu_id'],
                    'ein_es_lider'       => $i['es_lider'],
                    'ein_fecha_ingreso'  => now()->toDateString(),
                    'ein_status'         => true,
                    'ein_id_usu_created' => auth()->id(),
                ]);
                if ($i['es_lider']) {
                    $this->otorgarAccesoLider(Usuario::findOrFail($i['usu_id']));
                }
            }

            if ($crearProyecto) {
                $mprId = !empty($proy['pco_id_mpr']) ? $proy['pco_id_mpr'] : (\App\Models\ModalidadProyecto::where('mpr_nombre', 'Nuevo')->value('mpr_id') ?? \App\Models\ModalidadProyecto::first()?->mpr_id);
                $fechaLim = !empty($proy['pco_fecha_limite']) ? $proy['pco_fecha_limite'] : now()->addMonths(6)->toDateString();

                $proyecto = ProyectoComunidad::create([
                    'pco_id_equ'          => $equipo->equ_id,
                    'pco_id_com'          => $proy['pco_id_com'],
                    'pco_id_tpr'          => $proy['pco_id_tpr'],
                    'pco_id_mpr'          => $mprId,
                    'pco_id_pco_previo'   => !empty($proy['pco_id_pco_previo']) ? $proy['pco_id_pco_previo'] : null,
                    'pco_resumen'         => $proy['pco_resumen'] ?? '',
                    'pco_fecha_limite'    => $fechaLim,
                    'pco_fecha_aprobacion'=> now()->toDateString(),   // aprobado al crear
                    'pco_id_usu_aprobado' => auth()->id(),
                    'pco_status'          => true,
                    'pco_id_usu_created'  => auth()->id(),
                ]);

                foreach ($beneficiarios as $b) {
                    ProyectoBeneficiario::create([
                        'pbe_id_pco'         => $proyecto->pco_id,
                        'pbe_id_tbe'         => $b['tbe_id'],
                        'pbe_cantidad'       => $b['cantidad'],
                        'pbe_id_usu_created' => auth()->id(),
                    ]);
                }

                HistorialEquipo::create([
                    'heq_id_equ'         => $equipo->equ_id,
                    'heq_id_tee'         => $this->eventoId('Proyecto asignado'),
                    'heq_motivo'         => 'Proyecto asignado en la comunidad "'
                                            . (Comunidad::find($proy['pco_id_com'])?->com_nombre ?? '—') . '".',
                    'heq_fecha_evento'   => now()->toDateString(),
                    'heq_id_usu_created' => auth()->id(),
                ]);
            }
        });

        return back()->with('success', "Equipo {$codigo} creado correctamente. El líder puede ingresar con su cédula.");
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::with('proyectoComunidad')->findOrFail($id);
        $this->autorizarEquipoId($equipo->equ_id);

        $proyecto    = $equipo->proyectoComunidad;
        $fechaActual = $proyecto ? substr((string) $proyecto->pco_fecha_limite, 0, 10) : null;

        $reglaFecha = ['nullable', 'date'];
        if ($request->filled('pco_fecha_limite') && $request->pco_fecha_limite !== $fechaActual) {
            $reglaFecha = ['date', 'after_or_equal:today', 'before_or_equal:' . now()->addYear()->toDateString()];
        }

        $request->validate([
            'equ_titulo'          => 'required|string|max:150',
            'pco_id_com'          => 'nullable|exists:comunidad,com_id',
            'pco_id_tpr'          => 'nullable|exists:tipo_proyecto,tpr_id',
            'pco_id_mpr'          => 'nullable|exists:modalidad_proyecto,mpr_id',
            'pco_id_pco_previo'   => 'nullable|exists:proyecto_comunidad,pco_id',
            'pco_resumen'         => 'nullable|string|max:150',
            'pco_fecha_limite'    => $reglaFecha,
        ], ['equ_titulo.required' => 'El título es obligatorio.']);

        DB::transaction(function () use ($request, $equipo, $proyecto) {
            $equipo->update(['equ_titulo' => $request->equ_titulo, 'equ_id_usu_updated' => auth()->id()]);

            if (!$proyecto) {
                if ($request->filled('pco_id_com')) {
                    $mprId = $request->pco_id_mpr ?: (\App\Models\ModalidadProyecto::where('mpr_nombre', 'Nuevo')->value('mpr_id') ?? \App\Models\ModalidadProyecto::first()?->mpr_id);
                    $tprId = $request->pco_id_tpr ?: (\App\Models\TipoProyecto::first()?->tpr_id);
                    $fechaLim = $request->filled('pco_fecha_limite') ? $request->pco_fecha_limite : now()->addMonths(6)->toDateString();

                    $proyecto = ProyectoComunidad::create([
                        'pco_id_equ'          => $equipo->equ_id,
                        'pco_id_com'          => $request->pco_id_com,
                        'pco_id_tpr'          => $tprId,
                        'pco_id_mpr'          => $mprId,
                        'pco_id_pco_previo'   => $request->pco_id_pco_previo ?: null,
                        'pco_resumen'         => $request->pco_resumen ?? '',
                        'pco_fecha_limite'    => $fechaLim,
                        'pco_fecha_aprobacion'=> now()->toDateString(),
                        'pco_id_usu_aprobado' => auth()->id(),
                        'pco_status'          => true,
                        'pco_id_usu_created'  => auth()->id(),
                    ]);

                    HistorialEquipo::create([
                        'heq_id_equ'         => $equipo->equ_id,
                        'heq_id_tee'         => $this->eventoId('Proyecto asignado'),
                        'heq_motivo'         => 'Proyecto asignado en la comunidad "' . (Comunidad::find($request->pco_id_com)?->com_nombre ?? '—') . '".',
                        'heq_fecha_evento'   => now()->toDateString(),
                        'heq_id_usu_created' => auth()->id(),
                    ]);
                }
                return;
            }

            $comunidadAnterior = (int) $proyecto->pco_id_com;

            $proyecto->update([
                'pco_id_com'         => $request->pco_id_com ?: $proyecto->pco_id_com,
                'pco_id_tpr'         => $request->pco_id_tpr ?: $proyecto->pco_id_tpr,
                'pco_id_mpr'         => $request->pco_id_mpr ?: $proyecto->pco_id_mpr,
                'pco_resumen'        => $request->pco_resumen ?? $proyecto->pco_resumen,
                'pco_fecha_limite'   => $request->pco_fecha_limite ?: $proyecto->pco_fecha_limite,
                'pco_id_usu_updated' => auth()->id(),
            ]);

            if ($request->filled('pco_id_com') && (int) $request->pco_id_com !== $comunidadAnterior) {
                $carta = CartaPresentacion::where('cpr_id_pco', $proyecto->pco_id)->first();

                if ($carta) {
                    $carta->update(['cpr_solicitada' => false, 'cpr_fecha_aprobacion' => null, 'cpr_sello' => false]);

                    $liderId = EquipoIntegrante::where('ein_id_equ', $equipo->equ_id)
                        ->where('ein_es_lider', true)->where('ein_status', true)->value('ein_id_usu');
                    $tno = TipoNotificacion::where('tno_nombre', 'Carta invalidada por cambio de comunidad')->value('tno_id');

                    if ($liderId && $tno) {
                        Notificacion::create([
                            'not_id_usu'  => $liderId,
                            'not_id_tno'  => $tno,
                            'not_mensaje' => 'La comunidad de tu proyecto cambió. Debes solicitar una nueva carta de presentación.',
                            'not_leida'   => false,
                            'not_id_equ'  => $equipo->equ_id,
                        ]);
                    }
                }

                HistorialEquipo::create([
                    'heq_id_equ'         => $equipo->equ_id,
                    'heq_id_tee'         => $this->eventoId('Proyecto modificado'),
                    'heq_motivo'         => 'Comunidad cambiada a "' . (Comunidad::find($request->pco_id_com)?->com_nombre ?? '—') . '".'
                                            . ($carta ? ' Carta anterior invalidada.' : ''),
                    'heq_fecha_evento'   => now()->toDateString(),
                    'heq_id_usu_created' => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $this->autorizarEquipoId($id);
        $equipo = Equipo::findOrFail($id);
        $equipo->update(['equ_status' => false, 'equ_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Equipo desactivado.');
    }

    

    public function removerIntegrante(Request $request, $equipoId, $integranteId)
    {
        $this->autorizarEquipoId($equipoId);
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

    public function trasladarIntegrante(Request $request, $equipoId, $integranteId)
    {
        $this->autorizarEquipoId($equipoId);
        $request->validate([
            'nuevo_equipo_id' => 'required|exists:equipo,equ_id',
        ], [
            'nuevo_equipo_id.required' => 'Debes seleccionar el equipo de destino.',
        ]);

        if ((int)$request->nuevo_equipo_id === (int)$equipoId) {
            return back()->withErrors(['integrante' => 'El equipo de destino debe ser diferente al actual.']);
        }

        $integrante = EquipoIntegrante::where('ein_id', $integranteId)
            ->where('ein_id_equ', $equipoId)
            ->where('ein_status', true)
            ->with('usuario')
            ->firstOrFail();

        if ($integrante->ein_es_lider) {
            return back()->withErrors(['integrante' => 'No puedes trasladar al líder del equipo. Primero asigna otro líder.']);
        }

        $equipoOrigen = Equipo::findOrFail($equipoId);
        $equipoDestino = Equipo::with('integrantes')->findOrFail($request->nuevo_equipo_id);

        $this->autorizarEquipoId($equipoDestino->equ_id);

        $integrantesDestino = $equipoDestino->integrantes->where('ein_status', true);
        if ($integrantesDestino->count() >= 6) {
            return back()->withErrors(['integrante' => "El equipo destino {$equipoDestino->equ_codigo} ya tiene el máximo de 6 integrantes."]);
        }

        if ($integrantesDestino->contains('ein_id_usu', $integrante->ein_id_usu)) {
            return back()->withErrors(['integrante' => 'El estudiante ya pertenece al equipo de destino.']);
        }

        DB::transaction(function () use ($integrante, $equipoOrigen, $equipoDestino) {
            $integrante->update([
                'ein_status'         => false,
                'ein_fecha_salida'   => now()->toDateString(),
                'ein_id_usu_updated' => auth()->id(),
            ]);

            EquipoIntegrante::create([
                'ein_id_equ'         => $equipoDestino->equ_id,
                'ein_id_usu'         => $integrante->ein_id_usu,
                'ein_es_lider'       => false,
                'ein_fecha_ingreso'  => now()->toDateString(),
                'ein_status'         => true,
                'ein_id_usu_created' => auth()->id(),
            ]);

            $nombre = $integrante->usuario?->usu_primer_nombre . ' ' . $integrante->usuario?->usu_primer_apellido;

            HistorialEquipo::create([
                'heq_id_equ'         => $equipoOrigen->equ_id,
                'heq_id_tee'         => $this->eventoId('Integrante Removido') ?? 1,
                'heq_motivo'         => "Integrante {$nombre} trasladado al equipo {$equipoDestino->equ_codigo}.",
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            HistorialEquipo::create([
                'heq_id_equ'         => $equipoDestino->equ_id,
                'heq_id_tee'         => $this->eventoId('Modificado') ?? 1,
                'heq_motivo'         => "Integrante {$nombre} recibido por traslado desde el equipo {$equipoOrigen->equ_codigo}.",
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);
        });

        return back()->with('success', "Integrante trasladado correctamente al equipo {$equipoDestino->equ_codigo}.");
    }

    

    public function historial($equipoId)
    {
        $this->autorizarEquipoId($equipoId);
        $equipo = Equipo::with([
            'historial.tipoEvento',
            'historial' => fn($q) => $q->orderBy('heq_fecha_evento', 'desc'),
        ])->findOrFail($equipoId);
        return response()->json($equipo->historial);
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
                                    'seccion.trayecto','trayecto','integrantes.usuario',
                                    'proyectoComunidad.comunidad',
                                    'proyectoComunidad.tipoProyecto',
                                    'resultadoProyecto',
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
            'comunidades'      => \App\Models\Comunidad::where('com_status', true)->with('proyectos.equipo')->orderBy('com_nombre')->get(),
            'tiposProyecto'    => \App\Models\TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'      => \App\Models\ModalidadProyecto::where('mpr_status', true)->get(),
            'tiposOrganizacion'=> \App\Models\TipoOrganizacion::where('tor_status', true)->orderBy('tor_nombre')->get(),
            'tipoBeneficiario' => \App\Models\TipoBeneficiario::where('tbe_status', true)->get(),
            'puedeCrear'       => true,
            'modoProfesor'     => true,
            'sede' => config('siscon.sede', 'ACA'),
        ]);
    }
    
    public function generarPaqueteCierre($id)
    {
        $this->autorizarEquipoId($id);
        $equipo = Equipo::with([
            'seccion.trayecto',
            'integrantes.usuario',
            'proyectoComunidad.comunidad',
            'proyectoComunidad.tipoProyecto',
            'entregables' => fn($q) => $q->where('entr_aprobado', true)
                                        ->where('entr_status', true),
            'entregables.tipoEntregable',
            'puntosControl.seguimientos.asistencias.usuario',
            'socializaciones.tipoSocializacion',
            'cartaPresentacion',
            'resultadoProyecto.estadoProyecto',
        ])->findOrFail($id);

        // Verificar que el equipo tiene resultado registrado
        if (!$equipo->resultadoProyecto) {
            return back()->withErrors([
                'equipo' => 'El equipo no tiene resultado final registrado. Registra el resultado antes de generar el paquete de cierre.'
            ]);
        }

        $zipNombre = 'paquete_cierre_' . $equipo->codigo_archivo . '_' . now()->format('Ymd_His') . '.zip';
        $zipRuta   = storage_path('app/temp/' . $zipNombre);

        // Crear directorio temp si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipRuta, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['equipo' => 'No se pudo crear el archivo ZIP.']);
        }

        // ── 1. Resumen JSON ──────────────────────────────────────────────
        $resumen = [
            'equipo' => [
                'codigo'    => $equipo->equ_codigo,
                'titulo'    => $equipo->equ_titulo,
                'seccion'   => $equipo->seccion?->sec_codigo,
                'trayecto'  => $equipo->seccion?->trayecto?->tra_nombre,
            ],
            'proyecto' => [
                'comunidad'    => $equipo->proyectoComunidad?->comunidad?->com_nombre,
                'tipo'         => $equipo->proyectoComunidad?->tipoProyecto?->tpr_nombre,
                'fecha_inicio' => $equipo->proyectoComunidad?->created_at?->toDateString(),
                'fecha_limite' => $equipo->proyectoComunidad?->pco_fecha_limite,
            ],
            'resultado' => [
                'estado'          => $equipo->resultadoProyecto?->estadoProyecto?->epr_nombre,
                'fecha_registro'  => $equipo->resultadoProyecto?->rpr_fecha_registro,
                'observaciones'   => $equipo->resultadoProyecto?->rpr_observaciones,
            ],
            'integrantes' => $equipo->integrantes->map(fn($i) => [
                'nombre'   => $i->usuario?->usu_primer_nombre . ' ' . $i->usuario?->usu_primer_apellido,
                'cedula'   => $i->usuario?->usu_cedula,
                'es_lider' => (bool) $i->ein_es_lider,
            ])->toArray(),
            'entregables_aprobados' => $equipo->entregables->map(fn($e) => [
                'nombre'  => $e->entr_nombre_archivo,
                'tipo'    => $e->tipoEntregable?->tet_nombre,
                'version' => $e->entr_version,
            ])->toArray(),
            'generado_en' => now()->toDateTimeString(),
            'generado_por' => auth()->user()->usu_primer_nombre . ' ' . auth()->user()->usu_primer_apellido,
        ];

        $zip->addFromString('resumen.json', json_encode($resumen, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // ── 2. Entregables aprobados ─────────────────────────────────────
        foreach ($equipo->entregables as $entregable) {
            $rutaArchivo = storage_path('app/public/' . $entregable->entr_ruta);
            if (file_exists($rutaArchivo)) {
                $tipo      = $entregable->tipoEntregable?->tet_nombre ?? 'Entregable';
                $extension = pathinfo($entregable->entr_nombre_archivo, PATHINFO_EXTENSION);
                $nombreZip = 'entregables/' . str_replace(['/', '\\', ' '], '_', $tipo)
                        . '_v' . ($entregable->entr_version ?? '1')
                        . '.' . $extension;
                $zip->addFile($rutaArchivo, $nombreZip);
            }
        }

        // ── 3. PDF carta de presentación ────────────────────────────────
        if ($equipo->cartaPresentacion?->cpr_fecha_aprobacion) {
            try {
                $coordinador = \App\Models\Usuario::whereHas('roles', fn($q) =>
                    $q->where('rol_nombre', 'coordinador')
                )->first();

                $pdfCarta = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.carta_presentacion', [
                    'carta'       => $equipo->cartaPresentacion->load([
                        'proyectoComunidad.comunidad',
                        'proyectoComunidad.equipo.integrantes.usuario',
                        'proyectoComunidad.equipo.seccion',
                        'proyectoComunidad.equipo.trayecto',
                    ]),
                    'proyecto'    => $equipo->cartaPresentacion->proyectoComunidad,
                    'coordinador' => $coordinador,
                ])->setPaper('letter', 'portrait');

                $zip->addFromString(
                    'carta_presentacion_' . $equipo->codigo_archivo . '.pdf',
                    $pdfCarta->output()
                );
            } catch (\Exception $e) {
                \Log::warning('Error en carta de presentación: ' . $e->getMessage());
            }
        }

        // ── 4. PDF solvencia del equipo ──────────────────────────────────
        try {
            $todosLosSeguimientos = $equipo->puntosControl
                ->flatMap(fn($p) => $p->seguimientos);
            $totalPuntos = $todosLosSeguimientos->count();

            $integrantesSolvencia = $equipo->integrantes->map(function ($integrante) use ($todosLosSeguimientos, $totalPuntos) {
                $asistencias = 0;
                foreach ($todosLosSeguimientos as $seq) {
                    $asistio = $seq->asistencias
                        ->where('apc_id_usu', $integrante->ein_id_usu)->first();
                    if ($asistio && $asistio->apc_asistio) $asistencias++;
                }
                $porcentaje = $totalPuntos > 0 ? round($asistencias / $totalPuntos * 100) : 0;
                return [
                    'nombre'      => $integrante->usuario?->usu_primer_nombre . ' ' . $integrante->usuario?->usu_primer_apellido,
                    'cedula'      => $integrante->usuario?->usu_cedula,
                    'es_lider'    => (bool) $integrante->ein_es_lider,
                    'asistencias' => $asistencias,
                    'total'       => $totalPuntos,
                    'porcentaje'  => $porcentaje,
                    'solvente'    => $porcentaje >= 75,
                ];
            })->toArray();

            $reporteEquipo = [[
                'equipo'      => $equipo->equ_codigo,
                'titulo'      => $equipo->equ_titulo,
                'comunidad'   => $equipo->proyectoComunidad?->comunidad?->com_nombre,
                'resultado'   => $equipo->resultadoProyecto?->estadoProyecto?->epr_nombre,
                'integrantes' => $integrantesSolvencia,
            ]];

            $pdfSolvencia = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte_solvencia', [
                'seccion' => $equipo->seccion,
                'reporte' => $reporteEquipo,
            ])->setPaper('letter', 'portrait');

            $zip->addFromString(
                'solvencia_' . $equipo->codigo_archivo . '.pdf',
                $pdfSolvencia->output()
            );
        } catch (\Exception $e) {
            \Log::warning('Error en solvencia del equipo: ' . $e->getMessage());
        }

        $zip->close();

        // Registrar en bitácora
        \App\Models\Bitacora::create([
            'bit_id_usu'      => auth()->id(),
            'bit_id_entidad'  => \App\Models\Entidad::where('ent_nombre', 'equipo')->value('ent_id'),
            'bit_accion'      => 'PAQUETE_CIERRE',
            'bit_registro_id' => $equipo->equ_id,
            'bit_fecha_hora'  => now(),
        ]);

        return response()->download($zipRuta, $zipNombre, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }
    public function disolver(Request $request, $id)
    {
        $request->validate(['motivo' => 'required|string|max:500'], ['motivo.required' => 'El motivo de la disolución es obligatorio.']);
        $equipo = Equipo::findOrFail($id);
        $this->autorizarEquipoId($equipo->equ_id);

        DB::transaction(function () use ($equipo, $request) {
            $liderId = $equipo->integrantes()->where('ein_status', true)->where('ein_es_lider', true)->value('ein_id_usu');

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Disuelto'),
                'heq_motivo'         => $request->motivo,
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            $equipo->integrantes()->where('ein_status', true)->update([
                'ein_status' => false, 'ein_fecha_salida' => now()->toDateString(), 'ein_id_usu_updated' => auth()->id(),
            ]);
            $equipo->update(['equ_status' => false, 'equ_id_usu_updated' => auth()->id()]);

            if ($liderId) {
                $this->revocarAccesoLider($liderId);
            }
        });

        return back()->with('success', 'Equipo disuelto correctamente.');
    }

    public function cambiarLider(Request $request, $equipoId)
    {
        $request->validate(['nuevo_lider_id' => 'required|integer'], ['nuevo_lider_id.required' => 'Debes seleccionar el nuevo líder.']);
        $equipo = Equipo::findOrFail($equipoId);
        $this->autorizarEquipoId($equipo->equ_id);

        $nuevo = EquipoIntegrante::where('ein_id', $request->nuevo_lider_id)
            ->where('ein_id_equ', $equipo->equ_id)->where('ein_status', true)->first();

        if (!$nuevo) {
            return back()->withErrors(['nuevo_lider_id' => 'El integrante seleccionado no pertenece a este equipo.']);
        }
        if ($nuevo->ein_es_lider) {
            return back()->withErrors(['nuevo_lider_id' => 'Ese integrante ya es el líder.']);
        }

        DB::transaction(function () use ($equipo, $nuevo) {
            $actual = EquipoIntegrante::where('ein_id_equ', $equipo->equ_id)
                ->where('ein_es_lider', true)->where('ein_status', true)->first();

            if ($actual) {
                $actual->update(['ein_es_lider' => false, 'ein_id_usu_updated' => auth()->id()]);
                $this->revocarAccesoLider($actual->ein_id_usu);
            }

            $nuevo->update(['ein_es_lider' => true, 'ein_id_usu_updated' => auth()->id()]);
            $usuario = Usuario::findOrFail($nuevo->ein_id_usu);
            $this->otorgarAccesoLider($usuario);

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Líder Cambiado'),
                'heq_motivo'         => 'Nuevo líder: ' . $usuario->usu_primer_nombre . ' ' . $usuario->usu_primer_apellido,
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Líder cambiado. El nuevo líder entra con su cédula.');
    }

    public function reactivar(Request $request, $id)
    {
        $equipo = Equipo::where('equ_status', false)->findOrFail($id);
        $this->autorizarEquipoId($equipo->equ_id);

        $fechaDisolucion = HistorialEquipo::where('heq_id_equ', $equipo->equ_id)
            ->whereHas('tipoEvento', fn($q) => $q->where('tee_nombre', 'Disuelto'))
            ->latest('heq_id')->value('heq_fecha_evento');

        $porRestaurar = EquipoIntegrante::where('ein_id_equ', $equipo->equ_id)
            ->where('ein_status', false)
            ->when($fechaDisolucion, fn($q) => $q->whereDate('ein_fecha_salida', $fechaDisolucion))
            ->get();

        if ($porRestaurar->isEmpty()) {
            return back()->withErrors(['equipo' => 'No hay integrantes para restaurar en este equipo.']);
        }

        $ocupado = EquipoIntegrante::whereIn('ein_id_usu', $porRestaurar->pluck('ein_id_usu'))
            ->where('ein_status', true)
            ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
            ->exists();
        if ($ocupado) {
            return back()->withErrors(['equipo' => 'Algún integrante ya pertenece a otro equipo activo.']);
        }

        DB::transaction(function () use ($equipo, $porRestaurar, $request) {
            $equipo->update(['equ_status' => true, 'equ_id_usu_updated' => auth()->id()]);

            EquipoIntegrante::whereIn('ein_id', $porRestaurar->pluck('ein_id'))->update([
                'ein_status' => true, 'ein_fecha_salida' => null, 'ein_id_usu_updated' => auth()->id(),
            ]);

            $lider = $porRestaurar->firstWhere('ein_es_lider', true);
            if ($lider) {
                $this->otorgarAccesoLider(Usuario::findOrFail($lider->ein_id_usu));
            }

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Reactivado'),
                'heq_motivo'         => $request->input('motivo', 'Reactivación del equipo.'),
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);
        });

        return back()->with('success', "Equipo {$equipo->equ_codigo} reactivado correctamente.");
    }
}