<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $contador = Role::firstOrCreate(['name' => 'contador', 'display_name' => 'Contador']);
        // $contador->givePermissionTo([
        //     Permission::where('name', 'finanzas.ingresos.create')->first(),
        //     Permission::where('name', 'finanzas.ingresos.update')->first(),
        //     Permission::where('name', 'finanzas.ingresos.delete')->first(),
        //     Permission::where('name', 'finanzas.ingresos.view')->first(),
        //     Permission::where('name', 'finanzas.gastos.create')->first(),
        //     Permission::where('name', 'finanzas.gastos.update')->first(),
        //     Permission::where('name', 'finanzas.gastos.delete')->first(),
        //     Permission::where('name', 'finanzas.gastos.view')->first(),
        // ]);

        // Rol Super Admin
        $superAdmin = Role::firstOrCreate([
            "name" => "super_admin",
            "display_name" => "Super Admin",
        ]);
        $superAdmin->givePermissionTo(Permission::all());
    }
}
