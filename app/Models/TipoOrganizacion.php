<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoOrganizacion extends Model
{
    protected $table = 'tipo_organizacion';
    protected $primaryKey = 'tor_id';
    public $timestamps = false;

    protected $fillable = [
        'tor_nombre',
        'tor_status',
        'tor_created_at',
        'tor_updated_at',
        'tor_id_usu_created',
        'tor_id_usu_updated',
    ];

    public function comunidades()
    {
        return $this->hasMany(Comunidad::class, 'com_id_tor', 'tor_id');
    }
}