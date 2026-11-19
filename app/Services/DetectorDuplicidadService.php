<?php

namespace App\Services;

use App\Models\ProyectoComunidad;
use App\Models\Equipo;
use App\Models\ModalidadProyecto;
use Illuminate\Support\Facades\DB;

class DetectorDuplicidadService
{
    /**
     * Evalúa si un título/proyecto en una comunidad colisiona con otro proyecto existente.
     *
     * @param int|null $idComunidad
     * @param string|null $titulo
     * @param int|null $idEquipoExcluir
     * @param int|null $idTipoProyecto
     * @param string|null $justificacion
     * @param int|null $idModalidad
     * @return array
     */
    public function verificarCoincidencia(
        ?int $idComunidad,
        ?string $titulo,
        ?int $idEquipoExcluir = null,
        ?int $idTipoProyecto = null,
        ?string $justificacion = null,
        ?int $idModalidad = null
    ): array {
        if (!$idComunidad || empty(trim($titulo ?? ''))) {
            return [
                'hay_coincidencia'       => false,
                'porcentaje'             => 0,
                'proyecto_coincidente'   => null,
                'requiere_justificacion' => false,
                'es_valido'              => true,
                'mensaje_error'          => null,
            ];
        }

        $tituloNormalizado = mb_strtolower(trim($titulo), 'UTF-8');
        $palabrasConsultadas = array_filter(
            explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $tituloNormalizado)),
            fn($w) => mb_strlen($w) >= 4
        );

        // Buscar proyectos activos en la misma comunidad
        $proyectosQuery = ProyectoComunidad::where('pco_id_com', $idComunidad)
            ->where('pco_status', true)
            ->with(['equipo', 'tipoProyecto', 'modalidad']);

        if ($idEquipoExcluir) {
            $proyectosQuery->where('pco_id_equ', '!=', $idEquipoExcluir);
        }

        $proyectos = $proyectosQuery->get();

        $maxSimilitud = 0;
        $proyectoCoincidente = null;
        $motivoCoincidencia = '';

        foreach ($proyectos as $prev) {
            $tituloPrevio = mb_strtolower(trim($prev->equipo?->equ_titulo ?? ''), 'UTF-8');
            if (empty($tituloPrevio)) {
                continue;
            }

            // 1. Coincidencia exacta
            if ($tituloNormalizado === $tituloPrevio) {
                $maxSimilitud = 100.0;
                $proyectoCoincidente = $prev;
                $motivoCoincidencia = 'Título idéntico';
                break;
            }

            // 2. Similitud textual
            similar_text($tituloNormalizado, $tituloPrevio, $percent);

            // 3. Coincidencia de palabras clave
            $palabrasPrevias = array_filter(
                explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', '', $tituloPrevio)),
                fn($w) => mb_strlen($w) >= 4
            );
            $interseccion = array_intersect($palabrasConsultadas, $palabrasPrevias);
            $scorePalabras = count($palabrasConsultadas) > 0
                ? (count($interseccion) / count($palabrasConsultadas)) * 100
                : 0;

            $similitudCalculada = max($percent, $scorePalabras);

            // 4. Mismo tipo de proyecto en la comunidad incrementa la ponderación temática
            if ($idTipoProyecto && $prev->pco_id_tpr == $idTipoProyecto) {
                $similitudCalculada = max($similitudCalculada, 55.0);
            }

            if ($similitudCalculada > $maxSimilitud) {
                $maxSimilitud = round($similitudCalculada, 1);
                $proyectoCoincidente = $prev;
                $motivoCoincidencia = "Similitud temática del {$maxSimilitud}%";
            }
        }

        $hayCoincidencia = ($maxSimilitud >= 50.0 && $proyectoCoincidente !== null);

        if (!$hayCoincidencia) {
            return [
                'hay_coincidencia'       => false,
                'porcentaje'             => 0,
                'proyecto_coincidente'   => null,
                'requiere_justificacion' => false,
                'es_valido'              => true,
                'mensaje_error'          => null,
            ];
        }

        // Si hay coincidencia, la justificación es obligatoria (mínimo 15 caracteres)
        $justificacionLimpia = trim($justificacion ?? '');
        $tieneJustificacionValida = mb_strlen($justificacionLimpia) >= 15;

        if (!$tieneJustificacionValida) {
            $nomComunidad = $proyectoCoincidente->comunidad?->com_nombre ?? 'la comunidad seleccionada';
            $codEquipo = $proyectoCoincidente->equipo?->equ_codigo ?? 'otro equipo';
            $titEquipo = $proyectoCoincidente->equipo?->equ_titulo ?? 'proyecto similar';

            return [
                'hay_coincidencia'       => true,
                'porcentaje'             => $maxSimilitud,
                'proyecto_coincidente'   => [
                    'pco_id'     => $proyectoCoincidente->pco_id,
                    'equ_id'     => $proyectoCoincidente->equipo?->equ_id,
                    'equ_codigo' => $proyectoCoincidente->equipo?->equ_codigo,
                    'equ_titulo' => $proyectoCoincidente->equipo?->equ_titulo,
                    'tipo'       => $proyectoCoincidente->tipoProyecto?->tpr_nombre,
                ],
                'requiere_justificacion' => true,
                'es_valido'              => false,
                'mensaje_error'          => "Se detectó coincidencia temática ({$maxSimilitud}%) con el proyecto '{$titEquipo}' (Equipo {$codEquipo}) en {$nomComunidad}. Para registrar este proyecto, debe ingresar una justificación obligatoria de al menos 15 caracteres explicando el motivo (ej: continuación de fase, módulo distinto o alcance complementario).",
            ];
        }

        return [
            'hay_coincidencia'       => true,
            'porcentaje'             => $maxSimilitud,
            'proyecto_coincidente'   => [
                'pco_id'     => $proyectoCoincidente->pco_id,
                'equ_id'     => $proyectoCoincidente->equipo?->equ_id,
                'equ_codigo' => $proyectoCoincidente->equipo?->equ_codigo,
                'equ_titulo' => $proyectoCoincidente->equipo?->equ_titulo,
                'tipo'       => $proyectoCoincidente->tipoProyecto?->tpr_nombre,
            ],
            'requiere_justificacion' => true,
            'es_valido'              => true,
            'justificacion'          => $justificacionLimpia,
            'mensaje_error'          => null,
        ];
    }
}
