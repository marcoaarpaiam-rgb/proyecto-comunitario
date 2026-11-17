<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'seccion';
    protected $primaryKey = 'sec_id';
    public $timestamps = false;

    protected $fillable = [
        'sec_codigo',
        'sec_sogac_id',
        'sec_status',
        'sec_created_at',
        'sec_updated_at',
        'sec_id_tra',
        'sec_semestre',
        'sec_id_tur',
        'sec_id_usu_created',
        'sec_id_usu_updated',
    ];

    public function trayecto()
    {
        return $this->belongsTo(Trayecto::class, 'sec_id_tra', 'tra_id');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'sec_id_tur', 'tur_id');
    }

    public function profesores()
    {
        return $this->hasMany(ProfesorSeccion::class, 'pse_id_sec', 'sec_id');
    }

    public function profesorActual()
    {
        return $this->hasOne(ProfesorSeccion::class, 'pse_id_sec', 'sec_id')->where('pse_status', true);
    }

    /**
     * Obtiene el número de semestre oficial de la sección (1 al 8).
     * Regla oficial UPTP: El primer número del código de la sección es el semestre del equipo.
     * Ejemplo: 131 -> Semestre 1, 231 -> Semestre 2, 331 -> Semestre 3, 431/441 -> Semestre 4.
     */
    public function getSemestreNumero(): int
    {
        // 1. Regla oficial: Primer dígito del código de la sección
        if (!empty($this->sec_codigo)) {
            $codigo = trim((string) $this->sec_codigo);
            if (preg_match('/\d/', $codigo, $matches)) {
                $primerDigito = (int) $matches[0];
                if ($primerDigito >= 1 && $primerDigito <= 8) {
                    return $primerDigito;
                }
            }
        }

        // 2. Si sec_semestre está en base de datos
        if (!empty($this->sec_semestre) && is_numeric($this->sec_semestre)) {
            return (int) $this->sec_semestre;
        }

        // 3. Fallback por trayecto
        if (!empty($this->sec_id_tra)) {
            return (int) $this->sec_id_tra;
        }

        return 1;
    }

    public function esSemestreImpar(): bool
    {
        return $this->getSemestreNumero() % 2 !== 0;
    }

    public function esSemestrePar(): bool
    {
        return $this->getSemestreNumero() % 2 === 0;
    }

    public function esAlfabetizacion(): bool
    {
        return (int) $this->sec_id_tra === 1;
    }

    public function estudiantes()
    {
        return $this->hasMany(EstudianteSeccion::class, 'ese_id_sec', 'sec_id');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'equ_id_sec', 'sec_id');
    }
}