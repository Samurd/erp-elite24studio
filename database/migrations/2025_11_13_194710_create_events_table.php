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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del evento
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete(); // Tipo (Stand + Flyer, etc.)
            $table->date('event_date');
            $table->string('location')->nullable();
            // Estado: 'planned', 'confirmed', 'executed', etc.
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // O unsignedBigInteger si relacionas con tabla users

            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
