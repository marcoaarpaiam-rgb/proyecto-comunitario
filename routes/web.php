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


// Rutas públicas
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/home', fn() => Inertia::render('Home'))->name('home');
Route::middleware('auth')->get('/notificaciones/lista', function () {
    return \App\Models\Notificacion::where('not_id_usu', auth()->id())
        ->orderBy('not_fecha_generacion', 'desc')
        ->take(10)
        ->get();
});

// Coordinador
Route::middleware(['auth', 'rol:coordinador'])->prefix('coordinador')->name('coordinador.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Coordinador/Dashboard', [
            'stats' => [
                'equipos'       => Equipo::where('equ_status', true)->count(),
                'proyectos'     => ProyectoComunidad::where('pco_status', true)->count(),
                'puntosControl' => PuntoControl::where('puc_status', true)->count(),
                'comunidades'   => Comunidad::where('com_status', true)->count(),
            ],
        ]);
    })->name('dashboard');

    // Tablas maestras
    Route::resource('tipos-proyecto', MaestraController::class)->parameters(['tipos-proyecto' => 'id']);
    Route::resource('comunidades', ComunidadController::class)->parameters(['comunidades' => 'id']);
    Route::resource('secciones', SeccionController::class)->parameters(['secciones' => 'id']);

    

    // Tablas maestras genéricas
    Route::get('maestras/{tabla}', [TablaMaestraController::class, 'index'])->name('maestras.index');
    Route::post('maestras/{tabla}', [TablaMaestraController::class, 'store'])->name('maestras.store');
    Route::put('maestras/{tabla}/{id}', [TablaMaestraController::class, 'update'])->name('maestras.update');
    Route::delete('maestras/{tabla}/{id}', [TablaMaestraController::class, 'destroy'])->name('maestras.destroy');

    

    Route::resource('equipos', EquipoController::class)
        ->parameters(['equipos' => 'id']);

    

    Route::resource('proyectos', ProyectoController::class)
        ->parameters(['proyectos' => 'id'])
        ->except(['edit','create','show']);
    Route::post('proyectos/{id}/aprobar',
        [ProyectoController::class, 'aprobar'])
        ->name('proyectos.aprobar');
    
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

    Route::get('cartas/{id}/descargar',
        [CartaController::class, 'descargar'])
        ->name('cartas.descargar');
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
            ->where('equ_status', true)->get();

        return Inertia::render('Profesor/Dashboard', [
            'stats' => [
                'secciones'             => $secciones->count(),
                'equipos'               => $equiposIds->count(),
                'entregablesPendientes' => \App\Models\Entregable::whereIn('entr_id_equ', $equiposIds)
                    ->whereNull('entr_aprobado')->count(),
            ],
            'equipos'               => $equipos,
            'notificacionesSinLeer' => 0,
            'proximosPuntos'        => \App\Models\PuntoControl::whereIn('puc_id_equ', $equiposIds)
                ->where('puc_status', true)
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
    Route::post('puntos-control/{id}/reprogramar',
        [App\Http\Controllers\Profesor\PuntoControlController::class, 'reprogramar'])
        ->name('puntos-control.reprogramar');
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
    

    // Equipos — el profesor crea, el coordinador también puede todo
    Route::get('equipos', [EquipoController::class, 'index'])
        ->name('equipos.index');
    Route::post('equipos', [EquipoController::class, 'store'])
        ->name('equipos.store');
    Route::put('equipos/{id}', [EquipoController::class, 'update'])
        ->name('equipos.update');
    Route::delete('equipos/{id}', [EquipoController::class, 'destroy'])
        ->name('equipos.destroy');
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

    

    Route::get('carta/{id}/descargar',
        [App\Http\Controllers\Coordinador\CartaController::class, 'descargar'])
        ->name('carta.descargar');
});


