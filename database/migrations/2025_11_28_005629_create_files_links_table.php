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
        Schema::create("files_links", function (Blueprint $table) {
            $table->id();

            $table->foreignId("area_id")
                ->nullable()
                ->constrained("areas")
                ->nullOnDelete();

            // 1. El Archivo (Si se borra el archivo físico, se borra el link)
            $table->foreignId("file_id")
                ->constrained("files")
                ->cascadeOnDelete();

            // 2. El Destino (Project, Task, Comment, etc.)
            // Esto crea: fileable_id y fileable_type + Index
            $table->morphs("fileable");

            // Opcional: Timestamps para saber cuándo se vinculó
            $table->timestamps();

            // 3. Candado de seguridad:
            // Evita que vincules el MISMO archivo al MISMO proyecto dos veces.
            $table->unique(["file_id", "fileable_id", "fileable_type"], "files_links_unique");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("files_links");
    }
};
