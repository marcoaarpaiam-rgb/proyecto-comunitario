<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoComunidad extends Model
{
    protected $table = 'proyecto_comunidad';
    protected $primaryKey = 'pco_id';
    public $timestamps = false;

    protected $fillable = [
        'pco_resumen',
        'pco_fecha_limite',
        'pco_entregado_a_tiempo',
        'pco_fecha_aprobacion',
        'pco_status',
        'pco_created_at',
        'pco_updated_at',
        'pco_id_equ',
        'pco_id_com',
        'pco_id_tpr',
        'pco_id_mpr',
        'pco_id_pco_previo',
        'pco_id_usu_aprobado',
        'pco_id_usu_created',
        'pco_id_usu_updated',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'pco_id_equ', 'equ_id');
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class, 'pco_id_com', 'com_id');
    }

    public function tipoProyecto()
    {
        return $this->belongsTo(TipoProyecto::class, 'pco_id_tpr', 'tpr_id');
    }

    public function modalidad()
    {
        return $this->belongsTo(ModalidadProyecto::class, 'pco_id_mpr', 'mpr_id');
    }

    public function proyectoPrevio()
    {
        return $this->belongsTo(ProyectoComunidad::class, 'pco_id_pco_previo', 'pco_id');
    }

    public function continuaciones()
    {
        return $this->hasMany(ProyectoComunidad::class, 'pco_id_pco_previo', 'pco_id');
    }

    public function usuarioAprobador()
    {
        return $this->belongsTo(Usuario::class, 'pco_id_usu_aprobado', 'usu_id');
    }

    public function beneficiarios()
    {
        return $this->hasMany(ProyectoBeneficiario::class, 'pbe_id_pco', 'pco_id');
    }

    public function tutoresJurados()
    {
        return $this->hasMany(TutorJurado::class, 'tuj_id_pco', 'pco_id');
    }

    public function resultado()
    {
        return $this->hasOne(ResultadoProyecto::class, 'rpr_id_pco', 'pco_id');
    }

    public function cartaPresentacion()
    {
        return $this->hasOne(CartaPresentacion::class, 'cpr_id_pco', 'pco_id');
    }
}