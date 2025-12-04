<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@domain.com')],
            [
                'name' => env('ADMIN_NAME', 'Administrador'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'adminpassword')),
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        // Support user
        $support = User::firstOrCreate(
            ['email' => env('SUPPORT_EMAIL', 'soporte@domain.com')],
            [
                'name' => env('SUPPORT_NAME', 'Soporte'),
                'password' => Hash::make(env('SUPPORT_PASSWORD', 'supportpassword')),
            ]
        );

        if (!$support->hasRole('super_admin')) {
            $support->assignRole('super_admin');
        }
    }
}
