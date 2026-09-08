<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'bit_id';
    public $timestamps = false;

    protected $fillable = [
        'bit_accion',
        'bit_estado_anterior',
        'bit_estado_nuevo',
        'bit_registro_id',
        'bit_detalle',
        'bit_fecha_hora',
        'bit_id_usu',
        'bit_id_entidad',
    ];

    protected $casts = [
        'bit_estado_anterior' => 'array',
        'bit_estado_nuevo'    => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'bit_id_usu', 'usu_id');
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'bit_id_entidad', 'ent_id');
    }
}