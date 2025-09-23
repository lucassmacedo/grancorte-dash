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
        Schema::create('logistica_roterizacao_rateios', function (Blueprint $table) {
            $table->id();
            $table->integer('roterizacao_id');
            $table->foreign('roterizacao_id')->references('id')->on('logistica_roterizacaos');
            $table->integer('cod_filial');
            $table->decimal('peso_total', 10);
            $table->decimal('percentual_peso', 10);
            $table->decimal('valor_descarga', 10);
            $table->decimal('valor_pedagio', 10)->nullable();
            $table->decimal('valor_escolta', 10)->nullable();
            $table->decimal('valor_despesa_extra', 10)->nullable();
            $table->decimal('valor_acrescimo', 10)->nullable();
            $table->decimal('valor_desconto', 10)->nullable();
            $table->decimal('valor_total_carga', 10)->nullable();
            $table->unique(['roterizacao_id', 'cod_filial']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistica_roterizacao_rateios');
    }
};
