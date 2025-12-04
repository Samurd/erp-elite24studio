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
        Schema::create("shares", function (Blueprint $table) {
            $table->id();

            // Quién comparte (opcional, por si quieres rastrear)
            $table->foreignId("user_id")->constrained()->onDelete("cascade");

            // Con quién se comparte
            $table
                ->foreignId("shared_with_user_id")
                ->nullable()
                ->constrained("users")
                ->onDelete("cascade");
            $table
                ->foreignId("shared_with_team_id")
                ->nullable()
                ->constrained("teams")
                ->onDelete("cascade");

            // Archivo o carpeta
            $table->morphs("shareable"); // shareable_type + shareable_id

            // Permisos
            $table->enum("permission", ["view", "edit"])->default("view");

            // Acceso público opcional
            $table->string("share_token")->nullable()->unique();
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("shares");
    }
};
