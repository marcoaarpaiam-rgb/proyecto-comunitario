<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'not_id';
    public $timestamps = false;

    protected $fillable = [
        'not_mensaje',
        'not_leida',
        'not_fecha_generacion',
        'not_fecha_lectura',
        'not_id_usu',
        'not_id_tno',
        'not_id_equ',
        'not_id_puc',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'not_id_usu', 'usu_id');
    }

    public function tipoNotificacion()
    {
        return $this->belongsTo(TipoNotificacion::class, 'not_id_tno', 'tno_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'not_id_equ', 'equ_id');
    }

    public function puntoControl()
    {
        return $this->belongsTo(PuntoControl::class, 'not_id_puc', 'puc_id');
    }
}