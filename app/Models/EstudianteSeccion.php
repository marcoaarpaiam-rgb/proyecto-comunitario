<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudianteSeccion extends Model
{
    protected $table = 'estudiante_seccion';
    protected $primaryKey = 'ese_id';
    public $timestamps = false;

    protected $fillable = [
        'ese_id_usu',
        'ese_id_sec',
        'ese_fecha_asignacion',
        'ese_fecha_fin',
        'ese_status',
        'ese_id_usu_created',
    ];

    protected $casts = [
        'ese_status' => 'boolean',
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