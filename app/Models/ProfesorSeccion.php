<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfesorSeccion extends Model
{
    protected $table = 'profesor_seccion';
    protected $primaryKey = 'pse_id';
    public $timestamps = false;

    protected $fillable = [
        'pse_fecha_asignacion',
        'pse_fecha_fin',
        'pse_status',
        'pse_created_at',
        'pse_updated_at',
        'pse_id_usu',
        'pse_id_sec',
        'pse_id_usu_created',
        'pse_id_usu_updated',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'pse_id_usu', 'usu_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'pse_id_sec', 'sec_id');
    }
}