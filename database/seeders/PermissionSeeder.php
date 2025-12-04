<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all();

        $acciones = ['view', 'create', 'update', 'delete'];

        foreach ($areas as $area) {
            foreach ($acciones as $accion) {

                $full_name = ($area->parent) ? $area->parent->slug . '.' . $area->slug . '.' . $accion : $area->slug . '.' . $accion;

                Permission::firstOrCreate(
                    [
                        'name'       => $full_name,
                        'action'     => $accion,
                        'guard_name' => 'web',
                        'area_id'    => $area->id,
                    ],
                );
            }
        }
    }
}
