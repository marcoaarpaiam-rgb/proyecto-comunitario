<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoProyecto extends Model
{
    protected $table = 'resultado_proyecto';
    protected $primaryKey = 'rpr_id';
    public $timestamps = false;

    protected $fillable = [
        'rpr_habilitado_tardio',
        'rpr_observaciones',
        'rpr_fecha_registro',
        'rpr_status',
        'rpr_created_at',
        'rpr_updated_at',
        'rpr_id_pco',
        'rpr_id_equ',
        'rpr_id_tra',
        'rpr_id_epr',
        'rpr_id_usu_created',
        'rpr_id_usu_updated',
    ];

    public function proyectoComunidad()
    {
        return $this->belongsTo(ProyectoComunidad::class, 'rpr_id_pco', 'pco_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'rpr_id_equ', 'equ_id');
    }

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'rpr_id_tra', 'tra_id');
    }

    public function estadoProyecto()
    {
        return $this->belongsTo(EstadoProyecto::class, 'rpr_id_epr', 'epr_id');
    }

    public function envioRepositorio()
    {
        return $this->hasOne(EnvioRepositorio::class, 'enr_id_rpr', 'rpr_id');
    }

    public function esAprobado(): bool
    {
        return $this->estadoProyecto?->epr_nombre === 'Aprobado';
    }

    public function esAplazado(): bool
    {
        return $this->estadoProyecto?->epr_nombre === 'Aplazado';
    }
}