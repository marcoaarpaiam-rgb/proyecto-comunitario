<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'rol_nombre',
        'rol_descripcion',
        'rol_status',
        'rol_created_at',
        'rol_updated_at',
        'rol_id_usu_created',
        'rol_id_usu_updated',
    ];

    public function usuariosRol()
    {
        return $this->hasMany(UsuarioRol::class, 'uro_id_rol', 'rol_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_rol', 'uro_id_rol', 'uro_id_usu');
    }
}