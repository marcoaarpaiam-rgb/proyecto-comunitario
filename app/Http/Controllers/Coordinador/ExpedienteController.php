<?php
namespace App\Http\Controllers\Coordinador;
use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Inertia\Inertia;

class ExpedienteController extends Controller
{
    public function show($id)
    {
        return Inertia::render('Coordinador/ExpedienteEquipo', [
            'equipo' => Equipo::with([
                'seccion.trayecto','seccion.turno','trayecto',
                'integrantes.usuario',
                'historial.tipoEvento',
                'proyectoComunidad.comunidad',
                'proyectoComunidad.tipoProyecto',
                'proyectoComunidad.modalidad',
                'puntoControl.seguimiento',
                'entregables.tipoEntregable',
                'socializaciones.tipoSocializacion',
                'socializaciones.jurados',
                'cartaPresentacion',
                'resultadoProyecto.estadoProyecto',
            ])->findOrFail($id),
        ]);
    }
}