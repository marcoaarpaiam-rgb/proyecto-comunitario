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
            'rol' => $request->user()?->usuarioRoles()
                ->where('uro_status', true)
                ->with('rol')
                ->first()
                ?->rol
                ?->rol_nombre,
        ],
        'flash' => [
            'success' => fn() => $request->session()->get('success'),
            'error'   => fn() => $request->session()->get('error'),
        ],
        'errors' => fn() => $request->session()->get('errors')
            ? $request->session()->get('errors')->getBag('default')->getMessages()
            : (object)[],
    ];
}
}
