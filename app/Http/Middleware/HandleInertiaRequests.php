<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user() ? [
                'usu_id'              => $request->user()->usu_id,
                'usu_primer_nombre'   => $request->user()->usu_primer_nombre,
                'usu_primer_apellido' => $request->user()->usu_primer_apellido,
                'usu_username'        => $request->user()->usu_username,
                'usu_email'           => $request->user()->usu_email,
            ] : null,
            'rol' => function () use ($request) {
                $user = $request->user();
                if (!$user) return null;

                $roles = $user->roles()->pluck('rol_nombre')->all();
                if (empty($roles)) return null;

                // 1. Priorizar por el contexto de la ruta actual
                if ($request->is('coordinador*') && in_array('coordinador', $roles)) {
                    return 'coordinador';
                }
                if ($request->is('profesor*') && in_array('profesor_proyecto', $roles)) {
                    return 'profesor_proyecto';
                }
                if ($request->is('lider*') && in_array('lider', $roles)) {
                    return 'lider';
                }

                // 2. Jerarquía institucional por defecto
                $jerarquia = ['coordinador', 'profesor_proyecto', 'lider'];
                foreach ($jerarquia as $jRol) {
                    if (in_array($jRol, $roles)) {
                        return $jRol;
                    }
                }

                return $roles[0] ?? null;
            },
        ],
        'flash' => [
            'success' => fn() => $request->session()->get('success'),
            'error'   => fn() => $request->session()->get('error'),
        ],
        'errors' => fn() => $request->session()->get('errors')
            ? $request->session()->get('errors')->getBag('default')->getMessages()
            : (object)[],
        'notificacionesNoLeidas' => fn() => $request->user()
            ? \App\Models\Notificacion::where('not_id_usu', $request->user()->usu_id)
                ->where('not_leida', false)
                ->count()
            : 0,
        'periodoActivo' => function () {
            $p = \App\Models\PeriodoAcademico::periodoActivo();
            if (!$p) return null;
            return [
                'id_periodo'           => $p->id_periodo,
                'lapso_academico'      => $p->lapso_academico,
                'tipo_semestre'        => $p->tipo_semestre,
                'fecha_inicio'         => $p->fecha_inicio ? $p->fecha_inicio->format('Y-m-d') : null,
                'fecha_fin'            => $p->fecha_fin ? $p->fecha_fin->format('Y-m-d') : null,
                'fecha_inicio_formato' => $p->fecha_inicio ? $p->fecha_inicio->format('d/m/Y') : null,
                'fecha_fin_formato'    => $p->fecha_fin ? $p->fecha_fin->format('d/m/Y') : null,
            ];
        },
        'googleMapsApiKey' => config('services.google_maps.key', ''),
    ];
}
}
