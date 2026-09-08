<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socializacion extends Model
{
    protected $table = 'socializacion';
    protected $primaryKey = 'soc_id';
    public $timestamps = false;

    protected $fillable = [
        'soc_fecha',
        'soc_hora',
        'soc_lugar',
        'soc_apto',
        'soc_aprobado',
        'soc_cantidad_jurados',
        'soc_observaciones_no_apto',
        'soc_observaciones',
        'soc_status',
        'soc_created_at',
        'soc_updated_at',
        'soc_id_equ',
        'soc_id_tra',
        'soc_id_tso',
        'soc_id_soc_previa',
        'soc_id_usu_created',
        'soc_id_usu_updated',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'soc_id_equ', 'equ_id');
    }

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'soc_id_tra', 'tra_id');
    }

    public function tipoSocializacion()
    {
        return $this->belongsTo(TipoSocializacion::class, 'soc_id_tso', 'tso_id');
    }

    public function socializacionPrevia()
    {
        return $this->belongsTo(Socializacion::class, 'soc_id_soc_previa', 'soc_id');
    }

    public function jurados()
    {
        return $this->hasMany(JuradoSocializacion::class, 'jso_id_soc', 'soc_id');
    }

    public function integrantes()
    {
        return $this->hasMany(IntegranteSocializacion::class, 'iso_id_soc', 'soc_id');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaSocializacion::class, 'eso_id_soc', 'soc_id');
    }

    public function entregables()
    {
        return $this->hasMany(Entregable::class, 'entr_id_soc', 'soc_id');
    }

    public function esPreSocializacion(): bool
    {
        return $this->tipoSocializacion?->tso_orden === 1;
    }

    public function esSocializacionFinal(): bool
    {
        return $this->tipoSocializacion?->tso_orden === 2;
    }
}