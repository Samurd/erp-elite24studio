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
        Schema::create("quotes", function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId("contact_id")
                ->nullable()
                ->constrained("contacts")
                ->nullOnDelete();

            // $table
            //     ->foreignId("project_id")
            //     ->nullable()
            //     ->constrained("projects")
            //     ->nullOnDelete();

            $table->date("issued_at")->nullable();
            $table
                ->foreignId("status_id")
                ->nullable()
                ->constrained("tags")
                ->nullOnDelete();
            $table->bigInteger("total")->nullable();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("quotes");
    }
};
