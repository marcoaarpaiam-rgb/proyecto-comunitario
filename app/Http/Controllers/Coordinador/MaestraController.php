<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\TipoProyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaestraController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/TiposProyecto', [
            'tipos' => TipoProyecto::orderBy('tpr_nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tpr_nombre' => 'required|string|max:100|unique:tipo_proyecto,tpr_nombre',
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
            'tpr_nombre.unique'   => 'Ya existe un tipo de proyecto con ese nombre.',
        ]);

        TipoProyecto::create([
            'tpr_nombre'        => $request->tpr_nombre,
            'tpr_status'        => true,
            'tpr_id_usu_created' => auth()->id(),
        ]);

        return back()->with('success', 'Tipo de proyecto creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoProyecto::findOrFail($id);

        $request->validate([
            'tpr_nombre' => "required|string|max:100|unique:tipo_proyecto,tpr_nombre,{$id},tpr_id",
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
            'tpr_nombre.unique'   => 'Ya existe un tipo de proyecto con ese nombre.',
        ]);

        $tipo->update([
            'tpr_nombre'        => $request->tpr_nombre,
            'tpr_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Tipo de proyecto actualizado.');
    }

    public function destroy($id)
    {
        $tipo = TipoProyecto::findOrFail($id);
        $tipo->update(['tpr_status' => false, 'tpr_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Tipo de proyecto desactivado.');
    }
}