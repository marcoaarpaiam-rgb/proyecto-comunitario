<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trayecto extends Model
{
    protected $table = 'trayecto';
    protected $primaryKey = 'tra_id';
    public $timestamps = false;

    protected $fillable = [
        'tra_numero',
        'tra_nombre',
        'tra_status',
        'tra_created_at',
        'tra_updated_at',
        'tra_id_pnf',
        'tra_id_usu_created',
        'tra_id_usu_updated',
    ];

    public function pnf()
    {
        return $this->belongsTo(Pnf::class, 'tra_id_pnf', 'pnf_id');
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'sec_id_tra', 'tra_id');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'equ_id_tra', 'tra_id');
    }

    public function puntosControl()
    {
        return $this->hasMany(PuntoControl::class, 'puc_id_tra', 'tra_id');
    }

    public function socializaciones()
    {
        return $this->hasMany(Socializacion::class, 'soc_id_tra', 'tra_id');
    }

    public function resultadosProyecto()
    {
        return $this->hasMany(ResultadoProyecto::class, 'rpr_id_tra', 'tra_id');
    }
    
}