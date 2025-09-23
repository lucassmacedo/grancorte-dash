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
        Schema::create('logistica_rotas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->boolean('status')->default(true);
            $table->string('cep_inicial')->nullable();
            $table->string('cep_final')->nullable();
            $table->integer('ordem')->nullable();
            $table->json('dias_entrega')->nullable();
            $table->json('horarios_entrega')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistica_rotas');
    }
};
