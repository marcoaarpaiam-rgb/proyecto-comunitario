<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipo';
    protected $primaryKey = 'equ_id';
    public $timestamps = false;

    protected $fillable = [
        'equ_codigo',
        'equ_titulo',
        'equ_status',
        'equ_created_at',
        'equ_updated_at',
        'equ_id_sec',
        'equ_id_tra',
        'equ_id_usu_created',
        'equ_id_usu_updated',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'equ_id_sec', 'sec_id');
    }

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'equ_id_tra', 'tra_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialEquipo::class, 'heq_id_equ', 'equ_id');
    }

    public function integrantes()
    {
        return $this->hasMany(EquipoIntegrante::class, 'ein_id_equ', 'equ_id');
    }

    public function tutor()
    {
        return $this->hasOne(Tutor::class, 'tut_id_equ', 'equ_id');
    }

    public function proyectoComunidad()
    {
        return $this->hasOne(ProyectoComunidad::class, 'pco_id_equ', 'equ_id');
    }

    public function puntosControl()
    {
        return $this->hasMany(PuntoControl::class, 'puc_id_equ', 'equ_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoEquipo::class, 'seq_id_equ', 'equ_id');
    }

    public function socializaciones()
    {
        return $this->hasMany(Socializacion::class, 'soc_id_equ', 'equ_id');
    }

    public function entregables()
    {
        return $this->hasMany(Entregable::class, 'entr_id_equ', 'equ_id');
    }

    public function cartaPresentacion()
    {
        return $this->hasOneThrough(
            \App\Models\CartaPresentacion::class,
            \App\Models\ProyectoComunidad::class,
            'pco_id_equ', // FK en proyecto_comunidad → equipo
            'cpr_id_pco', // FK en carta_presentacion → proyecto_comunidad
            'equ_id',     // PK en equipo
            'pco_id'      // PK en proyecto_comunidad
        );
    }

    public function resultadoProyecto()
    {
        return $this->hasOneThrough(
            \App\Models\ResultadoProyecto::class,
            \App\Models\ProyectoComunidad::class,
            'pco_id_equ',
            'rpr_id_pco',
            'equ_id',
            'pco_id'
        );
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'not_id_equ', 'equ_id');
    }

    public function lider()
    {
        return $this->hasOne(EquipoIntegrante::class, 'ein_id_equ', 'equ_id')
            ->where('ein_es_lider', true)->where('ein_status', true);
    }
}