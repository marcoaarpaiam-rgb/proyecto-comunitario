<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\PuntoControl;
use App\Models\Equipo;
use App\Models\Trayecto;
use App\Models\SeguimientoEquipo;
use App\Models\AsistenciaPuntoControl;
use App\Models\Usuario;
use App\Models\TipoEncuentro;
use App\Models\AcuerdoEncuentro;
use App\Services\DiagnosticoProyectoService;
use App\Http\Requests\StorePuntoControlRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;

class PuntoControlController extends Controller
{   
    use AutorizaPorSeccion;

    protected DiagnosticoProyectoService $diagnosticoService;

    public function __construct(DiagnosticoProyectoService $diagnosticoService)
    {
        $this->diagnosticoService = $diagnosticoService;
    }

    public function index()
    {
        $profesor  = auth()->user();
        $profSecciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->with(['seccion.trayecto', 'seccion.turno'])
            ->get();
        $seccionesIds = $profSecciones->pluck('pse_id_sec');

        $equipos = Equipo::whereIn('equ_id_sec', $seccionesIds)
            ->where('equ_status', true)
            ->with(['trayecto', 'seccion', 'integrantes.usuario'])
            ->get();

        $puntos = PuntoControl::whereIn('puc_id_equ', $equipos->pluck('equ_id'))
            ->where('puc_status', true)
            ->with([
                'equipo.integrantes.usuario',
                'trayecto',
                'seguimientos.asistencias.usuario',
                'seguimientos.asistencias.usuarioUpdated',
                'seguimientos.acuerdos',
                'seguimientos.tipoEncuentro'
            ])
            ->orderBy('puc_fecha_limite')
            ->get();

        // Enlazar observaciones del punto anterior de cada equipo
        $puntosPorEquipo = $puntos->groupBy('puc_id_equ');
        foreach ($puntosPorEquipo as $equId => $puntosDelEquipo) {
            $sorted = $puntosDelEquipo->sortBy('puc_orden')->values();
            for ($i = 0; $i < $sorted->count(); $i++) {
                if ($i > 0) {
                    $puntoAnterior = $sorted[$i - 1];
                    $seqAnt = $puntoAnterior->seguimientos->first();
                    $sorted[$i]->punto_previo_nombre = $puntoAnterior->puc_nombre;
                    $sorted[$i]->punto_previo_observaciones = $seqAnt?->seq_observaciones;
                } else {
                    $sorted[$i]->punto_previo_nombre = null;
                    $sorted[$i]->punto_previo_observaciones = null;
                }
            }
        }

        // Estimar semana actual de la fase (1 a 18) con base en el período académico activo o último punto registrado
        $semanaActual = $this->diagnosticoService->calcularSemanaActualPeriodo();
        $maxSemanaRegistrada = $puntos->max('puc_semana');
        if ($semanaActual <= 1 && $maxSemanaRegistrada) {
            $semanaActual = min(18, max(1, (int) $maxSemanaRegistrada));
        }

        // Diagnósticos de ruta crítica y semáforo de equipos
        $diagnosticosEquipos = $this->diagnosticoService->diagnosticarSeccion(
            $equipos->pluck('equ_id')->toArray(),
            $semanaActual
        );

        // Esquemas de evaluación asignados a las secciones del profesor
        $esquemaPorDefecto = \App\Models\EsquemaEvaluacion::where('esq_activo', true)
            ->where('esq_es_plantilla', true)
            ->first();

        $baremoPorDefecto = [
            ['letra' => 'A', 'nivel' => 'Excelente', 'rango' => '19-20', 'aprobado' => true],
            ['letra' => 'B', 'nivel' => 'Bueno / Aprobado', 'rango' => '16-18', 'aprobado' => true],
            ['letra' => 'C', 'nivel' => 'En Proceso / Nivelación', 'rango' => '12-15', 'aprobado' => false],
            ['letra' => 'D', 'nivel' => 'Deficiente', 'rango' => '01-11', 'aprobado' => false],
        ];

        $esquemasPorSeccion = [];
        foreach ($profSecciones as $ps) {
            $secId = $ps->pse_id_sec;
            $numSemestreSec = $ps->seccion ? $ps->seccion->getSemestreNumero() : 1;
            $esImparSec = ($numSemestreSec % 2 !== 0);

            $esq = \Illuminate\Support\Facades\DB::table('esquema_seccion as es')
                ->join('esquemas_evaluacion as ee', 'es.esq_sec_id_esq', '=', 'ee.esq_id')
                ->where('es.esq_sec_id_sec', $secId)
                ->where('es.esq_sec_activo', 1)
                ->where('ee.esq_activo', 1)
                ->select(
                    'ee.esq_id',
                    'ee.esq_nombre',
                    'ee.esq_modo',
                    'ee.esq_escala_min',
                    'ee.esq_escala_max',
                    'ee.esq_nota_aprobatoria',
                    'ee.esq_criterios',
                    'es.esq_sec_lapso'
                )
                ->first();

            $tieneEsquemaConfigurado = (bool) $esq;
            
            // Esquema recomendado según paridad del semestre
            $esquemaRecomendado = \App\Models\EsquemaEvaluacion::where('esq_activo', true)
                ->where('esq_modo', $esImparSec ? 'cualitativo_respaldado' : 'cuantitativo')
                ->orderBy('esq_es_plantilla', 'desc')
                ->first() ?: $esquemaPorDefecto;

            $rawCriterios = $esq?->esq_criterios ?? $esquemaRecomendado?->esq_criterios;
            $decoded = is_string($rawCriterios) ? json_decode($rawCriterios, true) : ($rawCriterios ?: []);
            $criterios = (is_array($decoded) && array_key_exists('criterios', $decoded))
                ? ($decoded['criterios'] ?? [])
                : (is_array($decoded) ? $decoded : []);
            $baremo = (is_array($decoded) && !empty($decoded['baremo']))
                ? $decoded['baremo']
                : ($esImparSec || ($esq?->esq_modo === 'cualitativo_respaldado') ? $baremoPorDefecto : []);

            $esquemasPorSeccion[$secId] = [
                'esq_id'                    => $esq->esq_id ?? $esquemaRecomendado?->esq_id,
                'nombre'                    => $esq->esq_nombre ?? $esquemaRecomendado?->esq_nombre ?? ($esImparSec ? 'Evaluación Cualitativa Literal PNFI' : 'Evaluación Cuantitativa PST UPTP'),
                'modo'                      => $esq->esq_modo ?? ($esImparSec ? 'cualitativo_respaldado' : 'cuantitativo'),
                'escala_min'                => $esq->escala_min ?? $esquemaRecomendado?->esq_escala_min ?? 1,
                'escala_max'                => $esq->escala_max ?? $esquemaRecomendado?->esq_escala_max ?? 20,
                'nota_aprobatoria'          => $esq->nota_aprobatoria ?? $esquemaRecomendado?->esq_nota_aprobatoria ?? 16,
                'lapso'                     => $esq->esq_sec_lapso ?? \App\Models\PeriodoAcademico::lapsoActivoTexto(),
                'criterios'                 => $criterios,
                'baremo'                    => $baremo,
                'es_semestre_impar'         => $esImparSec,
                'semestre_numero'           => $numSemestreSec,
                'tiene_esquema_configurado' => $tieneEsquemaConfigurado,
                'id_esquema_recomendado'    => $esquemaRecomendado?->esq_id,
            ];
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('tipos_encuentro_puntual')
            && TipoEncuentro::where('tep_activo', true)->count() === 0) {
            (new \Database\Seeders\TipoEncuentroSeeder())->run();
        }

        $tiposEncuentro = \Illuminate\Support\Facades\Schema::hasTable('tipos_encuentro_puntual')
            ? TipoEncuentro::where('tep_activo', true)->orderBy('tep_id')->get()
            : collect([]);

        return Inertia::render('Profesor/PuntosControl', [
            'puntosControl'       => $puntos,
            'equipos'             => $equipos,
            'secciones'           => $profSecciones->map(fn($ps) => $ps->seccion)->filter()->values(),
            'trayectos'           => Trayecto::where('tra_status', true)->get(),
            'diagnosticosEquipos' => $diagnosticosEquipos,
            'semanaActual'        => $semanaActual,
            'tiposEncuentro'      => $tiposEncuentro,
            'esquemasPorSeccion'  => $esquemasPorSeccion,
            'plantillasEsquemas'  => \App\Models\EsquemaEvaluacion::where('esq_activo', true)->get(),
            'tiposEntregable'     => \App\Models\TipoEntregableTrayecto::where('tet_status', true)->get(),
        ]);
    }

