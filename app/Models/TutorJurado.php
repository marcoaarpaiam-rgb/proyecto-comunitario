<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorJurado extends Model
{
    protected $table = 'tutor_jurado';
    protected $primaryKey = 'tuj_id';
    public $timestamps = false;

    protected $fillable = [
        'tuj_nombre_externo',
        'tuj_rol',
        'tuj_status',
        'tuj_created_at',
        'tuj_updated_at',
        'tuj_id_pco',
        'tuj_id_usu',
        'tuj_id_tju',
        'tuj_id_usu_created',
        'tuj_id_usu_updated',
    ];

    public function proyectoComunidad()
    {
        return $this->belongsTo(ProyectoComunidad::class, 'tuj_id_pco', 'pco_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'tuj_id_usu', 'usu_id');
    }

    public function tipoJurado()
    {
        return $this->belongsTo(TipoJurado::class, 'tuj_id_tju', 'tju_id');
    }
}