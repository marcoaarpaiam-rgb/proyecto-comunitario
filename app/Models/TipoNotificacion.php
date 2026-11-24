<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoNotificacion extends Model
{
    protected $table = 'tipo_notificacion';
    protected $primaryKey = 'tno_id';
    public $timestamps = false;

    protected $fillable = [
        'tno_nombre',
        'tno_status',
        'tno_created_at',
        'tno_updated_at',
        'tno_id_usu_created',
        'tno_id_usu_updated',
    ];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'not_id_tno', 'tno_id');
    }
}