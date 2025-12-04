<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            // Relación al equipo
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');

            // Usuario miembro del equipo
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relación al rol
            $table->foreignId('role_id')->constrained('team_roles')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
