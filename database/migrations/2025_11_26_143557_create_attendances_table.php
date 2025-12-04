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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date'); // fecha de registo
            $table->time('check_in'); // hora de entrada
            $table->time('check_out'); // hora de salida

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            $table->foreignId('modality_id')->nullable()->constrained('tags')->nullOnDelete(); // modalidad de trabajo

            $table->text('observations')->nullable(); // observaciones

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
