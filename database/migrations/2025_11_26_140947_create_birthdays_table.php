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
        Schema::create('birthdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // Tipo de relacion (Empleado, cliente, proveedor, Contratista, etc.)


            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete(); // Proyecto asociado

            $table->date('date'); // Fecha de cumpleaños/ nacimiento

            $table->string('whatsapp')->nullable(); // WhatsApp


            $table->text('comments')->nullable(); // Comentarios adicionales

            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable de registro

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthdays');
    }
};
