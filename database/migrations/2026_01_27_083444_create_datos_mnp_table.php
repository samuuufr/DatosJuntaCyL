<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datos_mnp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained('municipios')->onDelete('cascade');
            $table->year('anno');
            $table->enum('tipo_evento', ['nacimiento', 'defuncion', 'matrimonio']);
            $table->integer('valor')->default(0);
            $table->dateTime('ultima_actualizacion')->useCurrent();
            $table->timestamps();

            $table->index(['municipio_id', 'anno', 'tipo_evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_mnp');
    }
};
