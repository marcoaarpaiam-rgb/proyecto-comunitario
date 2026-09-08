<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoEquipo extends Model
{
    protected $table = 'seguimiento_equipo';
    protected $primaryKey = 'seq_id';
    public $timestamps = false;

    protected $fillable = [
        'seq_cumplido',
        'seq_fecha_cumplimiento',
        'seq_cumplido_a_tiempo',
        'seq_observaciones',
        'seq_es_previa_socializacion',
        'seq_fecha_registro',
        'seq_status',
        'seq_created_at',
        'seq_updated_at',
        'seq_id_equ',
        'seq_id_puc',
        'seq_id_usu_created',
        'seq_id_usu_updated',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'seq_id_equ', 'equ_id');
    }

    public function puntoControl()
    {
        return $this->belongsTo(PuntoControl::class, 'seq_id_puc', 'puc_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaPuntoControl::class, 'apc_id_seq', 'seq_id');
    }
}