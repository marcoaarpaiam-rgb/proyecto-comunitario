<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EvaluacionTrayectoService;
use App\Services\TransicionSemestralService;
use App\Models\Equipo;
use App\Models\ProyectoComunidad;
use Inertia\Inertia;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluacionTrayectoController extends Controller
{
    protected EvaluacionTrayectoService $evaluacionService;
    protected TransicionSemestralService $transicionService;

    public function __construct(
        EvaluacionTrayectoService $evaluacionService,
        TransicionSemestralService $transicionService
    ) {
        $this->evaluacionService = $evaluacionService;
        $this->transicionService = $transicionService;
    }

    /**
     * Listado general de proyectos con estado del recorrido de evaluación.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $roles = $user ? $user->roles()->pluck('rol_nombre')->toArray() : [];
        $esProfesor = in_array('profesor_proyecto', $roles) || in_array('profesor', $roles);
        $esCoordinador = in_array('coordinador', $roles) || in_array('admin', $roles);

        $query = DB::table('proyecto_comunidad as p')
            ->join('equipo as e', 'e.equ_id', '=', 'p.pco_id_equ')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'e.equ_id_tra')
            ->leftJoin('seccion as s', 's.sec_id', '=', 'e.equ_id_sec')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->leftJoin('actas_finales_trayecto as a', 'a.id_proyecto', '=', 'p.pco_id')
            ->select([
                'p.pco_id as id_proyecto',
                'p.pco_resumen as resumen',
                'p.pin_defensa',
                'e.equ_id as id_equipo',
                'e.equ_codigo as codigo_equipo',
                'e.equ_titulo as titulo_proyecto',
                't.tra_nombre as trayecto_nombre',
                's.sec_id as id_seccion',
                's.sec_codigo as seccion_codigo',
                'c.com_nombre as comunidad_nombre',
                'a.id_acta',
                'a.nota_semestre_1',
                'a.nota_semestre_2',
                'a.calificacion_definitiva_cuantitativa',
                'a.calificacion_definitiva_cualitativa',
                'a.cerrada as acta_cerrada',
                'a.hash_verificacion',
            ])
            ->where('p.pco_status', true);

        if ($esProfesor && !$esCoordinador) {
            $seccionesProf = DB::table('profesor_seccion')
                ->where('pse_id_usu', $user->usu_id)
                ->where('pse_status', true)
                ->pluck('pse_id_sec');
            $query->whereIn('e.equ_id_sec', $seccionesProf);
        }

        $proyectos = $query->orderBy('e.equ_codigo')->get();

        // Cargar estado de Semestre 1 y Semestre 2 para cada proyecto
        foreach ($proyectos as $proy) {
            $this->evaluacionService->inicializarRecorridoProyecto(
                (int) $proy->id_proyecto,
                (int) $proy->id_equipo,
                (int) DB::table('equipo')->where('equ_id', $proy->id_equipo)->value('equ_id_tra')
            );

            if (!$proy->pin_defensa) {
                $proy->pin_defensa = DB::table('proyecto_comunidad')->where('pco_id', $proy->id_proyecto)->value('pin_defensa');
            }

            $recorridos = DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $proy->id_proyecto)
                ->get()
                ->keyBy('semestre');

            $proy->semestre1 = $recorridos->get('Semestre 1');
            $proy->semestre2 = $recorridos->get('Semestre 2');
        }

        return Inertia::render('Evaluacion/Index', [
            'proyectos' => $proyectos,
        ]);
    }

    /**
     * Vista de la línea de tiempo y recorrido del proyecto (Semestre 1 y Semestre 2).
     */
    public function recorrido(int $id_proyecto)
    {
        $data = $this->evaluacionService->obtenerRecorridoCompleto($id_proyecto);

        if (!$data) {
            return redirect()->route('evaluaciones.index')->with('error', 'El proyecto especificado no fue encontrado.');
        }

        // Rúbricas disponibles para este trayecto
        $rubricas = DB::table('rubricas')
            ->where('id_trayecto', $data->proyecto->id_trayecto)
            ->where('activo', true)
            ->get()
            ->groupBy('semestre');

        // Buscar registro de Socialización Final (orden 2) para el equipo
        $socializacionFinal = DB::table('socializacion as s')
            ->join('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
            ->where('s.soc_id_equ', $data->proyecto->id_equipo)
            ->where('ts.tso_orden', 2)
            ->where('s.soc_status', true)
            ->select('s.*', 'ts.tso_nombre')
            ->first();

        $tieneFechaSocializacion = !empty($socializacionFinal?->soc_fecha);
        $metricasIndividuales = $this->evaluacionService->calcularMetricasIndividualesEquipo(
            (int) $data->proyecto->id_equipo,
            (int) $id_proyecto
        );

        return Inertia::render('Evaluacion/Recorrido', [
            'proyecto'                => $data->proyecto,
            'semestres'               => $data->semestres,
            'acta'                    => $data->acta,
            'integrantes'             => $data->integrantes,
            'rubricas'                => $rubricas,
            'socializacionFinal'      => $socializacionFinal,
            'tieneFechaSocializacion' => $tieneFechaSocializacion,
            'metricasIndividuales'    => $metricasIndividuales,
        ]);
    }

    /**
     * Regenera el PIN de defensa (6 dígitos) para el proyecto.
     */
    public function generarPin(int $id_proyecto)
    {
        $nuevoPin = $this->evaluacionService->generarPinDefensa($id_proyecto);

        if (request()->wantsJson()) {
            return response()->json(['pin' => $nuevoPin, 'mensaje' => 'PIN de defensa generado exitosamente.']);
        }

        return redirect()->back()->with('success', "Nuevo PIN de defensa generado: {$nuevoPin}");
    }

    /**
     * Registra o actualiza la fecha y lugar de la Socialización Final.
     * Al guardarse, se habilita el PIN de defensa y el módulo de evaluación final.
     */
    public function guardarFechaSocializacion(Request $request, int $id_proyecto)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora'  => 'nullable',
            'lugar' => 'required|string|max:150',
        ], [
            'fecha.required' => 'La fecha de la socialización final es obligatoria.',
            'lugar.required' => 'El lugar del acto de defensa es obligatorio.',
        ]);

        // Validación de fecha en rango de período académico activo
        if ($errFecha = \App\Models\PeriodoAcademico::validarFechaEnRango($request->fecha)) {
            return redirect()->back()->withErrors(['fecha' => $errFecha]);
        }

        $proyecto = DB::table('proyecto_comunidad')->where('pco_id', $id_proyecto)->first();
        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        $idEquipo = $proyecto->pco_id_equ;
        $equipo = DB::table('equipo')->where('equ_id', $idEquipo)->first();

        // Verificar que el equipo tenga Pre-Socialización aprobada (Apto)
        $preSocAprobada = DB::table('socializacion as s')
            ->join('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
            ->where('s.soc_id_equ', $idEquipo)
            ->where('ts.tso_orden', 1)
            ->where('s.soc_apto', 1)
            ->first();

        if (!$preSocAprobada) {
            return redirect()->back()->withErrors([
                'fecha' => 'Bloqueado: No se puede programar la Socialización Final sin haber aprobado previamente la Pre-Socialización (condición académica requerida: Apto).'
            ]);
        }

        $tipoSocFinal = DB::table('tipo_socializacion')->where('tso_orden', 2)->first()
            ?? DB::table('tipo_socializacion')->where('tso_nombre', 'like', '%Final%')->first();
        $idTso = $tipoSocFinal?->tso_id ?? 2;

        $existeSoc = DB::table('socializacion')
            ->where('soc_id_equ', $idEquipo)
            ->where('soc_id_tso', $idTso)
            ->first();

        $obsBase = trim(str_replace(['[CD/INFORME ENTREGADO]', '[CD/INFORME PENDIENTE]'], '', (string) ($existeSoc->soc_observaciones ?? '')));
        $tagCd = $request->boolean('entrega_cd_informe') ? '[CD/INFORME ENTREGADO]' : '[CD/INFORME PENDIENTE]';
        $obsFinal = trim($tagCd . ($obsBase ? ' ' . $obsBase : ''));

        if ($existeSoc) {
            DB::table('socializacion')->where('soc_id', $existeSoc->soc_id)->update([
                'soc_fecha'          => $request->fecha,
                'soc_hora'           => $request->hora,
                'soc_lugar'          => $request->lugar,
                'soc_observaciones'  => $obsFinal,
                'soc_updated_at'     => now(),
                'soc_id_usu_updated' => auth()->id(),
            ]);
        } else {
            DB::table('socializacion')->insert([
                'soc_id_equ'           => $idEquipo,
                'soc_id_tra'           => $equipo->equ_id_tra ?? 1,
                'soc_id_tso'           => $idTso,
                'soc_fecha'            => $request->fecha,
                'soc_hora'             => $request->hora,
                'soc_lugar'            => $request->lugar,
                'soc_observaciones'    => $obsFinal,
                'soc_cantidad_jurados' => 0,
                'soc_status'           => true,
                'soc_created_at'       => now(),
                'soc_id_usu_created'   => auth()->id(),
            ]);
        }

        // Generar PIN si no existe
        if (empty($proyecto->pin_defensa)) {
            $nuevoPin = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            DB::table('proyecto_comunidad')->where('pco_id', $id_proyecto)->update(['pin_defensa' => $nuevoPin]);
            DB::table('recorrido_evaluacion_semestral')->where('id_proyecto', $id_proyecto)->update(['pin_defensa' => $nuevoPin]);
        }

        return redirect()->back()->with('success', 'Fecha y lugar de la Socialización Final guardados exitosamente. El PIN de defensa y el módulo de evaluación final han sido habilitados.');
    }

    /**
     * Formulario interactivo de evaluación por rúbrica.
     */
    public function evaluar(int $id_recorrido, string $rol_evaluador)
    {
        $user = auth()->user();
        if ($user && $user->esCoordinador() && !$user->esProfesor() && !$user->esLider()) {
            abort(403, 'El rol Coordinador es de supervisión, consulta y fe pública; no puede alterar ni forzar notas de cortes o rúbricas.');
        }
        $recorrido = DB::table('recorrido_evaluacion_semestral as r')
            ->join('proyecto_comunidad as p', 'p.pco_id', '=', 'r.id_proyecto')
            ->join('equipo as e', 'e.equ_id', '=', 'r.id_equipo')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'r.id_trayecto')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->where('r.id_recorrido', $id_recorrido)
            ->select([
                'r.*',
                'p.pco_id as id_proyecto',
                'e.equ_id as id_equipo',
                'e.equ_codigo as codigo_equipo',
                'e.equ_titulo as titulo_proyecto',
                't.tra_nombre as trayecto_nombre',
                'c.com_nombre as comunidad_nombre',
            ])
            ->first();

        if (!$recorrido) {
            return redirect()->route('evaluaciones.index')->with('error', 'Registro de recorrido no encontrado.');
        }

        // Buscar la rúbrica oficial correspondiente
        $rubrica = DB::table('rubricas')
            ->where('id_trayecto', $recorrido->id_trayecto)
            ->where('semestre', $recorrido->semestre)
            ->where(function ($q) use ($rol_evaluador) {
                if (in_array($rol_evaluador, ['docente_proyecto', 'tutor'])) {
                    $q->whereIn('rol_evaluador', ['docente_proyecto', 'tutor']);
                } else {
                    $q->where('rol_evaluador', $rol_evaluador);
                }
            })
            ->where('activo', true)
            ->first();

        if (!$rubrica) {
            return redirect()->back()->with('error', "No se encontró una rúbrica activa configurada para {$rol_evaluador} en {$recorrido->semestre}.");
        }

        // Criterios de la rúbrica
        $criterios = DB::table('criterios_rubrica')
            ->where('id_rubrica', $rubrica->id_rubrica)
            ->orderBy('orden')
            ->get();

        // Buscar si el usuario actual o el rol ya emitieron una evaluación
        $evaluacionPrevia = DB::table('evaluaciones_emitidas')
            ->where('id_recorrido', $id_recorrido)
            ->where(function ($q) use ($rol_evaluador) {
                if (in_array($rol_evaluador, ['docente_proyecto', 'tutor'])) {
                    $q->whereIn('rol_evaluador', ['docente_proyecto', 'tutor']);
                } else {
                    $q->where('rol_evaluador', $rol_evaluador);
                }
            })
            ->when(!in_array($rol_evaluador, ['comunidad', 'estudiante']) && auth()->check(), function ($q) {
                $q->where('id_profesor', auth()->id());
            })
            ->first();

        $detallesPrevios = [];
        if ($evaluacionPrevia) {
            $detallesPrevios = DB::table('evaluacion_detalles')
                ->where('id_evaluacion', $evaluacionPrevia->id_evaluacion)
                ->pluck('calificacion_obtenida', 'id_criterio')
                ->all();
        }

        // Lista de profesores para selección de evaluador si aplica
        $profesores = DB::table('usuario as u')
            ->join('usuario_rol as ur', 'ur.uro_id_usu', '=', 'u.usu_id')
            ->join('rol as r', 'r.rol_id', '=', 'ur.uro_id_rol')
            ->where('r.rol_nombre', 'profesor_proyecto')
            ->where('u.usu_status', true)
            ->select('u.usu_id', 'u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula')
            ->orderBy('u.usu_primer_nombre')
            ->get();

        return Inertia::render('Evaluacion/Evaluar', [
            'recorrido'        => $recorrido,
            'rubrica'          => $rubrica,
            'criterios'        => $criterios,
            'rol_evaluador'    => $rol_evaluador,
            'evaluacionPrevia' => $evaluacionPrevia,
            'detallesPrevios'  => $detallesPrevios,
            'profesores'       => $profesores,
        ]);
    }

    /**
     * Guarda la evaluación con cálculo automático y validación.
     */
    public function guardarEvaluacion(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->esCoordinador() && !$user->esProfesor() && !$user->esLider()) {
            abort(403, 'El rol Coordinador es de supervisión y auditoría; no puede emitir o alterar calificaciones.');
        }
        $request->validate([
            'id_recorrido'            => 'required|integer|exists:recorrido_evaluacion_semestral,id_recorrido',
            'id_proyecto'             => 'required|integer|exists:proyecto_comunidad,pco_id',
            'rol_evaluador'           => 'required|in:docente_proyecto,tutor,comunidad,jurado,estudiante',
            'id_profesor'             => 'nullable|integer|exists:usuario,usu_id',
            'observacion_cualitativa' => 'required|string|min:15|max:2000',
            'nivel_logro_cualitativo' => 'required|in:Logrado con Excelencia,Logrado,En Proceso,No Logrado',
            'criterios'               => 'required|array|min:1',
        ], [
            'observacion_cualitativa.required' => 'La observación y síntesis cualitativa es obligatoria.',
            'observacion_cualitativa.min'      => 'La observación cualitativa debe contener al menos 15 caracteres descriptivos.',
            'nivel_logro_cualitativo.required' => 'Debe seleccionar un nivel de logro cualitativo oficial.',
            'criterios.required'               => 'Debe calificar todos los criterios de la rúbrica.',
        ]);

        $idProfesor = $request->id_profesor;
        $idEstudiante = null;

        if ($request->rol_evaluador === 'estudiante') {
            $idEstudiante = auth()->id();
        } elseif ($request->rol_evaluador !== 'comunidad' && empty($idProfesor)) {
            $idProfesor = auth()->id();
        }

        $datos = [
            'id_recorrido'            => (int) $request->id_recorrido,
            'id_proyecto'             => (int) $request->id_proyecto,
            'id_profesor'             => $idProfesor ? (int) $idProfesor : null,
            'id_estudiante'           => $idEstudiante ? (int) $idEstudiante : null,
            'rol_evaluador'           => $request->rol_evaluador,
            'observacion_cualitativa' => trim($request->observacion_cualitativa),
            'nivel_logro_cualitativo' => $request->nivel_logro_cualitativo,
            'estado'                  => 'consolidado',
        ];

        $this->evaluacionService->registrarEvaluacion($datos, $request->input('criterios', []));

        return redirect()->route('evaluaciones.recorrido', $request->id_proyecto)
            ->with('success', "Evaluación de {$request->rol_evaluador} registrada y consolidada exitosamente.");
    }

    /**
     * TAREA 1.3: Vista informativa si se intenta ingresar a la ruta obsoleta /evaluacion-invitado.
     */
    public function mostrarAccesoInvitado()
    {
        return Inertia::render('Evaluacion/EnlaceExpirado', [
            'invalido' => true,
            'mensaje'  => 'El portal público general ha sido desactivado por motivos de seguridad académica. Para ingresar a evaluar, solicite al Docente de Proyecto el enlace temporal de 12 horas correspondiente al equipo.',
        ]);
    }

    /**
     * Procesa la validación de PIN + Cédula para el invitado.
     */
    public function procesarAccesoInvitado(Request $request)
    {
        $request->validate([
            'pin'    => 'required|string|size:6',
            'cedula' => 'required|string',
            'tipo'   => 'required|in:comunidad,jurado',
        ], [
            'pin.required'    => 'Debe ingresar el PIN de defensa de 6 dígitos.',
            'pin.size'        => 'El PIN debe tener exactamente 6 dígitos.',
            'cedula.required' => 'Debe ingresar su cédula de identidad.',
        ]);

        $acceso = $this->evaluacionService->validarAccesoInvitado($request->pin, $request->cedula, $request->tipo);

        if (!$acceso) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cédula o PIN no coinciden con ningún registro activo para este proyecto.');
        }

        // Guardar sesión de invitado temporal
        session(['invitado_evaluacion' => [
            'id_proyecto'  => $acceso->proyecto->id_proyecto,
            'id_recorrido' => $acceso->recorrido->id_recorrido,
            'rol'          => $acceso->rol,
            'id_usuario'   => $acceso->evaluador->usu_id ?? null,
            'nombre'       => $acceso->nombre,
            'cedula'       => $acceso->cedula,
        ]]);

        return redirect()->route('evaluaciones.evaluar', [
            'id_recorrido'  => $acceso->recorrido->id_recorrido,
            'rol_evaluador' => $acceso->rol,
        ])->with('success', "Bienvenido(a) {$acceso->nombre}. Puede emitir su evaluación.");
    }

    /**
     * TAREA 2: Transición de Semestre Impar a Semestre Par (ej. 331 -> 431).
     */
    public function transicionSemestre(int $id_equipo)
    {
        try {
            $resultado = $this->transicionService->transicionSemestreImparAPar($id_equipo, auth()->id());
            return redirect()->back()->with('success', $resultado['mensaje']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * TAREA 2: Enviar proyecto culminado al Repositorio Institucional.
     */
    public function enviarRepositorio(int $id_equipo)
    {
        try {
            $resultado = $this->transicionService->transicionSemestreParARepositorio($id_equipo, auth()->id());
            return redirect()->back()->with('success', $resultado['mensaje']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Genera y descarga el Acta Final del Trayecto en PDF con DomPDF.
     */
    public function generarActaPdf(int $id_proyecto)
    {
        $data = $this->evaluacionService->obtenerRecorridoCompleto($id_proyecto);

        if (!$data || !$data->acta) {
            return redirect()->back()->with('error', 'El proyecto aún no cuenta con un Acta Final cerrada. Ambos semestres deben culminar sus evaluaciones.');
        }

        $acta = $data->acta;
        $proyecto = $data->proyecto;
        $semestres = $data->semestres;
        $integrantes = $data->integrantes;

        // Generar URL pública de verificación basada en el hash
        $urlVerificacion = route('evaluaciones.verificar', ['hash' => $acta->hash_verificacion]);

        $pdf = Pdf::loadView('pdf.acta_final_trayecto', compact(
            'acta',
            'proyecto',
            'semestres',
            'integrantes',
            'urlVerificacion'
        ))->setPaper('letter', 'portrait');

        $nombreArchivo = 'Acta_Final_PST_' . str_replace(['/', '\\', ' '], '_', $proyecto->codigo_equipo) . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Verificación pública de autenticidad del Acta por Hash SHA-256.
     */
    public function verificarActa(string $hash)
    {
        $acta = DB::table('actas_finales_trayecto as a')
            ->join('proyecto_comunidad as p', 'p.pco_id', '=', 'a.id_proyecto')
            ->join('equipo as e', 'e.equ_id', '=', 'a.id_equipo')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'e.equ_id_tra')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->where('a.hash_verificacion', $hash)
            ->select([
                'a.*',
                'p.pco_id as id_proyecto',
                'e.equ_codigo as codigo_equipo',
                'e.equ_titulo as titulo_proyecto',
                't.tra_nombre as trayecto_nombre',
                'c.com_nombre as comunidad_nombre',
            ])
            ->first();

        return Inertia::render('Evaluacion/Verificar', [
            'acta' => $acta,
            'hash' => $hash,
        ]);
    }

    /**
     * Genera o regenera un enlace de acceso temporal para el Jurado con vigencia de 12 horas.
     */
    public function generarLinkJurado(Request $request, int $id_equipo)
    {
        $equipo = Equipo::with('proyectoComunidad')->findOrFail($id_equipo);
        $proyecto = $equipo->proyectoComunidad;

        // Si el equipo no tiene proyecto_comunidad registrado aún, inicializarlo automáticamente
        if (!$proyecto) {
            $tprId = \App\Models\TipoProyecto::where('tpr_status', true)->value('tpr_id') ?? 1;
            $mprId = \App\Models\ModalidadProyecto::where('mpr_status', true)->value('mpr_id') ?? 1;
            $comId = \App\Models\Comunidad::where('com_status', true)->value('com_id') ?? 1;

            $proyecto = ProyectoComunidad::create([
                'pco_id_equ'              => $equipo->equ_id,
                'pco_id_com'              => $comId,
                'pco_id_tpr'              => $tprId,
                'pco_id_mpr'              => $mprId,
                'pco_resumen'             => substr($equipo->equ_titulo ?: 'Proyecto Sociotecnológico Comunitario', 0, 150),
                'pco_linea_investigacion' => 'Desarrollo de Software y Sistemas de Información',
                'pco_fecha_limite'        => now()->addMonths(6)->toDateString(),
                'pco_fecha_aprobacion'    => now()->toDateString(),
                'pin_defensa'             => str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                'pco_status'              => true,
            ]);

            $this->evaluacionService->inicializarRecorridoProyecto(
                (int) $proyecto->pco_id,
                (int) $equipo->equ_id,
                (int) ($equipo->equ_id_tra ?: 1)
            );
        }

        // Generar token único criptográficamente seguro de 48 caracteres alfanuméricos
        $token = bin2hex(random_bytes(24));
        $expiraAt = now()->addHours(12);

        $proyecto->token_jurado = $token;
        $proyecto->token_jurado_expira_at = $expiraAt;

        // Asegurar PIN de 6 dígitos
        if (empty($proyecto->pin_defensa)) {
            $proyecto->pin_defensa = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        }
        $proyecto->save();

        // Sincronizar pin_defensa en recorrido si existe
        DB::table('recorrido_evaluacion_semestral')
            ->where('id_proyecto', $proyecto->pco_id)
            ->update(['pin_defensa' => $proyecto->pin_defensa]);

        $enlaceUrl = url("/evaluacion-jurado/{$token}");
        $minutosRestantes = 720; // 12 horas

        if ($request->header('X-Inertia')) {
            return redirect()->back()->with([
                'success'       => 'Enlace temporal para Jurado generado exitosamente. Vigencia de 12 horas hasta ' . $expiraAt->format('d/m/Y h:i A'),
                'link_jurado'   => $enlaceUrl,
                'expira_jurado' => $expiraAt->toIso8601String(),
            ]);
        }

        return response()->json([
            'success'           => true,
            'token'             => $token,
            'link'              => $enlaceUrl,
            'pin'               => $proyecto->pin_defensa,
            'id_proyecto'       => $proyecto->pco_id,
            'expira_at'         => $expiraAt->toIso8601String(),
            'expira_formateado' => $expiraAt->format('d/m/Y h:i A'),
            'horas_vigencia'    => 12,
            'minutos_restantes' => $minutosRestantes,
            'mensaje'           => 'Enlace temporal para Jurado generado exitosamente (Vigencia de 12 Horas).',
        ]);
    }

    /**
     * Panel de Acceso del Jurado con antecedentes, historial, expediente y formulario de evaluación.
     * Control estricto de vigencia de 12 horas.
     */
    public function panelJurado(string $token)
    {
        $proyecto = DB::table('proyecto_comunidad')->where('token_jurado', $token)->first();

        if (!$proyecto) {
            return Inertia::render('Evaluacion/EnlaceExpirado', [
                'invalido' => true,
                'mensaje'  => 'El enlace de evaluación no es válido o no existe en el sistema.',
            ]);
        }

        $expiraAt = $proyecto->token_jurado_expira_at ? Carbon::parse($proyecto->token_jurado_expira_at) : null;

        if (!$expiraAt || now()->greaterThan($expiraAt)) {
            return Inertia::render('Evaluacion/EnlaceExpirado', [
                'invalido'            => false,
                'expirado'            => true,
                'expira_formateado'   => $expiraAt ? $expiraAt->format('d/m/Y h:i A') : 'Desconocida',
                'tiempo_transcurrido' => $expiraAt ? $expiraAt->diffForHumans() : '',
                'codigo_equipo'       => DB::table('equipo')->where('equ_id', $proyecto->pco_id_equ)->value('equ_codigo') ?? '',
                'mensaje'             => 'Este enlace temporal de evaluación ha expirado (vigencia de 12 horas superada). Por favor, solicite un nuevo enlace de acceso al Docente de Proyecto.',
            ]);
        }

        $minutosRestantes = max(0, (int) now()->diffInMinutes($expiraAt, false));
        $horasRestantes = floor($minutosRestantes / 60);
        $mins = $minutosRestantes % 60;
        $tiempoRestanteTexto = "{$horasRestantes}h {$mins}m";

        // 1. Datos del Grupo y Proyecto Evaluado
        $equipo = Equipo::with([
            'seccion.trayecto',
            'seccion.turno',
            'trayecto',
            'integrantes.usuario',
            'proyectoComunidad.comunidad',
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
        ])->findOrFail($proyecto->pco_id_equ);

        // Tutor del proyecto
        $tutor = DB::table('tutor as t')
            ->leftJoin('usuario as u', 'u.usu_id', '=', 't.tut_id_usu')
            ->where('t.tut_id_equ', $equipo->equ_id)
            ->where('t.tut_status', true)
            ->select('u.usu_id', 'u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula', 'u.usu_email', 'u.usu_telefono')
            ->first();

        // Docente de la sección
        $docente = DB::table('profesor_seccion as ps')
            ->join('usuario as u', 'u.usu_id', '=', 'ps.pse_id_usu')
            ->where('ps.pse_id_sec', $equipo->equ_id_sec)
            ->where('ps.pse_status', true)
            ->select('u.usu_id', 'u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula', 'u.usu_email')
            ->first();

        // Responsable comunitario
        $responsableComunidad = DB::table('responsable_comunidad')
            ->where('rco_id_com', $proyecto->pco_id_com)
            ->where('rco_status', true)
            ->first();

        // 2. Historial Académico del Grupo
        $historialEventos = DB::table('historial_equipo as he')
            ->leftJoin('tipo_evento_equipo as te', 'te.tee_id', '=', 'he.heq_id_tee')
            ->where('he.heq_id_equ', $equipo->equ_id)
            ->orderBy('he.heq_fecha_evento', 'desc')
            ->select('he.*', 'te.tee_nombre as teq_nombre')
            ->get();

        // Puntos de Control evaluados
        $puntosControl = DB::table('punto_control as pc')
            ->leftJoin('seguimiento_equipo as se', function ($join) use ($equipo) {
                $join->on('se.seq_id_puc', '=', 'pc.puc_id')
                     ->where('se.seq_id_equ', '=', $equipo->equ_id);
            })
            ->where(function ($q) use ($equipo) {
                $q->where('pc.puc_id_equ', $equipo->equ_id)
                  ->orWhere('pc.puc_id_tra', $equipo->equ_id_tra);
            })
            ->where('pc.puc_status', true)
            ->orderBy('pc.puc_orden')
            ->select([
                'pc.puc_id',
                'pc.puc_semana as puc_semana_corte',
                'pc.puc_fecha_limite as puc_fecha',
                'pc.puc_nombre as puc_tipo_instrumento',
                'pc.puc_descripcion',
                'se.seq_id',
                'se.seq_observaciones',
                'se.seq_cumplio_previo as seq_cumplio_observaciones_anteriores',
                'se.seq_cumplido',
            ])
            ->get();

        // Evaluaciones semestrales acumuladas
        $evaluacionesSemestrales = DB::table('recorrido_evaluacion_semestral')
            ->where('id_proyecto', $proyecto->pco_id)
            ->orderBy('semestre', 'asc')
            ->get();

        // Pre-socialización y Socialización Final
        $socializaciones = DB::table('socializacion as s')
            ->leftJoin('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
            ->where('s.soc_id_equ', $equipo->equ_id)
            ->where('s.soc_status', true)
            ->orderBy('ts.tso_orden')
            ->select('s.*', 'ts.tso_nombre', 'ts.tso_orden')
            ->get();

        // 3. Expediente Individual por Estudiante
        $integrantesDetalles = [];
        $integrantesEquipo = $equipo->integrantes()->with('usuario')->where('ein_status', true)->get();

        foreach ($integrantesEquipo as $integ) {
            $u = $integ->usuario;
            if (!$u) continue;

            // Récord de asistencia en puntos de control
            $asistencias = DB::table('asistencia_punto_control as apc')
                ->join('seguimiento_equipo as se', 'se.seq_id', '=', 'apc.apc_id_seq')
                ->join('punto_control as pc', 'pc.puc_id', '=', 'se.seq_id_puc')
                ->where('apc.apc_id_usu', $u->usu_id)
                ->where('se.seq_id_equ', $equipo->equ_id)
                ->select([
                    'pc.puc_semana as puc_semana_corte',
                    'pc.puc_fecha_limite as puc_fecha',
                    'apc.apc_asistio',
                    'apc.apc_justificado as apc_justificacion',
                    'apc.apc_observaciones as apc_observacion',
                ])
                ->orderBy('pc.puc_orden')
                ->get();

            // Estado en Pre-socialización
            $preSocInteg = DB::table('integrante_socializacion as iso')
                ->join('socializacion as s', 's.soc_id', '=', 'iso.iso_id_soc')
                ->join('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
                ->where('iso.iso_id_usu', $u->usu_id)
                ->where('s.soc_id_equ', $equipo->equ_id)
                ->where('ts.tso_orden', 1)
                ->select('iso.iso_asistio', 'iso.iso_avanza', 'iso.iso_justificacion', 's.soc_fecha')
                ->first();

            // Calificaciones asignadas previas por jurado
            $califPrevias = DB::table('evaluaciones_jurado_estudiante')
                ->where('id_proyecto', $proyecto->pco_id)
                ->where('id_estudiante', $u->usu_id)
                ->select('rol_evaluador', 'nombre_evaluador', 'calificacion', 'observacion_individual', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            // Métricas individuales PNF (regla de 75% asistencia y mínimo 16.00 pts)
            $metricasEq = $this->evaluacionService->calcularMetricasIndividualesEquipo((int) $equipo->equ_id, (int) $proyecto->pco_id);
            $metricaEst = $metricasEq[$u->usu_id] ?? null;

            $integrantesDetalles[] = [
                'usu_id'                  => $u->usu_id,
                'cedula'                  => $u->usu_cedula,
                'nombre_completo'         => trim("{$u->usu_primer_nombre} {$u->usu_primer_apellido}"),
                'email'                   => $u->usu_email,
                'telefono'                => $u->usu_telefono,
                'es_lider'                => (bool) $integ->ein_es_lider,
                'asistencias'             => $asistencias,
                'pre_socializacion'       => $preSocInteg,
                'calificaciones'          => $califPrevias,
                'porcentaje_asistencia'   => $metricaEst['porcentaje_asistencia'] ?? 100.0,
                'porcentaje_inasistencia' => $metricaEst['porcentaje_inasistencia'] ?? 0.0,
                'reprobado_inasistencia'  => (bool) ($metricaEst['reprobado_inasistencia'] ?? false),
                'condicion_pnf'           => $metricaEst['condicion_pnf'] ?? 'En Curso',
            ];
        }

        // Calificaciones ya emitidas con este token (para modo consulta o recibo)
        $evaluacionesToken = DB::table('evaluaciones_jurado_estudiante')
            ->where('token_usado', $token)
            ->get();

        // Rúbrica estructurada por criterios e indicadores desagregados para Semestre 2
        $rubricaJurado = DB::table('rubricas')
            ->where('id_trayecto', $equipo->equ_id_tra)
            ->where('semestre', 'Semestre 2')
            ->where('rol_evaluador', 'jurado')
            ->where('activo', true)
            ->first();

        $criteriosRubrica = $rubricaJurado
            ? DB::table('criterios_rubrica')
                ->where('id_rubrica', $rubricaJurado->id_rubrica)
                ->orderBy('orden')
                ->get()
            : collect([]);

        if ($criteriosRubrica->isEmpty()) {
            $criteriosRubrica = collect([
                (object) ['id_criterio' => 1, 'nombre_criterio' => 'Presentación y Dominio Técnico del Proyecto', 'puntaje_maximo' => 5.00, 'indicadores' => ['Claridad expositiva y uso de vocabulario técnico', 'Manejo de preguntas del jurado']],
                (object) ['id_criterio' => 2, 'nombre_criterio' => 'Calidad Metodológica e Informe Técnico', 'puntaje_maximo' => 5.00, 'indicadores' => ['Coherencia metodológica e IAPT', 'Documentación técnica y redacción']],
                (object) ['id_criterio' => 3, 'nombre_criterio' => 'Funcionamiento del Producto / Sistema Sociotecnológico', 'puntaje_maximo' => 6.00, 'indicadores' => ['Operatividad y pruebas del sistema', 'Seguridad y arquitectura implementada']],
                (object) ['id_criterio' => 4, 'nombre_criterio' => 'Impacto y Pertinencia Comunitaria', 'puntaje_maximo' => 4.00, 'indicadores' => ['Solución efectiva al problema diagnosticado', 'Transferencia tecnológica a la comunidad']],
            ]);
        }

        $socFinalActiva = $socializaciones->firstWhere('tso_orden', 2);
        $fechaSocFinal = $socFinalActiva?->soc_fecha;
        $esDiaDeSocializacion = !$fechaSocFinal || now()->toDateString() >= Carbon::parse($fechaSocFinal)->toDateString();
        $pinVerificado = empty($proyecto->pin_defensa) || session("jurado_pin_ok_{$token}") === true;

        return Inertia::render('Evaluacion/PanelJurado', [
            'token'                   => $token,
            'tiempo_restante_texto'   => $tiempoRestanteTexto,
            'minutos_restantes'       => $minutosRestantes,
            'expira_formateado'       => $expiraAt->format('d/m/Y h:i A'),
            'pin_verificado'          => $pinVerificado,
            'es_dia_socializacion'    => $esDiaDeSocializacion,
            'fecha_socializacion'     => $fechaSocFinal,
            'criterios_rubrica'       => $criteriosRubrica,
            'equipo'                  => $equipo,
            'proyecto'                => $proyecto,
            'tutor'                   => $tutor,
            'docente'                 => $docente,
            'responsable_comunidad'   => $responsableComunidad,
            'historial_eventos'       => $historialEventos,
            'puntos_control'          => $puntosControl,
            'evaluaciones_semestrales'=> $evaluacionesSemestrales,
            'socializaciones'         => $socializaciones,
            'integrantes_detalles'    => $integrantesDetalles,
            'evaluaciones_token'      => $evaluacionesToken,
        ]);
    }

    /**
     * Desbloquea el acceso del jurado mediante el PIN de 6 dígitos el día fijado.
     */
    public function verificarPinJurado(Request $request, string $token)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
        ], [
            'pin.required' => 'Debe ingresar el PIN de 6 dígitos.',
            'pin.size'     => 'El PIN debe tener exactamente 6 dígitos.',
        ]);

        $proyecto = DB::table('proyecto_comunidad')->where('token_jurado', $token)->first();
        if (!$proyecto) {
            return back()->withErrors(['pin' => 'Enlace inválido.']);
        }

        if (trim((string) $request->pin) !== trim((string) $proyecto->pin_defensa)) {
            return back()->withErrors(['pin' => 'El PIN ingresado es incorrecto. Verifica con el Docente de Proyecto.']);
        }

        session(["jurado_pin_ok_{$token}" => true]);

        return back()->with('success', 'Acceso desbloqueado correctamente. Ya puede consignar su evaluación.');
    }

    /**
     * Exporta la Planilla Oficial de Evaluación en blanco (PDF) para respaldo híbrido / firma autógrafa.
     */
    public function exportarPlanillaEvaluacionPdf(int $id_equipo)
    {
        $equipo = Equipo::with([
            'seccion.trayecto',
            'seccion.turno',
            'trayecto',
            'integrantes.usuario',
            'proyectoComunidad.comunidad',
            'socializaciones.tipoSocializacion',
        ])->findOrFail($id_equipo);

        $socFinal = $equipo->socializaciones->first(fn($s) => $s->tipoSocializacion?->tso_orden === 2);

        $rubrica = DB::table('rubricas')
            ->where('id_trayecto', $equipo->equ_id_tra)
            ->where('semestre', 'Semestre 2')
            ->where('rol_evaluador', 'jurado')
            ->where('activo', true)
            ->first();

        $criterios = $rubrica
            ? DB::table('criterios_rubrica')->where('id_rubrica', $rubrica->id_rubrica)->orderBy('orden')->get()
            : collect([
                (object) ['nombre_criterio' => 'Presentación y Dominio Técnico del Proyecto', 'puntaje_maximo' => 5.00],
                (object) ['nombre_criterio' => 'Calidad Metodológica e Informe Técnico', 'puntaje_maximo' => 5.00],
                (object) ['nombre_criterio' => 'Funcionamiento del Producto / Sistema Sociotecnológico', 'puntaje_maximo' => 6.00],
                (object) ['nombre_criterio' => 'Impacto y Pertinencia Comunitaria', 'puntaje_maximo' => 4.00],
            ]);

        $docente = DB::table('profesor_seccion as ps')
            ->join('usuario as u', 'u.usu_id', '=', 'ps.pse_id_usu')
            ->where('ps.pse_id_sec', $equipo->equ_id_sec)
            ->where('ps.pse_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula')
            ->first();

        $pdf = Pdf::loadView('pdf.planilla_evaluacion_jurado', [
            'equipo'    => $equipo,
            'proyecto'  => $equipo->proyectoComunidad,
            'socFinal'  => $socFinal,
            'criterios' => $criterios,
            'docente'   => $docente,
        ])->setPaper('letter', 'portrait');

        $nombre = 'Planilla_Evaluacion_PST_' . $equipo->codigo_archivo . '.pdf';
        return $pdf->stream($nombre);
    }

    /**
     * Guarda la calificación emitida por el Jurado o Vocero Comunitario.
     */
    public function guardarCalificacionJurado(Request $request, string $token)
    {
        $proyecto = DB::table('proyecto_comunidad')->where('token_jurado', $token)->first();

        if (!$proyecto) {
            return back()->with('error', 'El enlace de evaluación no es válido.');
        }

        $expiraAt = $proyecto->token_jurado_expira_at ? Carbon::parse($proyecto->token_jurado_expira_at) : null;
        if (!$expiraAt || now()->greaterThan($expiraAt)) {
            return back()->with('error', 'El enlace de evaluación ha expirado. Debe solicitar uno nuevo al Docente de Proyecto.');
        }

        $request->validate([
            'rol_evaluador'           => 'required|in:jurado,comunidad',
            'nombre_evaluador'        => 'required|string|min:3|max:150',
            'cedula_evaluador'        => 'required|string|min:4|max:50',
            'institucion_evaluador'   => 'nullable|string|max:150',
            'calificaciones'          => 'required|array|min:1',
            'calificaciones.*'        => 'required|numeric|min:1|max:20',
            'observaciones_individuales' => 'nullable|array',
            'observacion_cualitativa' => 'required|string|min:15|max:2000',
            'nivel_logro_cualitativo' => 'required|in:Logrado con Excelencia,Logrado,En Proceso,No Logrado',
        ], [
            'nombre_evaluador.required'        => 'Debe ingresar su nombre completo como evaluador.',
            'cedula_evaluador.required'        => 'Debe ingresar su cédula de identidad.',
            'calificaciones.required'          => 'Debe asignar la calificación a cada estudiante del equipo.',
            'calificaciones.*.min'             => 'La calificación mínima permitida en la escala oficial es 1.00 punto.',
            'calificaciones.*.max'             => 'La calificación máxima permitida en la escala oficial es 20.00 puntos.',
            'observacion_cualitativa.required' => 'La observación y síntesis cualitativa de la socialización es obligatoria.',
            'observacion_cualitativa.min'      => 'La observación cualitativa debe contener al menos 15 caracteres descriptivos.',
            'nivel_logro_cualitativo.required' => 'Debe seleccionar un nivel de logro cualitativo oficial.',
        ]);

        DB::transaction(function () use ($request, $token, $proyecto) {
            $idProyecto = $proyecto->pco_id;
            $idEquipo = $proyecto->pco_id_equ;
            $rol = $request->rol_evaluador;

            $recorrido = DB::table('recorrido_evaluacion_semestral')
                ->where('id_proyecto', $idProyecto)
                ->where('semestre', 'Semestre 2')
                ->first();

            $idRecorrido = $recorrido?->id_recorrido;

            $calificaciones = $request->input('calificaciones', []);
            $obsIndividuales = $request->input('observaciones_individuales', []);

            $suma = 0;
            $conteo = 0;

            foreach ($calificaciones as $idEstudiante => $nota) {
                $notaVal = round(floatval($nota), 2);
                $suma += $notaVal;
                $conteo++;

                DB::table('evaluaciones_jurado_estudiante')->updateOrInsert(
                    [
                        'id_proyecto'      => $idProyecto,
                        'cedula_evaluador' => trim($request->cedula_evaluador),
                        'id_estudiante'    => (int) $idEstudiante,
                    ],
                    [
                        'id_equipo'              => $idEquipo,
                        'id_recorrido'           => $idRecorrido,
                        'token_usado'            => $token,
                        'rol_evaluador'          => $rol,
                        'nombre_evaluador'       => trim($request->nombre_evaluador),
                        'institucion_evaluador'  => trim($request->institucion_evaluador ?? ''),
                        'calificacion'           => $notaVal,
                        'observacion_individual' => $obsIndividuales[$idEstudiante] ?? null,
                        'observacion_general'    => trim($request->observacion_cualitativa),
                        'nivel_logro'            => $request->nivel_logro_cualitativo,
                        'updated_at'             => now(),
                        'created_at'             => now(),
                    ]
                );
            }

            $promedioEquipo = $conteo > 0 ? round($suma / $conteo, 2) : 0;

            // Registrar o actualizar en la tabla unificada evaluaciones_emitidas
            if ($idRecorrido) {
                $queryEval = DB::table('evaluaciones_emitidas')
                    ->where('id_recorrido', $idRecorrido)
                    ->where('rol_evaluador', $rol);

                $evalExistente = $queryEval->first();

                $obsCompleta = "EVALUADOR: {$request->nombre_evaluador} (C.I. {$request->cedula_evaluador}) - {$request->institucion_evaluador}\n"
                             . "OBSERVACIÓN: {$request->observacion_cualitativa}";

                if ($evalExistente) {
                    DB::table('evaluaciones_emitidas')
                        ->where('id_evaluacion', $evalExistente->id_evaluacion)
                        ->update([
                            'subtotal_cuantitativo'   => $promedioEquipo,
                            'observacion_cualitativa' => $obsCompleta,
                            'nivel_logro_cualitativo' => $request->nivel_logro_cualitativo,
                            'estado'                  => 'consolidado',
                            'updated_at'              => now(),
                        ]);
                } else {
                    DB::table('evaluaciones_emitidas')->insert([
                        'id_recorrido'            => $idRecorrido,
                        'id_proyecto'             => $idProyecto,
                        'rol_evaluador'           => $rol,
                        'subtotal_cuantitativo'   => $promedioEquipo,
                        'observacion_cualitativa' => $obsCompleta,
                        'nivel_logro_cualitativo' => $request->nivel_logro_cualitativo,
                        'estado'                  => 'consolidado',
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ]);
                }

                // Recalcular consolidado en el recorrido
                $this->evaluacionService->calcularConsolidadoSemestre($idRecorrido);
            }

            // Si existe socialización final registrada, sincronizar integrante_socializacion
            $socFinal = DB::table('socializacion as s')
                ->join('tipo_socializacion as ts', 'ts.tso_id', '=', 's.soc_id_tso')
                ->where('s.soc_id_equ', $idEquipo)
                ->where('ts.tso_orden', 2)
                ->where('s.soc_status', true)
                ->select('s.soc_id')
                ->first();

            if ($socFinal) {
                $metricasAsist = $this->evaluacionService->calcularMetricasIndividualesEquipo((int) $idEquipo, (int) $idProyecto);
                foreach ($calificaciones as $idEstudiante => $nota) {
                    $reprobInasist = (bool) ($metricasAsist[(int) $idEstudiante]['reprobado_inasistencia'] ?? false);
                    $aprobIndiv = !$reprobInasist && floatval($nota) >= 16.00;
                    $justifText = $reprobInasist
                        ? "REPROBADO POR INASISTENCIA (>25% faltas). Calificación ({$rol}): {$nota} pts"
                        : "Calificación Socialización Final ({$rol}): {$nota} pts (Mín. PNF 16.00)";

                    DB::table('integrante_socializacion')->updateOrInsert(
                        [
                            'iso_id_soc' => $socFinal->soc_id,
                            'iso_id_usu' => (int) $idEstudiante,
                        ],
                        [
                            'iso_asistio'       => true,
                            'iso_avanza'        => $aprobIndiv,
                            'iso_justificacion' => $justifText,
                        ]
                    );
                }

                DB::table('socializacion')->where('soc_id', $socFinal->soc_id)->update([
                    'soc_apto'       => true,
                    'soc_aprobado'   => $promedioEquipo >= 16.00,
                    'soc_updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', '¡Calificación de Socialización registrada exitosamente! Se ha consolidado la evaluación del equipo y de cada estudiante.');
    }
}
