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
        Schema::create('kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete(); // ID del usuario que solicitó el kit
            $table->string('position_area'); // Cargo o Área a la que pertenece el kit
            $table->string('recipient_name'); // Nombre de la persona que recibirá el kit
            $table->string('recipient_role'); // Rol de la persona que recibirá el kit
            $table->string('kit_type')->nullable(); // Tipo de kit (e.g., básico, avanzado)
            $table->text('kit_contents')->nullable(); // Descripción del contenido del kit
            $table->date('request_date'); // Fecha en que se solicitó el kit
            $table->date('delivery_date')->nullable(); // Fecha estimada o real de entrega del kit
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // ID del estado actual del kit (e.g., pendiente, entregado)
            $table->foreignId('delivery_responsible_user_id')->nullable()->constrained('users')->nullOnDelete(); // ID del usuario responsable de la entrega del kit
            $table->text('observations')->nullable(); // Observaciones adicionales sobre el kit o la entrega

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kits');
    }
};
