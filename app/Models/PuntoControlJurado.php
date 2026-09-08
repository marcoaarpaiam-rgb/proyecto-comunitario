<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoControlJurado extends Model
{
    protected $table = 'punto_control_jurado';
    protected $primaryKey = 'pcj_id';
    public $timestamps = false;

    protected $fillable = [
        'pcj_nombre_externo',
        'pcj_status',
        'pcj_created_at',
        'pcj_id_puc',
        'pcj_id_usu',
        'pcj_id_tju',
        'pcj_id_usu_created',
    ];

    public function puntoControl()
    {
        return $this->belongsTo(PuntoControl::class, 'pcj_id_puc', 'puc_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'pcj_id_usu', 'usu_id');
    }

    public function tipoJurado()
    {
        return $this->belongsTo(TipoJurado::class, 'pcj_id_tju', 'tju_id');
    }
}