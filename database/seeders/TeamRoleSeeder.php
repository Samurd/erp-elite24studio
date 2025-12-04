<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamRole;

class TeamRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamRole::updateOrCreate(['slug' => 'owner'], [
            'name' => 'Propietario',
        ]);

        TeamRole::updateOrCreate(['slug' => 'member'], [
            'name' => 'Miembro',
        ]);
    }
}
