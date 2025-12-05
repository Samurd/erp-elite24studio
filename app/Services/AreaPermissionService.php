<?php

namespace App\Services;

use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
class AreaPermissionService
{
    /**
     * Create a new class instance.
     */

    public static function canArea(string $action, string $slug): bool
    {
        $areas = Cache::get('area_structure');

        if (!$areas) {
            $area = Area::where("slug", $slug)->with("parent")->first();
        } else {
            $area = $areas->get($slug);
        }

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