    public function store(StorePuntoControlRequest $request)
    {
        $this->autorizarEquipoId($request->puc_id_equ);

        // Obtener el trayecto del equipo automáticamente
        $equipo = \App\Models\Equipo::findOrFail($request->puc_id_equ);

        $ultimoOrden = PuntoControl::where('puc_id_equ', $request->puc_id_equ)
            ->where('puc_status', true)
            ->max('puc_orden') ?? 0;

        // Traer observaciones del punto anterior como criterios si existen
        $puntoPrevio = PuntoControl::where('puc_id_equ', $request->puc_id_equ)
            ->where('puc_status', true)
            ->with(['seguimientos'])
            ->orderBy('puc_orden', 'desc')
            ->first();

        $criteriosPrevios = [];
        if ($puntoPrevio) {
            $seqPrevio = $puntoPrevio->seguimientos->first();
            if ($seqPrevio && !empty($seqPrevio->seq_observaciones)) {
                $criteriosPrevios[] = [
                    'origen' => "Observaciones del Punto previo ({$puntoPrevio->puc_nombre})",
                    'texto'  => $seqPrevio->seq_observaciones,
                ];
            }
        }

        $punto = PuntoControl::create([
            'puc_id_equ'          => $request->puc_id_equ,
            'puc_id_tra'          => $equipo->equ_id_tra,
            'puc_nombre'          => $request->puc_nombre,
            'puc_descripcion'     => $request->puc_descripcion,
            'puc_semana'          => $request->puc_semana ?? ($ultimoOrden == 0 ? 6 : ($ultimoOrden == 1 ? 10 : 14)),
            'puc_criterios_evaluacion' => $criteriosPrevios,
            'puc_fecha_limite'    => $request->puc_fecha_limite,
            'puc_orden'           => $ultimoOrden + 1,
            'puc_dias_aviso'      => $request->puc_dias_aviso ?? 7,
            'puc_es_entregable'   => $request->puc_es_entregable ?? false,
            'puc_es_ultimo_punto' => $request->boolean('puc_es_ultimo_punto'),
            'puc_status'          => true,
            'puc_id_usu_created'  => auth()->id(),
        ]);

        // Notificar al líder del equipo
        $lider = \App\Models\EquipoIntegrante::where('ein_id_equ', $request->puc_id_equ)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();
        if ($lider) {
            $tnoId = \App\Models\TipoNotificacion::where('tno_nombre', 'like', '%punto%')
                ->orWhere('tno_nombre', 'like', '%control%')
                ->value('tno_id') ?? \App\Models\TipoNotificacion::first()?->tno_id;

            \App\Models\Notificacion::create([
                'not_id_usu'           => $lider->ein_id_usu,
                'not_id_tno'           => $tnoId,
                'not_id_equ'           => $request->puc_id_equ,
                'not_id_puc'           => $punto->puc_id,
                'not_mensaje'          => "Se ha programado el Punto de Control: \"{$punto->puc_nombre}\" con día de encuentro: {$punto->puc_fecha_limite}.",
                'not_leida'            => false,
                'not_fecha_generacion' => now(),
            ]);
        }

        return back()->with('success', 'Punto de control creado correctamente.');
    }

