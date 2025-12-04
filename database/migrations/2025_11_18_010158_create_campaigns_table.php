<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Nombre de la campaña
            $table->date('date_event')->nullable(); // Fecha del evento

            $table->string('address')->nullable(); // Lugar del evento

            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable de la campaña / evento

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado de la campaña / evento

            $table->text('alliances')->nullable(); // Alianzas involucradas

            $table->bigInteger('goal')->nullable(); // Objetivo meta

            $table->bigInteger('estimated_budget')->nullable(); // Presupuesto estimado

            $table->text('description')->nullable(); // Descripcion de actividad


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
