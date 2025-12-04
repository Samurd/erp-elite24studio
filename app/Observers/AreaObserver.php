<?php

namespace App\Observers;

use App\Models\Area;
use App\Models\Permission;

class AreaObserver
{
    /**
     * Handle the Area "created" event.
     */
    public function created(Area $area): void
    {
        //
    }

    /**
     * Handle the Area "updated" event.
     */
    public function updated(Area $area): void
    {
        // Solo actuar si el campo "slug" cambió
        if ($area->wasChanged('slug')) {
            $this->syncPermissions($area);
        }
    }

    /**
     * Handle the Area "deleted" event.
     */
    public function deleted(Area $area): void
    {
        //
    }

    /**
     * Handle the Area "restored" event.
     */
    public function restored(Area $area): void
    {
        //
    }

    /**
     * Handle the Area "force deleted" event.
     */
    public function forceDeleted(Area $area): void
    {
        //
    }

    protected function syncPermissions(Area $area)
    {
        // Buscamos el área y sus subáreas
        $areas = Area::where('id', $area->id)
            ->orWhere('parent_id', $area->id)
            ->get();

        foreach ($areas as $subarea) {
            $permisos = Permission::where('area_id', $subarea->id)->get();

            foreach ($permisos as $permiso) {
                $permiso->name = $this->buildFullName($subarea, $permiso->action);
                $permiso->save();
            }
        }
    }

    protected function buildFullName(Area $area, string $action): string
    {
        if ($area->parent) {
            return $area->parent->slug . '.' . $area->slug . '.' . $action;
        }
        return $area->slug . '.' . $action;
    }
}
