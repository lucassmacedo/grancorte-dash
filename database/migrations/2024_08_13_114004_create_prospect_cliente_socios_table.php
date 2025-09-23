<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_cliente_socios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('prospect_clientes')->onDelete('cascade');
            $table->string('nome');
            $table->string('empresa');
            $table->string('cpf');
            $table->string('rg')->nullable();
            $table->string('anexo_residencia')->nullable(); // Comprovante de residência
            $table->string('anexo_cpf')->nullable();        // Comprovante de CPF
            $table->string('anexo_rg')->nullable();         // Comprovante de RG
            $table->softDeletes();
            // Campos de auditoria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_cliente_socios');
    }
};
