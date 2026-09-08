<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JuradoSocializacion extends Model
{
    protected $table = 'jurado_socializacion';
    protected $primaryKey = 'jso_id';
    public $timestamps = false;

    protected $fillable = [
        'jso_nombre_externo',
        'jso_es_de_comunidad',
        'jso_created_at',
        'jso_id_soc',
        'jso_id_usu',
        'jso_id_tju',
        'jso_id_usu_created',
    ];

    public function socializacion()
    {
        return $this->belongsTo(Socializacion::class, 'jso_id_soc', 'soc_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'jso_id_usu', 'usu_id');
    }

    public function tipoJurado()
    {
        return $this->belongsTo(TipoJurado::class, 'jso_id_tju', 'tju_id');
    }
}