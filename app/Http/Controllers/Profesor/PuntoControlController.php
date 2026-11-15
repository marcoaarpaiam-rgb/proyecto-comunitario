<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\PuntoControl;
use App\Models\Equipo;
use App\Models\Trayecto;
use App\Models\SeguimientoEquipo;
use App\Models\AsistenciaPuntoControl;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;

class PuntoControlController extends Controller
{   
    use AutorizaPorSeccion;
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
            ->with(['equipo.integrantes.usuario', 'trayecto', 'seguimientos.asistencias.usuario'])
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

        return Inertia::render('Profesor/PuntosControl', [
            'puntosControl' => $puntos,
            'equipos'   => $equipos,
            'secciones' => $profSecciones->map(fn($ps) => $ps->seccion)->filter()->values(),
            'trayectos' => Trayecto::where('tra_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'puc_id_equ'      => 'required|exists:equipo,equ_id',
            'puc_nombre'      => 'required|string|max:150',
            'puc_semana'      => 'nullable|integer|min:1|max:24',
            'puc_descripcion' => 'nullable|string',
            'puc_fecha_limite'=> 'required|date|after_or_equal:today',
            'puc_dias_aviso'  => 'required|integer|min:1|max:30',
        ], [
            'puc_id_equ.required'            => 'El equipo es obligatorio.',
            'puc_nombre.required'            => 'El nombre es obligatorio.',
            'puc_semana.integer'             => 'La semana debe ser un número entero.',
            'puc_semana.min'                 => 'La semana debe ser mínimo 1.',
            'puc_semana.max'                 => 'La semana no puede ser mayor a 24.',
            'puc_fecha_limite.required'      => 'La fecha del encuentro es obligatoria.',
            'puc_fecha_limite.after_or_equal'=> 'La fecha del encuentro no puede ser anterior a la fecha de hoy.',
        ]);

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
            'puc_id_equ'         => $request->puc_id_equ,
            'puc_id_tra'         => $equipo->equ_id_tra,
            'puc_nombre'         => $request->puc_nombre,
            'puc_descripcion'    => $request->puc_descripcion,
            'puc_semana'         => $request->puc_semana ?? ($ultimoOrden == 0 ? 6 : ($ultimoOrden == 1 ? 10 : 14)),
            'puc_criterios_evaluacion' => $criteriosPrevios,
            'puc_fecha_limite'   => $request->puc_fecha_limite,
            'puc_orden'          => $ultimoOrden + 1,
            'puc_dias_aviso'     => $request->puc_dias_aviso ?? 7,
            'puc_es_entregable'  => $request->puc_es_entregable ?? false,
            'puc_status'         => true,
            'puc_id_usu_created' => auth()->id(),
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
        $request->validate([
            'seq_cumplido'                 => 'required|boolean',
            'seq_observaciones'            => 'nullable|string',
            'seq_cumplio_previo'           => 'nullable|boolean',
            'seq_obs_previas_verificacion' => 'nullable|string',
            'asistencias'                  => 'nullable',
        ]);

        $punto = PuntoControl::findOrFail($id);

        $this->autorizarEquipoId($punto->puc_id_equ);

        // Bloquear si ya fue evaluado
        $seguimientoExistente = SeguimientoEquipo::where('seq_id_equ', $punto->puc_id_equ)
            ->where('seq_id_puc', $punto->puc_id)
            ->first();

        if ($seguimientoExistente && $seguimientoExistente->seq_cumplido !== null) {
            return back()->withErrors(['error' => 'Este punto de control ya ha sido evaluado y su resultado no puede ser modificado.']);
        }

        $seq = SeguimientoEquipo::updateOrCreate(
            ['seq_id_equ' => $punto->puc_id_equ, 'seq_id_puc' => $punto->puc_id],
            [
                'seq_cumplido'                 => $request->seq_cumplido,
                'seq_fecha_cumplimiento'       => $request->seq_cumplido ? now()->toDateString() : null,
                'seq_cumplido_a_tiempo'        => $request->seq_cumplido
                    ? (now()->toDateString() <= $punto->puc_fecha_limite)
                    : null,
                'seq_cumplio_previo'           => $request->seq_cumplio_previo,
                'seq_obs_previas_verificacion' => $request->seq_obs_previas_verificacion,
                'seq_observaciones'            => $request->seq_observaciones,
                'seq_fecha_registro'           => now()->toDateString(),
                'seq_id_usu_updated'           => auth()->id(),
            ]
        );

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
            AsistenciaPuntoControl::updateOrCreate(
                ['apc_id_seq' => $seq->seq_id, 'apc_id_usu' => $asistencia['usu_id']],
                [
                    'apc_asistio'        => (bool)($asistencia['asistio'] ?? false),
                    'apc_justificado'    => (bool)($asistencia['justificado'] ?? false),
                    'apc_justificativo'  => $asistencia['justificativo'] ?? null,
                    'apc_observaciones'  => $asistencia['observacion'] ?? ($asistencia['observaciones'] ?? null),
                    'apc_id_usu_created' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Seguimiento y asistencia registrados correctamente.');
    }

    public function reprogramar(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'nueva_fecha' => 'required|date|after_or_equal:today',
            'motivo'      => 'required|string|max:300',
        ], [
            'nueva_fecha.required'       => 'La nueva fecha es obligatoria.',
            'nueva_fecha.after_or_equal' => 'La nueva fecha no puede ser anterior a la fecha de hoy.',
            'motivo.required'            => 'El motivo de la reprogramación es obligatorio.',
            'motivo.max'                 => 'El motivo de la reprogramación no puede exceder 300 caracteres.',
        ]);

        $punto = PuntoControl::findOrFail($id);

        $this->autorizarEquipoId($punto->puc_id_equ);

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

        $punto->update([
            'puc_fecha_reprogramada'      => $request->nueva_fecha,
            'puc_motivo_reprogramacion'   => $request->motivo,
            'puc_id_usu_updated'          => auth()->id(),
        ]);

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