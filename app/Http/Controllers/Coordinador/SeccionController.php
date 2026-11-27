<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Trayecto;
use App\Models\Turno;
use App\Models\Usuario;
use App\Models\ProfesorSeccion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SeccionController extends Controller
{
    /**
     * Calcula los semestres válidos para un trayecto dado.
     * Trayecto 1 → [1,2], Trayecto 2 → [3,4], etc.
     */
    private function semestresValidos(int $traId): array
    {
        return [($traId * 2) - 1, $traId * 2];
    }

    /**
     * Valida que el código numérico de sección pertenezca a alguno de los dos semestres del trayecto.
     * Ej: Trayecto 1 → 100-299, Trayecto 2 → 300-499, Trayecto 3 → 500-699, Trayecto 4 → 700-899.
     */
    private function validarCentena(string $codigo, int $traNumero): bool
    {
        if (!ctype_digit($codigo)) return true; // códigos no numéricos se omiten
        $num = (int) $codigo;
        if ($num < 100 || $num > 899) return false;
        $centena = (int) floor($num / 100);
        return (int) ceil($centena / 2) === $traNumero;
    }

    public function index()
    {
        return Inertia::render('Coordinador/Secciones', [
            'secciones'     => Seccion::with(['trayecto', 'turno', 'profesorActual.usuario'])
                ->where('sec_status', true)
                ->orderBy('sec_codigo')
                ->get(),
            'trayectos'     => Trayecto::where('tra_status', true)->get(),
            'turnos'        => Turno::where('tur_status', true)->get(),
            'periodoActivo' => \App\Models\PeriodoAcademico::periodoActivo(),
            'profesores'    => Usuario::where('usu_status', true)
                ->whereHas('usuarioRoles', fn($q) => $q->where('uro_status', true)
                    ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto')))
                ->orderBy('usu_primer_apellido')
                ->get(['usu_id', 'usu_primer_nombre', 'usu_primer_apellido', 'usu_cedula']),
        ]);
    }

    public function store(Request $request)
    {
        $trayecto = Trayecto::find($request->sec_id_tra);
        $semestresValidos = $trayecto ? $this->semestresValidos((int) $trayecto->tra_id) : [];

        $request->validate([
            'sec_codigo'   => 'required|string|max:20|unique:seccion,sec_codigo',
            'sec_id_tra'   => 'required|exists:trayecto,tra_id',
            'sec_semestre' => ['nullable', 'integer', 'min:1', 'max:8',
                Rule::in($semestresValidos ?: range(1, 8)),
            ],
            'sec_id_tur'   => 'required|exists:turno,tur_id',
            'pse_id_usu'   => 'nullable|exists:usuario,usu_id',
        ], [
            'sec_codigo.required'    => 'El código es obligatorio.',
            'sec_codigo.unique'      => 'Ya existe una sección con ese código.',
            'sec_id_tra.required'    => 'El trayecto es obligatorio.',
            'sec_id_tur.required'    => 'El turno es obligatorio.',
            'sec_semestre.in'        => $trayecto
                ? 'El semestre no corresponde al trayecto seleccionado. Para ' . $trayecto->tra_nombre . ' los semestres válidos son: ' . implode(' y ', $semestresValidos) . '.'
                : 'El semestre no es válido para el trayecto seleccionado.',
        ]);

        // Validación de centena del código
        if ($trayecto && !$this->validarCentena($request->sec_codigo, (int) $trayecto->tra_numero)) {
            $min = (($trayecto->tra_numero * 2) - 1) * 100;
            $max = ($trayecto->tra_numero * 2) * 100 + 99;
            return back()->withErrors([
                'sec_codigo' => "El código de la sección debe estar en el rango {$min}–{$max} para {$trayecto->tra_nombre}.",
            ])->withInput();
        }

        $primerDigito = preg_match('/\d/', (string) $request->sec_codigo, $m) ? (int)$m[0] : null;
        $seccion = Seccion::create([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_semestre'       => $request->sec_semestre ?? ($primerDigito ?? ($request->sec_id_tra ? ($request->sec_id_tra * 2 - 1) : 1)),
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_status'         => true,
            'sec_id_usu_created' => auth()->id(),
        ]);

        if ($request->pse_id_usu) {
            ProfesorSeccion::create([
                'pse_id_usu'           => $request->pse_id_usu,
                'pse_id_sec'           => $seccion->sec_id,
                'pse_fecha_asignacion' => now()->toDateString(),
                'pse_status'           => true,
                'pse_id_usu_created'   => auth()->id(),
            ]);
        }

        return back()->with('success', 'Sección creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $seccion  = Seccion::findOrFail($id);
        $trayecto = Trayecto::find($request->sec_id_tra);
        $semestresValidos = $trayecto ? $this->semestresValidos((int) $trayecto->tra_id) : [];

        $request->validate([
            'sec_codigo'   => "required|string|max:20|unique:seccion,sec_codigo,{$id},sec_id",
            'sec_id_tra'   => 'required|exists:trayecto,tra_id',
            'sec_semestre' => ['nullable', 'integer', 'min:1', 'max:8',
                Rule::in($semestresValidos ?: range(1, 8)),
            ],
            'sec_id_tur'   => 'required|exists:turno,tur_id',
            'pse_id_usu'   => 'nullable|exists:usuario,usu_id',
        ], [
            'sec_semestre.in' => $trayecto
                ? 'El semestre no corresponde al trayecto. Para ' . $trayecto->tra_nombre . ' los semestres válidos son: ' . implode(' y ', $semestresValidos) . '.'
                : 'El semestre no es válido para el trayecto seleccionado.',
        ]);

        // Validación de centena del código
        if ($trayecto && !$this->validarCentena($request->sec_codigo, (int) $trayecto->tra_numero)) {
            $min = (($trayecto->tra_numero * 2) - 1) * 100;
            $max = ($trayecto->tra_numero * 2) * 100 + 99;
            return back()->withErrors([
                'sec_codigo' => "El código de la sección debe estar en el rango {$min}–{$max} para {$trayecto->tra_nombre}.",
            ])->withInput();
        }

        $primerDigito = preg_match('/\d/', (string) $request->sec_codigo, $m) ? (int)$m[0] : null;
        $seccion->update([
            'sec_codigo'         => $request->sec_codigo,
            'sec_id_tra'         => $request->sec_id_tra,
            'sec_semestre'       => $request->sec_semestre ?? ($primerDigito ?? $seccion->sec_semestre),
            'sec_id_tur'         => $request->sec_id_tur,
            'sec_id_usu_updated' => auth()->id(),
        ]);

        if ($request->has('pse_id_usu')) {
            $nuevoProf = $request->pse_id_usu;
            $profActual = ProfesorSeccion::where('pse_id_sec', $seccion->sec_id)
                ->where('pse_status', true)
                ->first();

            if ($profActual && $profActual->pse_id_usu != $nuevoProf) {
                $profActual->update([
                    'pse_status'    => false,
                    'pse_fecha_fin' => now()->toDateString(),
                ]);
            }

            if ($nuevoProf && (!$profActual || $profActual->pse_id_usu != $nuevoProf)) {
                ProfesorSeccion::create([
                    'pse_id_usu'           => $nuevoProf,
                    'pse_id_sec'           => $seccion->sec_id,
                    'pse_fecha_asignacion' => now()->toDateString(),
                    'pse_status'           => true,
                    'pse_id_usu_created'   => auth()->id(),
                ]);
            }
        }

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