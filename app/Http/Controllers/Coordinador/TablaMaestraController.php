<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TablaMaestraController extends Controller
{
    private function config(string $tabla): array
    {
        $configs = [
            'modalidades' => [
                'tabla'    => 'modalidad_proyecto',
                'pk'       => 'mpr_id',
                'campo'    => 'mpr_nombre',
                'status'   => 'mpr_status',
                'created'  => 'mpr_id_usu_created',
                'updated'  => 'mpr_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/Modalidades',
                'titulo'   => 'Modalidades de Proyecto',
                'subtitulo'=> 'Nuevo, Continuación, Reingeniería, etc.',
                'max'      => 100,
            ],
            'tipos-jurado' => [
                'tabla'    => 'tipo_jurado',
                'pk'       => 'tju_id',
                'campo'    => 'tju_nombre',
                'status'   => 'tju_status',
                'created'  => 'tju_id_usu_created',
                'updated'  => 'tju_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/TiposJurado',
                'titulo'   => 'Tipos de Jurado',
                'subtitulo'=> 'Invitado, Institucional, De Comunidad',
                'max'      => 100,
            ],
            'tipos-beneficiario' => [
                'tabla'    => 'tipo_beneficiario',
                'pk'       => 'tbe_id',
                'campo'    => 'tbe_nombre',
                'status'   => 'tbe_status',
                'created'  => 'tbe_id_usu_created',
                'updated'  => 'tbe_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/TiposBeneficiario',
                'titulo'   => 'Tipos de Beneficiario',
                'subtitulo'=> 'Estudiantes, Profesores, Comunidad, etc.',
                'max'      => 100,
            ],
            'tipos-evento-equipo' => [
                'tabla'    => 'tipo_evento_equipo',
                'pk'       => 'tee_id',
                'campo'    => 'tee_nombre',
                'status'   => 'tee_status',
                'created'  => 'tee_id_usu_created',
                'updated'  => 'tee_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/TiposEventoEquipo',
                'titulo'   => 'Tipos de Evento de Equipo',
                'subtitulo'=> 'Disuelto, Reactivado, Modificado, etc.',
                'max'      => 50,
            ],
            'turnos' => [
                'tabla'    => 'turno',
                'pk'       => 'tur_id',
                'campo'    => 'tur_nombre',
                'status'   => 'tur_status',
                'created'  => 'tur_id_usu_created',
                'updated'  => 'tur_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/Turnos',
                'titulo'   => 'Turnos',
                'subtitulo'=> 'Mañana, Tarde, Noche, Fin de Semana',
                'max'      => 50,
            ],
            'estados-proyecto' => [
                'tabla'    => 'estado_proyecto',
                'pk'       => 'epr_id',
                'campo'    => 'epr_nombre',
                'status'   => 'epr_status',
                'created'  => 'epr_id_usu_created',
                'updated'  => 'epr_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/EstadosProyecto',
                'titulo'   => 'Estados de Proyecto',
                'subtitulo'=> 'Aprobado, Aplazado, No Presentado, etc.',
                'max'      => 50,
            ],
            'tipos-entregable' => [
                'tabla'    => 'tipo_entregable_trayecto',
                'pk'       => 'tet_id',
                'campo'    => 'tet_nombre',
                'status'   => 'tet_status',
                'created'  => 'tet_id_usu_created',
                'updated'  => 'tet_id_usu_updated',
                'vista'    => 'Coordinador/Maestras/TiposEntregable',
                'titulo'   => 'Tipos de Entregable',
                'subtitulo'=> 'Manuales, Guías por trayecto',
                'max'      => 150,
                'extra'    => 'tet_trayecto_numero',
            ],
        ];

        return $configs[$tabla] ?? abort(404);
    }

    public function index(string $tabla)
    {
        $c = $this->config($tabla);
        $registros = DB::table($c['tabla'])->orderBy($c['campo'])->get();
        return Inertia::render('Coordinador/Maestras/GenericaMaestra', [
            'registros' => $registros,
            'config'    => $c,
            'tabla'     => $tabla,
        ]);
    }

    public function store(Request $request, string $tabla)
    {
        $c = $this->config($tabla);
        $request->validate([
            $c['campo'] => "required|string|max:{$c['max']}|unique:{$c['tabla']},{$c['campo']}",
        ]);

        $data = [
            $c['campo']   => $request->input($c['campo']),
            $c['status']  => true,
            $c['created'] => auth()->id(),
        ];

        if (isset($c['extra']) && $request->has($c['extra'])) {
            $data[$c['extra']] = $request->input($c['extra']);
        }

        DB::table($c['tabla'])->insert($data);
        return back()->with('success', 'Registro creado correctamente.');
    }

    public function update(Request $request, string $tabla, $id)
    {
        $c = $this->config($tabla);
        $request->validate([
            $c['campo'] => "required|string|max:{$c['max']}|unique:{$c['tabla']},{$c['campo']},{$id},{$c['pk']}",
        ]);

        $data = [
            $c['campo']   => $request->input($c['campo']),
            $c['updated'] => auth()->id(),
        ];

        if (isset($c['extra']) && $request->has($c['extra'])) {
            $data[$c['extra']] = $request->input($c['extra']);
        }

        DB::table($c['tabla'])->where($c['pk'], $id)->update($data);
        return back()->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(string $tabla, $id)
    {
        $c = $this->config($tabla);
        DB::table($c['tabla'])->where($c['pk'], $id)->update([
            $c['status']  => false,
            $c['updated'] => auth()->id(),
        ]);
        return back()->with('success', 'Registro desactivado.');
    }
}