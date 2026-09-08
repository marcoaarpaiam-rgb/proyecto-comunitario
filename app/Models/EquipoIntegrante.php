<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipoIntegrante extends Model
{
    protected $table = 'equipo_integrante';
    protected $primaryKey = 'ein_id';
    public $timestamps = false;

    protected $fillable = [
        'ein_es_lider',
        'ein_fecha_ingreso',
        'ein_fecha_salida',
        'ein_status',
        'ein_created_at',
        'ein_updated_at',
        'ein_id_equ',
        'ein_id_usu',
        'ein_id_usu_created',
        'ein_id_usu_updated',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'ein_id_equ', 'equ_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ein_id_usu', 'usu_id');
    }
}