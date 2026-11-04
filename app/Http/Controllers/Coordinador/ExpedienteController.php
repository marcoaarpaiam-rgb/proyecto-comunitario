<?php
namespace App\Http\Controllers\Coordinador;
use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Inertia\Inertia;
use App\Http\Controllers\Concerns\AutorizaPorSeccion;

class ExpedienteController extends Controller
{
    use AutorizaPorSeccion;
    public function show($id)
    {
        $this->autorizarEquipoId($id);
        
        return Inertia::render('Coordinador/ExpedienteEquipo', [
            'equipo' => Equipo::with([
            'seccion.trayecto',
            'seccion.turno',
            'trayecto',
            'integrantes.usuario',
            'historial.tipoEvento',
            'proyectoComunidad.comunidad',
            'proyectoComunidad.tipoProyecto',
            'proyectoComunidad.modalidad',
            'puntosControl.seguimientos.asistencias.usuario',
            'entregables.tipoEntregable',
            'socializaciones.tipoSocializacion',
            'cartaPresentacion',
            'resultadoProyecto.estadoProyecto',
        ])->findOrFail($id),
        ]);
    }
}