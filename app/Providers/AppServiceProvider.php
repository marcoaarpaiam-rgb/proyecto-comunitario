<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\BitacoraObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    
    public function boot(): void
    {
        $modelos = [
            \App\Models\Equipo::class,
            \App\Models\EquipoIntegrante::class,
            \App\Models\ProyectoComunidad::class,
            \App\Models\PuntoControl::class,
            \App\Models\SeguimientoEquipo::class,
            \App\Models\Entregable::class,
            \App\Models\Socializacion::class,
            \App\Models\ResultadoProyecto::class,
            \App\Models\CartaPresentacion::class,
            \App\Models\Comunidad::class,
            \App\Models\Usuario::class,
        ];

        foreach ($modelos as $modelo) {
            $modelo::observe(BitacoraObserver::class);
        }
    }
}
