<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->only(['id', 'name', 'email', 'profile_photo_path']) + [
                    'profile_photo_url' => $request->user()->profile_photo_url,
                    'two_factor_enabled' => !is_null($request->user()->two_factor_secret),
                ] : null,
                'permissions' => [
                    'dashboard' => true, // Dashboard is always accessible, or check logic
                    'finanzas' => \App\Services\AreaPermissionService::canArea('view', 'finanzas'),
                    'contactos' => \App\Services\AreaPermissionService::canArea('view', 'contactos'),
                    'aprobaciones' => \App\Services\AreaPermissionService::canArea('view', 'aprobaciones'),
                    'donaciones' => \App\Services\AreaPermissionService::canArea('view', 'donaciones'),
                    'registro-casos' => \App\Services\AreaPermissionService::canArea('view', 'registro-casos'),
                    'reportes' => \App\Services\AreaPermissionService::canArea('view', 'reportes'),
                    'cotizaciones' => \App\Services\AreaPermissionService::canArea('view', 'cotizaciones'),
                    'suscripciones' => \App\Services\AreaPermissionService::canArea('view', 'suscripciones'),
                    'rrhh' => \App\Services\AreaPermissionService::canArea('view', 'rrhh'), // Assuming rrhh maps to a slug
                    'politicas' => \App\Services\AreaPermissionService::canArea('view', 'politicas'),
                    'certificados' => \App\Services\AreaPermissionService::canArea('view', 'certificados'),
                    'licencias' => \App\Services\AreaPermissionService::canArea('view', 'licencias'),
                    'proyectos' => \App\Services\AreaPermissionService::canArea('view', 'proyectos'),
                    'obras' => \App\Services\AreaPermissionService::canArea('view', 'obras'),
                    'kpis' => \App\Services\AreaPermissionService::canArea('view', 'kpis'),
                    'marketing' => \App\Services\AreaPermissionService::canArea('view', 'marketing'),
                    'planner' => true, // Assuming planner is always accessible or add check
                    'calendar' => true, // Assuming calendar is always accessible
                    'cloud' => \App\Services\AreaPermissionService::canArea('view', 'cloud'),
                    'reuniones' => \App\Services\AreaPermissionService::canArea('view', 'reuniones'),
                    'usuarios' => \App\Services\AreaPermissionService::canArea('view', 'usuarios'),
                ],
                'user_role' => $request->user()?->roles()->first()?->display_name ?? 'Usuario',
            ],
            'jetstream' => [
                'canManageTwoFactorAuthentication' => \Laravel\Fortify\Features::canManageTwoFactorAuthentication(),
                'canUpdatePassword' => \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::updatePasswords()),
                'canUpdateProfileInformation' => \Laravel\Fortify\Features::canUpdateProfileInformation(),
                'hasAccountDeletionFeatures' => \Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures(),
                'hasApiFeatures' => \Laravel\Jetstream\Jetstream::hasApiFeatures(),
                'hasTeamFeatures' => \Laravel\Jetstream\Jetstream::hasTeamFeatures(),
                'hasTermsAndPrivacyPolicyFeature' => \Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature(),
                'managesProfilePhotos' => \Laravel\Jetstream\Jetstream::managesProfilePhotos(),
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
