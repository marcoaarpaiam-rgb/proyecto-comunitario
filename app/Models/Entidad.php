<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    protected $table = 'entidad';
    protected $primaryKey = 'ent_id';
    public $timestamps = false;

    protected $fillable = [
        'ent_nombre',
        'ent_descripcion',
        'ent_status',
        'ent_created_at',
        'ent_updated_at',
        'ent_id_usu_created',
        'ent_id_usu_updated',
    ];

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'bit_id_entidad', 'ent_id');
    }
}