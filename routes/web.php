<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Coordinador\MaestraController;
use App\Http\Controllers\Coordinador\ComunidadController;
use App\Http\Controllers\Coordinador\SeccionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Models\ProyectoComunidad;
use App\Models\PuntoControl;
use App\Models\Comunidad;
use App\Http\Controllers\Coordinador\TablaMaestraController;
use App\Http\Controllers\Coordinador\EquipoController;
use App\Http\Controllers\Profesor\PuntoControlController;
use App\Http\Controllers\Coordinador\ProyectoController;
use App\Http\Controllers\Profesor\SocializacionController;
use App\Http\Controllers\Profesor\EntregableController;
use App\Http\Controllers\Lider\LiderController;
use App\Http\Controllers\Coordinador\ResultadoController;
use App\Http\Controllers\Coordinador\ReporteController;
use App\Http\Controllers\Coordinador\CartaController;
use App\Http\Controllers\Coordinador\BitacoraController;
use App\Http\Controllers\Coordinador\SocializacionCoordController;
use App\Http\Controllers\Coordinador\SeccionDetalleController;
use App\Http\Controllers\Coordinador\ConfiguracionController;
use App\Http\Controllers\Coordinador\ExpedienteController;


// Rutas públicas
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/home', fn() => Inertia::render('Home'))->name('home');


Route::middleware('auth')->group(function () {
    Route::get('/notificaciones/lista', function () {
        $user = auth()->user();
        if (!$user) return response()->json([]);
        $userId = $user->usu_id;

        // 1. Líder: puntos de control cercanos
        $liderEquipos = \App\Models\EquipoIntegrante::where('ein_id_usu', $userId)
            ->where('ein_es_lider', true)
            ->where('ein_status', true)
            ->pluck('ein_id_equ');

        if ($liderEquipos->isNotEmpty()) {
            $puntosCercanos = \App\Models\PuntoControl::whereIn('puc_id_equ', $liderEquipos)
                ->where('puc_status', true)
                ->where('puc_fecha_limite', '>=', now()->toDateString())
                ->where('puc_fecha_limite', '<=', now()->addDays(7)->toDateString())
                ->get();

            foreach ($puntosCercanos as $punto) {
                $yaNotificado = \App\Models\Notificacion::where('not_id_usu', $userId)
                    ->where('not_id_puc', $punto->puc_id)
                    ->where('not_mensaje', 'like', '%próximo a vencer%')
                    ->exists();

                if (!$yaNotificado) {
                    \App\Models\Notificacion::create([
                        'not_id_usu'           => $userId,
                        'not_id_tno'           => \App\Models\TipoNotificacion::first()?->tno_id,
                        'not_id_equ'           => $punto->puc_id_equ,
                        'not_id_puc'           => $punto->puc_id,
                        'not_mensaje'          => "Recordatorio: El punto de control \"{$punto->puc_nombre}\" está próximo a vencer el {$punto->puc_fecha_limite}.",
                        'not_leida'            => false,
                        'not_fecha_generacion' => now(),
                    ]);
                }
            }
        }

        // 2. Profesor: puntos de control por vencer en sus secciones
        if ($user->esProfesor()) {
            $seccionesIds = $user->seccionesProfesor()
                ->where('pse_status', true)
                ->pluck('pse_id_sec');

            if ($seccionesIds->isNotEmpty()) {
                $equiposProf = \App\Models\Equipo::whereIn('equ_id_sec', $seccionesIds)
                    ->where('equ_status', true)
                    ->get();

                $puntosProf = \App\Models\PuntoControl::whereIn('puc_id_equ', $equiposProf->pluck('equ_id'))
                    ->where('puc_status', true)
                    ->where('puc_fecha_limite', '>=', now()->toDateString())
                    ->where('puc_fecha_limite', '<=', now()->addDays(7)->toDateString())
                    ->with('equipo')
                    ->get();

                foreach ($puntosProf as $punto) {
                    $yaNotificado = \App\Models\Notificacion::where('not_id_usu', $userId)
                        ->where('not_id_puc', $punto->puc_id)
                        ->where('not_mensaje', 'like', '%próximo a vencer%')
                        ->exists();

                    if (!$yaNotificado) {
                        \App\Models\Notificacion::create([
                            'not_id_usu'           => $userId,
                            'not_id_tno'           => \App\Models\TipoNotificacion::first()?->tno_id,
                            'not_id_equ'           => $punto->puc_id_equ,
                            'not_id_puc'           => $punto->puc_id,
                            'not_mensaje'          => "El punto de control \"{$punto->puc_nombre}\" del equipo {$punto->equipo?->equ_codigo} está próximo a vencer el {$punto->puc_fecha_limite}.",
                            'not_leida'            => false,
                            'not_fecha_generacion' => now(),
                        ]);
                    }
                }
            }
        }

        return \App\Models\Notificacion::where('not_id_usu', $userId)
            ->orderBy('not_fecha_generacion', 'desc')
            ->take(20)->get();
    });

    Route::post('/notificaciones/{id}/leer', function ($id) {
        \App\Models\Notificacion::where('not_id', $id)
            ->where('not_id_usu', auth()->id())
            ->update([
                'not_leida'        => true,
                'not_fecha_lectura'=> now(),
            ]);
        return response()->json(['ok' => true]);
    });

    Route::post('/notificaciones/leer-todas', function () {
        \App\Models\Notificacion::where('not_id_usu', auth()->id())
            ->update([
                'not_leida'        => true,
                'not_fecha_lectura'=> now(),
            ]);
        return response()->json(['ok' => true]);
    });
});

