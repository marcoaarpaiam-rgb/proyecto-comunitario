<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Equipo;
use Inertia\Inertia;

class SeccionDetalleController extends Controller
{
    public function show($id)
    {
        $seccion = Seccion::with(['trayecto','turno'])->findOrFail($id);
        $equipos = Equipo::where('equ_id_sec', $id)
            ->where('equ_status', true)
            ->with([
                'integrantes.usuario',
                'proyectoComunidad.comunidad',
                'proyectoComunidad.tipoProyecto',
                'resultadoProyecto.estadoProyecto',
                'trayecto',
            ])->get();

        return Inertia::render('Coordinador/SeccionDetalle', [
            'seccion' => $seccion,
            'equipos' => $equipos,
        ]);
    }
}