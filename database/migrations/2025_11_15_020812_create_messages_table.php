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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // Usuario que envió el mensaje
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Mensaje puede pertenecer a canal (Slack style)
            $table->foreignId('channel_id')
                ->nullable()
                ->constrained('team_channels')
                ->nullOnDelete();

            // O mensaje puede pertenecer a chat privado
            $table->foreignId('private_chat_id')
                ->nullable()
                ->constrained('private_chats')
                ->nullOnDelete();

            // Contenido del mensaje
            $table->text('content')->nullable();

            // text, image, file, system
            $table->string('type')->default('text');

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