// Coordinador
Route::middleware(['auth', 'rol:coordinador'])->prefix('coordinador')->name('coordinador.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Coordinador/Dashboard', [
            'stats' => [
                'estudiantes'      => \App\Models\EstudianteSeccion::where('ese_status', true)->count(),
                'equipos'          => \App\Models\Equipo::where('equ_status', true)->count(),
                'comunidades'      => \App\Models\Comunidad::where('com_status', true)->count(),
                'porVencer'        => \App\Models\ProyectoComunidad::where('pco_status', true)
                    ->whereBetween('pco_fecha_limite', [now()->toDateString(), now()->addDays(30)->toDateString()])->count(),
                'aprobados'        => \App\Models\ResultadoProyecto::whereHas('estadoProyecto', fn($q) => $q->where('epr_nombre', 'Aprobado'))->count(),
                'profesores'       => \App\Models\Usuario::where('usu_status', true)
                    ->whereHas('usuarioRoles', fn($q) => $q->where('uro_status', true)
                        ->whereHas('rol', fn($r) => $r->where('rol_nombre', 'profesor_proyecto')))->count(),
                'notificaciones'   => \App\Models\Notificacion::where('not_id_usu', auth()->id())->where('not_leida', false)->count(),
                'cartasPendientes' => \App\Models\CartaPresentacion::where('cpr_solicitada', true)->whereNull('cpr_fecha_aprobacion')->count(),
            ],
            'secciones' => \App\Models\Seccion::with('turno')->where('sec_status', true)->orderBy('sec_codigo')->get()
                ->map(function ($s) {
                    $equipos = \App\Models\Equipo::where('equ_id_sec', $s->sec_id)
                        ->where('equ_status', true)
                        ->with([
                            'proyectoComunidad',
                            'puntosControl.seguimientos',
                            'entregables',
                            'socializaciones.tipoSocializacion',
                            'resultadoProyecto'
                        ])
                        ->get();

                    $total = $equipos->count();
                    $avance = 0;
                    if ($total > 0) {
                        $sumaAvances = 0;
                        foreach ($equipos as $eq) {
                            if ($eq->resultadoProyecto) {
                                $sumaAvances += 100;
                                continue;
                            }
                            $puntos = 10;
                            if ($eq->proyectoComunidad) {
                                $puntos += 25;
                            }
                            $pucActivos = $eq->puntosControl->where('puc_status', true);
                            if ($pucActivos->isNotEmpty()) {
                                $cumplidos = $pucActivos->filter(fn($p) => $p->seguimientos->where('seq_cumplido', true)->isNotEmpty())->count();
                                $puntos += round(($cumplidos / $pucActivos->count()) * 25);
                            }
                            $entrActivos = $eq->entregables->where('entr_status', true);
                            if ($entrActivos->isNotEmpty()) {
                                $entrAprob = $entrActivos->where('entr_aprobado', true)->count();
                                $puntos += round(($entrAprob / $entrActivos->count()) * 20);
                            }
                            $preSocApta = $eq->socializaciones
                                ->filter(fn($soc) => $soc->tipoSocializacion?->tso_orden === 1 && $soc->soc_apto === true)
                                ->isNotEmpty();
                            if ($preSocApta) {
                                $puntos += 20;
                            }
                            $sumaAvances += min(100, $puntos);
                        }
                        $avance = round($sumaAvances / $total);
                    }

                    return [
                        'sec_id'        => $s->sec_id,
                        'sec_codigo'    => $s->sec_codigo,
                        'turno'         => $s->turno,
                        'total_equipos' => $total,
                        'avance'        => $avance,
                    ];
                }),
            'notificaciones' => \App\Models\Notificacion::where('not_id_usu', auth()->id())
                ->latest('not_fecha_generacion')
                ->take(10)
                ->get(),
        ]);
    })->name('dashboard');

    // Tablas maestras
    Route::resource('tipos-proyecto', MaestraController::class)
        ->parameters(['tipos-proyecto' => 'id'])
        ->except(['create', 'show', 'edit']);

    Route::resource('comunidades', ComunidadController::class)
        ->parameters(['comunidades' => 'id'])
        ->except(['create', 'show', 'edit']);

    Route::resource('secciones', SeccionController::class)
        ->parameters(['secciones' => 'id'])
        ->except(['create', 'show', 'edit']);

    

    // Tablas maestras genéricas
    Route::get('maestras/{tabla}', [TablaMaestraController::class, 'index'])->name('maestras.index');
    Route::post('maestras/{tabla}', [TablaMaestraController::class, 'store'])->name('maestras.store');
    Route::put('maestras/{tabla}/{id}', [TablaMaestraController::class, 'update'])->name('maestras.update');
    Route::delete('maestras/{tabla}/{id}', [TablaMaestraController::class, 'destroy'])->name('maestras.destroy');
    Route::post('maestras/{tabla}/{id}/reactivar', [TablaMaestraController::class, 'reactivar'])->name('maestras.reactivar');

    

   Route::resource('equipos', EquipoController::class)
        ->parameters(['equipos' => 'id'])
        ->except(['create', 'show', 'edit']);

    

    Route::resource('proyectos', ProyectoController::class)
        ->parameters(['proyectos' => 'id'])
        ->except(['edit','create','show']);
    
    // Resultados
    Route::get('resultados', [ResultadoController::class, 'index'])
        ->name('resultados.index');
    Route::post('resultados', [ResultadoController::class, 'store'])
        ->name('resultados.store');
    Route::post('resultados/{id}/habilitar-tardio',
        [ResultadoController::class, 'habilitarTardio'])
        ->name('resultados.habilitar-tardio');

    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');
    Route::get('reportes/solvencia', [ReporteController::class, 'solvencia'])
        ->name('reportes.solvencia');
    Route::get('reportes/filtrar', [ReporteController::class, 'filtrar'])
        ->name('reportes.filtrar');
    Route::get('reportes/por-comunidad', [ReporteController::class, 'porComunidad'])
        ->name('reportes.porComunidad');
    Route::get('reportes/entregables', [ReporteController::class, 'porEntregables'])
        ->name('reportes.entregables');
    Route::get('reportes/puntos-control', [ReporteController::class, 'porPuntosControl'])
        ->name('reportes.puntosControl');

    // Cartas
    Route::get('cartas', [CartaController::class, 'index'])
        ->name('cartas.index');
    Route::post('cartas/{id}/aprobar',
        [CartaController::class, 'aprobar'])
        ->name('cartas.aprobar');
    Route::post('cartas/firma',
        [CartaController::class, 'actualizarFirma'])
        ->name('cartas.firma');

    // Bitácora
    Route::get('bitacora', [BitacoraController::class, 'index'])
        ->name('bitacora.index');
    

    Route::get('socializaciones',
        [SocializacionCoordController::class, 'index'])
        ->name('socializaciones.index');
    

    Route::get('secciones/{id}/detalle',
        [SeccionDetalleController::class, 'show'])
        ->name('secciones.detalle');
    
    Route::post('secciones/{id}/profesores', [SeccionDetalleController::class, 'asignarProfesor']);

    Route::get('cartas/{id}/descargar',
        [CartaController::class, 'descargar'])
        ->name('cartas.descargar');
    

    Route::get('configuracion', [ConfiguracionController::class, 'index'])
        ->name('configuracion.index');
    Route::post('configuracion/firma',
        [ConfiguracionController::class, 'actualizarFirma'])
        ->name('configuracion.firma');
    Route::post('configuracion/perfil',
        [ConfiguracionController::class, 'actualizarPerfil'])
        ->name('configuracion.perfil');
    Route::post('configuracion/password',
        [ConfiguracionController::class, 'cambiarPassword'])
        ->name('configuracion.password');
    Route::get('reportes/exportar-solvencia',
        [ReporteController::class, 'exportarSolvenciaPdf'])
        ->name('reportes.exportar-solvencia');
    Route::get('reportes/exportar-general',
        [ReporteController::class, 'exportarGeneralPdf'])
        ->name('reportes.exportar-general');

    
    Route::post('equipos/{id}/disolver', [EquipoController::class, 'disolver'])
        ->name('equipos.disolver');

    Route::delete('equipos/{id}', [EquipoController::class, 'destroy'])
        ->name('coordinador.equipos.destroy');

    Route::delete('equipos/{equipoId}/integrantes/{integranteId}',
        [EquipoController::class, 'removerIntegrante'])
        ->name('equipos.integrantes.remover');

    Route::post('equipos/{equipoId}/integrantes/{integranteId}/trasladar',
        [EquipoController::class, 'trasladarIntegrante'])
        ->name('equipos.integrantes.trasladar');

    Route::post('equipos/{equipoId}/cambiar-lider',
        [EquipoController::class, 'cambiarLider'])
        ->name('equipos.cambiar-lider');

    Route::get('equipos/{equipoId}/historial',
        [EquipoController::class, 'historial'])
        ->name('equipos.historial');

    Route::get('equipos/{id}/historial-academico',
        [EquipoController::class, 'historialAcademico'])
        ->name('equipos.historial-academico');

    
    Route::get('equipos/{id}/expediente', [ExpedienteController::class, 'show'])
        ->name('equipos.expediente');
    
    Route::get('/equipos/{id}/paquete-cierre', [EquipoController::class, 'generarPaqueteCierre'])
        ->name('coordinador.equipos.paqueteCierre');
    
    Route::post('configuracion/reset-lider',
        [ConfiguracionController::class, 'resetPasswordLider'])
        ->name('configuracion.reset-lider');

    Route::post('/equipos/{id}/reactivar', [EquipoController::class, 'reactivar'])
        ->name('coordinador.equipos.reactivar');
    
});

