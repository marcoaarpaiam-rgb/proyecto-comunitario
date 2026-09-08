<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponsableComunidad extends Model
{
    protected $table = 'responsable_comunidad';
    protected $primaryKey = 'rco_id';
    public $timestamps = false;

    protected $fillable = [
        'rco_fecha_inicio',
        'rco_fecha_fin',
        'rco_status',
        'rco_created_at',
        'rco_id_usu',
        'rco_id_com',
        'rco_id_usu_created',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'rco_id_usu', 'usu_id');
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'rco_id_com', 'com_id');
    }
}