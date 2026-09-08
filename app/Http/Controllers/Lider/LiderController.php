<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Entregable;
use App\Models\TipoEntregableTrayecto;
use App\Models\EquipoIntegrante;
use App\Models\PuntoControl;
use App\Models\Socializacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LiderController extends Controller
{
    private function getEquipo()
    {
        return EquipoIntegrante::where('ein_id_usu', auth()->id())
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->with([
                'equipo.trayecto',
                'equipo.seccion',
                'equipo.integrantes.usuario',
                'equipo.proyectoComunidad.comunidad',
                'equipo.proyectoComunidad.tipoProyecto',
            ])
            ->first()
            ?->equipo;
    }

    public function dashboard()
    {
        $equipo = $this->getEquipo();
        $stats  = ['integrantes' => 0, 'puntosCompletados' => 0,
                   'entregablesSubidos' => 0, 'pendientes' => 0];
        $progreso = ['puntos' => 0, 'entregables' => 0, 'asistencia' => 0];

        if ($equipo) {
            $totalPuntos = PuntoControl::where('puc_id_equ', $equipo->equ_id)->count();
            $puntosOk    = $equipo->seguimientos()->where('seq_cumplido', true)->count();
            $totalEntr   = Entregable::where('entr_id_equ', $equipo->equ_id)->count();
            $entrAprobados = Entregable::where('entr_id_equ', $equipo->equ_id)
                ->where('entr_aprobado', true)->count();

            $stats = [
                'integrantes'       => $equipo->integrantes->count(),
                'puntosCompletados' => $puntosOk,
                'entregablesSubidos'=> $totalEntr,
                'pendientes'        => Entregable::where('entr_id_equ', $equipo->equ_id)
                    ->whereNull('entr_aprobado')->count(),
            ];

            $progreso = [
                'puntos'      => $totalPuntos ? round($puntosOk / $totalPuntos * 100) : 0,
                'entregables' => $totalEntr ? round($entrAprobados / $totalEntr * 100) : 0,
                'asistencia'  => 0,
            ];
        }

        return Inertia::render('Lider/Dashboard', [
            'equipo'   => $equipo,
            'stats'    => $stats,
            'progreso' => $progreso,
        ]);
    }

    public function entregables()
    {
        $equipo = $this->getEquipo();
        if (!$equipo) return redirect()->route('lider.dashboard');

        return Inertia::render('Lider/Entregables', [
            'equipo'      => $equipo,
            'entregables' => Entregable::where('entr_id_equ', $equipo->equ_id)
                ->with(['tipoEntregable'])
                ->orderBy('entr_fecha_subida', 'desc')
                ->get(),
            'tiposEntregable' => TipoEntregableTrayecto::where(
                'tet_trayecto_numero',
                $equipo->trayecto->tra_numero
            )->where('tet_status', true)->get(),
        ]);
    }

    public function subirEntregable(Request $request)
    {
        $request->validate([
            'entr_id_tet'       => 'required|exists:tipo_entregable_trayecto,tet_id',
            'entr_nombre_archivo' => 'required|string|max:255',
            'archivo'           => 'required|file|max:20480',
        ], [
            'entr_id_tet.required'         => 'El tipo de entregable es obligatorio.',
            'entr_nombre_archivo.required' => 'El nombre del archivo es obligatorio.',
            'archivo.required'             => 'Debes seleccionar un archivo.',
            'archivo.max'                  => 'El archivo no puede superar 20MB.',
        ]);

        $equipo = $this->getEquipo();
        if (!$equipo) return back()->withErrors(['equipo' => 'No tienes equipo asignado.']);

        $ruta = $request->file('archivo')->store('entregables/' . $equipo->equ_codigo, 'public');

        $fechaLimite = $equipo->proyectoComunidad?->pco_fecha_limite;
        $aTiempo = $fechaLimite ? now()->toDateString() <= $fechaLimite : null;

        Entregable::create([
            'entr_id_equ'          => $equipo->equ_id,
            'entr_id_tet'          => $request->entr_id_tet,
            'entr_nombre_archivo'  => $request->entr_nombre_archivo,
            'entr_ruta'            => $ruta,
            'entr_version'         => $request->entr_version,
            'entr_es_entregable_final' => $request->entr_es_entregable_final ?? false,
            'entr_a_tiempo'        => $aTiempo,
            'entr_aprobado'        => null,
            'entr_status'          => true,
            'entr_id_usu_created'  => auth()->id(),
        ]);

        return back()->with('success', 'Entregable subido. El profesor lo revisará pronto.');
    }

    public function miEquipo()
    {
        $equipo = $this->getEquipo();
        return Inertia::render('Lider/MiEquipo', [
            'equipo' => $equipo,
        ]);
    }

    public function miProyecto()
    {
        $equipo = $this->getEquipo();
        return Inertia::render('Lider/MiProyecto', [
            'equipo' => $equipo,
        ]);
    }
}