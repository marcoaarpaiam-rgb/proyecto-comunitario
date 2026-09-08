<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    protected $table = 'usuario_rol';
    protected $primaryKey = 'uro_id';
    public $timestamps = false;

    protected $fillable = [
        'uro_fecha_inicio',
        'uro_fecha_fin',
        'uro_status',
        'uro_created_at',
        'uro_id_usu',
        'uro_id_rol',
        'uro_id_usu_created',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'uro_id_usu', 'usu_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'uro_id_rol', 'rol_id');
    }
}