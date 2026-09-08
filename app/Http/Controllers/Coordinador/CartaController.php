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
            'cartas'    => CartaPresentacion::with([
                'proyectoComunidad.equipo.integrantes.usuario',
                'proyectoComunidad.comunidad',
            ])->orderBy('cpr_fecha_generacion', 'desc')->get(),
            'proyectos' => ProyectoComunidad::with(['equipo','comunidad'])
                ->whereNotNull('pco_fecha_aprobacion')
                ->where('pco_status', true)
                ->get(),
            'solicitudes' => CartaPresentacion::with([
                'proyectoComunidad.equipo',
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
        $request->validate([
            'cpr_id_pco' => 'required|exists:proyecto_comunidad,pco_id',
        ]);

        $existe = CartaPresentacion::where('cpr_id_pco', $request->cpr_id_pco)
            ->exists();

        if ($existe) {
            return back()->withErrors([
                'cpr_id_pco' => 'Ya existe una carta para este proyecto.'
            ]);
        }

        CartaPresentacion::create([
            'cpr_id_pco'       => $request->cpr_id_pco,
            'cpr_ruta_archivo' => '',
            'cpr_solicitada'   => true,
            'cpr_sello'        => false,
            'cpr_id_usu_created' => auth()->id(),
        ]);

        return back()->with('success', 'Carta solicitada correctamente. El coordinador la revisará pronto.');
    }

    public function aprobar(Request $request, $id)
    {
        $carta       = CartaPresentacion::with([
            'proyectoComunidad.equipo.integrantes.usuario',
            'proyectoComunidad.equipo.seccion',
            'proyectoComunidad.equipo.trayecto',
            'proyectoComunidad.comunidad',
        ])->findOrFail($id);
        $coordinador = auth()->user();
        $proyecto    = $carta->proyectoComunidad;

        $pdf  = Pdf::loadView('pdf.carta_presentacion', compact('carta', 'proyecto', 'coordinador'));
        $nombre = 'carta_' . $proyecto->equipo->equ_codigo . '_' . now()->timestamp . '.pdf';
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
        $pdf = Pdf::loadView('pdf.carta_presentacion', compact('carta', 'proyecto', 'coordinador'));
        $nombre = 'Carta_' . $proyecto->equipo->equ_codigo . '.pdf';

        return $pdf->download($nombre);
    }
}