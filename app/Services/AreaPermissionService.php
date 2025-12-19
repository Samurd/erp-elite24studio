<?php

namespace App\Services;

use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
class AreaPermissionService
{
    /**
     * Request-level cache for areas.
     */
    protected static $areas;

    /**
     * Check if the user has permission to view a specific area.
     */
    public static function canArea(string $action, string $slug): bool
    {
        // 1. Load all areas once per request (Memoization)
        if (self::$areas === null) {
            // Check persistent cache first (optional, but good if 'area_structure' is reliable)
            // If reliability is unknown, it's safer to just query DB once per request:
            // self::$areas = Area::with('parent')->get()->keyBy('slug');

            // Re-using existing cache logic if user trusts it, but making it request-static
            $cached = Cache::get('area_structure');
            if ($cached) {
                self::$areas = $cached;
            } else {
                // Fallback to DB and cache for next time if desired, or just request-cache
                self::$areas = Area::with('parent')->get()->keyBy('slug');
                // Optional: Cache::put('area_structure', self::$areas, 60 * 60); 
            }
        }

        // 2. Retrieve from memory
        $area = self::$areas->get($slug);

        if (!$area) {
            return false;
        }

        $permissionName = $area->parent
            ? $area->parent->slug . "." . $area->slug . "." . $action
            : $area->slug . "." . $action;

        $user = Auth::user();

        return $user?->can($permissionName) ?? false;
    }
}
