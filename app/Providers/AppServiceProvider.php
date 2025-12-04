<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\File;
use App\Models\Folder;
use App\Models\Team;
use App\Observers\AreaObserver;
use App\Policies\FolderPolicy;
use App\Policies\FilePolicy;
use App\Policies\TeamPolicy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Area::observe(AreaObserver::class);

        // Registrar policies
        Gate::policy(Team::class, TeamPolicy::class);

        Gate::policy(Folder::class, FolderPolicy::class);
        Gate::policy(File::class, FilePolicy::class);

        Blade::directive('canArea', function ($expression) {
            // $expression será algo como: "create, 'ingresos'"
            return "<?php if(auth()->check() && app(\App\Services\AreaPermissionService::class)->canArea($expression)): ?>";
        });

        Blade::directive('endCanArea', function () {
            return '<?php endif; ?>';
        });

        // Gate::before(function ($user, $ability) {
        //     $permiso = Permission::where('full_name', $ability)->first();
        //     if ($permiso) {
        //         return $user->permissions->contains($permiso);
        //     }
        // });
    }
}
