<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoAcademico extends Model
{
    protected $table = 'periodos_academicos';
    protected $primaryKey = 'id_periodo';

    protected $fillable = [
        'lapso_academico',
        'tipo_semestre',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public static function periodoActivo()
    {
        return static::where('activo', true)->orderBy('id_periodo', 'desc')->first();
    }

    public static function lapsoActivoTexto(): string
    {
        $activo = static::periodoActivo();
        if ($activo && !empty($activo->lapso_academico)) {
            return $activo->lapso_academico;
        }

        return date('Y') . '-I';
    }

    /**
     * Valida si una fecha se encuentra dentro del rango del período académico activo.
     * Retorna mensaje de error si está fuera del rango, o null si es válida.
     */
    public static function validarFechaEnRango($fecha): ?string
    {
        if (empty($fecha)) {
            return null;
        }

        $periodo = static::periodoActivo();
        if (!$periodo || !$periodo->fecha_inicio || !$periodo->fecha_fin) {
            return null;
        }

        try {
            $f = \Carbon\Carbon::parse($fecha)->startOfDay();
            $ini = \Carbon\Carbon::parse($periodo->fecha_inicio)->startOfDay();
            $fin = \Carbon\Carbon::parse($periodo->fecha_fin)->endOfDay();

            if ($f->lt($ini) || $f->gt($fin)) {
                $iniTxt = $periodo->fecha_inicio->format('d/m/Y');
                $finTxt = $periodo->fecha_fin->format('d/m/Y');
                return "La fecha seleccionada ({$f->format('d/m/Y')}) debe estar comprendida dentro del período académico activo {$periodo->lapso_academico} (del {$iniTxt} al {$finTxt}).";
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
