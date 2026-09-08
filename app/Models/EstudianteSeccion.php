<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudianteSeccion extends Model
{
    protected $table = 'estudiante_seccion';
    protected $primaryKey = 'ese_id';
    public $timestamps = false;

    protected $fillable = [
        'ese_fecha_asignacion',
        'ese_fecha_fin',
        'ese_status',
        'ese_created_at',
        'ese_updated_at',
        'ese_id_usu',
        'ese_id_sec',
        'ese_id_usu_created',
        'ese_id_usu_updated',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ese_id_usu', 'usu_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'ese_id_sec', 'sec_id');
    }
}