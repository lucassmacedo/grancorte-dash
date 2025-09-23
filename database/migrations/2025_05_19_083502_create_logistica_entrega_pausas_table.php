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
        Schema::create('logistica_entrega_pausas', function (Blueprint $table) {
            $table->id();
            $table->integer('carga');
            $table->string('placa', 10);
            $table->string('tipo');
            $table->text('descricao')->nullable();
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fim')->nullable();
            $table->decimal('latitude_inicio', 10, 7);
            $table->decimal('longitude_inicio', 10, 7);
            $table->decimal('latitude_fim', 10, 7)->nullable();
            $table->decimal('longitude_fim', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistica_entrega_pausas');
    }
};
