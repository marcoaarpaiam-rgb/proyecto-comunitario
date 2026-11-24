<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turno';
    protected $primaryKey = 'tur_id';
    public $timestamps = false;

    protected $fillable = [
        'tur_nombre',
        'tur_status',
        'tur_created_at',
        'tur_updated_at',
        'tur_id_usu_created',
        'tur_id_usu_updated',
    ];

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'sec_id_tur', 'tur_id');
    }
}