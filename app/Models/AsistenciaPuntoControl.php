<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaPuntoControl extends Model
{
    protected $table = 'asistencia_punto_control';
    protected $primaryKey = 'apc_id';
    public $timestamps = false;

    protected $fillable = [
        'apc_asistio',
        'apc_observaciones',
        'apc_created_at',
        'apc_id_seq',
        'apc_id_usu',
        'apc_id_usu_created',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoEquipo::class, 'apc_id_seq', 'seq_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'apc_id_usu', 'usu_id');
    }
}