<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';
    protected $primaryKey = 'usu_id';
    public $timestamps = false;

    protected $fillable = [
        'usu_cedula',
        'usu_primer_nombre',
        'usu_segundo_nombre',
        'usu_primer_apellido',
        'usu_segundo_apellido',
        'usu_email',
        'usu_telefono',
        'usu_tiene_acceso',
        'usu_username',
        'usu_password',
        'usu_sogac_id',
        'usu_status',
        'usu_created_at',
        'usu_updated_at',
        'usu_id_usu_created',
        'usu_id_usu_updated',
        'usu_firma_digital',
    ];

    protected $hidden = ['usu_password'];

    protected $casts = [
        'usu_password' => 'hashed',
        'usu_tiene_acceso' => 'boolean',
        'usu_status' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->usu_password;
    }

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->usu_primer_nombre} {$this->usu_segundo_nombre} {$this->usu_primer_apellido} {$this->usu_segundo_apellido}");
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'uro_id_usu', 'uro_id_rol');
    }

    public function usuarioRoles()
    {
        return $this->hasMany(UsuarioRol::class, 'uro_id_usu', 'usu_id');
    }

    public function cargos()
    {
        return $this->hasMany(PersonaCargo::class, 'pca_id_usu', 'usu_id');
    }

    public function seccionesProfesor()
    {
        return $this->hasMany(ProfesorSeccion::class, 'pse_id_usu', 'usu_id');
    }

    public function seccionesEstudiante()
    {
        return $this->hasMany(EstudianteSeccion::class, 'ese_id_usu', 'usu_id');
    }

    public function equiposIntegrante()
    {
        return $this->hasMany(EquipoIntegrante::class, 'ein_id_usu', 'usu_id');
    }

    public function tutores()
    {
        return $this->hasMany(Tutor::class, 'tut_id_usu', 'usu_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'not_id_usu', 'usu_id');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'bit_id_usu', 'usu_id');
    }

    public function hasRol(string $rol): bool
    {
        return $this->roles()->where('rol_nombre', $rol)->exists();
    }

    public function esCoordinador(): bool { return $this->hasRol('coordinador'); }
    public function esProfesor(): bool { return $this->hasRol('profesor_proyecto'); }
    public function esLider(): bool { return $this->hasRol('lider'); }
}