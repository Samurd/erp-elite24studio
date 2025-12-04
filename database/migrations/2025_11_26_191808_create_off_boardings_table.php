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
        Schema::create('off_boardings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete(); // Proyecto asociado

            $table->text('reason')->nullable(); // Motivo de salida

            $table->date('exit_date')->nullable(); // Fecha de salida

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();


            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable del proceso


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('off_boardings');
    }
};
