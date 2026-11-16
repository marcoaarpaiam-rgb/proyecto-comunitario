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
}
