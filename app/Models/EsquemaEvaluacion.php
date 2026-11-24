<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsquemaEvaluacion extends Model
{
    protected $table = 'esquemas_evaluacion';
    protected $primaryKey = 'esq_id';

    protected $fillable = [
        'esq_nombre',
        'esq_descripcion',
        'esq_id_usu_creador',
        'esq_modo',
        'esq_escala_min',
        'esq_escala_max',
        'esq_criterios',
        'esq_nota_aprobatoria',
        'esq_activo',
        'esq_es_plantilla',
    ];

    protected $casts = [
        'esq_criterios'     => 'array',
        'esq_activo'        => 'boolean',
        'esq_es_plantilla'  => 'boolean',
        'esq_escala_min'    => 'decimal:2',
        'esq_escala_max'    => 'decimal:2',
        'esq_nota_aprobatoria' => 'decimal:2',
    ];

    // ─── Relaciones ───────────────────────────────────────────────

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'esq_id_usu_creador', 'usu_id');
    }

    public function secciones()
    {
        return $this->belongsToMany(
            Seccion::class,
            'esquema_seccion',
            'esq_sec_id_esq',
            'esq_sec_id_sec',
            'esq_id',
            'sec_id'
        )->withPivot('esq_sec_lapso', 'esq_sec_activo')
         ->withTimestamps();
    }
}
