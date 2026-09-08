<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoJurado extends Model
{
    protected $table = 'tipo_jurado';
    protected $primaryKey = 'tju_id';
    public $timestamps = false;

    protected $fillable = [
        'tju_nombre',
        'tju_status',
        'tju_created_at',
        'tju_updated_at',
        'tju_id_usu_created',
        'tju_id_usu_updated',
    ];

    public function tutoresJurados()
    {
        return $this->hasMany(TutorJurado::class, 'tuj_id_tju', 'tju_id');
    }

    public function juradosSocializacion()
    {
        return $this->hasMany(JuradoSocializacion::class, 'jso_id_tju', 'tju_id');
    }

    public function juradosPuntoControl()
    {
        return $this->hasMany(PuntoControlJurado::class, 'pcj_id_tju', 'tju_id');
    }
}