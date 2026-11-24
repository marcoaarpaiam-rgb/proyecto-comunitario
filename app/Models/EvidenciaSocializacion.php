<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaSocializacion extends Model
{
    protected $table = 'evidencia_socializacion';
    protected $primaryKey = 'eso_id';
    public $timestamps = false;

    protected $fillable = [
        'eso_nombre_archivo',
        'eso_ruta',
        'eso_descripcion',
        'eso_fecha_subida',
        'eso_id_soc',
        'eso_id_usu_created',
    ];

    public function socializacion()
    {
        return $this->belongsTo(Socializacion::class, 'eso_id_soc', 'soc_id');
    }
}