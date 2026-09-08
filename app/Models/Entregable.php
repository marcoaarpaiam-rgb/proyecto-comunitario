<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entregable extends Model
{
    protected $table = 'entregable';
    protected $primaryKey = 'entr_id';
    public $timestamps = false;

    protected $fillable = [
        'entr_nombre_archivo',
        'entr_ruta',
        'entr_version',
        'entr_es_entregable_final',
        'entr_a_tiempo',
        'entr_aprobado',
        'entr_fecha_aprobacion',
        'entr_observacion_rechazo',
        'entr_fecha_subida',
        'entr_status',
        'entr_id_equ',
        'entr_id_tet',
        'entr_id_puc',
        'entr_id_soc',
        'entr_id_usu_aprobado',
        'entr_id_usu_created',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'entr_id_equ', 'equ_id');
    }

    public function tipoEntregable()
    {
        return $this->belongsTo(TipoEntregableTrayecto::class, 'entr_id_tet', 'tet_id');
    }

    public function puntoControl()
    {
        return $this->belongsTo(PuntoControl::class, 'entr_id_puc', 'puc_id');
    }

    public function socializacion()
    {
        return $this->belongsTo(Socializacion::class, 'entr_id_soc', 'soc_id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(Usuario::class, 'entr_id_usu_aprobado', 'usu_id');
    }

    public function estaPendiente(): bool { return is_null($this->entr_aprobado); }
    public function estaAprobado(): bool { return $this->entr_aprobado === true; }
    public function estaRechazado(): bool { return $this->entr_aprobado === false; }
}