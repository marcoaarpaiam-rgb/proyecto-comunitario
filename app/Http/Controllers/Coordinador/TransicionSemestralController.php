<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Seccion;
use App\Models\Equipo;
use App\Services\TransicionSemestralService;
use Illuminate\Support\Facades\DB;

class TransicionSemestralController extends Controller
{
    protected TransicionSemestralService $transicionService;

    public function __construct(TransicionSemestralService $transicionService)
    {
        $this->transicionService = $transicionService;
    }

    public function index(Request $request)
    {
        // Secciones activas del semestre impar (primer dígito 1, 3, 5, 7 o sec_semestre impar)
        $seccionesImpares = Seccion::with([
                'trayecto',
                'turno',
                'profesorActual.usuario',
                'equipos' => function ($q) {
                    $q->where('equ_status', true)
                      ->with([
                          'proyectoComunidad.comunidad',
                          'integrantes.usuario',
                          'socializaciones.tipoSocializacion',
                          'puntosControl.seguimientos',
                      ]);
                }
            ])
            ->where('sec_status', true)
            ->where(function ($q) {
                $q->whereRaw('MOD(sec_semestre, 2) != 0')
                  ->orWhereRaw('SUBSTRING(sec_codigo, 1, 1) IN ("1", "3", "5", "7")');
            })
            ->orderBy('sec_codigo')
            ->get();

        // Mapear equipos con sus datos de promoción hacia semestre par
        $seccionesData = $seccionesImpares->map(function ($sec) {
            $primerDigito = (int) substr((string) $sec->sec_codigo, 0, 1);
            $nuevoDigito = $primerDigito + 1;
            $codigoDestino = $nuevoDigito . substr((string) $sec->sec_codigo, 1);

            $equipos = $sec->equipos->map(function ($eq) use ($sec, $codigoDestino) {
                // Verificar si aprobó o completó Pre-Socialización (orden 1)
                $preSoc = $eq->socializaciones
                    ->filter(fn($s) => $s->tipoSocializacion?->tso_orden === 1)
                    ->sortByDesc('soc_fecha')
                    ->first();

                $preSocApta = $preSoc ? ($preSoc->soc_apto === true || $preSoc->soc_apto == 1) : false;

                // Contar puntos de control evaluados
                $puntosCumplidos = $eq->puntosControl
                    ->filter(fn($p) => $p->seguimientos->where('seq_cumplido', true)->isNotEmpty())
                    ->count();

                return [
                    'equ_id'             => $eq->equ_id,
                    'equ_codigo'         => $eq->equ_codigo,
                    'equ_titulo'         => $eq->equ_titulo,
                    'proyecto_resumen'   => $eq->proyectoComunidad?->pco_resumen,
                    'comunidad_nombre'   => $eq->proyectoComunidad?->comunidad?->com_nombre ?? 'Sin comunidad asignada',
                    'integrantes_count'  => $eq->integrantes->where('ein_status', true)->count(),
                    'lider'              => $eq->integrantes->firstWhere('ein_es_lider', true)?->usuario?->usu_primer_nombre . ' ' . $eq->integrantes->firstWhere('ein_es_lider', true)?->usuario?->usu_primer_apellido,
                    'seccion_actual'     => $sec->sec_codigo,
                    'seccion_destino'    => $codigoDestino,
                    'pre_soc_apta'       => $preSocApta,
                    'pre_soc_evaluada'   => $preSoc ? ($preSoc->soc_apto !== null) : false,
                    'puntos_cumplidos'   => $puntosCumplidos,
                ];
            });

            return [
                'sec_id'             => $sec->sec_id,
                'sec_codigo'         => $sec->sec_codigo,
                'sec_codigo_destino' => $codigoDestino,
                'trayecto'           => $sec->trayecto?->tra_nombre,
                'turno'              => $sec->turno?->tur_nombre,
                'profesor'           => $sec->profesorActual?->usuario ? "{$sec->profesorActual->usuario->usu_primer_nombre} {$sec->profesorActual->usuario->usu_primer_apellido}" : 'Sin profesor asignado',
                'total_equipos'      => $equipos->count(),
                'equipos'            => $equipos,
            ];
        });

        // Secciones pares para gestión de Cierre Definitivo y Repositorio Institucional
        $seccionesPares = Seccion::with([
                'trayecto',
                'turno',
                'profesorActual.usuario',
                'equipos' => function ($q) {
                    $q->where('equ_status', true)
                      ->with([
                          'proyectoComunidad.comunidad',
                          'integrantes.usuario',
                      ]);
                }
            ])
            ->where('sec_status', true)
            ->where(function ($q) {
                $q->whereRaw('MOD(sec_semestre, 2) = 0')
                  ->orWhereRaw('SUBSTRING(sec_codigo, 1, 1) IN ("2", "4", "6", "8")');
            })
            ->orderBy('sec_codigo')
            ->get();

        $seccionesParesData = $seccionesPares->map(function ($sec) {
            $equipos = $sec->equipos->map(function ($eq) {
                $acta = DB::table('actas_finales_trayecto as aft')
                    ->join('proyecto_comunidad as pco', 'pco.pco_id', '=', 'aft.id_proyecto')
                    ->where('pco.pco_id_equ', $eq->equ_id)
                    ->select('aft.calificacion_definitiva_cuantitativa', 'aft.calificacion_definitiva_cualitativa', 'aft.cerrada', 'aft.hash_verificacion')
                    ->first();

                $envioRepo = DB::table('envio_repositorio')
                    ->where('enr_id_equ', $eq->equ_id)
                    ->first();

                return [
                    'equ_id'               => $eq->equ_id,
                    'equ_codigo'           => $eq->equ_codigo,
                    'equ_titulo'           => $eq->equ_titulo,
                    'comunidad_nombre'     => $eq->proyectoComunidad?->comunidad?->com_nombre ?? 'Sin comunidad asignada',
                    'integrantes_count'    => $eq->integrantes->where('ein_status', true)->count(),
                    'acta_cerrada'         => (bool) ($acta?->cerrada ?? false),
                    'calificacion'         => $acta ? (float) $acta->calificacion_definitiva_cuantitativa : null,
                    'mencion'              => $acta?->calificacion_definitiva_cualitativa,
                    'hash_verificacion'    => $acta?->hash_verificacion,
                    'en_repositorio'       => (bool) $envioRepo,
                    'fecha_envio_repo'     => $envioRepo?->enr_fecha_envio,
                    'hash_repositorio'     => $envioRepo?->enr_hash_archivo,
                    'aprobado_para_cierre' => $acta && ((float) $acta->calificacion_definitiva_cuantitativa >= 16.00),
                ];
            });

            return [
                'sec_id'        => $sec->sec_id,
                'sec_codigo'    => $sec->sec_codigo,
                'trayecto'      => $sec->trayecto?->tra_nombre,
                'turno'         => $sec->turno?->tur_nombre,
                'profesor'      => $sec->profesorActual?->usuario ? "{$sec->profesorActual->usuario->usu_primer_nombre} {$sec->profesorActual->usuario->usu_primer_apellido}" : 'Sin profesor asignado',
                'total_equipos' => $equipos->count(),
                'equipos'       => $equipos,
            ];
        });

        return Inertia::render('Coordinador/TransicionSemestral', [
            'seccionesImpares' => $seccionesData,
            'seccionesPares'   => $seccionesParesData,
        ]);
    }

