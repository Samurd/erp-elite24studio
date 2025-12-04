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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete(); // Tipo de contrato
            $table->foreignId('category_id')->nullable()->constrained('tags')->nullOnDelete(); // Categoria de contrato

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado de contrato

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->bigInteger('amount')->nullable(); // Salario/Pago

            $table->string('schedule')->nullable(); // Horario laboral

            $table->foreignId('registered_by_id')->nullable()->constrained('users')->nullOnDelete();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
