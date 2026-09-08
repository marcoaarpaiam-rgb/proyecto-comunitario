<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    protected $table = 'estado_proyecto';
    protected $primaryKey = 'epr_id';
    public $timestamps = false;

    protected $fillable = [
        'epr_nombre',
        'epr_status',
        'epr_created_at',
        'epr_updated_at',
        'epr_id_usu_created',
        'epr_id_usu_updated',
    ];

    public function resultadosProyecto()
    {
        return $this->hasMany(ResultadoProyecto::class, 'rpr_id_epr', 'epr_id');
    }
}