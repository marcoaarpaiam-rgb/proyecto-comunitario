<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Comunidad;
use App\Models\TipoOrganizacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComunidadController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Comunidades', [
            'comunidades' => \App\Models\Comunidad::where('com_status', true)
                ->withCount(['proyectosComunidad as total_equipos' => fn($q) =>
                    $q->whereHas('equipo', fn($q2) => $q2->where('equ_status', true))
                ])
                ->with(['proyectosComunidad.equipo' => fn($q) =>
                    $q->where('equ_status', true)->select('equ_id','equ_codigo','equ_titulo','equ_id_sec')
                ])
                ->orderBy('com_nombre')
                ->get(),
            'tiposOrganizacion' => TipoOrganizacion::where('tor_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'com_nombre'    => 'required|string|max:150|unique:comunidad,com_nombre',
            'com_ubicacion' => 'nullable|string|max:255',
            'com_id_tor'    => 'required|exists:tipo_organizacion,tor_id',
            'com_lat'       => 'nullable|numeric|between:-90,90',
            'com_lng'       => 'nullable|numeric|between:-180,180',
        ], [
            'com_nombre.required' => 'El nombre es obligatorio.',
            'com_nombre.unique'   => 'Ya existe una comunidad con ese nombre.',
            'com_id_tor.required' => 'El tipo de organización es obligatorio.',
            'com_lat.between'     => 'La latitud debe estar entre -90 y 90.',
            'com_lng.between'     => 'La longitud debe estar entre -180 y 180.',
        ]);

        $comunidad = Comunidad::create([
            'com_nombre'         => $request->com_nombre,
            'com_ubicacion'      => $request->com_ubicacion ?: 'Sin ubicación registrada',
            'com_lat'            => $request->com_lat,
            'com_lng'            => $request->com_lng,
            'com_id_tor'         => $request->com_id_tor,
            'com_status'         => true,
            'com_id_usu_created' => auth()->id(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'comunidad' => $comunidad->load('tipoOrganizacion'),
            ]);
        }

        return back()->with('success', 'Comunidad registrada correctamente.')->with('nueva_comunidad_id', $comunidad->com_id);
    }

    public function update(Request $request, $id)
    {
        $comunidad = Comunidad::findOrFail($id);

        $request->validate([
            'com_nombre'    => "required|string|max:150|unique:comunidad,com_nombre,{$id},com_id",
            'com_ubicacion' => 'required|string|max:255',
            'com_id_tor'    => 'required|exists:tipo_organizacion,tor_id',
            'com_lat'       => 'nullable|numeric|between:-90,90',
            'com_lng'       => 'nullable|numeric|between:-180,180',
        ], [
            'com_lat.between' => 'La latitud debe estar entre -90 y 90.',
            'com_lng.between' => 'La longitud debe estar entre -180 y 180.',
        ]);

        $comunidad->update([
            'com_nombre'         => $request->com_nombre,
            'com_ubicacion'      => $request->com_ubicacion,
            'com_lat'            => $request->com_lat,
            'com_lng'            => $request->com_lng,
            'com_id_tor'         => $request->com_id_tor,
            'com_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Comunidad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $comunidad = Comunidad::findOrFail($id);
        $comunidad->update([
            'com_status'         => false,
            'com_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Comunidad desactivada.');
    }

    /**
     * Radar de Comunidad y Detector Anti-Duplicidad de Proyectos.
     * Retorna en vivo los proyectos activos, culminados y analiza similitud de títulos.
     */
    public function radarProyectos(Request $request, int $id)
    {
        $comunidad = Comunidad::with('tipoOrganizacion')->findOrFail($id);

        $equipoIdExcluir = $request->query('equipo_id');
        $tituloConsultado = trim((string) $request->query('titulo', ''));

        // Proyectos en esta comunidad
        $proyectosQuery = \App\Models\ProyectoComunidad::where('pco_id_com', $id)
            ->where('pco_status', true)
            ->with([
                'equipo.seccion.trayecto',
                'equipo.integrantes.usuario',
                'tipoProyecto',
                'modalidad',
            ]);

        if ($equipoIdExcluir) {
            $proyectosQuery->where('pco_id_equ', '!=', $equipoIdExcluir);
        }

        $todosProyectos = $proyectosQuery->get();

        $activos = [];
        $culminados = [];
        $alertaSimilitud = null;
        $maxSimilitud = 0;
        $proyectoCoincidente = null;

        foreach ($todosProyectos as $p) {
            $eq = $p->equipo;
            if (!$eq) continue;

            $lider = $eq->integrantes->firstWhere('ein_es_lider', true)?->usuario;
            $nombreLider = $lider ? "{$lider->usu_primer_nombre} {$lider->usu_primer_apellido}" : null;

            // Verificar si el proyecto ya fue culminado
            $envioRepo = \Illuminate\Support\Facades\DB::table('envio_repositorio')->where('enr_id_equ', $eq->equ_id)->first();
            $actaFinal = \Illuminate\Support\Facades\DB::table('actas_finales_trayecto')->where('id_proyecto', $p->pco_id)->first();
            $esCulminado = (bool) $envioRepo || ($actaFinal && $actaFinal->cerrada);

            $item = [
                'pco_id'           => $p->pco_id,
                'equ_id'           => $eq->equ_id,
                'equ_codigo'       => $eq->equ_codigo,
                'equ_titulo'       => $eq->equ_titulo,
                'seccion'          => $eq->seccion?->sec_codigo,
                'trayecto'         => $eq->seccion?->trayecto?->tra_nombre,
                'tipo_proyecto'    => $p->tipoProyecto?->tpr_nombre,
                'modalidad'        => $p->modalidad?->mpr_nombre,
                'lider'            => $nombreLider,
                'es_culminado'     => $esCulminado,
                'calificacion'     => $actaFinal ? (float) $actaFinal->calificacion_definitiva_cuantitativa : null,
                'hash_repositorio' => $envioRepo?->enr_hash_archivo ?: $actaFinal?->hash_verificacion,
            ];

            if ($esCulminado) {
                $culminados[] = $item;
            } else {
                $activos[] = $item;
            }

            // Chequeo de similitud si se proporcionó un título
            if (!empty($tituloConsultado) && !empty($eq->equ_titulo)) {
                $t1 = mb_strtolower($tituloConsultado, 'UTF-8');
                $t2 = mb_strtolower($eq->equ_titulo, 'UTF-8');

                similar_text($t1, $t2, $percent);

                // Coincidencia por palabras clave significativas (>= 4 letras)
                $palabras1 = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $t1)), fn($w) => mb_strlen($w) >= 4);
                $palabras2 = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $t2)), fn($w) => mb_strlen($w) >= 4);

                $interseccion = array_intersect($palabras1, $palabras2);
                $scorePalabras = count($palabras1) > 0 ? (count($interseccion) / count($palabras1)) * 100 : 0;

                $similitudFinal = max($percent, $scorePalabras);

                if ($similitudFinal > $maxSimilitud) {
                    $maxSimilitud = round($similitudFinal, 1);
                    $proyectoCoincidente = $item;
                }
            }
        }

        if ($maxSimilitud >= 50.0 && $proyectoCoincidente) {
            $alertaSimilitud = [
                'existe'               => true,
                'porcentaje'           => $maxSimilitud,
                'proyecto_coincidente' => $proyectoCoincidente,
                'es_critico'           => $maxSimilitud >= 65.0,
                'mensaje'              => "Se detectó un {$maxSimilitud}% de similitud con el proyecto '{$proyectoCoincidente['equ_titulo']}' ({$proyectoCoincidente['equ_codigo']}) en esta misma comunidad. Si se trata del escalamiento o fase posterior, active la modalidad 'Continuación o Reingeniería'.",
            ];
        }

        return response()->json([
            'comunidad'            => [
                'com_id'               => $comunidad->com_id,
                'com_nombre'           => $comunidad->com_nombre,
                'com_ubicacion'        => $comunidad->com_ubicacion,
                'com_persona_contacto' => $comunidad->com_persona_contacto,
                'com_telefono'         => $comunidad->com_telefono,
                'tipo_organizacion'    => $comunidad->tipoOrganizacion?->tor_nombre,
            ],
            'total_historico'      => count($todosProyectos),
            'proyectos_activos'    => $activos,
            'proyectos_culminados' => $culminados,
            'alerta_similitud'     => $alertaSimilitud,
        ]);
    }
}