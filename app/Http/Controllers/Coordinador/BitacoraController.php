<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Entidad;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with(['usuario','entidad'])
            ->orderBy('bit_fecha_hora', 'desc');

        if ($request->busqueda) {
            $query->where('bit_accion', 'like', '%' . $request->busqueda . '%');
        }

        if ($request->entidad_id) {
            $query->where('bit_id_entidad', $request->entidad_id);
        }

        if ($request->usuario_id) {
            $query->where('bit_id_usu', $request->usuario_id);
        }

        if ($request->fecha_desde) {
            $query->whereDate('bit_fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->whereDate('bit_fecha_hora', '<=', $request->fecha_hasta);
        }

        return Inertia::render('Coordinador/Bitacora', [
            'registros' => $query->paginate(25)->withQueryString(),
            'entidades' => Entidad::where('ent_status', true)->get(),
            'usuarios'  => Usuario::where('usu_tiene_acceso', true)
                ->where('usu_status', true)->get([
                    'usu_id','usu_primer_nombre','usu_primer_apellido'
                ]),
            'filtros'   => $request->only([
                'busqueda','entidad_id','usuario_id','fecha_desde','fecha_hasta'
            ]),
        ]);
    }
}