    public function promoverEquipo(int $id_equipo)
    {
        try {
            $resultado = $this->transicionService->transicionSemestreImparAPar($id_equipo, auth()->id());
            return redirect()->back()->with('success', $resultado['mensaje']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function promoverSeccion(int $id_sec)
    {
        try {
            $equipos = Equipo::where('equ_id_sec', $id_sec)
                ->where('equ_status', true)
                ->pluck('equ_id');

            if ($equipos->isEmpty()) {
                return redirect()->back()->with('error', 'La sección no tiene equipos activos para promover.');
            }

            $promovidos = 0;
            $errores = [];

            foreach ($equipos as $equId) {
                try {
                    $this->transicionService->transicionSemestreImparAPar($equId, auth()->id());
                    $promovidos++;
                } catch (\Exception $e) {
                    $errores[] = $e->getMessage();
                }
            }

            if ($promovidos > 0) {
                $msg = "Se promovieron exitosamente {$promovidos} equipo(s) al semestre par.";
                if (!empty($errores)) {
                    $msg .= " Observaciones: " . implode('; ', array_unique($errores));
                }
                return redirect()->back()->with('success', $msg);
            }

            return redirect()->back()->with('error', 'No se pudo promover ningún equipo: ' . implode('; ', array_unique($errores)));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cerrarRepositorioEquipo(int $id_equipo)
    {
        try {
            $resultado = $this->transicionService->transicionSemestreParARepositorio($id_equipo, auth()->id());
            return redirect()->back()->with('success', $resultado['mensaje']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cerrarRepositorioSeccion(int $id_sec)
    {
        try {
            $equipos = Equipo::where('equ_id_sec', $id_sec)
                ->where('equ_status', true)
                ->pluck('equ_id');

            if ($equipos->isEmpty()) {
                return redirect()->back()->with('error', 'La sección no tiene equipos activos para cerrar.');
            }

            $cerrados = 0;
            $errores = [];

            foreach ($equipos as $equId) {
                try {
                    $this->transicionService->transicionSemestreParARepositorio($equId, auth()->id());
                    $cerrados++;
                } catch (\Exception $e) {
                    $errores[] = $e->getMessage();
                }
            }

            if ($cerrados > 0) {
                $msg = "Se cerraron y archivaron en Repositorio Institucional exitosamente {$cerrados} proyecto(s).";
                if (!empty($errores)) {
                    $msg .= " Observaciones: " . implode('; ', array_unique($errores));
                }
                return redirect()->back()->with('success', $msg);
            }

            return redirect()->back()->with('error', 'No se pudo cerrar ningún proyecto en Repositorio: ' . implode('; ', array_unique($errores)));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Sincronizar automáticamente transiciones semestrales cuando SOGAC actualice secciones/periodos.
     */
    public function sincronizarSogac(Request $request)
    {
        try {
            $resultado = $this->transicionService->sincronizarTransicionSogac(auth()->id());
            return redirect()->back()->with('success', $resultado['mensaje'] ?? 'Sincronización semestral completada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
