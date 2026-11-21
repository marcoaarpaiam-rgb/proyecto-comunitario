<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\EsquemaEvaluacion;
use App\Models\Seccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EsquemaEvaluacionController extends Controller
{
    /**
     * Listar esquemas del profesor autenticado + plantillas globales.
     */
    public function index()
    {
        $profesor = Auth::user();

        // IDs de secciones del profesor activo
        $idsSecciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)
            ->pluck('pse_id_sec');

        // Esquemas propios del profesor + plantillas globales
        $esquemas = EsquemaEvaluacion::where('esq_activo', true)
            ->where(function ($q) use ($profesor) {
                $q->where('esq_id_usu_creador', $profesor->usu_id)
                  ->orWhere('esq_es_plantilla', true);
            })
            ->with(['creador:usu_id,usu_primer_nombre,usu_primer_apellido'])
            ->get();

        // Secciones del profesor para asignación
        $secciones = Seccion::whereIn('sec_id', $idsSecciones)->get();

        return Inertia::render('Profesor/EsquemasEvaluacion', [
            'esquemas'  => $esquemas,
            'secciones' => $secciones,
        ]);
    }

    /**
     * Crear un nuevo esquema de evaluación.
     */
    public function store(Request $request)
    {
        $request->validate([
            'esq_nombre'      => 'required|string|max:200',
            'esq_modo'        => 'required|in:cuantitativo,cualitativo_respaldado',
            'esq_escala_min'  => 'required|numeric|min:0',
            'esq_escala_max'  => 'required|numeric|gt:esq_escala_min',
            'esq_criterios'   => 'nullable|array',
            'esq_descripcion' => 'nullable|string',
            'esq_nota_aprobatoria' => 'nullable|numeric|min:0',
            'esq_es_plantilla'     => 'nullable|boolean',
        ]);

        $esquema = EsquemaEvaluacion::create([
            'esq_nombre'          => $request->esq_nombre,
            'esq_descripcion'     => $request->esq_descripcion,
            'esq_id_usu_creador'  => Auth::id(),
            'esq_modo'            => $request->esq_modo,
            'esq_escala_min'      => $request->esq_escala_min,
            'esq_escala_max'      => $request->esq_escala_max,
            'esq_criterios'       => $request->esq_criterios,
            'esq_nota_aprobatoria'=> $request->input('esq_nota_aprobatoria', 16.00),
            'esq_activo'          => true,
            'esq_es_plantilla'    => $request->boolean('esq_es_plantilla', false),
        ]);

        return back()->with('success', 'Esquema de evaluación creado exitosamente.');
    }

    /**
     * Actualizar esquema (solo si es del profesor o coordinador).
     */
    public function update(Request $request, $id)
    {
        $esquema  = EsquemaEvaluacion::findOrFail($id);
        $profesor = Auth::user();

        // Autorización: solo el creador puede editar (o coordinadores con otro rol)
        $esCoordinador = $profesor->roles()
            ->whereIn('rol_nombre', ['coordinador', 'admin'])
            ->exists();

        if ((int) $esquema->esq_id_usu_creador !== (int) $profesor->usu_id && !$esCoordinador) {
            abort(403, 'No tienes permiso para modificar este esquema.');
        }

        $request->validate([
            'esq_nombre'      => 'required|string|max:200',
            'esq_modo'        => 'required|in:cuantitativo,cualitativo_respaldado',
            'esq_escala_min'  => 'required|numeric|min:0',
            'esq_escala_max'  => 'required|numeric|gt:esq_escala_min',
            'esq_criterios'   => 'nullable|array',
            'esq_descripcion' => 'nullable|string',
            'esq_nota_aprobatoria' => 'nullable|numeric|min:0',
        ]);

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

        if ($request->accion === 'asignar') {
            // Evitar duplicados
            \DB::table('esquema_seccion')->updateOrInsert(
                [
                    'esq_sec_id_esq' => $request->esq_sec_id_esq,
                    'esq_sec_id_sec' => $request->esq_sec_id_sec,
                    'esq_sec_lapso'  => $request->esq_sec_lapso,
                ],
                [
                    'esq_sec_activo'  => true,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
            $mensaje = 'Esquema asignado a la sección.';
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
}
