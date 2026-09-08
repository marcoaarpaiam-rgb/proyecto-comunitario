<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalidadProyecto extends Model
{
    protected $table = 'modalidad_proyecto';
    protected $primaryKey = 'mpr_id';
    public $timestamps = false;

    protected $fillable = [
        'mpr_nombre',
        'mpr_status',
        'mpr_created_at',
        'mpr_updated_at',
        'mpr_id_usu_created',
        'mpr_id_usu_updated',
    ];

    public function proyectosComunidad()
    {
        return $this->hasMany(ProyectoComunidad::class, 'pco_id_mpr', 'mpr_id');
    }
}