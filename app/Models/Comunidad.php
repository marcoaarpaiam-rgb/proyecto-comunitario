<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunidad extends Model
{
    protected $table = 'comunidad';
    protected $primaryKey = 'com_id';
    public $timestamps = false;

    protected $fillable = [
        'com_nombre',
        'com_ubicacion',
        'com_status',
        'com_created_at',
        'com_updated_at',
        'com_id_tor',
        'com_id_usu_created',
        'com_id_usu_updated',
    ];

    public function tipoOrganizacion()
    {
        return $this->belongsTo(TipoOrganizacion::class, 'com_id_tor', 'tor_id');
    }

    public function responsables()
    {
        return $this->hasMany(ResponsableComunidad::class, 'rco_id_com', 'com_id');
    }

    public function proyectos()
    {
        return $this->hasMany(ProyectoComunidad::class, 'pco_id_com', 'com_id');
    }
}