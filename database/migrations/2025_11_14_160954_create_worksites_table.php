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
        Schema::create('worksites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete(); // Proyecto asociado
            $table->string('name'); // Nombre de la obra
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete(); // Tipo de obra
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado de obra
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable de la obra
            $table->string('address')->nullable(); // Dirección de la obra
            $table->date('start_date')->nullable(); // Fecha de inicio estimada
            $table->date('end_date')->nullable(); // Fecha de entrega estimada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksites');
    }
};
