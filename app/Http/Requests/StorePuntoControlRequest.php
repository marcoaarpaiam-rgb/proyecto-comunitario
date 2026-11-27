<?php

namespace App\Http\Requests;

use App\Models\PeriodoAcademico;
use Illuminate\Foundation\Http\FormRequest;

class StorePuntoControlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esProfesor();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'puc_id_equ' => 'required|exists:equipo,equ_id',
            'puc_nombre' => 'required|string|max:150',
            'puc_semana' => 'required|integer|min:1|max:18',
            'puc_descripcion' => 'nullable|string',
            'puc_fecha_limite' => [
                'required',
                'date',
                'after_or_equal:today',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($error = PeriodoAcademico::validarFechaEnRango($value)) {
                        $fail($error);
                    }
                },
            ],
            'puc_dias_aviso' => 'required|integer|min:1|max:30',
        ];
    }

    /**
     * Custom error messages in Spanish.
     */
    public function messages(): array
    {
        return [
            'puc_id_equ.required'             => 'El equipo es obligatorio.',
            'puc_nombre.required'             => 'El nombre del punto de control es obligatorio.',
            'puc_semana.required'             => 'La semana del punto de control es obligatoria (rango del 1 al 18).',
            'puc_semana.integer'              => 'La semana debe ser un número entero válido (sin caracteres especiales ni letras).',
            'puc_semana.min'                  => 'La semana debe ser como mínimo la semana 1.',
            'puc_semana.max'                  => 'La semana no puede ser mayor a la semana 18.',
            'puc_fecha_limite.required'       => 'La fecha del encuentro es obligatoria.',
            'puc_fecha_limite.after_or_equal' => 'La fecha del encuentro no puede ser anterior al día de hoy.',
            'puc_dias_aviso.required'         => 'Los días de aviso son obligatorios.',
            'puc_dias_aviso.integer'          => 'Los días de aviso deben ser un número entero.',
            'puc_dias_aviso.min'              => 'Los días de aviso deben ser al menos 1 día.',
            'puc_dias_aviso.max'              => 'Los días de aviso no pueden superar 30 días.',
        ];
    }
}