    public function registrarSeguimiento(Request $request, $id)
    {
        $rules = [
            'seq_cumplido'                 => 'required|boolean',
            'seq_observaciones'            => $request->seq_cumplido ? 'nullable|string' : 'required|string|min:4',
            'seq_cumplio_previo'           => 'nullable|boolean',
            'seq_obs_previas_verificacion' => 'nullable|string',
            'seq_estado_semaforo'          => 'nullable|in:conforme,observado,no_conforme,no_viable,en_ruta,acelerar,riesgo_alto,inviable,positivo,neutral,negativo',
            'seq_valoracion_avance'        => 'nullable|integer|min:0|max:100',
            'seq_valoracion_literal'       => 'nullable|string|max:10',
            'seq_nivel_logro'              => 'nullable|string|max:100',
            'seq_equivalencia_referencial' => 'nullable|string|max:50',
            'seq_aprobado_cualitativo'     => 'nullable|boolean',
            'seq_id_tipo_encuentro'        => 'nullable|exists:tipos_encuentro_puntual,tep_id',
            'acuerdos'                     => 'nullable|array',
            'asistencias'                  => 'nullable',
            'desea_reprogramar'            => 'nullable|boolean',
            'nueva_fecha'                  => 'nullable|date',
            'nueva_semana'                 => 'nullable|integer|min:1|max:18',
            'es_ultimo_punto'              => 'nullable|boolean',
            'siguiente_puc_nombre'         => 'nullable|string|max:150',
            'siguiente_puc_fecha'          => 'nullable|date',
            'siguiente_puc_semana'         => 'nullable|integer|min:1|max:18',
        ];

        $messages = [
            'seq_cumplido.required'      => 'Debe indicar si el equipo cumplió o no con este punto de control.',
            'seq_observaciones.required' => 'Debe justificar el motivo por el cual no se cumplió el punto de control.',
            'seq_observaciones.min'      => 'La justificación debe tener al menos 4 caracteres.',
        ];

        $request->validate($rules, $messages);

        $punto = PuntoControl::findOrFail($id);

        $this->autorizarEquipoId($punto->puc_id_equ);

        // Validar programación del siguiente punto de control (si se proporcionó y no es último punto)
        $crearSiguiente = $request->boolean('seq_cumplido')
            && !$request->boolean('es_ultimo_punto')
            && ($request->filled('siguiente_puc_nombre') || $request->filled('siguiente_puc_fecha'));

        if ($crearSiguiente) {
            if (!$request->filled('siguiente_puc_nombre') || !$request->filled('siguiente_puc_fecha')) {
                return back()->withErrors([
                    'siguiente_puc_fecha' => 'Para programar el siguiente punto de control debes indicar tanto el nombre como la fecha límite.',
                ]);
            }
            $hoy = now()->toDateString();
            if ($request->siguiente_puc_fecha < $hoy) {
                return back()->withErrors([
                    'siguiente_puc_fecha' => 'La fecha del siguiente punto de control no puede ser anterior a hoy.',
                ]);
            }
            if ($errSig = \App\Models\PeriodoAcademico::validarFechaEnRango($request->siguiente_puc_fecha)) {
                return back()->withErrors(['siguiente_puc_fecha' => $errSig]);
            }
        }

        // Si no cumplió y el profesor seleccionó reprogramar
        if (!$request->seq_cumplido && $request->boolean('desea_reprogramar')) {
            if (!$request->filled('nueva_fecha')) {
                return back()->withErrors(['nueva_fecha' => 'Debe indicar la nueva fecha para reprogramar el punto de control.']);
            }
            $hoy = now()->toDateString();
            $maxFechaReprog = now()->addMonth()->toDateString();
            if ($request->nueva_fecha < $hoy) {
                return back()->withErrors(['nueva_fecha' => 'La fecha de reprogramación no puede ser en el pasado (debe ser a partir de hoy).']);
            }
            if ($request->nueva_fecha > $maxFechaReprog) {
                return back()->withErrors(['nueva_fecha' => 'La fecha de reprogramación no puede exceder el límite de un mes a partir de hoy.']);
            }
            if ($errFecha = \App\Models\PeriodoAcademico::validarFechaEnRango($request->nueva_fecha)) {
                return back()->withErrors(['nueva_fecha' => $errFecha]);
            }
        }

        // Permitir registrar o actualizar el seguimiento del punto de control
        $seguimientoExistente = SeguimientoEquipo::where('seq_id_equ', $punto->puc_id_equ)
            ->where('seq_id_puc', $punto->puc_id)
            ->first();

        $estadoSemaforo = $request->seq_cumplido
            ? ($request->seq_estado_semaforo ?? 'conforme')
            : ($request->seq_estado_semaforo ?? 'no_conforme');

        if ($request->seq_cumplido && $request->filled('seq_valoracion_literal')) {
            $estadoSemaforo = $this->diagnosticoService->traducirDesempenoCualitativoASemaforo(
                $request->seq_valoracion_literal,
                $request->has('seq_aprobado_cualitativo') ? $request->boolean('seq_aprobado_cualitativo') : null,
                $request->seq_nivel_logro
            );
        }

        $seq = SeguimientoEquipo::updateOrCreate(
            ['seq_id_equ' => $punto->puc_id_equ, 'seq_id_puc' => $punto->puc_id],
            [
                'seq_cumplido'                 => $request->seq_cumplido,
                'seq_fecha_cumplimiento'       => $request->seq_cumplido ? now()->toDateString() : null,
                'seq_cumplido_a_tiempo'        => $request->seq_cumplido
                    ? (now()->toDateString() <= $punto->puc_fecha_limite)
                    : false,
                'seq_cumplio_previo'           => $request->seq_cumplido ? $request->seq_cumplio_previo : false,
                'seq_obs_previas_verificacion' => $request->seq_cumplido ? $request->seq_obs_previas_verificacion : null,
                'seq_observaciones'            => $request->seq_observaciones,
                'seq_estado_semaforo'          => $estadoSemaforo,
                'seq_valoracion_avance'        => $request->seq_cumplido ? $request->seq_valoracion_avance : 0,
                'seq_valoracion_literal'       => $request->seq_cumplido ? $request->seq_valoracion_literal : null,
                'seq_nivel_logro'              => $request->seq_cumplido ? $request->seq_nivel_logro : null,
                'seq_equivalencia_referencial' => $request->seq_cumplido ? $request->seq_equivalencia_referencial : null,
                'seq_aprobado_cualitativo'     => $request->seq_cumplido && $request->has('seq_aprobado_cualitativo') ? $request->boolean('seq_aprobado_cualitativo') : null,
                'seq_id_tipo_encuentro'        => $request->seq_cumplido ? $request->seq_id_tipo_encuentro : null,
                'seq_fecha_registro'           => now()->toDateString(),
                'seq_id_usu_updated'           => auth()->id(),
            ]
        );

        // Si no cumplió y se marcó reprogramar, actualizar la reprogramación del punto de control
        if (!$request->seq_cumplido && $request->boolean('desea_reprogramar')) {
            $punto->update([
                'puc_fecha_reprogramada'    => $request->nueva_fecha,
                'puc_semana'                => $request->filled('nueva_semana') ? $request->nueva_semana : $punto->puc_semana,
                'puc_motivo_reprogramacion' => $request->seq_observaciones ?: 'Reprogramado tras no cumplimiento en fecha original',
                'puc_reprogramado'          => true,
                'puc_id_usu_reprogramo'     => auth()->id(),
                'puc_fecha_reprogramacion'  => now(),
            ]);

            $fechaFormateada = \Carbon\Carbon::parse($request->nueva_fecha)->format('d/m/Y');
            return back()->with('success', "Punto de control marcado como no cumplido y reprogramado exitosamente para el {$fechaFormateada}.");
        }

        // Si no cumplió y no se reprogramó
        if (!$request->seq_cumplido) {
            return back()->with('success', 'Punto de control evaluado y registrado como no cumplido.');
        }

        // Registro y actualización de acuerdos / compromisos del encuentro
        if ($request->has('acuerdos') && is_array($request->acuerdos)) {
            foreach ($request->acuerdos as $acuerdo) {
                if (empty($acuerdo['descripcion'])) continue;
                $estadoAcuerdo = in_array($acuerdo['estado'] ?? '', ['pendiente', 'en_proceso', 'rectificado'], true)
                    ? $acuerdo['estado']
                    : 'pendiente';

                if (!empty($acuerdo['acue_id'])) {
                    AcuerdoEncuentro::where('acue_id', $acuerdo['acue_id'])
                        ->where('acue_id_seq', $seq->seq_id)
                        ->update([
                            'acue_descripcion'        => $acuerdo['descripcion'],
                            'acue_estado'             => $estadoAcuerdo,
                            'acue_fecha_limite'       => !empty($acuerdo['fecha_limite']) ? $acuerdo['fecha_limite'] : null,
                            'acue_observacion_cierre' => $acuerdo['observacion_cierre'] ?? null,
                            'updated_at'              => now(),
                        ]);
                } else {
                    AcuerdoEncuentro::create([
                        'acue_id_seq'             => $seq->seq_id,
                        'acue_descripcion'        => $acuerdo['descripcion'],
                        'acue_estado'             => $estadoAcuerdo,
                        'acue_fecha_limite'       => !empty($acuerdo['fecha_limite']) ? $acuerdo['fecha_limite'] : null,
                        'acue_observacion_cierre' => $acuerdo['observacion_cierre'] ?? null,
                    ]);
                }
            }
        }

        $rawAsistencias = $request->input('asistencias', []);
        $asistenciasList = [];
        if (is_array($rawAsistencias)) {
            if (array_is_list($rawAsistencias)) {
                $asistenciasList = $rawAsistencias;
            } else {
                foreach ($rawAsistencias as $uId => $val) {
                    if (is_array($val)) {
                        $asistenciasList[] = array_merge(['usu_id' => $uId], $val);
                    } else {
                        $asistenciasList[] = [
                            'usu_id'  => $uId,
                            'asistio' => (bool)$val,
                        ];
                    }
                }
            }
        }

        foreach ($asistenciasList as $asistencia) {
            if (empty($asistencia['usu_id'])) continue;

            $existente = AsistenciaPuntoControl::where('apc_id_seq', $seq->seq_id)
                ->where('apc_id_usu', $asistencia['usu_id'])
                ->first();

            $justificado = (bool)($asistencia['justificado'] ?? false);
            $asistio     = $justificado ? false : (bool)($asistencia['asistio'] ?? false);
            $participo   = ($asistio && !$justificado) ? (bool)($asistencia['participo'] ?? false) : false;

            $datos = [
                'apc_asistio'            => $asistio,
                'apc_justificado'        => $justificado,
                'apc_participo'          => $participo,
                'apc_valoracion_literal' => $asistencia['valoracion_literal'] ?? null,
                'apc_nivel_logro'        => $asistencia['nivel_logro'] ?? null,
                'apc_justificativo'      => $asistencia['justificativo'] ?? null,
                'apc_observaciones'      => $asistencia['observacion'] ?? ($asistencia['observaciones'] ?? null),
            ];

            if ($existente) {
                // Si ya existía, registrar trazabilidad de edición si cambió o si se especificó motivo
                if (!empty($asistencia['motivo_edicion'])) {
                    $datos['apc_motivo_edicion'] = $asistencia['motivo_edicion'];
                }
                $datos['apc_updated_at']      = now();
                $datos['apc_id_usu_updated']  = auth()->id();
                $existente->update($datos);
            } else {
                $datos['apc_id_seq']         = $seq->seq_id;
                $datos['apc_id_usu']         = $asistencia['usu_id'];
                $datos['apc_id_usu_created'] = auth()->id();
                AsistenciaPuntoControl::create($datos);
            }
        }

        // Si este punto está marcado como último punto de control, propagar sus observaciones a la Pre-Socialización
        if ($punto->puc_es_ultimo_punto || $request->boolean('es_ultimo_punto')) {
            $punto->update(['puc_es_ultimo_punto' => true]);
            $seq->update(['seq_es_previa_socializacion' => true]);

            if (!empty($request->seq_observaciones)) {
                $tipoPreSoc = \App\Models\TipoSocializacion::where('tso_orden', 1)->first()
                    ?? \App\Models\TipoSocializacion::first();

                if ($tipoPreSoc) {
                    $preSoc = \App\Models\Socializacion::where('soc_id_equ', $punto->puc_id_equ)
                        ->where('soc_id_tso', $tipoPreSoc->tso_id)
                        ->first();

                    $obsTexto = "Observaciones del Último Punto de Control ({$punto->puc_nombre}): " . $request->seq_observaciones;
                    if ($preSoc) {
                        if (empty($preSoc->soc_observaciones)) {
                            $preSoc->update(['soc_observaciones' => $obsTexto]);
                        } elseif (!str_contains($preSoc->soc_observaciones, $request->seq_observaciones)) {
                            $preSoc->update(['soc_observaciones' => $preSoc->soc_observaciones . "\n\n" . $obsTexto]);
                        }
                    } else {
                        \App\Models\Socializacion::create([
                            'soc_id_equ'         => $punto->puc_id_equ,
                            'soc_id_tra'         => $punto->puc_id_tra,
                            'soc_id_tso'         => $tipoPreSoc->tso_id,
                            'soc_fecha'          => now()->toDateString(),
                            'soc_hora'           => '08:00',
                            'soc_lugar'          => 'Aula de Clases / UPTP JJ Montilla',
                            'soc_apto'           => null,
                            'soc_observaciones'  => $obsTexto,
                            'soc_status'         => true,
                            'soc_id_usu_created' => auth()->id(),
                        ]);
                    }
                }
            }
        }

        // Crear automáticamente el siguiente punto de control si el profesor lo programó en este seguimiento
        $mensajeSiguiente = '';
        if ($crearSiguiente) {
            $ultimoOrden = PuntoControl::where('puc_id_equ', $punto->puc_id_equ)
                ->where('puc_status', true)
                ->max('puc_orden') ?? $punto->puc_orden;

            $criteriosSig = [];
            if (!empty($request->seq_observaciones)) {
                $criteriosSig[] = [
                    'origen' => "Observaciones del Punto previo ({$punto->puc_nombre})",
                    'texto'  => $request->seq_observaciones,
                ];
            }

            $semSig = $request->filled('siguiente_puc_semana')
                ? (int) $request->siguiente_puc_semana
                : min(18, max(1, (int) ($punto->puc_semana ?? 1) + 2));

            $nuevoPunto = PuntoControl::create([
                'puc_id_equ'               => $punto->puc_id_equ,
                'puc_id_tra'               => $punto->puc_id_tra,
                'puc_nombre'               => trim($request->siguiente_puc_nombre),
                'puc_descripcion'          => "Generado como continuación del punto \"{$punto->puc_nombre}\".",
                'puc_semana'               => $semSig,
                'puc_criterios_evaluacion' => $criteriosSig,
                'puc_fecha_limite'         => $request->siguiente_puc_fecha,
                'puc_orden'                => $ultimoOrden + 1,
                'puc_dias_aviso'           => $punto->puc_dias_aviso ?? 7,
                'puc_es_entregable'        => false,
                'puc_es_ultimo_punto'      => false,
                'puc_status'               => true,
                'puc_id_usu_created'       => auth()->id(),
            ]);

            $lider = \App\Models\EquipoIntegrante::where('ein_id_equ', $punto->puc_id_equ)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->first();
            if ($lider) {
                $tnoId = \App\Models\TipoNotificacion::where('tno_nombre', 'like', '%punto%')
                    ->orWhere('tno_nombre', 'like', '%control%')
                    ->value('tno_id') ?? \App\Models\TipoNotificacion::first()?->tno_id;
                if ($tnoId) {
                    \App\Models\Notificacion::create([
                        'not_id_usu'           => $lider->ein_id_usu,
                        'not_id_tno'           => $tnoId,
                        'not_id_equ'           => $punto->puc_id_equ,
                        'not_id_puc'           => $nuevoPunto->puc_id,
                        'not_mensaje'          => "Se programó el siguiente Punto de Control: \"{$nuevoPunto->puc_nombre}\" para el {$nuevoPunto->puc_fecha_limite}.",
                        'not_leida'            => false,
                        'not_fecha_generacion' => now(),
                    ]);
                }
            }
            $mensajeSiguiente = " y se programó el siguiente punto \"{$nuevoPunto->puc_nombre}\"";
        }

        return back()->with('success', 'Seguimiento y asistencia registrados correctamente' . ($punto->puc_es_ultimo_punto ? ' y observaciones transferidas a la Pre-Socialización.' : ($mensajeSiguiente . '.')));
    }

