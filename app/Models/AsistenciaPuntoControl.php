<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaPuntoControl extends Model
{
    protected $table = 'asistencia_punto_control';
    protected $primaryKey = 'apc_id';
    public $timestamps = false;

    protected $fillable = [
        'apc_id_seq',
        'apc_id_usu',
        'apc_asistio',
        'apc_justificado',
        'apc_participo',
        'apc_justificativo',
        'apc_observaciones',
        'apc_motivo_edicion',
        'apc_created_at',
        'apc_updated_at',
        'apc_id_usu_created',
        'apc_id_usu_updated',
    ];

    protected $casts = [
        'apc_asistio'     => 'boolean',
        'apc_justificado' => 'boolean',
        'apc_participo'   => 'boolean',
        'apc_created_at'  => 'datetime',
        'apc_updated_at'  => 'datetime',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoEquipo::class, 'apc_id_seq', 'seq_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'apc_id_usu', 'usu_id');
    }

    public function usuarioUpdated()
    {
        return $this->belongsTo(Usuario::class, 'apc_id_usu_updated', 'usu_id');
    }

    public function estudiante()
    {
        return $this->usuario();
    }
}
