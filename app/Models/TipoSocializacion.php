<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSocializacion extends Model
{
    protected $table = 'tipo_socializacion';
    protected $primaryKey = 'tso_id';
    public $timestamps = false;

    protected $fillable = [
        'tso_nombre',
        'tso_orden',
        'tso_status',
        'tso_created_at',
        'tso_updated_at',
        'tso_id_usu_created',
        'tso_id_usu_updated',
    ];

    public function socializaciones()
    {
        return $this->hasMany(Socializacion::class, 'soc_id_tso', 'tso_id');
    }
}