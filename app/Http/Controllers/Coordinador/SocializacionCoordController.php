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
                'equipo.seccion',
                'trayecto',
                'tipoSocializacion',
                'jurados',
                'integrantes.usuario',
            ])->orderBy('soc_fecha', 'desc')->get(),
        ]);
    }
}