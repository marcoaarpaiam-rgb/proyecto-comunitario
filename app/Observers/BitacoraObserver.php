<?php

namespace App\Observers;

use App\Models\Bitacora;
use App\Models\Entidad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BitacoraObserver
{
    private function registrar(Model $model, string $accion, ?array $anterior = null): void
    {
        if (!Auth::check()) return;

        $tabla = $model->getTable();
        $entidad = Entidad::where('ent_nombre', $tabla)->first();

        Bitacora::create([
            'bit_accion'          => $accion . ' en ' . $tabla,
            'bit_estado_anterior' => $anterior ? json_encode($anterior) : null,
            'bit_estado_nuevo'    => json_encode($model->toArray()),
            'bit_registro_id'     => $model->getKey(),
            'bit_fecha_hora'      => now(),
            'bit_id_usu'          => Auth::id(),
            'bit_id_entidad'      => $entidad?->ent_id,
        ]);
    }

    public function created(Model $model): void
    {
        $this->registrar($model, 'CREAR');
    }

    public function updated(Model $model): void
    {
        $this->registrar($model, 'ACTUALIZAR', $model->getOriginal());
    }

    public function deleted(Model $model): void
    {
        $this->registrar($model, 'ELIMINAR', $model->toArray());
    }
}