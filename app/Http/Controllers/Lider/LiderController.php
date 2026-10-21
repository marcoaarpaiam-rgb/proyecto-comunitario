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
            
            'notificaciones' => \App\Models\Notificacion::where('not_id_usu', auth()->id())
                ->with('tipoNotificacion')
                ->orderBy('not_fecha_generacion', 'desc')
                ->take(10)
                ->get(),
            'socializaciones' => $equipo
                ? \App\Models\Socializacion::where('soc_id_equ', $equipo->equ_id)
                    ->with('tipoSocializacion')
                    ->orderBy('soc_fecha', 'desc')
                    ->get()
                : [],
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

        if (!$equipo->proyectoComunidad?->pco_fecha_aprobacion) {
            return back()->withErrors([
                'equipo' => 'El profesor aún no ha aprobado la comunidad del proyecto.'
            ]);
        }

        $ruta = $request->file('archivo')->store('entregables/' . $equipo->equ_codigo, 'public');

        $fechaLimite = $equipo->proyectoComunidad?->pco_fecha_limite;
        $aTiempo = $fechaLimite ? now()->toDateString() <= $fechaLimite : null;
        // Verificar si ya existe un entregable del mismo tipo pendiente o aprobado
        $entregableActivo = \App\Models\Entregable::where('entr_id_equ', $equipo->equ_id)
            ->where('entr_id_tet', $request->entr_id_tet)
            ->where('entr_status', true)
            ->whereNull('entr_aprobado') // pendiente
            ->orWhere(function($q) use ($equipo, $request) {
                $q->where('entr_id_equ', $equipo->equ_id)
                ->where('entr_id_tet', $request->entr_id_tet)
                ->where('entr_status', true)
                ->where('entr_aprobado', true); // aprobado
            })
            ->latest('entr_id')
            ->first();

        if ($entregableActivo && is_null($entregableActivo->entr_aprobado)) {
            return back()->withErrors([
                'entr_id_tet' => 'Ya tienes un entregable de este tipo pendiente de revisión. Espera a que el profesor lo revise antes de subir otro.'
            ]);
        }

        // Calcular versión automáticamente
        $ultimaVersion = \App\Models\Entregable::where('entr_id_equ', $equipo->equ_id)
            ->where('entr_id_tet', $request->entr_id_tet)
            ->max('entr_version') ?? '0';

        $nuevaVersion = (string)(intval($ultimaVersion) + 1) . '.0';

        Entregable::create([
            'entr_id_equ'          => $equipo->equ_id,
            'entr_id_tet'          => $request->entr_id_tet,
            'entr_nombre_archivo'  => $request->entr_nombre_archivo,
            'entr_ruta'            => $ruta,
            'entr_version' => $nuevaVersion, 
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
            'tutor' => \App\Models\Tutor::where('tut_id_equ', $equipo->equ_id)
                ->where('tut_status', true)
                ->with('usuario')
                ->first(),

            'profesoresDisponibles' => \App\Models\Usuario::whereHas('usuarioRoles', fn($q) =>
                $q->where('uro_status', true)
                ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto'))
            )->get(['usu_id', 'usu_cedula', 'usu_primer_nombre', 'usu_primer_apellido']),
        ]);
    }

    public function miProyecto()
    {
        $equipo = $this->getEquipo();

        // Buscar responsable actual de la comunidad
        $responsable = $equipo->proyectoComunidad
            ? \App\Models\ResponsableComunidad::where('rco_id_com', $equipo->proyectoComunidad->pco_id_com)
                ->where('rco_status', true)
                ->latest('rco_id')
                ->first()
            : null;
        return Inertia::render('Lider/MiProyecto', [
            'equipo' => $equipo,
            'responsable' => $responsable,
        ]);
    }
    public function descargarCarta($id)
    {
        $lider = auth()->user();

        $integrante = \App\Models\EquipoIntegrante::where('ein_id_usu', $lider->usu_id)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if (!$integrante) abort(403, 'No tienes equipo asignado.');

        $equipo = \App\Models\Equipo::where('equ_id', $integrante->ein_id_equ)
            ->where('equ_status', true)
            ->firstOrFail();

        $carta = \App\Models\CartaPresentacion::where('cpr_id', $id)
            ->whereHas('proyectoComunidad', fn($q) =>
                $q->where('pco_id_equ', $equipo->equ_id)
            )
            ->whereNotNull('cpr_fecha_aprobacion')
            ->firstOrFail();
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.carta_presentacion', [
                'carta'       => $carta->load([
                    'proyectoComunidad.comunidad',
                    'proyectoComunidad.equipo.integrantes.usuario',
                    'proyectoComunidad.equipo.seccion',
                    'proyectoComunidad.equipo.trayecto',
                ]),
                'proyecto'    => $carta->proyectoComunidad, // AGREGA ESTA LÍNEA
                'coordinador' => \App\Models\Usuario::whereHas('roles', fn($q) =>
                    $q->where('rol_nombre', 'coordinador')
                )->first(),
            ])->setPaper('letter', 'portrait');

            return $pdf->download("carta-presentacion-{$equipo->equ_codigo}.pdf");
        } catch (\Exception $e) {
            return back()->withErrors(['carta' => 'Error al generar el PDF: '.$e->getMessage()]);
        }
    }

    public function registrarResponsable(\Illuminate\Http\Request $request)
    {
        $lider = auth()->user();

        $integrante = \App\Models\EquipoIntegrante::where('ein_id_usu', $lider->usu_id)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if (!$integrante) {
            return back()->withErrors(['equipo' => 'No tienes equipo asignado.']);
        }

        $equipo = \App\Models\Equipo::with('proyectoComunidad')
            ->findOrFail($integrante->ein_id_equ);

        if (!$equipo->proyectoComunidad) {
            return back()->withErrors(['proyecto' => 'Tu equipo no tiene proyecto asignado.']);
        }

        $request->validate([
            'rco_nombre'        => 'required|string|max:100',
            'rco_cargo'         => 'required|string|max:100',
            'rco_telefono'      => 'nullable|string|max:20',
            'rco_email'         => 'nullable|email|max:100',
            'rco_fecha_inicio'  => 'required|date',
        ], [
            'rco_nombre.required'       => 'El nombre del responsable es obligatorio.',
            'rco_cargo.required'        => 'El cargo es obligatorio.',
            'rco_fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
        ]);

        // Desactivar responsable anterior si existe
        \App\Models\ResponsableComunidad::where('rco_id_com', $equipo->proyectoComunidad->pco_id_com)
            ->where('rco_status', true)
            ->update(['rco_status' => false, 'rco_fecha_fin' => now()->toDateString()]);

        \App\Models\ResponsableComunidad::create([
            'rco_id_usu'         => null, // No es usuario del sistema
            'rco_id_com'         => $equipo->proyectoComunidad->pco_id_com,
            'rco_fecha_inicio'   => $request->rco_fecha_inicio,
            'rco_status'         => true,
            'rco_id_usu_created' => $lider->usu_id,
        ]);

        return back()->with('success', 'Responsable de la comunidad registrado correctamente.');
    }

    public function eliminarResponsable($id)
    {
        $responsable = \App\Models\ResponsableComunidad::findOrFail($id);
        $responsable->update([
            'rco_status'   => false,
            'rco_fecha_fin'=> now()->toDateString(),
        ]);
        return back()->with('success', 'Responsable eliminado correctamente.');
    }

    public function registrarTutor(\Illuminate\Http\Request $request)
    {
        $lider = auth()->user();

        $integrante = \App\Models\EquipoIntegrante::where('ein_id_usu', $lider->usu_id)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->first();

        if (!$integrante) {
            return back()->withErrors(['equipo' => 'No tienes equipo asignado.']);
        }

        $request->validate([
            'tut_id_usu' => 'required|exists:usuario,usu_id',
        ], [
            'tut_id_usu.required' => 'Debes seleccionar un tutor.',
            'tut_id_usu.exists'   => 'El profesor seleccionado no existe.',
        ]);

        // Verificar que el usuario seleccionado es profesor
        $esProfesort = \App\Models\UsuarioRol::where('uro_id_usu', $request->tut_id_usu)
            ->where('uro_status', true)
            ->whereHas('rol', fn($q) => $q->where('rol_nombre', 'profesor_proyecto'))
            ->exists();

        if (!$esProfesort) {
            return back()->withErrors(['tut_id_usu' => 'El usuario seleccionado no es un profesor del PNF.']);
        }

        // Desactivar tutor anterior si existe
        \App\Models\Tutor::where('tut_id_equ', $integrante->ein_id_equ)
            ->where('tut_status', true)
            ->update(['tut_status' => false]);

        \App\Models\Tutor::create([
            'tut_id_equ'         => $integrante->ein_id_equ,
            'tut_id_usu'         => $request->tut_id_usu,
            'tut_status'         => true,
            'tut_id_usu_created' => $lider->usu_id,
        ]);

        return back()->with('success', 'Tutor asignado correctamente.');
    }
}