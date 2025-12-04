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
        Schema::create('inductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // Empleado

            $table->foreignId('type_bond_id')->nullable()->constrained('tags')->nullOnDelete(); // Tipo de Vinculo (ej: Nomina, Contratista, Pasantia, etc...)

            $table->date('entry_date'); // Fecha de ingreso

            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable de induccion

            $table->date('date')->nullable(); // Fecha programada de induccion

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado de induccion


            $table->foreignId('confirmation_id')->nullable()->constrained('tags')->nullOnDelete(); // Confirmacion

            $table->string('resource')->nullable(); // Link de la induccion o informacion del lugar


            $table->time('duration', 0)->nullable(); // Duracion estimada de la reunion en horas:minutos
            

            $table->text('observations')->nullable(); // Observaciones

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inductions');
    }
};
