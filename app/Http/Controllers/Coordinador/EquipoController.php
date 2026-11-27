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
            'proyectoComunidad.comunidad.tipoOrganizacion',
            'proyectoComunidad.comunidad.responsables' => fn($q) => $q->where('rco_status', true)->with(['usuario.cargos' => fn($c) => $c->where('pca_status', true)]),
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
            'socializaciones.tipoSocializacion',
            'resultadoProyecto',
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
        // Coordinador: ve todos los disponibles con sección activa y sec_id asociado
        // Profesor: ve solo los de sus secciones que no están en equipo activo
        if ($esProfesor && $seccionesIds && $seccionesIds->isNotEmpty()) {
            $estudiantes = Usuario::whereHas('seccionesEstudiante', fn($q) =>
                    $q->whereIn('ese_id_sec', $seccionesIds)->where('ese_status', true)
                )
                ->where('usu_status', true)
                ->whereDoesntHave('equiposIntegrante', fn($q) =>
                    $q->where('ein_status', true)
                      ->whereHas('equipo', fn($q2) =>
                          $q2->where('equ_status', true)
                      )
                )
                ->with(['seccionesEstudiante' => fn($q) =>
                    $q->whereIn('ese_id_sec', $seccionesIds)->where('ese_status', true)
                ])
                ->get(['usu_id','usu_cedula','usu_primer_nombre','usu_primer_apellido','usu_tiene_acceso'])
                ->map(function ($u) {
                    $u->sec_id = $u->seccionesEstudiante->first()?->ese_id_sec;
                    unset($u->seccionesEstudiante);
                    return $u;
                });
        } else {
            // Coordinador: todos los disponibles con sección activa
            $estudiantes = Usuario::whereHas('seccionesEstudiante', fn($q) =>
                    $q->where('ese_status', true)
                )
                ->where('usu_status', true)
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
                ->with(['seccionesEstudiante' => fn($q) =>
                    $q->where('ese_status', true)
                ])
                ->get(['usu_id','usu_cedula','usu_primer_nombre','usu_primer_apellido','usu_tiene_acceso'])
                ->map(function ($u) {
                    $u->sec_id = $u->seccionesEstudiante->first()?->ese_id_sec;
                    unset($u->seccionesEstudiante);
                    return $u;
                });
        }

        $equiposDisueltosQuery = Equipo::with([
            'seccion', 'trayecto', 'integrantes.usuario'
        ])->where('equ_status', false)->orderBy('equ_codigo');

        if ($esProfesor && $seccionesIds) {
            $equiposDisueltosQuery->whereIn('equ_id_sec', $seccionesIds);
        }

        $equiposCollection = $equiposQuery->get();
        $diagnosticosEquipos = app(\App\Services\DiagnosticoProyectoService::class)->diagnosticarSeccion(
            $equiposCollection->pluck('equ_id')->toArray()
        );

        $vista = $esProfesor ? 'Profesor/MisEquipos' : 'Shared/Equipos';

        return Inertia::render($vista, [
            'equipos'             => $equiposCollection,
            'diagnosticosEquipos' => $diagnosticosEquipos,
            'secciones'           => $seccionesQuery->get(),
            'trayectos'           => Trayecto::where('tra_status', true)->get(),
            'equiposDisueltos'    => $equiposDisueltosQuery->get(),
            'estudiantes'         => $estudiantes,
            'comunidades'         => \App\Models\Comunidad::where('com_status', true)
                                        ->with(['proyectos.equipo'])
                                        ->orderBy('com_nombre')->get(),
            'tiposProyecto'       => \App\Models\TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'         => \App\Models\ModalidadProyecto::where('mpr_status', true)->get(),
            'tiposOrganizacion'   => \App\Models\TipoOrganizacion::where('tor_status', true)->orderBy('tor_nombre')->get(),
            'tipoBeneficiario'    => \App\Models\TipoBeneficiario::where('tbe_status', true)->get(),
            'puedeCrear'          => $esProfesor,
            'modoProfesor'        => $esProfesor,
            'sede'                => config('siscon.sede', 'ACA'),
        ]);
    }

    // ── Creación y gestión autónoma por el Docente de Proyecto ────────

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user && $user->esProfesor(), 403, 'Solo el Docente de Proyecto puede crear equipos en sus secciones.');

        $request->validate([
            'equ_id_sec'                        => 'required|exists:seccion,sec_id',
            'equ_numero_grupo'                  => 'required|integer|min:1|max:99',
            'equ_codigo'                        => 'nullable|string|max:30',
            'equ_total_seccion'                 => 'nullable|integer|min:1|max:99',
            'equ_semestre'                      => 'nullable|integer|min:1|max:9',
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
            'proyecto.beneficiarios.*.cantidad' => 'nullable|integer|min:1|max:10000',
        ], [
            'equ_id_sec.required'                       => 'La sección es obligatoria.',
            'equ_numero_grupo.required'                 => 'El número de grupo es obligatorio.',
            'equ_titulo.required'                       => 'El título es obligatorio.',
            'integrantes.required'                      => 'Debe agregar al menos un integrante.',
            'integrantes.max'                           => 'El equipo no puede tener más de 6 integrantes.',
            'integrantes.*.usu_id.distinct'             => 'Un estudiante no puede repetirse en el equipo.',
            'proyecto.pco_fecha_limite.after_or_equal'  => 'La fecha límite no puede ser anterior a hoy.',
            'proyecto.pco_fecha_limite.before_or_equal' => 'La fecha límite no puede superar un año desde hoy.',
            'proyecto.beneficiarios.*.cantidad.min'     => 'La cantidad de beneficiarios debe ser mayor a 0.',
        ]);

        $seccion = Seccion::with('trayecto.pnf')->findOrFail($request->equ_id_sec);
        $this->autorizarSeccionId($seccion->sec_id);

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

        // Generación estándar institucional del código del equipo:
        // PNFI-ACARIGUATRAYECTOSEMESTRE-SECCIÓN-NºDEEQUIPO/CANTDEEQUIPOSENLASECCIÓN (ej: PNFI-ACA11-131-1/7)
        $numero   = (int) $request->equ_numero_grupo;

        // El total de equipos de la sección se fija al crear el primer grupo, pero el docente puede modificarlo si lo habilita
        $equiposActivosSeccion = Equipo::where('equ_id_sec', $seccion->sec_id)
            ->where('equ_status', true)
            ->orderBy('equ_id')
            ->get(['equ_id', 'equ_codigo']);

        $primerEquipoSeccion = $equiposActivosSeccion->first();
        $maxNumeroExistente = 0;
        foreach ($equiposActivosSeccion as $eqSec) {
            if (preg_match('/-(\d+)\/\d+$/', (string) $eqSec->equ_codigo, $mNum)) {
                $maxNumeroExistente = max($maxNumeroExistente, (int) $mNum[1]);
            }
        }

        if ($primerEquipoSeccion && !$request->boolean('modificar_total_seccion') && preg_match('/\/(\d+)$/', (string) $primerEquipoSeccion->equ_codigo, $matchTot)) {
            $totalSec = (int) $matchTot[1];
        } else {
            $totalSec = (int) ($request->equ_total_seccion ?: $numero);
        }

        // Validación del semestre: el primer número de la sección es el semestre del equipo
        preg_match('/\d/', $seccion->sec_codigo, $matches);
        $primerNumeroSeccion = isset($matches[0]) ? (int) $matches[0] : null;

        if ($primerNumeroSeccion !== null) {
            if ($request->filled('equ_semestre') && (int) $request->equ_semestre !== $primerNumeroSeccion) {
                return back()->withErrors([
                    'equ_semestre' => "El semestre seleccionado ({$request->equ_semestre}) no coincide con la sección {$seccion->sec_codigo}. El primer número de la sección ({$primerNumeroSeccion}) debe ser el semestre del equipo."
                ]);
            }
            $semestre = $primerNumeroSeccion;
        } else {
            $semestre = (int) ($request->equ_semestre ?: 1);
        }

        $traNum = (int) ceil($semestre / 2); // 131/231->T1, 331/431->T2, 531/631->T3, 731/831->T4
        $trayecto = Trayecto::where('tra_numero', $traNum)->first() ?? $seccion->trayecto;
        $sede     = strtoupper(config('siscon.sede', 'ACA'));
        $pnf      = strtoupper($trayecto?->pnf?->pnf_siglas ?? $seccion->trayecto?->pnf?->pnf_siglas ?? 'PNFI');

        $minimoPermitido = max($numero, $maxNumeroExistente);
        if ($totalSec < $minimoPermitido) {
            return back()->withErrors([
                'equ_total_seccion' => "La cantidad total de equipos ({$totalSec}) no puede ser menor al número de grupo más alto de la sección ({$minimoPermitido})."
            ]);
        }

        // Código institucional garantizado con el semestre del equipo
        $codigo = "{$pnf}-{$sede}{$traNum}{$semestre}-{$seccion->sec_codigo}-{$numero}/{$totalSec}";

        // Validar que el código no esté duplicado entre equipos activos
        if (Equipo::where('equ_codigo', $codigo)->where('equ_status', true)->exists()) {
            return back()->withErrors(['equ_numero_grupo' => "Ya existe un equipo activo con el código {$codigo}."]);
        }

        $ocupado = EquipoIntegrante::whereIn('ein_id_usu', $integrantes->pluck('usu_id'))
            ->where('ein_status', true)
            ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
            ->with('usuario')->first();
        if ($ocupado) {
            $n = $ocupado->usuario->usu_primer_nombre . ' ' . $ocupado->usuario->usu_primer_apellido;
            return back()->withErrors(['integrantes' => "El estudiante {$n} ya pertenece a otro equipo activo."]);
        }

        // Candado de Prelación: ningún estudiante puede inscribirse en trayecto superior si su trayecto anterior no está aprobado con >= 16.00
        $evalService = app(\App\Services\EvaluacionTrayectoService::class);
        if ($traNum > 1) {
            foreach ($integrantes as $integ) {
                $errorPrelacion = $evalService->verificarPrelacionEstudiante((int) $integ['usu_id'], (int) $traNum);
                if ($errorPrelacion) {
                    return back()->withErrors(['integrantes' => $errorPrelacion]);
                }
            }
        }

        // Proyecto opcional: si se eligen comunidad y tipo, se crea el proyecto; beneficiarios son opcionales
        $proy          = $request->input('proyecto', []);
        $crearProyecto = !empty($proy['pco_id_com']) && !empty($proy['pco_id_tpr']);
        $beneficiarios = collect($proy['beneficiarios'] ?? [])
            ->filter(fn($b) => !empty($b['tbe_id']) && (int) ($b['cantidad'] ?? 0) > 0)->values();

        $justifDuplicidad = $proy['pco_justificacion_duplicidad']
            ?? $request->input('justificacion_duplicidad')
            ?? $request->input('pco_justificacion_duplicidad');

        // Validación estricta anti-duplicidad si se selecciona comunidad
        if (!empty($proy['pco_id_com'])) {
            $verificacion = app(\App\Services\DetectorDuplicidadService::class)->verificarCoincidencia(
                (int) $proy['pco_id_com'],
                $request->equ_titulo,
                null,
                !empty($proy['pco_id_tpr']) ? (int) $proy['pco_id_tpr'] : null,
                $justifDuplicidad,
                !empty($proy['pco_id_mpr']) ? (int) $proy['pco_id_mpr'] : null
            );

            if (!$verificacion['es_valido']) {
                return back()->withErrors([
                    'justificacion_duplicidad' => $verificacion['mensaje_error'],
                    'equ_titulo'               => $verificacion['mensaje_error'],
                ]);
            }
        }

        DB::transaction(function () use ($request, $seccion, $trayecto, $codigo, $integrantes, $total, $crearProyecto, $proy, $beneficiarios, $totalSec, $justifDuplicidad) {
            $equipo = Equipo::create([
                'equ_codigo'                   => $codigo,
                'equ_titulo'                   => $request->equ_titulo,
                'equ_justificacion_duplicidad' => $justifDuplicidad,
                'equ_justificacion_cupo'       => $total < 3,
                'equ_justificacion_cupo_motivo'=> $total < 3 ? $request->justificacion_integrantes : null,
                'equ_id_sec'                   => $seccion->sec_id,
                'equ_id_tra'                   => $trayecto->tra_id,
                'equ_status'                   => true,
                'equ_id_usu_created'           => auth()->id(),
            ]);

            // Si se estableció o modificó el total de equipos de la sección,
            // sincronizar el denominador en los códigos de todos los demás equipos de esta sección
            if ($totalSec > 0) {
                $otrosEquipos = Equipo::where('equ_id_sec', $seccion->sec_id)
                    ->where('equ_id', '!=', $equipo->equ_id)
                    ->get();

                foreach ($otrosEquipos as $otro) {
                    if (preg_match('/^(.*-\d+)\/\d+$/', $otro->equ_codigo, $match)) {
                        $nuevoCodigo = $match[1] . '/' . $totalSec;
                        if ($nuevoCodigo !== $otro->equ_codigo) {
                            $otro->update([
                                'equ_codigo'         => $nuevoCodigo,
                                'equ_id_usu_updated' => auth()->id(),
                            ]);
                        }
                    }
                }
            }

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
                    'pco_id_equ'                   => $equipo->equ_id,
                    'pco_id_com'                   => $proy['pco_id_com'],
                    'pco_id_tpr'                   => $proy['pco_id_tpr'],
                    'pco_id_mpr'                   => $mprId,
                    'pco_id_pco_previo'            => !empty($proy['pco_id_pco_previo']) ? $proy['pco_id_pco_previo'] : null,
                    'pco_resumen'                  => $proy['pco_resumen'] ?? '',
                    'pco_justificacion_duplicidad' => $justifDuplicidad,
                    'pco_fecha_limite'             => $fechaLim,
                    'pco_fecha_aprobacion'         => now()->toDateString(),
                    'pco_comunidad_aprobada'       => true,
                    'pco_estado_postulacion'       => 'aprobada',
                    'pco_id_usu_aprobado'          => auth()->id(),
                    'pco_status'                   => true,
                    'pco_id_usu_created'           => auth()->id(),
                ]);

                foreach ($beneficiarios as $b) {
                    ProyectoBeneficiario::create([
                        'pbe_id_pco'         => $proyecto->pco_id,
                        'pbe_id_tbe'         => $b['tbe_id'],
                        'pbe_cantidad'       => $b['cantidad'],
                        'pbe_id_usu_created' => auth()->id(),
                    ]);
                }

                $motivoHist = 'Proyecto asignado en la comunidad "' . (Comunidad::find($proy['pco_id_com'])?->com_nombre ?? '—') . '"';
                if ($justifDuplicidad) {
                    $motivoHist .= ". Justificación coincidencia/duplicidad: {$justifDuplicidad}";
                }

                HistorialEquipo::create([
                    'heq_id_equ'         => $equipo->equ_id,
                    'heq_id_tee'         => $this->eventoId('Proyecto asignado'),
                    'heq_motivo'         => $motivoHist,
                    'heq_fecha_evento'   => now()->toDateString(),
                    'heq_id_usu_created' => auth()->id(),
                ]);
            } else {
                // Etapa 1 liviana: crear registro pendiente de postulación comunitaria por el Líder
                ProyectoComunidad::create([
                    'pco_id_equ'             => $equipo->equ_id,
                    'pco_comunidad_aprobada' => false,
                    'pco_estado_postulacion' => 'sin_postular',
                    'pco_fecha_aprobacion'   => null,
                    'pco_status'             => true,
                    'pco_id_usu_created'     => auth()->id(),
                ]);
            }
        });

        return back()->with('success', "Equipo {$codigo} creado correctamente. El líder puede ingresar con su cédula.");
    }

    public function actualizarTotalSeccion(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless($user && $user->esProfesor(), 403, 'Solo el Docente de Proyecto puede modificar el total de equipos de la sección.');

        $seccion = Seccion::findOrFail($id);
        $this->autorizarSeccionId($seccion->sec_id);

        $request->validate([
            'equ_total_seccion' => 'required|integer|min:1|max:99',
        ], [
            'equ_total_seccion.required' => 'Debe indicar el total de equipos de la sección.',
            'equ_total_seccion.integer'  => 'El total de equipos debe ser un número entero.',
            'equ_total_seccion.min'      => 'El total de equipos debe ser al menos 1.',
            'equ_total_seccion.max'      => 'El total de equipos no puede superar 99.',
        ]);

        $nuevoTotal = (int) $request->equ_total_seccion;

        $equiposSeccion = Equipo::where('equ_id_sec', $seccion->sec_id)->get();
        $maxNumeroActivo = 0;
        foreach ($equiposSeccion->where('equ_status', true) as $eq) {
            if (preg_match('/-(\d+)\/\d+$/', (string) $eq->equ_codigo, $m)) {
                $maxNumeroActivo = max($maxNumeroActivo, (int) $m[1]);
            }
        }

        if ($nuevoTotal < $maxNumeroActivo) {
            return back()->withErrors([
                'equ_total_seccion' => "El total de equipos ({$nuevoTotal}) no puede ser menor al número del equipo existente más alto ({$maxNumeroActivo}).",
            ]);
        }

        DB::transaction(function () use ($equiposSeccion, $nuevoTotal) {
            foreach ($equiposSeccion as $eq) {
                if (preg_match('/^(.*-\d+)\/\d+$/', (string) $eq->equ_codigo, $match)) {
                    $nuevoCodigo = $match[1] . '/' . $nuevoTotal;
                    if ($nuevoCodigo !== $eq->equ_codigo) {
                        $eq->update([
                            'equ_codigo'         => $nuevoCodigo,
                            'equ_id_usu_updated' => auth()->id(),
                        ]);
                    }
                }
            }
        });

        return back()->with('success', "Total de equipos de la sección {$seccion->sec_codigo} actualizado a {$nuevoTotal}.");
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
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

        $comId = $request->pco_id_com ?: ($proyecto?->pco_id_com);
        $justifDuplicidad = $request->pco_justificacion_duplicidad
            ?? $request->justificacion_duplicidad
            ?? $proyecto?->pco_justificacion_duplicidad;

        if ($comId) {
            $verificacion = app(\App\Services\DetectorDuplicidadService::class)->verificarCoincidencia(
                (int) $comId,
                $request->equ_titulo,
                (int) $equipo->equ_id,
                $request->pco_id_tpr ? (int) $request->pco_id_tpr : ($proyecto?->pco_id_tpr ? (int) $proyecto->pco_id_tpr : null),
                $justifDuplicidad,
                $request->pco_id_mpr ? (int) $request->pco_id_mpr : ($proyecto?->pco_id_mpr ? (int) $proyecto->pco_id_mpr : null)
            );

            if (!$verificacion['es_valido']) {
                return back()->withErrors([
                    'justificacion_duplicidad' => $verificacion['mensaje_error'],
                    'equ_titulo'               => $verificacion['mensaje_error'],
                ]);
            }
        }

        DB::transaction(function () use ($request, $equipo, $proyecto, $justifDuplicidad) {
            $equipo->update([
                'equ_titulo'                   => $request->equ_titulo,
                'equ_justificacion_duplicidad' => $justifDuplicidad,
                'equ_id_usu_updated'           => auth()->id(),
            ]);

            if (!$proyecto) {
                if ($request->filled('pco_id_com')) {
                    $mprId = $request->pco_id_mpr ?: (\App\Models\ModalidadProyecto::where('mpr_nombre', 'Nuevo')->value('mpr_id') ?? \App\Models\ModalidadProyecto::first()?->mpr_id);
                    $tprId = $request->pco_id_tpr ?: (\App\Models\TipoProyecto::first()?->tpr_id);
                    $fechaLim = $request->filled('pco_fecha_limite') ? $request->pco_fecha_limite : now()->addMonths(6)->toDateString();

                    $proyecto = ProyectoComunidad::create([
                        'pco_id_equ'                  => $equipo->equ_id,
                        'pco_id_com'                  => $request->pco_id_com,
                        'pco_id_tpr'                  => $tprId,
                        'pco_id_mpr'                  => $mprId,
                        'pco_id_pco_previo'           => $request->pco_id_pco_previo ?: null,
                        'pco_resumen'                 => $request->pco_resumen ?? '',
                        'pco_justificacion_duplicidad'=> $justifDuplicidad,
                        'pco_fecha_limite'            => $fechaLim,
                        'pco_fecha_aprobacion'        => now()->toDateString(),
                        'pco_id_usu_aprobado'         => auth()->id(),
                        'pco_status'                  => true,
                        'pco_id_usu_created'          => auth()->id(),
                    ]);

                    $motivoHist = 'Proyecto asignado en la comunidad "' . (Comunidad::find($request->pco_id_com)?->com_nombre ?? '—') . '"';
                    if ($justifDuplicidad) {
                        $motivoHist .= ". Justificación coincidencia/duplicidad: {$justifDuplicidad}";
                    }

                    HistorialEquipo::create([
                        'heq_id_equ'         => $equipo->equ_id,
                        'heq_id_tee'         => $this->eventoId('Proyecto asignado'),
                        'heq_motivo'         => $motivoHist,
                        'heq_fecha_evento'   => now()->toDateString(),
                        'heq_id_usu_created' => auth()->id(),
                    ]);

                    // Inicializar recorrido de evaluación del trayecto (Semestres 1 y 2)
                    app(\App\Services\EvaluacionTrayectoService::class)->inicializarRecorridoProyecto(
                        (int) $proyecto->pco_id,
                        (int) $equipo->equ_id,
                        (int) $equipo->equ_id_tra
                    );
                }
                return;
            }

            $comunidadAnterior = (int) $proyecto->pco_id_com;

            $proyecto->update([
                'pco_id_com'                   => $request->pco_id_com ?: $proyecto->pco_id_com,
                'pco_id_tpr'                   => $request->pco_id_tpr ?: $proyecto->pco_id_tpr,
                'pco_id_mpr'                   => $request->pco_id_mpr ?: $proyecto->pco_id_mpr,
                'pco_resumen'                  => $request->pco_resumen ?? $proyecto->pco_resumen,
                'pco_justificacion_duplicidad' => $justifDuplicidad,
                'pco_fecha_limite'             => $request->pco_fecha_limite ?: $proyecto->pco_fecha_limite,
                'pco_comunidad_aprobada'       => ($request->filled('pco_id_com') || $proyecto->pco_id_com) ? true : $proyecto->pco_comunidad_aprobada,
                'pco_estado_postulacion'       => ($request->filled('pco_id_com') || $proyecto->pco_id_com) ? 'aprobada' : $proyecto->pco_estado_postulacion,
                'pco_fecha_aprobacion'         => $proyecto->pco_fecha_aprobacion ?: ($request->filled('pco_id_com') ? now()->toDateString() : null),
                'pco_id_usu_aprobado'          => $proyecto->pco_id_usu_aprobado ?: ($request->filled('pco_id_com') ? auth()->id() : null),
                'pco_id_usu_updated'           => auth()->id(),
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
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
        $this->autorizarEquipoId($id);
        $equipo = Equipo::findOrFail($id);

        DB::transaction(function () use ($equipo) {
            $equipoId = $equipo->equ_id;

            // 1. Notificaciones
            \App\Models\Notificacion::where('not_id_equ', $equipoId)->delete();

            // 2. Seguimiento y Asistencia de Puntos de Control
            $seqIds = \App\Models\SeguimientoEquipo::where('seq_id_equ', $equipoId)->pluck('seq_id');
            if ($seqIds->isNotEmpty()) {
                DB::table('asistencia_punto_control')->whereIn('apc_id_seq', $seqIds)->delete();
                \App\Models\SeguimientoEquipo::whereIn('seq_id', $seqIds)->delete();
            }

            // 3. Entregables y archivos
            $entregables = \App\Models\Entregable::where('entr_id_equ', $equipoId)->get();
            foreach ($entregables as $entr) {
                if ($entr->entr_ruta && \Illuminate\Support\Facades\Storage::disk('public')->exists($entr->entr_ruta)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($entr->entr_ruta);
                }
                $entr->delete();
            }

            // 4. Puntos de Control
            $pucIds = \App\Models\PuntoControl::where('puc_id_equ', $equipoId)->pluck('puc_id');
            if ($pucIds->isNotEmpty()) {
                DB::table('notificacion')->whereIn('not_id_puc', $pucIds)->delete();
                DB::table('punto_control_jurado')->whereIn('pcj_id_puc', $pucIds)->delete();
                \App\Models\PuntoControl::whereIn('puc_id', $pucIds)->delete();
            }

            // 5. Socializaciones
            $socIds = \App\Models\Socializacion::where('soc_id_equ', $equipoId)->pluck('soc_id');
            if ($socIds->isNotEmpty()) {
                DB::table('evidencia_socializacion')->whereIn('eso_id_soc', $socIds)->delete();
                DB::table('integrante_socializacion')->whereIn('iso_id_soc', $socIds)->delete();
                DB::table('jurado_socializacion')->whereIn('jso_id_soc', $socIds)->delete();
                \App\Models\Socializacion::whereIn('soc_id', $socIds)->delete();
            }

            // 6. Resultado Proyecto
            \App\Models\ResultadoProyecto::where('rpr_id_equ', $equipoId)->delete();

            // 7. Tutor
            DB::table('tutor')->where('tut_id_equ', $equipoId)->delete();

            // 8. Proyecto Comunidad y sus dependientes
            $pco = \App\Models\ProyectoComunidad::where('pco_id_equ', $equipoId)->first();
            if ($pco) {
                DB::table('carta_presentacion')->where('cpr_id_pco', $pco->pco_id)->delete();
                DB::table('proyecto_beneficiario')->where('pbe_id_pco', $pco->pco_id)->delete();
                DB::table('tutor_jurado')->where('tuj_id_pco', $pco->pco_id)->delete();
                DB::table('proyecto_comunidad')->where('pco_id_pco_previo', $pco->pco_id)->update(['pco_id_pco_previo' => null]);
                $pco->delete();
            }

            // 9. Envio Repositorio
            DB::table('envio_repositorio')->where('enr_id_equ', $equipoId)->delete();

            // 10. Historial Equipo
            \App\Models\HistorialEquipo::where('heq_id_equ', $equipoId)->delete();

            // 11. Líder
            $liderId = $equipo->integrantes()->where('ein_es_lider', true)->value('ein_id_usu')
                ?? $equipo->todosIntegrantes()->where('ein_es_lider', true)->value('ein_id_usu');

            // 12. Integrantes
            \App\Models\EquipoIntegrante::where('ein_id_equ', $equipoId)->delete();

            if ($liderId) {
                $this->revocarAccesoLider($liderId);
            }

            // 13. Eliminar Equipo definitivamente
            $equipo->delete();
        });

        return back()->with('success', 'Equipo eliminado definitivamente.');
    }

    

    public function aprobarComunidad(Request $request, $id)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Solo el Docente de Proyecto puede aprobar la postulación comunitaria.');
        $this->autorizarEquipoId($id);

        $equipo = Equipo::with('proyectoComunidad.comunidad')->findOrFail($id);
        $proyecto = $equipo->proyectoComunidad;

        if (!$proyecto || !$proyecto->pco_id_com) {
            return back()->withErrors(['comunidad' => 'El equipo aún no ha postulado una comunidad beneficiada.']);
        }

        $justifDuplicidad = $request->input('justificacion_duplicidad')
            ?? $proyecto->pco_justificacion_duplicidad
            ?? $equipo->equ_justificacion_duplicidad;

        $verificacion = app(\App\Services\DetectorDuplicidadService::class)->verificarCoincidencia(
            (int) $proyecto->pco_id_com,
            $equipo->equ_titulo,
            (int) $equipo->equ_id,
            $proyecto->pco_id_tpr ? (int) $proyecto->pco_id_tpr : null,
            $justifDuplicidad,
            $proyecto->pco_id_mpr ? (int) $proyecto->pco_id_mpr : null
        );

        if (!$verificacion['es_valido']) {
            return back()->withErrors([
                'justificacion_duplicidad' => $verificacion['mensaje_error'],
            ]);
        }

        DB::transaction(function () use ($equipo, $proyecto, $justifDuplicidad) {
            $proyecto->update([
                'pco_comunidad_aprobada'       => true,
                'pco_estado_postulacion'       => 'aprobada',
                'pco_fecha_aprobacion'         => now()->toDateString(),
                'pco_id_usu_aprobado'          => auth()->id(),
                'pco_justificacion_duplicidad' => $justifDuplicidad,
                'pco_id_usu_updated'           => auth()->id(),
            ]);

            if ($justifDuplicidad) {
                $equipo->update(['equ_justificacion_duplicidad' => $justifDuplicidad]);
            }

            app(\App\Services\EvaluacionTrayectoService::class)->inicializarRecorridoProyecto(
                (int) $proyecto->pco_id,
                (int) $equipo->equ_id,
                (int) ($equipo->equ_id_tra ?: 1)
            );

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Comunidad Aprobada'),
                'heq_motivo'         => 'Docente aprobó vinculación comunitaria en "' . ($proyecto->comunidad?->com_nombre ?? '—') . '". Entregables y Carta de Presentación desbloqueados.',
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            $liderId = EquipoIntegrante::where('ein_id_equ', $equipo->equ_id)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->value('ein_id_usu');

            if ($liderId) {
                Notificacion::create([
                    'not_id_usu'           => $liderId,
                    'not_id_tno'           => TipoNotificacion::first()?->tno_id ?? 1,
                    'not_id_equ'           => $equipo->equ_id,
                    'not_mensaje'          => "¡Vinculación comunitaria aprobada! Ya puedes solicitar la Carta de Presentación y cargar entregables para {$equipo->equ_codigo}.",
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                ]);
            }
        });

        return back()->with('success', 'Postulación comunitaria aprobada. Se habilitaron los entregables y la Carta de Presentación para el equipo.');
    }

    public function rechazarComunidad(Request $request, $id)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Solo el Docente de Proyecto puede observar/devolver la postulación.');
        $this->autorizarEquipoId($id);

        $request->validate([
            'motivo' => 'required|string|min:5|max:500',
        ], [
            'motivo.required' => 'Debe indicar el motivo o corrección solicitada al equipo.',
        ]);

        $equipo = Equipo::with('proyectoComunidad')->findOrFail($id);
        $proyecto = $equipo->proyectoComunidad;
        if (!$proyecto) {
            return back()->withErrors(['comunidad' => 'El equipo no tiene postulación comunitaria registrada.']);
        }

        DB::transaction(function () use ($equipo, $proyecto, $request) {
            $proyecto->update([
                'pco_comunidad_aprobada' => false,
                'pco_estado_postulacion' => 'rechazada',
                'pco_fecha_aprobacion'   => null,
                'pco_id_usu_updated'     => auth()->id(),
            ]);

            HistorialEquipo::create([
                'heq_id_equ'         => $equipo->equ_id,
                'heq_id_tee'         => $this->eventoId('Postulación Observada'),
                'heq_motivo'         => 'Postulación comunitaria devuelta para ajuste: ' . $request->motivo,
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            $liderId = EquipoIntegrante::where('ein_id_equ', $equipo->equ_id)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->value('ein_id_usu');

            if ($liderId) {
                Notificacion::create([
                    'not_id_usu'           => $liderId,
                    'not_id_tno'           => TipoNotificacion::first()?->tno_id ?? 1,
                    'not_id_equ'           => $equipo->equ_id,
                    'not_mensaje'          => "Tu postulación comunitaria requiere ajustes: {$request->motivo}",
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                ]);
            }
        });

        return back()->with('success', 'Se devolvió la postulación comunitaria al líder para corrección.');
    }

    public function removerIntegrante(Request $request, $equipoId, $integranteId)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
        $this->autorizarEquipoId($equipoId);

        $request->validate([
            'motivo_baja' => 'required|string|min:5|max:500',
        ], [
            'motivo_baja.required' => 'El motivo de retiro o baja justificada es obligatorio.',
            'motivo_baja.min'      => 'El motivo debe tener al menos 5 caracteres.',
        ]);

        $integrante = EquipoIntegrante::where('ein_id', $integranteId)
            ->where('ein_id_equ', $equipoId)
            ->where('ein_status', true)
            ->with('usuario')
            ->firstOrFail();

        if ($integrante->ein_es_lider) {
            return back()->withErrors(['integrante' => 'No puedes desvincular al líder directamente. Primero asigna un nuevo líder.']);
        }

        $nombre = trim(($integrante->usuario?->usu_primer_nombre ?? '') . ' ' . ($integrante->usuario?->usu_primer_apellido ?? ''));
        $motivoBaja = trim($request->motivo_baja);

        DB::transaction(function () use ($equipoId, $integrante, $nombre, $motivoBaja) {
            $integrante->update([
                'ein_status'         => false,
                'ein_fecha_salida'   => now()->toDateString(),
                'ein_motivo_baja'    => $motivoBaja,
                'ein_id_usu_updated' => auth()->id(),
            ]);

            $equipo = Equipo::findOrFail($equipoId);
            $restantes = EquipoIntegrante::where('ein_id_equ', $equipoId)
                ->where('ein_status', true)
                ->count();

            // Si queda por debajo del mínimo reglamentario (3), activar bandera de excepción sin bloquear
            if ($restantes < 3) {
                $equipo->update([
                    'equ_justificacion_cupo'        => true,
                    'equ_justificacion_cupo_motivo' => "Excepción docente por baja justificada de {$nombre}: {$motivoBaja}",
                    'equ_id_usu_updated'            => auth()->id(),
                ]);
            }

            HistorialEquipo::create([
                'heq_id_equ'         => $equipoId,
                'heq_id_tee'         => $this->eventoId('Baja Justificada'),
                'heq_motivo'         => "Baja justificada de {$nombre}. Motivo: {$motivoBaja}" . ($restantes < 3 ? " (Activada excepción de cupo: {$restantes} integrante(s) activo(s))." : ''),
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            $lider = EquipoIntegrante::where('ein_id_equ', $equipoId)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->first();

            if ($lider) {
                Notificacion::create([
                    'not_id_usu'           => $lider->ein_id_usu,
                    'not_id_tno'           => TipoNotificacion::first()?->tno_id ?? 1,
                    'not_id_equ'           => $equipoId,
                    'not_mensaje'          => "El integrante {$nombre} fue desvinculado de tu equipo ({$equipo->equ_codigo}). Motivo: {$motivoBaja}",
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                    'not_status'           => true,
                    'not_id_usu_created'   => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'Desvinculación registrada correctamente.');
    }

    public function trasladarIntegrante(Request $request, $equipoId, $integranteId)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
        $this->autorizarEquipoId($equipoId);

        $request->validate([
            'nuevo_equipo_id'       => 'required|exists:equipo,equ_id',
            'nuevo_lider_origen_id' => 'nullable|integer',
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

        $equipoOrigen = Equipo::findOrFail($equipoId);
        $equipoDestino = Equipo::with(['integrantes', 'trayecto'])->findOrFail($request->nuevo_equipo_id);

        $this->autorizarEquipoId($equipoDestino->equ_id);

        if ((int) $equipoOrigen->equ_id_sec !== (int) $equipoDestino->equ_id_sec) {
            return back()->withErrors(['integrante' => 'Solo se permite trasladar estudiantes hacia otro equipo de la misma sección académica.']);
        }

        // Si el estudiante trasladado era Líder, exigir asignar un nuevo líder en el equipo origen
        $nuevoLiderOrigen = null;
        if ($integrante->ein_es_lider) {
            $otrosActivos = EquipoIntegrante::where('ein_id_equ', $equipoId)
                ->where('ein_status', true)
                ->where('ein_id', '!=', $integrante->ein_id)
                ->get();

            if ($otrosActivos->isEmpty()) {
                return back()->withErrors([
                    'nuevo_lider_origen_id' => 'El líder es el único integrante del equipo origen. Debe haber al menos otro integrante para asumir el liderazgo antes de trasladarlo.',
                ]);
            }

            if (!$request->filled('nuevo_lider_origen_id')) {
                return back()->withErrors([
                    'nuevo_lider_origen_id' => 'El estudiante seleccionado es el Líder actual. Debes designar un nuevo Líder para el equipo origen antes de trasladarlo.',
                ]);
            }

            $nuevoLiderOrigen = $otrosActivos->firstWhere('ein_id', (int) $request->nuevo_lider_origen_id);
            if (!$nuevoLiderOrigen) {
                return back()->withErrors([
                    'nuevo_lider_origen_id' => 'El nuevo líder seleccionado no pertenece activamente al equipo origen.',
                ]);
            }
        }

        $integrantesDestino = $equipoDestino->integrantes->where('ein_status', true);
        if ($integrantesDestino->count() >= 6) {
            return back()->withErrors(['integrante' => "El equipo destino {$equipoDestino->equ_codigo} ya tiene el máximo de 6 integrantes."]);
        }

        if ($integrantesDestino->contains('ein_id_usu', $integrante->ein_id_usu)) {
            return back()->withErrors(['integrante' => 'El estudiante ya pertenece al equipo de destino.']);
        }

        // Candado de Prelación al trasladar a un equipo de trayecto superior
        $traDestinoNum = (int) ($equipoDestino->trayecto?->tra_numero ?? 1);
        if ($traDestinoNum > 1) {
            $evalService = app(\App\Services\EvaluacionTrayectoService::class);
            $errorPrelacion = $evalService->verificarPrelacionEstudiante((int) $integrante->ein_id_usu, $traDestinoNum);
            if ($errorPrelacion) {
                return back()->withErrors(['integrante' => $errorPrelacion]);
            }
        }

        DB::transaction(function () use ($integrante, $equipoOrigen, $equipoDestino, $nuevoLiderOrigen) {
            if ($integrante->ein_es_lider && $nuevoLiderOrigen) {
                $nuevoLiderUsuario = Usuario::findOrFail($nuevoLiderOrigen->ein_id_usu);
                $nuevoLiderOrigen->update(['ein_es_lider' => true, 'ein_id_usu_updated' => auth()->id()]);
                $this->otorgarAccesoLider($nuevoLiderUsuario);
                $this->revocarAccesoLider($integrante->ein_id_usu);

                HistorialEquipo::create([
                    'heq_id_equ'         => $equipoOrigen->equ_id,
                    'heq_id_tee'         => $this->eventoId('Líder Cambiado'),
                    'heq_motivo'         => 'Nuevo líder designado por traslado del anterior: ' . $nuevoLiderUsuario->usu_primer_nombre . ' ' . $nuevoLiderUsuario->usu_primer_apellido,
                    'heq_fecha_evento'   => now()->toDateString(),
                    'heq_id_usu_created' => auth()->id(),
                ]);
            }

            $integrante->update([
                'ein_status'         => false,
                'ein_es_lider'       => false,
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

            $nombre = trim(($integrante->usuario?->usu_primer_nombre ?? '') . ' ' . ($integrante->usuario?->usu_primer_apellido ?? ''));
            $eventoTrasladoId = $this->eventoId('Traslado de Equipo');

            HistorialEquipo::create([
                'heq_id_equ'         => $equipoOrigen->equ_id,
                'heq_id_tee'         => $eventoTrasladoId,
                'heq_motivo'         => "Traslado de Equipo: {$nombre} fue trasladado desde {$equipoOrigen->equ_codigo} hacia {$equipoDestino->equ_codigo}.",
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            HistorialEquipo::create([
                'heq_id_equ'         => $equipoDestino->equ_id,
                'heq_id_tee'         => $eventoTrasladoId,
                'heq_motivo'         => "Traslado de Equipo: {$nombre} fue incorporado desde {$equipoOrigen->equ_codigo} hacia {$equipoDestino->equ_codigo}.",
                'heq_fecha_evento'   => now()->toDateString(),
                'heq_id_usu_created' => auth()->id(),
            ]);

            // Notificación al líder del equipo de origen
            $liderOrigen = EquipoIntegrante::where('ein_id_equ', $equipoOrigen->equ_id)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->first();

            if ($liderOrigen) {
                Notificacion::create([
                    'not_id_usu'           => $liderOrigen->ein_id_usu,
                    'not_id_tno'           => TipoNotificacion::first()?->tno_id ?? 1,
                    'not_id_equ'           => $equipoOrigen->equ_id,
                    'not_mensaje'          => "El integrante {$nombre} fue trasladado de tu equipo ({$equipoOrigen->equ_codigo}) al equipo {$equipoDestino->equ_codigo}.",
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                    'not_status'           => true,
                    'not_id_usu_created'   => auth()->id(),
                ]);
            }

            // Notificación al líder del equipo de destino
            $liderDestino = EquipoIntegrante::where('ein_id_equ', $equipoDestino->equ_id)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->first();

            if ($liderDestino) {
                Notificacion::create([
                    'not_id_usu'           => $liderDestino->ein_id_usu,
                    'not_id_tno'           => TipoNotificacion::first()?->tno_id ?? 1,
                    'not_id_equ'           => $equipoDestino->equ_id,
                    'not_mensaje'          => "El estudiante {$nombre} fue incorporado a tu equipo ({$equipoDestino->equ_codigo}) por traslado desde el equipo {$equipoOrigen->equ_codigo}.",
                    'not_leida'            => false,
                    'not_fecha_generacion' => now(),
                    'not_status'           => true,
                    'not_id_usu_created'   => auth()->id(),
                ]);
            }
        });

        return back()->with('success', "Integrante trasladado correctamente al equipo {$equipoDestino->equ_codigo}.");
    }

    

    public function historial(Request $request, $equipoId)
    {
        $this->autorizarEquipoId($equipoId);
        $equipo = Equipo::with([
            'historial.tipoEvento',
            'historial' => fn($q) => $q->orderBy('heq_fecha_evento', 'desc'),
        ])->findOrFail($equipoId);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json($equipo->historial);
        }

        $user = auth()->user();
        $prefix = $user && $user->esProfesor() ? 'profesor' : 'coordinador';
        return redirect()->to("/{$prefix}/equipos/{$equipoId}/expediente");
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

        $equiposSec = \App\Models\Equipo::with([
            'seccion.trayecto','trayecto','integrantes.usuario',
            'proyectoComunidad.comunidad.tipoOrganizacion',
            'proyectoComunidad.comunidad.responsables' => fn($q) => $q->where('rco_status', true)->with(['usuario.cargos' => fn($c) => $c->where('pca_status', true)]),
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
            'socializaciones.tipoSocializacion',
            'resultadoProyecto',
        ])->where('equ_id_sec', $id)
        ->where('equ_status', true)
        ->orderBy('equ_codigo')->get();

        $diagnosticosEquipos = app(\App\Services\DiagnosticoProyectoService::class)->diagnosticarSeccion(
            $equiposSec->pluck('equ_id')->toArray()
        );

        return \Inertia\Inertia::render('Profesor/MisEquipos', [
            'seccion'          => [
                'sec_id'     => $seccion->sec_id,
                'sec_codigo' => $seccion->sec_codigo,
                'trayecto'   => $seccion->trayecto?->tra_nombre,
                'turno'      => $seccion->turno?->tur_nombre,
            ],
            'equipos'             => $equiposSec,
            'diagnosticosEquipos' => $diagnosticosEquipos,
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
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
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

            $codigoArchivado = str_contains($equipo->equ_codigo, '#DIS')
                ? $equipo->equ_codigo
                : ($equipo->equ_codigo . '#DIS' . $equipo->equ_id);

            $equipo->update([
                'equ_codigo'         => $codigoArchivado,
                'equ_status'         => false,
                'equ_id_usu_updated' => auth()->id()
            ]);

            // Desactivar puntos de control y entregables del equipo disuelto
            $equipo->puntosControl()->update(['puc_status' => false]);
            $equipo->entregables()->whereNull('entr_aprobado')->update(['entr_status' => false]);

            // Desactivar o marcar notificaciones pendientes asociadas al equipo
            \App\Models\Notificacion::where('not_id_equ', $equipo->equ_id)->update(['not_leida' => true]);

            if ($liderId) {
                $this->revocarAccesoLider($liderId);
            }
        });

        return back()->with('success', 'Equipo disuelto correctamente.');
    }

    public function cambiarLider(Request $request, $equipoId)
    {
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
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
        abort_unless(auth()->user()?->esProfesor(), 403, 'Acción exclusiva del Docente de Proyecto.');
        $equipo = Equipo::where('equ_status', false)->findOrFail($id);
        $this->autorizarEquipoId($equipo->equ_id);

        $codigoOriginal = preg_replace('/#DIS\d+$/', '', $equipo->equ_codigo);
        $enUso = Equipo::where('equ_codigo', $codigoOriginal)->where('equ_status', true)->exists();
        if ($enUso) {
            return back()->withErrors(['equipo' => "No se puede reactivar: el código {$codigoOriginal} ya está siendo utilizado por otro equipo activo."]);
        }

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

        DB::transaction(function () use ($equipo, $porRestaurar, $request, $codigoOriginal) {
            $equipo->update([
                'equ_codigo'         => $codigoOriginal,
                'equ_status'         => true,
                'equ_id_usu_updated' => auth()->id()
            ]);

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

        return back()->with('success', "Equipo {$codigoOriginal} reactivado correctamente.");
    }

    public function historialAcademico(Request $request, $id)
    {
        $user = auth()->user();

        $equipo = Equipo::with([
            'seccion.trayecto.pnf',
            'trayecto',
            'integrantes.usuario',
            'proyectoComunidad.comunidad',
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
            'puntosControl.seguimientos.asistencias.usuario',
            'puntosControl.jurados.usuario',
            'entregables.tipoEntregable',
            'entregables.puntoControl',
            'socializaciones.tipoSocializacion',
            'socializaciones.jurados.usuario',
            'socializaciones.integrantes.usuario',
            'resultadoProyecto.estadoProyecto',
        ])->findOrFail($id);

        if ($user->esProfesor()) {
            $seccionesProfesor = $user->seccionesProfesor()->where('pse_status', true)->pluck('pse_id_sec');
            abort_unless($seccionesProfesor->contains($equipo->equ_id_sec), 403, 'No tienes acceso a este equipo.');
        }

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json(['equipo' => $equipo]);
        }

        $prefix = $user && $user->esProfesor() ? 'profesor' : 'coordinador';
        return redirect()->to("/{$prefix}/equipos/{$id}/expediente");
    }
}