<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'seccion';
    protected $primaryKey = 'sec_id';
    public $timestamps = false;

    protected $fillable = [
        'sec_codigo',
        'sec_sogac_id',
        'sec_status',
        'sec_created_at',
        'sec_updated_at',
        'sec_id_tra',
        'sec_id_tur',
        'sec_id_usu_created',
        'sec_id_usu_updated',
    ];

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'sec_id_tra', 'tra_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'sec_id_tur', 'tur_id');
    }

    public function profesores()
    {
        return $this->hasMany(ProfesorSeccion::class, 'pse_id_sec', 'sec_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(EstudianteSeccion::class, 'ese_id_sec', 'sec_id');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'equ_id_sec', 'sec_id');
    }
}