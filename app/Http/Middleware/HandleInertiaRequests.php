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
                    'dashboard' => true,
                    'finanzas' => $request->user()?->can('finanzas.view') ?? false,
                    'contactos' => $request->user()?->can('contactos.view') ?? false,
                    'aprobaciones' => $request->user()?->can('aprobaciones.view') ?? false,
                    'donaciones' => $request->user()?->can('donaciones.view') ?? false,
                    'registro-casos' => $request->user()?->can('registro-casos.view') ?? false,
                    'reportes' => $request->user()?->can('reportes.view') ?? false,
                    'cotizaciones' => $request->user()?->can('cotizaciones.view') ?? false,
                    'suscripciones' => $request->user()?->can('suscripciones.view') ?? false,
                    'rrhh' => $request->user()?->can('rrhh.view') ?? false,
                    'politicas' => $request->user()?->can('politicas.view') ?? false,
                    'certificados' => $request->user()?->can('certificados.view') ?? false,
                    'licencias' => $request->user()?->can('licencias.view') ?? false,
                    'proyectos' => $request->user()?->can('proyectos.view') ?? false,
                    'obras' => $request->user()?->can('obras.view') ?? false,
                    'kpis' => $request->user()?->can('kpis.view') ?? false,
                    'marketing' => $request->user()?->can('marketing.view') ?? false,
                    'planner' => true,
                    'calendar' => true,
                    'cloud' => $request->user()?->can('cloud.view') ?? false,
                    'reuniones' => $request->user()?->can('reuniones.view') ?? false,
                    'usuarios' => $request->user()?->can('usuarios.view') ?? false,
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
