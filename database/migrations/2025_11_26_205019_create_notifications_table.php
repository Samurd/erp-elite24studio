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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // now, scheduled, recurring, reminder
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Datos adicionales

            // Relación polimórfica con cualquier modelo
            $table->nullableMorphs('notifiable');

            // Usuario destinatario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Control de lectura
            $table->timestamp('read_at')->nullable();

            // Programación y recurrencia
            $table->timestamp('scheduled_at')->nullable();
            $table->json('recurring_pattern')->nullable(); // {interval: 'daily'|'monthly'|'days', value: X, day: X}
            $table->integer('reminder_days')->nullable(); // Días antes para recordatorios
            $table->timestamp('expires_at')->nullable();

            // Control de envío
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, expired

            $table->timestamps();

            // Índices para optimización
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('scheduled_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
