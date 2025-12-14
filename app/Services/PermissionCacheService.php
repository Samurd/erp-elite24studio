<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PermissionCacheService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_TTL = 3600;

    /**
     * Get permission IDs for a specific area (cached)
     */
    public static function getAreaPermissionIds(string $areaSlug): array
    {
        return Cache::remember("area_permissions:{$areaSlug}", self::CACHE_TTL, function () use ($areaSlug) {
            return Permission::whereHas('area', function ($query) use ($areaSlug) {
                $query->where('slug', $areaSlug);
            })->pluck('id')->toArray();
        });
    }

    /**
     * Get users with permissions in a specific area (cached)
     */
    public static function getUsersByArea(string $areaSlug): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("users_by_area:{$areaSlug}", self::CACHE_TTL, function () use ($areaSlug) {
            $permissionIds = self::getAreaPermissionIds($areaSlug);

            return User::whereHas('roles.permissions', function ($query) use ($permissionIds) {
                $query->whereIn('permissions.id', $permissionIds);
            })
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear all permission caches
     */
    public static function clearAll(): void
    {
        // Clear specific area caches
        $areas = ['aprobaciones', 'certificados', 'finanzas', 'marketing', 'politicas', 'reuniones', 'rrhh'];

        foreach ($areas as $area) {
            Cache::forget("area_permissions:{$area}");
            Cache::forget("users_by_area:{$area}");
        }
    }

    /**
     * Clear cache for a specific area
     */
    public static function clearArea(string $areaSlug): void
    {
        Cache::forget("area_permissions:{$areaSlug}");
        Cache::forget("users_by_area:{$areaSlug}");
    }
}