// Grupo Profesor 
    Route::middleware(['auth', 'rol:profesor_proyecto'])
        ->prefix('profesor')->name('profesor.')->group(function () {

        Route::get('/', function () {
        $profesor = auth()->user();
        $secciones = $profesor->seccionesProfesor()
            ->where('pse_status', true)->pluck('pse_id_sec');
        $equiposIds = \App\Models\Equipo::whereIn('equ_id_sec', $secciones)
            ->where('equ_status', true)->pluck('equ_id');

        $equipos = \App\Models\Equipo::whereIn('equ_id', $equiposIds)
            ->with(['seccion','trayecto','integrantes.usuario'])
            ->where('equ_status', true)
            ->orderBy('equ_codigo')
            ->get();

        $notificacionesBase = \App\Models\Notificacion::where('not_id_usu', auth()->id())
            ->where(function ($q) {
                $q->whereNull('not_id_equ')
                  ->orWhereHas('equipo', fn($eq) => $eq->where('equ_status', true));
            });

        return Inertia::render('Profesor/Dashboard', [
            'stats' => [
                'secciones'             => $secciones->count(),
                'equipos'               => $equiposIds->count(),
                'puntosVencer'          => \App\Models\PuntoControl::whereIn('puc_id_equ', $equiposIds)
                    ->where('puc_status', true)
                    ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
                    ->whereBetween('puc_fecha_limite', [now()->toDateString(), now()->addDays(7)->toDateString()])
                    ->count(),
                'entregablesPendientes' => \App\Models\Entregable::whereIn('entr_id_equ', $equiposIds)
                    ->where('entr_status', true)
                    ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
                    ->whereNull('entr_aprobado')->count(),
                'notificaciones'        => (clone $notificacionesBase)->where('not_leida', false)->count(),
            ],
            'notificaciones' => (clone $notificacionesBase)
                ->orderByDesc('not_fecha_generacion')->take(15)->get(),
            'notificacionesSinLeer' => (clone $notificacionesBase)
                ->where('not_leida', false)->count(),
            'equipos'               => $equipos,
            'proximosPuntos'        => \App\Models\PuntoControl::whereIn('puc_id_equ', $equiposIds)
                ->where('puc_status', true)
                ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
                ->where('puc_fecha_limite', '>=', now()->toDateString())
                ->orderBy('puc_fecha_limite')->take(5)->with('equipo')->get()
                ->map(fn($p) => [
                    'id'      => $p->puc_id,
                    'nombre'  => $p->puc_nombre,
                    'equipo'  => $p->equipo?->equ_codigo,
                    'fecha'   => $p->puc_fecha_limite,
                    'urgente' => $p->puc_fecha_limite <= now()->addDays(3)->toDateString(),
                ]),
            'entregablesPendientes' => \App\Models\Entregable::whereIn('entr_id_equ', $equiposIds)
                ->where('entr_status', true)
                ->whereHas('equipo', fn($q) => $q->where('equ_status', true))
                ->whereNull('entr_aprobado')->with('equipo')->take(5)->get()
                ->map(fn($e) => [
                    'id'     => $e->entr_id,
                    'nombre' => $e->entr_nombre_archivo,
                    'equipo' => $e->equipo?->equ_codigo,
                ]),
            
        ]);
    })->name('dashboard');

    // Puntos de Control
    Route::get('puntos-control',
        [App\Http\Controllers\Profesor\PuntoControlController::class, 'index'])
        ->name('puntos-control.index');
    Route::post('puntos-control',
        [App\Http\Controllers\Profesor\PuntoControlController::class, 'store'])
        ->name('puntos-control.store');
    Route::post('puntos-control/{id}/seguimiento',
        [App\Http\Controllers\Profesor\PuntoControlController::class, 'registrarSeguimiento'])
        ->name('puntos-control.seguimiento');
    Route::post('/puntos-control/{id}/reprogramar', [PuntoControlController::class, 'reprogramar'])
        ->name('profesor.puntos-control.reprogramar');
    Route::delete('puntos-control/{id}',
        [App\Http\Controllers\Profesor\PuntoControlController::class, 'destroy'])
        ->name('puntos-control.destroy');

    // Socializaciones
    Route::get('socializaciones',
        [App\Http\Controllers\Profesor\SocializacionController::class, 'index'])
        ->name('socializaciones.index');
    Route::post('socializaciones',
        [App\Http\Controllers\Profesor\SocializacionController::class, 'store'])
        ->name('socializaciones.store');
    Route::post('socializaciones/{id}/resultado',
        [App\Http\Controllers\Profesor\SocializacionController::class, 'registrarResultado'])
        ->name('socializaciones.resultado');

    // Entregables
    Route::get('entregables',
        [App\Http\Controllers\Profesor\EntregableController::class, 'index'])
        ->name('entregables.index');
    Route::post('entregables/{id}/aprobar',
        [App\Http\Controllers\Profesor\EntregableController::class, 'aprobar'])
        ->name('entregables.aprobar');
    Route::post('entregables/{id}/rechazar',
        [App\Http\Controllers\Profesor\EntregableController::class, 'rechazar'])
        ->name('entregables.rechazar');
    
    // Secciones del profesor
    Route::get('secciones', [EquipoController::class, 'misSecciones'])
        ->name('secciones.index');
    Route::get('secciones/{id}/equipos', [EquipoController::class, 'seccionEquipos'])
        ->name('secciones.equipos');
    // Equipos — el profesor crea, el coordinador también puede todo
    Route::get('equipos', [EquipoController::class, 'index'])
        ->name('equipos.index');
    Route::post('equipos', [EquipoController::class, 'store'])
        ->name('equipos.store');
    Route::put('equipos/{id}', [EquipoController::class, 'update'])
        ->name('equipos.update');
    Route::delete('equipos/{id}', [EquipoController::class, 'destroy'])
        ->name('equipos.destroy');


    Route::post('equipos/{id}/disolver', [EquipoController::class, 'disolver'])
        ->name('equipos.disolver');

    Route::delete('equipos/{id}', [EquipoController::class, 'destroy'])
        ->name('coordinador.equipos.destroy');

    Route::delete('equipos/{equipoId}/integrantes/{integranteId}',
        [EquipoController::class, 'removerIntegrante'])
        ->name('equipos.integrantes.remover');

    Route::post('equipos/{equipoId}/integrantes/{integranteId}/trasladar',
        [EquipoController::class, 'trasladarIntegrante'])
        ->name('equipos.integrantes.trasladar');

    Route::post('equipos/{equipoId}/cambiar-lider',
        [EquipoController::class, 'cambiarLider'])
        ->name('equipos.cambiar-lider');

    Route::get('equipos/{equipoId}/historial',
        [EquipoController::class, 'historial'])
        ->name('equipos.historial');

    Route::get('equipos/{id}/historial-academico',
        [EquipoController::class, 'historialAcademico'])
        ->name('equipos.historial-academico');
    

    Route::post('equipos/{id}/reactivar', [EquipoController::class, 'reactivar'])
        ->name('equipos.reactivar');

    Route::get('equipos/{id}/expediente', [ExpedienteController::class, 'show'])
        ->name('equipos.expediente');

    Route::get('/equipos/{id}/paquete-cierre', [EquipoController::class, 'generarPaqueteCierre'])
        ->name('profesor.equipos.paqueteCierre');

    // Dentro del grupo profesor:
    Route::post('/comunidades', [\App\Http\Controllers\Coordinador\ComunidadController::class, 'store'])
        ->name('profesor.comunidades.store');

    Route::post('tipos-proyecto', [MaestraController::class, 'store']);
});
// Grupo Líder 
Route::middleware(['auth', 'rol:lider'])
    ->prefix('lider')->name('lider.')->group(function () {
    Route::get('/', [App\Http\Controllers\Lider\LiderController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('equipo',
        [App\Http\Controllers\Lider\LiderController::class, 'miEquipo'])
        ->name('equipo');
    Route::get('proyecto',
        [App\Http\Controllers\Lider\LiderController::class, 'miProyecto'])
        ->name('proyecto');
    Route::get('entregables',
        [App\Http\Controllers\Lider\LiderController::class, 'entregables'])
        ->name('entregables.index');
    Route::post('entregables',
        [App\Http\Controllers\Lider\LiderController::class, 'subirEntregable'])
        ->name('entregables.store');

    Route::get('historial-academico',
        [App\Http\Controllers\Lider\LiderController::class, 'historialAcademico'])
        ->name('historial-academico');
    

    Route::get('carta', function () {
        $integrante = \App\Models\EquipoIntegrante::where('ein_id_usu', auth()->id())
            ->where('ein_es_lider', true)->where('ein_status', true)->first();
        if (!$integrante) return Inertia::render('Lider/Carta', [
            'carta' => null, 'proyecto' => null
        ]);
        $equipo = \App\Models\Equipo::find($integrante->ein_id_equ);
        $proyecto = \App\Models\ProyectoComunidad::where('pco_id_equ', $equipo->equ_id)
            ->with(['comunidad'])->first();
        $carta = $proyecto
            ? \App\Models\CartaPresentacion::where('cpr_id_pco', $proyecto->pco_id)->first()
            : null;
        return Inertia::render('Lider/Carta', [
            'carta'   => $carta,
            'proyecto'=> $proyecto,
            'equipo'  => $equipo,
        ]);
    })->name('carta');

    Route::post('carta', [CartaController::class, 'solicitar'])
        ->name('carta.solicitar');

    
    Route::get('/carta/{id}/descargar', [LiderController::class, 'descargarCarta'])
        ->name('lider.carta.descargar');

    Route::post('/responsable', [LiderController::class, 'registrarResponsable'])
        ->name('lider.responsable.store');
        
    Route::delete('/responsable/{id}', [LiderController::class, 'eliminarResponsable'])
        ->name('lider.responsable.destroy');
    
    Route::post('/tutor', [LiderController::class, 'registrarTutor'])
        ->name('lider.tutor.store');
});


