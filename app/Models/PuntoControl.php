<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoControl extends Model
{
    protected $table = 'punto_control';
    protected $primaryKey = 'puc_id';
    public $timestamps = false;

    protected $fillable = [
        'puc_nombre',
        'puc_descripcion',
        'puc_fecha_limite',
        'puc_fecha_reprogramada',
        'puc_motivo_reprogramacion',
        'puc_dias_aviso',
        'puc_orden',
        'puc_es_entregable',
        'puc_status',
        'puc_created_at',
        'puc_updated_at',
        'puc_id_tra',
        'puc_id_equ',
        'puc_id_usu_created',
        'puc_id_usu_updated',
    ];

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'puc_id_tra', 'tra_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'puc_id_equ', 'equ_id');
    }

    public function jurados()
    {
        return $this->hasMany(PuntoControlJurado::class, 'pcj_id_puc', 'puc_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoEquipo::class, 'seq_id_puc', 'puc_id');
    }

    public function entregables()
    {
        return $this->hasMany(Entregable::class, 'entr_id_puc', 'puc_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'not_id_puc', 'puc_id');
    }
}