    public function reprogramar(\Illuminate\Http\Request $request, $id)
    {
        $maxFechaReprog = now()->addMonth()->toDateString();

        $request->validate([
            'nueva_fecha'  => "required|date|after_or_equal:today|before_or_equal:{$maxFechaReprog}",
            'nueva_semana' => 'nullable|integer|min:1|max:18',
            'motivo'       => 'required|string|max:300',
        ], [
            'nueva_fecha.required'        => 'La nueva fecha es obligatoria.',
            'nueva_fecha.after_or_equal'  => 'La nueva fecha no puede ser en el pasado (debe ser a partir de hoy).',
            'nueva_fecha.before_or_equal' => 'La fecha de reprogramación no puede exceder el límite de un mes a partir de hoy.',
            'nueva_semana.integer'        => 'La nueva semana debe ser un número entero válido (1 al 18).',
            'nueva_semana.min'            => 'La nueva semana debe ser como mínimo 1.',
            'nueva_semana.max'            => 'La nueva semana no puede ser mayor a 18.',
            'motivo.required'             => 'El motivo de la reprogramación es obligatorio.',
            'motivo.max'                  => 'El motivo de la reprogramación no puede exceder 300 caracteres.',
        ]);

        // Validación contra el período académico activo
        if ($errFecha = \App\Models\PeriodoAcademico::validarFechaEnRango($request->nueva_fecha)) {
            return back()->withErrors(['nueva_fecha' => $errFecha]);
        }

        $punto = PuntoControl::findOrFail($id);

        $this->autorizarEquipoId($punto->puc_id_equ);

        // Validar programación del siguiente punto de control (si se proporcionó y no es último punto)
        $crearSiguiente = $request->boolean('seq_cumplido')
            && !$request->boolean('es_ultimo_punto')
            && ($request->filled('siguiente_puc_nombre') || $request->filled('siguiente_puc_fecha'));

        if ($crearSiguiente) {
            if (!$request->filled('siguiente_puc_nombre') || !$request->filled('siguiente_puc_fecha')) {
                return back()->withErrors([
                    'siguiente_puc_fecha' => 'Para programar el siguiente punto de control debes indicar tanto el nombre como la fecha límite.',
                ]);
            }
            $hoy = now()->toDateString();
            if ($request->siguiente_puc_fecha < $hoy) {
                return back()->withErrors([
                    'siguiente_puc_fecha' => 'La fecha del siguiente punto de control no puede ser anterior a hoy.',
                ]);
            }
            if ($errSig = \App\Models\PeriodoAcademico::validarFechaEnRango($request->siguiente_puc_fecha)) {
                return back()->withErrors(['siguiente_puc_fecha' => $errSig]);
            }
        }

        // No reprogramar si ya fue evaluado
        $seguimientoExistente = SeguimientoEquipo::where('seq_id_equ', $punto->puc_id_equ)
            ->where('seq_id_puc', $punto->puc_id)
            ->whereNotNull('seq_cumplido')
            ->first();

        if ($seguimientoExistente) {
            return back()->withErrors([
                'general' => 'No se puede reprogramar un punto de control que ya ha sido evaluado.',
                'error'   => 'No se puede reprogramar un punto de control que ya ha sido evaluado.',
            ]);
        }

        $datosActualizar = [
            'puc_fecha_reprogramada'      => $request->nueva_fecha,
            'puc_motivo_reprogramacion'   => $request->motivo,
            'puc_id_usu_updated'          => auth()->id(),
        ];
        if ($request->filled('nueva_semana')) {
            $datosActualizar['puc_semana'] = (int) $request->nueva_semana;
        }

        $punto->update($datosActualizar);

        // Notificar al líder del equipo
        try {
            $lider = \App\Models\EquipoIntegrante::where('ein_id_equ', $punto->puc_id_equ)
                ->where('ein_es_lider', true)
                ->where('ein_status', true)
                ->first();
            if ($lider) {
                $tnoId = \App\Models\TipoNotificacion::where('tno_nombre', 'like', '%reprogram%')
                    ->orWhere('tno_nombre', 'like', '%punto%')
                    ->value('tno_id') ?? \App\Models\TipoNotificacion::first()?->tno_id;

                if ($tnoId) {
                    \App\Models\Notificacion::create([
                        'not_id_usu'           => $lider->ein_id_usu,
                        'not_id_tno'           => $tnoId,
                        'not_id_equ'           => $punto->puc_id_equ,
                        'not_id_puc'           => $punto->puc_id,
                        'not_mensaje'          => "Se ha reprogramado el Punto de Control: \"{$punto->puc_nombre}\" para el día de encuentro: {$request->nueva_fecha}. Motivo: {$request->motivo}",
                        'not_leida'            => false,
                        'not_fecha_generacion' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("No se pudo enviar notificación de reprogramación: " . $e->getMessage());
        }

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'Punto de control reprogramado correctamente.',
                'punto'   => $punto,
            ]);
        }

        return redirect()->back()->with('success', 'Punto de control reprogramado correctamente.');
    }

    public function destroy($id)
    {
        $punto = PuntoControl::findOrFail($id);
        $this->autorizarEquipoId($punto->puc_id_equ);

        // No eliminar si ya fue evaluado
        $seguimientoExistente = SeguimientoEquipo::where('seq_id_equ', $punto->puc_id_equ)
            ->where('seq_id_puc', $punto->puc_id)
            ->whereNotNull('seq_cumplido')
            ->first();

        if ($seguimientoExistente) {
            return back()->withErrors(['error' => 'No se puede eliminar un punto de control que ya ha sido evaluado.']);
        }

        $punto->update([
            'puc_status'         => false,
            'puc_id_usu_updated' => auth()->id(),
        ]);
        // Cancelar seguimientos asociados
        $punto->seguimientos()->update(['seq_status' => false]);
        return back()->with('success', 'Punto de control eliminado.');
    }
    
}