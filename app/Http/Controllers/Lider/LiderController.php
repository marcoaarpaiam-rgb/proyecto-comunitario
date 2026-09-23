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
use App\Models\Equipo;
use App\Models\Notificacion;

class LiderController extends Controller
{
    private function getEquipo()
    {
        // 1. Buscamos primero el ID del equipo del líder activo
        $integrante = EquipoIntegrante::where('ein_id_usu', auth()->id())
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if (!$integrante) {
            return null;
        }

        // 2. Hacemos la consulta directa sobre Equipo exactamente como en la foto
        return Equipo::where('equ_id', $integrante->ein_id_equ)
            ->where('equ_status', true)
            ->with([
                'trayecto',
                'seccion',
                'integrantes.usuario',
                'proyectoComunidad.comunidad',
                'proyectoComunidad.tipoProyecto',
            ])
            ->first();
    }

    public function dashboard()
    {
        $equipo = $this->getEquipo();
        $carta  = ($equipo && $equipo->proyectoComunidad)
            ? \App\Models\CartaPresentacion::where('cpr_id_pco',
                $equipo->proyectoComunidad->pco_id)->first()
            : null;

        return Inertia::render('Lider/Dashboard', [
            'equipo'        => $equipo,
            'puntosControl' => $equipo
                ? \App\Models\PuntoControl::where('puc_id_equ', $equipo->equ_id)
                    ->where('puc_status', true)
                    ->with('seguimientos')
                    ->orderBy('puc_orden')->get()
                : [],
            'entregables'   => $equipo
                ? \App\Models\Entregable::where('entr_id_equ', $equipo->equ_id)
                    ->with('tipoEntregable')
                    ->orderBy('entr_fecha_subida','desc')->get()
                : [],
            
            'notificaciones' => Notificacion::where('not_id_usu', auth()->id())
                ->orderBy('not_fecha_generacion', 'desc')
                ->take(20)->get(),
            'carta'         => $carta,
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
    public function descargarCarta($id)
    {
        $lider  = auth()->user();
        $equipo = \App\Models\Equipo::where('equ_id_lider', $lider->usu_id)
                    ->where('equ_status', true)
                    ->firstOrFail();

        // Solo puede descargar la carta de su propio equipo
        $carta = \App\Models\CartaPresentacion::where('cpr_id', $id)
            ->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_id_equ', $equipo->equ_id)
            )
            ->where('cpr_fecha_aprobacion', '!=', null) // solo si está aprobada
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.carta_presentacion', [
            'carta'  => $carta->load(['proyectoComunidad.comunidad', 'proyectoComunidad.equipo.integrantes.usuario']),
            'coordinador' => \App\Models\Usuario::where('usu_rol', 'coordinador')->first(),
        ])->setPaper('letter', 'portrait');

        return $pdf->download("carta-presentacion-{$equipo->equ_codigo}.pdf");
    }
}