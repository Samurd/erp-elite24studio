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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['scheduled', 'recurring', 'reminder']);
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();

            // Usuario destinatario
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Relación polimórfica
            $table->nullableMorphs('notifiable');

            // Campos para scheduled
            $table->timestamp('scheduled_at')->nullable();

            // Campos para recurring
            $table->json('recurring_pattern')->nullable();

            // Campos para reminder
            $table->integer('reminder_days')->nullable();
            $table->timestamp('event_date')->nullable();

            // Control de envío
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('next_send_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Índices para optimizar queries
            $table->index(['type', 'is_active', 'next_send_at']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
