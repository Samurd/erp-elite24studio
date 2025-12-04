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
        Schema::create('team_channels', function (Blueprint $table) {
            $table->id();

            // Canal pertenece a un equipo
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique(); // Para URL tipo /team/channel/general
            $table->text('description')->nullable();

            // Public = todos del equipo; Private = solo los del canal
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_channels');
    }
};
