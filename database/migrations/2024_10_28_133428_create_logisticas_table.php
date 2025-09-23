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
        Schema::create('logistica_entregas', function (Blueprint $table) {
            $table->id();
            $table->integer('cod_filial');
            $table->integer('serie');
            $table->bigInteger('numero_nota');
            $table->bigInteger('numero_pedido');
            $table->integer('ordem');
            $table->bigInteger('cod_cli');
            $table->string('cidade');
            $table->string('uf', 2);
            $table->string('endereco');
            $table->integer('cod_vendedor');
            $table->integer('cod_gerente');
            $table->string('nome_gerente');
            $table->integer('cod_supervisor');
            $table->string('nome_supervisor');
            $table->string('chave_acesso');
            $table->timestamp('data_nota');
            $table->timestamp('data_pedido');
            $table->string('carga', 10)->nullable();
            $table->string('placa', 10)->nullable();
            $table->integer('cod_transportadora')->nullable();
            $table->string('nome_transportadora', 50)->nullable();
            $table->timestamp('data_carga');
            $table->string('canhoto_entrega')->nullable();
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
        Schema::dropIfExists('logistica_entregas');
    }
};
