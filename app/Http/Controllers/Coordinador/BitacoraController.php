<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Bitacora', [
            'registros' => Bitacora::with(['usuario','entidad'])
                ->orderBy('bit_fecha_hora', 'desc')
                ->paginate(50),
        ]);
    }
}