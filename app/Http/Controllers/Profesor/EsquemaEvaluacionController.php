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

        return Inertia::render('Profesor/EsquemasEvaluacion', [
            'esquemas'        => $esquemas,
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
            'esq_nota_aprobatoria' => 'nullable|numeric|min:0',
            'esq_es_plantilla'     => 'nullable|boolean',
        ]);

        $esPlantilla = $esCoordinador ? $request->boolean('esq_es_plantilla', false) : false;

        $esquema = EsquemaEvaluacion::create([
            'esq_nombre'          => $request->esq_nombre,
            'esq_descripcion'     => $request->esq_descripcion,
            'esq_id_usu_creador'  => $profesor->usu_id,
            'esq_modo'            => $request->esq_modo,
            'esq_escala_min'      => $request->esq_escala_min,
            'esq_escala_max'      => $request->esq_escala_max,
            'esq_criterios'       => $request->esq_criterios,
            'esq_nota_aprobatoria'=> $request->input('esq_nota_aprobatoria', 16.00),
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
            'esq_nota_aprobatoria' => 'nullable|numeric|min:0',
        ]);

        // Si el esquema es del profesor o el usuario es coordinador, se actualiza directamente
        if ((int) $esquema->esq_id_usu_creador === (int) $profesor->usu_id || $esCoordinador) {
            $esquema->update($request->only([
                'esq_nombre',
                'esq_descripcion',
                'esq_modo',
                'esq_escala_min',
                'esq_escala_max',
                'esq_criterios',
                'esq_nota_aprobatoria',
            ]));

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
                'esq_nota_aprobatoria'=> $request->input('esq_nota_aprobatoria', 16.00),
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

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $esqCualitativo = EsquemaEvaluacion::where('esq_activo', true)->where('esq_modo', 'cualitativo_respaldado')->first();
        $esqCuantitativo = EsquemaEvaluacion::where('esq_activo', true)->where('esq_modo', 'cuantitativo')->first();

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
            }
        }

        return back()->with('success', 'Esquemas de evaluación oficiales asignados correctamente a tus secciones.');
    }
}
