<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\TipoProyecto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaestraController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoProyecto::query()->orderBy('tpr_nombre');
        if ($request->filled('buscar')) {
            $b = mb_strtolower(trim($request->buscar), 'UTF-8');
            $query->whereRaw("LOWER(TRIM(tpr_nombre)) LIKE ?", ["%{$b}%"]);
        }
        $tipos = $query->paginate(3)->withQueryString();

        return Inertia::render('Coordinador/TiposProyecto', [
            'tipos'   => $tipos,
            'filtros' => [
                'buscar' => $request->input('buscar', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tpr_nombre' => 'required|string|max:100',
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
        ]);

        $nombre = trim($request->tpr_nombre);
        $nombreLower = mb_strtolower($nombre, 'UTF-8');

        if (in_array($nombreLower, ['otro', 'otros', 'otra', 'otras'])) {
            return back()->withErrors(['tpr_nombre' => 'No se permite registrar opciones genéricas como "Otro" o "Otros".']);
        }

        $existe = TipoProyecto::whereRaw("LOWER(TRIM(tpr_nombre)) = ?", [$nombreLower])->exists();
        if ($existe) {
            return back()->withErrors(['tpr_nombre' => 'Ya existe una línea de investigación con ese nombre (no se permiten duplicados).']);
        }

        $tipo = TipoProyecto::create([
            'tpr_nombre'         => $nombre,
            'tpr_status'         => true,
            'tpr_id_usu_created' => auth()->id(),
        ]);

        if (!$request->header('X-Inertia') && ($request->wantsJson() || $request->ajax())) {
            return response()->json([
                'success'      => true,
                'message'      => 'Línea de investigación creada correctamente.',
                'tipoProyecto' => $tipo,
            ]);
        }

        return back()->with('success', 'Línea de investigación creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoProyecto::findOrFail($id);

        $request->validate([
            'tpr_nombre' => "required|string|max:100",
        ], [
            'tpr_nombre.required' => 'El nombre es obligatorio.',
        ]);

        $nombre = trim($request->tpr_nombre);
        $nombreLower = mb_strtolower($nombre, 'UTF-8');

        if (in_array($nombreLower, ['otro', 'otros', 'otra', 'otras'])) {
            return back()->withErrors(['tpr_nombre' => 'No se permite registrar opciones genéricas como "Otro" o "Otros".']);
        }

        $existe = TipoProyecto::whereRaw("LOWER(TRIM(tpr_nombre)) = ?", [$nombreLower])
            ->where('tpr_id', '!=', $id)
            ->exists();

        if ($existe) {
            return back()->withErrors(['tpr_nombre' => 'Ya existe otra línea de investigación con ese nombre (no se permiten duplicados).']);
        }

        $tipo->update([
            'tpr_nombre'         => $nombre,
            'tpr_id_usu_updated' => auth()->id(),
        ]);

        return back()->with('success', 'Línea de investigación actualizada.');
    }

    public function destroy($id)
    {
        $tipo = TipoProyecto::findOrFail($id);
        $tipo->update(['tpr_status' => false, 'tpr_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Línea de investigación desactivada.');
    }

    public function reactivar($id)
    {
        $tipo = TipoProyecto::findOrFail($id);
        $tipo->update(['tpr_status' => true, 'tpr_id_usu_updated' => auth()->id()]);
        return back()->with('success', 'Línea de investigación reactivada.');
    }

    /**
     * Permite crear una nueva opción rápida para cualquier catálogo
     * aplicando validación estricta de no duplicidad (TRIM / LOWER).
     */
    public function crearOpcionRapida(Request $request)
    {
        $request->validate([
            'catalogo' => 'required|in:linea_investigacion,modalidad,tipo_organizacion,tipo_encuentro,tipo_beneficiario,tipo_jurado,tipo_entregable',
            'nombre'   => 'required|string|max:150',
        ], [
            'catalogo.required' => 'El tipo de catálogo es obligatorio.',
            'nombre.required'   => 'El nombre de la nueva opción es obligatorio.',
        ]);

        $catalogo    = $request->catalogo;
        $nombre      = trim($request->nombre);
        $nombreLower = mb_strtolower($nombre, 'UTF-8');
        $userId      = auth()->id();

        if (in_array($nombreLower, ['otro', 'otros', 'otra', 'otras'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se permite registrar opciones genéricas como "Otro" o "Otros". Especifique el nombre real.'
            ], 422);
        }

        switch ($catalogo) {
            case 'linea_investigacion':
                $item = \App\Models\TipoProyecto::whereRaw("LOWER(TRIM(tpr_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\TipoProyecto::create([
                        'tpr_nombre'         => $nombre,
                        'tpr_status'         => true,
                        'tpr_id_usu_created' => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tpr_id,
                    'nombre'  => $item->tpr_nombre,
                    'message' => 'Línea de investigación registrada exitosamente.'
                ]);

            case 'modalidad':
                $item = \App\Models\ModalidadProyecto::whereRaw("LOWER(TRIM(mpr_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\ModalidadProyecto::create([
                        'mpr_nombre'         => $nombre,
                        'mpr_status'         => true,
                        'mpr_id_usu_created' => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->mpr_id,
                    'nombre'  => $item->mpr_nombre,
                    'message' => 'Modalidad registrada exitosamente.'
                ]);

            case 'tipo_organizacion':
                $item = \App\Models\TipoOrganizacion::whereRaw("LOWER(TRIM(tor_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\TipoOrganizacion::create([
                        'tor_nombre'         => $nombre,
                        'tor_status'         => true,
                        'tor_id_usu_created' => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tor_id,
                    'nombre'  => $item->tor_nombre,
                    'message' => 'Tipo de organización registrado exitosamente.'
                ]);

            case 'tipo_encuentro':
                $item = \App\Models\TipoEncuentro::whereRaw("LOWER(TRIM(tep_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\TipoEncuentro::create([
                        'tep_nombre' => $nombre,
                        'tep_activo' => true,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tep_id,
                    'nombre'  => $item->tep_nombre,
                    'message' => 'Tipo de encuentro registrado exitosamente.'
                ]);

            case 'tipo_beneficiario':
                $item = \App\Models\TipoBeneficiario::whereRaw("LOWER(TRIM(tbe_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\TipoBeneficiario::create([
                        'tbe_nombre'         => $nombre,
                        'tbe_status'         => true,
                        'tbe_id_usu_created' => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tbe_id,
                    'nombre'  => $item->tbe_nombre,
                    'message' => 'Tipo de beneficiario registrado exitosamente.'
                ]);

            case 'tipo_jurado':
                $item = \App\Models\TipoJurado::whereRaw("LOWER(TRIM(tju_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $item = \App\Models\TipoJurado::create([
                        'tju_nombre'         => $nombre,
                        'tju_status'         => true,
                        'tju_id_usu_created' => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tju_id,
                    'nombre'  => $item->tju_nombre,
                    'message' => 'Tipo de jurado registrado exitosamente.'
                ]);

            case 'tipo_entregable':
                $item = \App\Models\TipoEntregableTrayecto::whereRaw("LOWER(TRIM(tet_nombre)) = ?", [$nombreLower])->first();
                if (!$item) {
                    $trayectoNum = (int) ($request->input('trayecto') ?: 1);
                    $item = \App\Models\TipoEntregableTrayecto::create([
                        'tet_nombre'          => $nombre,
                        'tet_trayecto_numero' => $trayectoNum,
                        'tet_status'          => true,
                        'tet_id_usu_created'  => $userId,
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id'      => $item->tet_id,
                    'nombre'  => $item->tet_nombre,
                    'message' => 'Tipo de entregable registrado exitosamente.'
                ]);
        }

        return response()->json(['success' => false, 'message' => 'Catálogo no válido.'], 400);
    }
}