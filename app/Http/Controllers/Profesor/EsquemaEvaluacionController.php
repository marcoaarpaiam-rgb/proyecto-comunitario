<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;
use App\Models\EsquemaEvaluacion;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EsquemaEvaluacionController extends Controller
{
    use AutorizaPorSeccion;

    /**
     * Listar esquemas del profesor autenticado + plantillas globales.
     */
    public function index()
    {
        $profesor = Auth::user();

        // Roles del usuario
        $esCoordinador = $profesor->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();

        // IDs de secciones donde el profesor está activo
        $idsSecciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        // Secciones disponibles para asignación (el profesor solo ve las secciones que tiene asignadas)
        if ($esCoordinador) {
            $secciones = Seccion::where('sec_status', true)
                ->with(['trayecto', 'turno'])
                ->orderBy('sec_codigo')
                ->get();
        } else {
            $secciones = Seccion::whereIn('sec_id', $idsSecciones)
                ->where('sec_status', true)
                ->with(['trayecto', 'turno'])
                ->orderBy('sec_codigo')
                ->get();
        }

        // Esquemas propios del profesor + plantillas globales (coordinador ve todos)
        $esquemas = EsquemaEvaluacion::where('esq_activo', true)
            ->where(function ($q) use ($profesor, $esCoordinador) {
                if ($esCoordinador) {
                    // Coordinador ve todos los esquemas
                } else {
                    $q->where('esq_id_usu_creador', $profesor->usu_id)
                      ->orWhere('esq_es_plantilla', true);
                }
            })
            ->with([
                'creador:usu_id,usu_primer_nombre,usu_primer_apellido',
                'secciones' => function ($q) use ($esCoordinador, $idsSecciones) {
                    $q->wherePivot('esq_sec_activo', true);
                    if (!$esCoordinador) {
                        $q->whereIn('seccion.sec_id', $idsSecciones);
                    }
                    $q->with(['trayecto', 'turno']);
                }
            ])
            ->orderBy('esq_es_plantilla', 'desc')
            ->orderBy('esq_id', 'desc')
            ->get();

        $trayectosProf = $secciones->pluck('sec_id_tra')->filter()->unique()->values();
        $rubricas = \Illuminate\Support\Facades\DB::table('rubricas as r')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'r.id_trayecto')
            ->where('r.activo', true)
            ->when(!$esCoordinador && $trayectosProf->isNotEmpty(), fn($q) => $q->whereIn('r.id_trayecto', $trayectosProf))
            ->select('r.*', 't.tra_nombre')
            ->orderBy('r.id_trayecto')
            ->orderBy('r.semestre')
            ->orderBy('r.rol_evaluador')
            ->get()
            ->map(function ($rub) {
                $rub->criterios = \Illuminate\Support\Facades\DB::table('criterios_rubrica')
                    ->where('id_rubrica', $rub->id_rubrica)
                    ->orderBy('orden')
                    ->get();
                return $rub;
            });

        return Inertia::render('Profesor/EsquemasEvaluacion', [
            'esquemas'        => $esquemas,
            'rubricas'        => $rubricas,
            'secciones'       => $secciones,
            'esCoordinador'   => $esCoordinador,
            'usuarioActual'   => [
                'usu_id' => $profesor->usu_id,
                'nombre' => "{$profesor->usu_primer_nombre} {$profesor->usu_primer_apellido}",
            ],
            'tiposEntregable' => \App\Models\TipoEntregableTrayecto::where('tet_status', true)->get(),
        ]);
    }

    /**
     * Crear un nuevo esquema de evaluación.
     */
    public function store(Request $request)
    {
        $profesor = Auth::user();
        $esCoordinador = $profesor->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();

        $request->validate([
            'esq_nombre'           => 'required|string|max:200',
            'esq_modo'             => 'required|in:cuantitativo,cualitativo_respaldado',
            'esq_escala_min'       => 'required|numeric|min:0',
            'esq_escala_max'       => 'required|numeric|gt:esq_escala_min',
            'esq_criterios'        => 'nullable|array',
            'esq_descripcion'      => 'nullable|string',
            'esq_nota_aprobatoria' => $request->esq_modo === 'cuantitativo' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'esq_es_plantilla'     => 'nullable|boolean',
        ], [
            'esq_nombre.required'           => 'El nombre del esquema es obligatorio.',
            'esq_escala_max.gt'             => 'La escala máxima debe ser mayor a la mínima.',
            'esq_nota_aprobatoria.required' => 'La nota mínima aprobatoria es obligatoria en modalidad cuantitativa.',
        ]);

        $esPlantilla = $esCoordinador ? $request->boolean('esq_es_plantilla', false) : false;
        $notaAprob = $request->esq_modo === 'cualitativo_respaldado'
            ? 0
            : (float) $request->input('esq_nota_aprobatoria', 16.00);

        $esquema = EsquemaEvaluacion::create([
            'esq_nombre'          => $request->esq_nombre,
            'esq_descripcion'     => $request->esq_descripcion,
            'esq_id_usu_creador'  => $profesor->usu_id,
            'esq_modo'            => $request->esq_modo,
            'esq_escala_min'      => $request->esq_escala_min,
            'esq_escala_max'      => $request->esq_escala_max,
            'esq_criterios'       => $request->esq_criterios,
            'esq_nota_aprobatoria'=> $notaAprob,
            'esq_activo'          => true,
            'esq_es_plantilla'    => $esPlantilla,
        ]);

        return back()->with('success', 'Esquema de evaluación creado exitosamente.');
    }

    /**
     * Actualizar esquema existente o generar una versión propia si es plantilla oficial.
     */
    public function update(Request $request, $id)
    {
        $esquema  = EsquemaEvaluacion::findOrFail($id);
        $profesor = Auth::user();

        $esCoordinador = $profesor->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();

        $request->validate([
            'esq_nombre'           => 'required|string|max:200',
            'esq_modo'             => 'required|in:cuantitativo,cualitativo_respaldado',
            'esq_escala_min'       => 'required|numeric|min:0',
            'esq_escala_max'       => 'required|numeric|gt:esq_escala_min',
            'esq_criterios'        => 'nullable|array',
            'esq_descripcion'      => 'nullable|string',
            'esq_nota_aprobatoria' => $request->esq_modo === 'cuantitativo' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
        ], [
            'esq_nombre.required'           => 'El nombre del esquema es obligatorio.',
            'esq_escala_max.gt'             => 'La escala máxima debe ser mayor a la mínima.',
            'esq_nota_aprobatoria.required' => 'La nota mínima aprobatoria es obligatoria en modalidad cuantitativa.',
        ]);

        $notaAprob = $request->esq_modo === 'cualitativo_respaldado'
            ? 0
            : (float) $request->input('esq_nota_aprobatoria', 16.00);

        // Si el esquema es del profesor o el usuario es coordinador, se actualiza directamente
        if ((int) $esquema->esq_id_usu_creador === (int) $profesor->usu_id || $esCoordinador) {
            $datosUpdate = $request->only([
                'esq_nombre',
                'esq_descripcion',
                'esq_modo',
                'esq_escala_min',
                'esq_escala_max',
                'esq_criterios',
            ]);
            $datosUpdate['esq_nota_aprobatoria'] = $notaAprob;
            $esquema->update($datosUpdate);

            return back()->with('success', 'Esquema actualizado correctamente.');
        }

        // Si es una plantilla oficial y el profesor la quiere ajustar a su manera,
        // creamos automáticamente su propia versión personalizada
        if ($esquema->esq_es_plantilla) {
            $nuevoEsquema = EsquemaEvaluacion::create([
                'esq_nombre'          => $request->esq_nombre,
                'esq_descripcion'     => $request->esq_descripcion,
                'esq_id_usu_creador'  => $profesor->usu_id,
                'esq_modo'            => $request->esq_modo,
                'esq_escala_min'      => $request->esq_escala_min,
                'esq_escala_max'      => $request->esq_escala_max,
                'esq_criterios'       => $request->esq_criterios,
                'esq_nota_aprobatoria'=> $notaAprob,
                'esq_activo'          => true,
                'esq_es_plantilla'    => false,
            ]);

            return back()->with('success', 'Se ha creado tu versión personalizada de este esquema para tus secciones.');
        }

        abort(403, 'No tienes permiso para modificar este esquema.');
    }

    /**
     * Clonar un esquema (plantilla oficial o propio) para adaptarlo y modificarlo a la medida.
     */
    public function clonar(Request $request, $id)
    {
        $esquema  = EsquemaEvaluacion::findOrFail($id);
        $profesor = Auth::user();

        $copia = EsquemaEvaluacion::create([
            'esq_nombre'          => $esquema->esq_nombre . ' (Personalizado)',
            'esq_descripcion'     => $esquema->esq_descripcion ?: 'Esquema adaptado por el profesor.',
            'esq_id_usu_creador'  => $profesor->usu_id,
            'esq_modo'            => $esquema->esq_modo,
            'esq_escala_min'      => $esquema->esq_escala_min,
            'esq_escala_max'      => $esquema->esq_escala_max,
            'esq_criterios'       => $esquema->esq_criterios,
            'esq_nota_aprobatoria'=> $esquema->esq_nota_aprobatoria,
            'esq_activo'          => true,
            'esq_es_plantilla'    => false,
        ]);

        return back()->with('success', 'Esquema personalizado creado exitosamente. Ahora puedes adaptarlo a tu manera.');
    }

    /**
     * Desactivar esquema (soft delete lógico).
     */
    public function destroy($id)
    {
        $esquema  = EsquemaEvaluacion::findOrFail($id);
        $profesor = Auth::user();

        $esCoordinador = $profesor->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();

        if ((int) $esquema->esq_id_usu_creador !== (int) $profesor->usu_id && !$esCoordinador) {
            abort(403, 'No tienes permiso para desactivar este esquema.');
        }

        $esquema->update(['esq_activo' => false]);

        return back()->with('success', 'Esquema desactivado.');
    }

    /**
     * Asignar o desasignar un esquema a una sección/lapso.
     */
    public function asignarSeccion(Request $request)
    {
        $request->validate([
            'esq_sec_id_esq' => 'required|exists:esquemas_evaluacion,esq_id',
            'esq_sec_id_sec' => 'required|exists:seccion,sec_id',
            'esq_sec_lapso'  => 'nullable|string|max:20',
            'accion'         => 'required|in:asignar,desasignar',
        ]);

        $esCoordinador = Auth::user()->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();
        if (!$esCoordinador) {
            $this->autorizarSeccionId($request->esq_sec_id_sec);
        }

        if ($request->accion === 'asignar') {
            // Evitar duplicados
            \DB::table('esquema_seccion')->updateOrInsert(
                [
                    'esq_sec_id_esq' => $request->esq_sec_id_esq,
                    'esq_sec_id_sec' => $request->esq_sec_id_sec,
                    'esq_sec_lapso'  => $request->esq_sec_lapso ?: 'semestre_1',
                ],
                [
                    'esq_sec_activo'  => true,
                    'updated_at'      => now(),
                ]
            );
            $mensaje = 'Esquema asignado a la sección correctamente.';
        } else {
            \DB::table('esquema_seccion')
                ->where('esq_sec_id_esq', $request->esq_sec_id_esq)
                ->where('esq_sec_id_sec', $request->esq_sec_id_sec)
                ->when($request->filled('esq_sec_lapso'), fn ($q) => $q->where('esq_sec_lapso', $request->esq_sec_lapso))
                ->update(['esq_sec_activo' => false, 'updated_at' => now()]);
            $mensaje = 'Esquema desasignado de la sección.';
        }

        return back()->with('success', $mensaje);
    }

    /**
     * Asignar automáticamente los esquemas oficiales recomendados según la paridad del semestre.
     */
    public function autoAsignarRecomendados(Request $request)
    {
        $profesor = Auth::user();
        $idsSecciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        if ($request->filled('sec_id')) {
            $idsSecciones = $idsSecciones->filter(fn($id) => (int) $id === (int) $request->sec_id);
        }

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $esqCualitativo = EsquemaEvaluacion::where('esq_activo', true)->where('esq_modo', 'cualitativo_respaldado')->first();
        if (!$esqCualitativo) {
            $esqCualitativo = EsquemaEvaluacion::create([
                'esq_nombre'           => 'Evaluación Cualitativa Literal PNFI',
                'esq_descripcion'      => 'Esquema oficial para semestres impares con baremo literal A/B/C/D.',
                'esq_modo'             => 'cualitativo_respaldado',
                'esq_escala_min'       => 1,
                'esq_escala_max'       => 20,
                'esq_nota_aprobatoria' => null,
                'esq_criterios'        => json_encode(['criterios' => []]),
                'esq_es_plantilla'     => true,
                'esq_activo'           => true,
            ]);
        }

        $esqCuantitativo = EsquemaEvaluacion::where('esq_activo', true)->where('esq_modo', 'cuantitativo')->first();
        if (!$esqCuantitativo) {
            $esqCuantitativo = EsquemaEvaluacion::create([
                'esq_nombre'           => 'Evaluación Cuantitativa PST UPTP',
                'esq_descripcion'      => 'Esquema oficial para semestres pares (escala 1 a 20 pts, mínima aprobatoria 16 pts).',
                'esq_modo'             => 'cuantitativo',
                'esq_escala_min'       => 1,
                'esq_escala_max'       => 20,
                'esq_nota_aprobatoria' => 16,
                'esq_criterios'        => json_encode(['criterios' => []]),
                'esq_es_plantilla'     => true,
                'esq_activo'           => true,
            ]);
        }

        $asignadas = 0;
        foreach ($idsSecciones as $secId) {
            $seccion = Seccion::find($secId);
            if (!$seccion) continue;

            $numSem = $seccion->getSemestreNumero();
            $esImpar = ($numSem % 2 !== 0);
            $esqElegido = $esImpar ? $esqCualitativo : $esqCuantitativo;

            if ($esqElegido) {
                \DB::table('esquema_seccion')->updateOrInsert(
                    [
                        'esq_sec_id_sec' => $secId,
                        'esq_sec_lapso'  => $lapsoActivo,
                    ],
                    [
                        'esq_sec_id_esq' => $esqElegido->esq_id,
                        'esq_sec_activo' => true,
                        'updated_at'     => now(),
                    ]
                );
                $asignadas++;
            }
        }

        return back()->with('success', "Esquema(s) oficial(es) confirmado(s) correctamente para {$asignadas} sección(es).");
    }

    /**
     * Actualizar título, peso y criterios de una rúbrica de socialización/evaluación.
     */
    public function updateRubrica(Request $request, $id)
    {
        $rubrica = \Illuminate\Support\Facades\DB::table('rubricas')->where('id_rubrica', $id)->first();
        if (!$rubrica) {
            return back()->withErrors(['rubrica' => 'Rúbrica no encontrada.']);
        }

        $request->validate([
            'titulo'                      => 'required|string|max:200',
            'peso_porcentual'             => 'required|numeric|min:1|max:100',
            'criterios'                   => 'required|array|min:1',
            'criterios.*.nombre_criterio' => 'required|string|max:250',
            'criterios.*.tipo_criterio'   => 'required|in:cuantitativo,cualitativo',
            'criterios.*.puntaje_maximo'  => 'required|numeric|min:0|max:20',
        ], [
            'titulo.required'                      => 'El título de la rúbrica es obligatorio.',
            'peso_porcentual.required'             => 'El peso porcentual es obligatorio.',
            'criterios.required'                   => 'Debe incluir al menos un criterio de evaluación.',
            'criterios.*.nombre_criterio.required' => 'Todos los criterios deben tener un nombre descriptivo.',
            'criterios.*.puntaje_maximo.required'  => 'Indique el puntaje máximo para cada criterio.',
        ]);

        $criteriosInput = $request->input('criterios', []);
        $sumaCuantitativa = 0;
        foreach ($criteriosInput as $crit) {
            if (($crit['tipo_criterio'] ?? 'cuantitativo') === 'cuantitativo') {
                $sumaCuantitativa += (float) ($crit['puntaje_maximo'] ?? 0);
            }
        }

        if ($sumaCuantitativa > 0 && abs($sumaCuantitativa - 20.0) > 0.01) {
            return back()->withErrors([
                'criterios' => "La suma de los puntajes máximos de los criterios cuantitativos debe ser exactamente 20 pts (actualmente suma {$sumaCuantitativa} pts)."
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $request, $criteriosInput) {
            \Illuminate\Support\Facades\DB::table('rubricas')
                ->where('id_rubrica', $id)
                ->update([
                    'titulo'          => trim($request->titulo),
                    'peso_porcentual' => (float) $request->peso_porcentual,
                    'updated_at'      => now(),
                ]);

            $idsConservados = [];
            foreach (array_values($criteriosInput) as $idx => $crit) {
                $idCrit = !empty($crit['id_criterio']) ? (int) $crit['id_criterio'] : null;
                $puntaje = ($crit['tipo_criterio'] === 'cualitativo') ? 0 : (float) $crit['puntaje_maximo'];

                if ($idCrit) {
                    \Illuminate\Support\Facades\DB::table('criterios_rubrica')
                        ->where('id_criterio', $idCrit)
                        ->where('id_rubrica', $id)
                        ->update([
                            'nombre_criterio' => trim($crit['nombre_criterio']),
                            'tipo_criterio'   => $crit['tipo_criterio'],
                            'puntaje_maximo'  => $puntaje,
                            'orden'           => $idx + 1,
                        ]);
                    $idsConservados[] = $idCrit;
                } else {
                    $nuevoId = \Illuminate\Support\Facades\DB::table('criterios_rubrica')->insertGetId([
                        'id_rubrica'      => $id,
                        'nombre_criterio' => trim($crit['nombre_criterio']),
                        'tipo_criterio'   => $crit['tipo_criterio'],
                        'puntaje_maximo'  => $puntaje,
                        'orden'           => $idx + 1,
                    ]);
                    $idsConservados[] = $nuevoId;
                }
            }

            // Eliminar únicamente los criterios removidos que no tengan evaluaciones emitidas
            $criteriosExistentes = \Illuminate\Support\Facades\DB::table('criterios_rubrica')
                ->where('id_rubrica', $id)
                ->pluck('id_criterio')
                ->all();
            $aEliminar = array_diff($criteriosExistentes, $idsConservados);
            foreach ($aEliminar as $idElim) {
                $tieneDetalles = \Illuminate\Support\Facades\DB::table('evaluacion_detalles')
                    ->where('id_criterio', $idElim)
                    ->exists();
                if (!$tieneDetalles) {
                    \Illuminate\Support\Facades\DB::table('criterios_rubrica')->where('id_criterio', $idElim)->delete();
                }
            }
        });

        return back()->with('success', 'Rúbrica de socialización guardada correctamente.');
    }
}
