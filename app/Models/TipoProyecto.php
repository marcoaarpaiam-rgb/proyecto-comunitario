<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProyecto extends Model
{
    protected $table = 'tipo_proyecto';
    protected $primaryKey = 'tpr_id';
    public $timestamps = false;

    protected $fillable = [
        'tpr_nombre',
        'tpr_status',
        'tpr_created_at',
        'tpr_updated_at',
        'tpr_id_usu_created',
        'tpr_id_usu_updated',
    ];

    public function proyectosComunidad()
    {
        return $this->hasMany(ProyectoComunidad::class, 'pco_id_tpr', 'tpr_id');
    }
}