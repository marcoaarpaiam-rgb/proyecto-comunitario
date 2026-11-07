<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\CartaPresentacion;
use App\Models\ProyectoComunidad;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Usuario;

class CartaController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Cartas', [
            'cartas' => \App\Models\CartaPresentacion::where('cpr_solicitada', true)
                ->with([
                    'proyectoComunidad.equipo.seccion',
                    'proyectoComunidad.equipo.trayecto',
                    'proyectoComunidad.equipo.integrantes.usuario',
                    'proyectoComunidad.comunidad',
                ])
                ->orderBy('cpr_fecha_generacion', 'desc')
                ->get(),
            'secciones' => \App\Models\Seccion::where('sec_status', true)
                ->orderBy('sec_codigo')
                ->get(['sec_id', 'sec_codigo']),
            'proyectos' => ProyectoComunidad::with(['equipo.seccion','comunidad'])
                ->whereNotNull('pco_fecha_aprobacion')
                ->where('pco_status', true)
                ->get(),
            'solicitudes' => CartaPresentacion::with([
                'proyectoComunidad.equipo.seccion',
                'proyectoComunidad.equipo.trayecto',
                'proyectoComunidad.equipo.integrantes.usuario',
                'proyectoComunidad.comunidad',
            ])
            ->whereNull('cpr_fecha_aprobacion')
            ->where('cpr_solicitada', true)
            ->get(),
            'firma' => auth()->user()->usu_firma_digital,
        ]);
    }

    public function solicitar(Request $request)
    {
        $request->validate(['cpr_id_pco' => 'required|exists:proyecto_comunidad,pco_id']);

        $miEquipoId = \App\Models\EquipoIntegrante::where('ein_id_usu', auth()->id())
            ->where('ein_es_lider', true)->where('ein_status', true)->value('ein_id_equ');

        $proyecto = \App\Models\ProyectoComunidad::where('pco_id', $request->cpr_id_pco)
            ->where('pco_id_equ', $miEquipoId)->first();
        abort_unless($proyecto, 403);

        $carta = CartaPresentacion::where('cpr_id_pco', $proyecto->pco_id)->first();

        if ($carta && $carta->cpr_solicitada) {
            return back()->withErrors(['cpr_id_pco' => $carta->cpr_fecha_aprobacion
                ? 'Tu carta ya fue aprobada.' : 'Ya solicitaste la carta; está pendiente de aprobación.']);
        }

        if ($carta) {            // fue invalidada por cambio de comunidad: se vuelve a solicitar
            $carta->update(['cpr_solicitada' => true, 'cpr_fecha_generacion' => now()]);
        } else {
            $carta = CartaPresentacion::create([
                'cpr_id_pco'           => $proyecto->pco_id,
                'cpr_ruta_archivo'     => '',
                'cpr_solicitada'       => true,
                'cpr_sello'            => false,
                'cpr_fecha_generacion' => now(),
                'cpr_id_usu_created'   => auth()->id(),
            ]);
        }

        // Notificar a todos los coordinadores sobre la solicitud de la carta
        $equipo = $proyecto->equipo;
        $codigoEquipo = $equipo ? preg_replace('/#DIS\d+$/', '', $equipo->equ_codigo) : 'Equipo';
        $nombreLider = auth()->user()->usu_primer_nombre . ' ' . auth()->user()->usu_primer_apellido;
        $tnoId = \App\Models\TipoNotificacion::first()?->tno_id;

        $coordinadores = Usuario::where('usu_status', true)
            ->whereHas('usuarioRoles', fn($q) => $q->where('uro_status', true)
                ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'coordinador')))->get();

        foreach ($coordinadores as $coord) {
            \App\Models\Notificacion::create([
                'not_id_usu'           => $coord->usu_id,
                'not_id_tno'           => $tnoId,
                'not_id_equ'           => $proyecto->pco_id_equ,
                'not_mensaje'          => "El líder {$nombreLider} del equipo {$codigoEquipo} ha solicitado la Carta de Presentación.",
                'not_leida'            => false,
                'not_fecha_generacion' => now(),
            ]);
        }

        return back()->with('success', 'Carta solicitada. El coordinador la revisará.');
    }

    public function aprobar(Request $request, $id)
    {
        $carta       = CartaPresentacion::with([
            'proyectoComunidad.equipo.integrantes.usuario',
            'proyectoComunidad.equipo.seccion',
            'proyectoComunidad.equipo.trayecto',
            'proyectoComunidad.comunidad',
        ])->findOrFail($id);
        if ($carta->cpr_fecha_aprobacion) {
            return back()->withErrors(['carta' => 'Esta carta ya fue aprobada anteriormente.']);
        }
        $coordinador = auth()->user();
        $proyecto    = $carta->proyectoComunidad;

        $pdf  = Pdf::loadView('pdf.carta_presentacion', compact('carta', 'proyecto', 'coordinador'));
        $nombre = 'carta_' . $proyecto->equipo->codigo_archivo . '_' . now()->timestamp . '.pdf';
        $ruta = 'cartas/' . $nombre;

        if (!file_exists(storage_path('app/public/cartas'))) {
            mkdir(storage_path('app/public/cartas'), 0755, true);
        }
        file_put_contents(storage_path('app/public/' . $ruta), $pdf->output());

        $carta->update([
            'cpr_firma_digital'   => $coordinador->usu_firma_digital,
            'cpr_sello'           => true,
            'cpr_fecha_aprobacion'=> now(),
            'cpr_ruta_archivo'    => $ruta,
        ]);

        $pco     = $carta->proyectoComunidad;
        $liderId = \App\Models\EquipoIntegrante::where('ein_id_equ', $pco->pco_id_equ)
            ->where('ein_es_lider', true)->where('ein_status', true)->value('ein_id_usu');
        $tno     = \App\Models\TipoNotificacion::where('tno_nombre', 'Carta de presentacion aprobada')->value('tno_id');

        if ($liderId && $tno) {
            \App\Models\Notificacion::create([
                'not_id_usu'  => $liderId,
                'not_id_tno'  => $tno,
                'not_mensaje' => 'Tu carta de presentación fue aprobada. Ya puedes descargarla.',
                'not_leida'   => false,
                'not_id_equ'  => $pco->pco_id_equ,
            ]);
        }
        
        return back()->with('success', 'Carta aprobada y PDF generado correctamente.');
    }

    public function actualizarFirma(Request $request)
    {
        $request->validate([
            'firma' => 'required|image|max:2048',
        ], [
            'firma.required' => 'Debes seleccionar una imagen de firma.',
            'firma.image'    => 'El archivo debe ser una imagen.',
            'firma.max'      => 'La imagen no puede superar 2MB.',
        ]);

        $ruta = $request->file('firma')->store('firmas', 'public');
        auth()->user()->update(['usu_firma_digital' => $ruta]);

        return back()->with('success', 'Firma actualizada correctamente.');
    }

    public function descargar($id)
    {
        $carta = CartaPresentacion::with([
            'proyectoComunidad.equipo.integrantes.usuario',
            'proyectoComunidad.equipo.seccion',
            'proyectoComunidad.equipo.trayecto',
            'proyectoComunidad.comunidad',
        ])->findOrFail($id);

        $coordinador = Usuario::whereHas('usuarioRoles', fn($q) =>
            $q->where('uro_status', true)
            ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'coordinador'))
        )->first();

        $proyecto = $carta->proyectoComunidad;
        $pdf = Pdf::loadView('pdf.carta_presentacion', [
            'carta'       => $carta->load([
                'proyectoComunidad.equipo.seccion',
                'proyectoComunidad.equipo.trayecto',
                'proyectoComunidad.comunidad',
            ]),
            'proyecto'    => $carta->proyectoComunidad,
            'coordinador' => $coordinador,
        ])->setPaper('letter', 'portrait');

        $nombre = 'Carta_' . $proyecto->equipo->codigo_archivo . '.pdf';

        return $pdf->download($nombre);
    }
}