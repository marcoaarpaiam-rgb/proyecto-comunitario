<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EvaluacionTrayectoService;
use App\Services\TransicionSemestralService;
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
        $proyectos = DB::table('proyecto_comunidad as p')
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
            ->where('p.pco_status', true)
            ->orderBy('e.equ_codigo')
            ->get();

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

        return view('evaluaciones.index', compact('proyectos'));
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

        return view('evaluaciones.recorrido', [
            'proyecto'    => $data->proyecto,
            'semestres'   => $data->semestres,
            'acta'        => $data->acta,
            'integrantes' => $data->integrantes,
            'rubricas'    => $rubricas,
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
     * Formulario interactivo de evaluación por rúbrica.
     */
    public function evaluar(int $id_recorrido, string $rol_evaluador)
    {
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

        return view('evaluaciones.evaluar', [
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
     * TAREA 1.3: Vista pública para acceso con PIN para Comunidad y Jurados.
     */
    public function mostrarAccesoInvitado()
    {
        return view('evaluaciones.invitado');
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

        return view('evaluaciones.verificar', compact('acta', 'hash'));
    }
}
