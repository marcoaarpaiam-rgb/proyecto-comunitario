<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoEntregablePnfi extends Model
{
    protected $table = 'catalogo_entregables_pnfi';
    protected $primaryKey = 'cep_id';

    protected $fillable = [
        'cep_id_trayecto',
        'cep_semestre',
        'cep_nombre',
        'cep_descripcion',
        'cep_semana_inicio_sugerida',
        'cep_semana_limite_sugerida',
        'cep_es_obligatorio',
        'cep_orden',
        'cep_activo',
    ];

    protected $casts = [
        'cep_es_obligatorio' => 'boolean',
        'cep_activo'         => 'boolean',
        'cep_semana_inicio_sugerida' => 'integer',
        'cep_semana_limite_sugerida' => 'integer',
        'cep_orden'                  => 'integer',
    ];

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'cep_id_trayecto', 'tra_id');
    }

    public function entregables()
    {
        return $this->hasMany(Entregable::class, 'entr_id_cep', 'cep_id');
    }
}
