<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Turno;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeccionController extends Controller
{
    public function index()
    {
        return Inertia::render('Coordinador/Secciones', [
            'secciones' => Seccion::with(['trayecto', 'turno'])
                ->where('sec_status', true)
                ->orderBy('sec_codigo')
                ->get(),
            'trayectos' => Trayecto::where('tra_status', true)->get(),
            'turnos'    => Turno::where('tur_status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sec_codigo' => 'required|string|max:20|unique:seccion,sec_codigo',
            'sec_id_tra' => 'required|exists:trayecto,tra_id',
            'sec_id_tur' => 'required|exists:turno,tur_id',
        ], [
            'sec_codigo.required' => 'El código es obligatorio.',
            'sec_codigo.unique'   => 'Ya existe una sección con ese código.',
            'sec_id_tra.required' => 'El trayecto es obligatorio.',
            'sec_id_tur.required' => 'El turno es obligatorio.',
        ]);

        Seccion::create([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_status'         => true,
            'sec_id_usu_created' => auth()->id(),
        ]);

        return back()->with('success', 'Sección creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $seccion = Seccion::findOrFail($id);

        $request->validate([
            'sec_codigo' => "required|string|max:20|unique:seccion,sec_codigo,{$id},sec_id",
            'sec_id_tra' => 'required|exists:trayecto,tra_id',
            'sec_id_tur' => 'required|exists:turno,tur_id',
        ]);

        $seccion->update([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Sección actualizada correctamente.');
    }

    public function destroy($id)
    {
        $seccion = Seccion::findOrFail($id);
        $seccion->update([
            'sec_status'         => false,
            'sec_id_usu_updated' => auth()->id(),
        ]);
        return back()->with('success', 'Sección desactivada.');
    }
}