<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegranteSocializacion extends Model
{
    protected $table = 'integrante_socializacion';
    protected $primaryKey = 'iso_id';
    public $timestamps = false;

    protected $fillable = [
        'iso_avanza',
        'iso_justificacion',
        'iso_created_at',
        'iso_id_soc',
        'iso_id_usu',
        'iso_id_usu_created',
    ];

    public function socializacion()
    {
        return $this->belongsTo(Socializacion::class, 'iso_id_soc', 'soc_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'iso_id_usu', 'usu_id');
    }
}