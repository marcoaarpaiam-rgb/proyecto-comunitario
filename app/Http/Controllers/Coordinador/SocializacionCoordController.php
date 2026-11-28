<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Socializacion;
use Inertia\Inertia;

class SocializacionCoordController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Socializaciones', [
            'socializaciones' => Socializacion::with([
                'equipo.seccion.trayecto',
                'equipo.proyectoComunidad.comunidad',
                'equipo.integrantes.usuario',
                'equipo.socializaciones.tipoSocializacion',
                'trayecto',
                'tipoSocializacion',
                'jurados',
                'integrantes.usuario',
                'socializacionPrevia',
            ])->orderBy('soc_fecha', 'desc')->get(),
            'secciones' => \App\Models\Seccion::where('sec_status', true)->with('trayecto')->orderBy('sec_codigo')->get(),
            'trayectos' => \App\Models\Trayecto::where('tra_status', true)->orderBy('tra_numero')->get(),
        ]);
    }
}