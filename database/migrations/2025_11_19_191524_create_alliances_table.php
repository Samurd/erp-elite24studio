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
        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Nombre de la entidad
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->date('start_date')->nullable(); // fecha de inicio
            $table->bigInteger('validity')->nullable(); // vigencia en meses, ej: 24 meses, 12 meses, etc.

            $table->boolean('certified')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliances');
    }
};
