<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEventoEquipo extends Model
{
    protected $table = 'tipo_evento_equipo';
    protected $primaryKey = 'tee_id';
    public $timestamps = false;

    protected $fillable = [
        'tee_nombre',
        'tee_status',
        'tee_created_at',
        'tee_updated_at',
        'tee_id_usu_created',
        'tee_id_usu_updated',
    ];

    public function historialesEquipo()
    {
        return $this->hasMany(HistorialEquipo::class, 'heq_id_tee', 'tee_id');
    }
}