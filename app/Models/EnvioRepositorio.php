<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioRepositorio extends Model
{
    protected $table = 'envio_repositorio';
    protected $primaryKey = 'enr_id';
    public $timestamps = false;

    protected $fillable = [
        'enr_estado_envio',
        'enr_respuesta',
        'enr_fecha_envio',
        'enr_created_at',
        'enr_id_equ',
        'enr_id_rpr',
        'enr_id_usu_created',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'enr_id_equ', 'equ_id');
    }

    public function resultadoProyecto()
    {
        return $this->belongsTo(ResultadoProyecto::class, 'enr_id_rpr', 'rpr_id');
    }
}