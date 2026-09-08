<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\ProyectoComunidad;
use App\Models\Equipo;
use App\Models\Comunidad;
use App\Models\TipoProyecto;
use App\Models\ModalidadProyecto;
use App\Models\TipoBeneficiario;
use App\Models\ProyectoBeneficiario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProyectoController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Proyectos', [
            'proyectos' => ProyectoComunidad::with([
                'equipo.seccion',
                'equipo.trayecto',
                'comunidad',
                'tipoProyecto',
                'modalidad',
                'beneficiarios.tipoBeneficiario',
                'usuarioAprobador',
            ])->where('pco_status', true)->get(),
            'equipos'      => Equipo::where('equ_status', true)
                ->with(['seccion','trayecto'])->get(),
            'comunidades'  => Comunidad::where('com_status', true)->get(),
            'tipos'        => TipoProyecto::where('tpr_status', true)->get(),
            'modalidades'  => ModalidadProyecto::where('mpr_status', true)->get(),
            'beneficiarios'=> TipoBeneficiario::where('tbe_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pco_id_equ'      => 'required|exists:equipo,equ_id',
            'pco_id_com'      => 'required|exists:comunidad,com_id',
            'pco_id_tpr'      => 'required|exists:tipo_proyecto,tpr_id',
            'pco_id_mpr'      => 'required|exists:modalidad_proyecto,mpr_id',
            'pco_resumen'     => 'required|string|max:150',
            'pco_fecha_limite'=> 'required|date|after:today',
            'beneficiarios'   => 'sometimes|array',
            'beneficiarios.*.tbe_id'   => 'required|exists:tipo_beneficiario,tbe_id',
            'beneficiarios.*.cantidad' => 'required|integer|min:1',
        ], [
            'pco_id_equ.required'       => 'El equipo es obligatorio.',
            'pco_id_com.required'       => 'La comunidad es obligatoria.',
            'pco_id_tpr.required'       => 'El tipo de proyecto es obligatorio.',
            'pco_id_mpr.required'       => 'La modalidad es obligatoria.',
            'pco_resumen.required'      => 'El resumen es obligatorio.',
            'pco_resumen.max'           => 'El resumen no puede superar 150 caracteres.',
            'pco_fecha_limite.required' => 'La fecha límite es obligatoria.',
            'pco_fecha_limite.after'    => 'La fecha límite debe ser posterior a hoy.',
        ]);

        $proyecto = ProyectoComunidad::create([
            'pco_id_equ'          => $request->pco_id_equ,
            'pco_id_com'          => $request->pco_id_com,
            'pco_id_tpr'          => $request->pco_id_tpr,
            'pco_id_mpr'          => $request->pco_id_mpr,
            'pco_id_pco_previo'   => $request->pco_id_pco_previo,
            'pco_resumen'         => $request->pco_resumen,
            'pco_fecha_limite'    => $request->pco_fecha_limite,
            'pco_status'          => true,
            'pco_id_usu_created'  => auth()->id(),
        ]);

        if ($request->beneficiarios) {
            foreach ($request->beneficiarios as $ben) {
                ProyectoBeneficiario::create([
                    'pbe_id_pco'         => $proyecto->pco_id,
                    'pbe_id_tbe'         => $ben['tbe_id'],
                    'pbe_cantidad'       => $ben['cantidad'],
                    'pbe_id_usu_created' => auth()->id(),
                ]);
            }
        }

        return back()->with('success', 'Proyecto registrado correctamente.');
    }

    public function aprobar(Request $request, $id)
    {
        $proyecto = ProyectoComunidad::findOrFail($id);
        $proyecto->update([
            'pco_id_usu_aprobado'  => auth()->id(),
            'pco_fecha_aprobacion' => now()->toDateString(),
            'pco_id_usu_updated'   => auth()->id(),
        ]);
        return back()->with('success', 'Comunidad aprobada correctamente.');
    }

    public function destroy($id)
    {
        $proyecto = ProyectoComunidad::findOrFail($id);
        $proyecto->update([
            'pco_status'         => false,
            'pco_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Proyecto desactivado.');
    }
}