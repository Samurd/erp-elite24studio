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
        Schema::create("projects", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description")->nullable();
            $table->string('direction')->nullable();

            // Relation to client (contact)
            $table
                ->foreignId("contact_id")
                ->nullable()
                ->constrained("contacts")
                ->nullOnDelete();

            // Relations to tags
            $table
                ->foreignId("status_id")
                ->nullable()
                ->constrained("tags")
                ->nullOnDelete();
            $table
                ->foreignId("project_type_id")
                ->nullable()
                ->constrained("tags")
                ->nullOnDelete();
            $table
                ->foreignId("current_stage_id")
                ->nullable()
                ->constrained("stages")
                ->nullOnDelete();
            $table->foreignId("responsible_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $table->foreignId("team_id")
                ->nullable()
                ->constrained("teams")
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("projects");
    }
};
