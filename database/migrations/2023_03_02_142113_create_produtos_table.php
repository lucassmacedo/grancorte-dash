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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();

            $table->integer('codigo')->unique();
            $table->string('descricao', 255);
            $table->string('conservacao', 20);
            $table->string('sif', 10);

            $table->string('cod_unidade_vda', 10);
            $table->string('cod_unidade_aux', 10);
            $table->string('desc_unidade_aux', 10);

            $table->integer('cod_lista');
            $table->string('desc_lista', 255);

            $table->integer('formula_preco');

            $table->string('status', 191);
            $table->decimal('peso_medio', 20, 2);

            $table->integer('cod_grupo')->nullable();
            $table->string('desc_grupo', 255)->nullable();
            $table->boolean('venda_por_par')->nullable();

            $table->decimal('tolerancia_preco_pedido', 10, 2)->default(10);
            $table->boolean('peso_padrao')->default(true);

            $table->timestamps();

            // índice extra, além do unique
            $table->index('codigo', 'produtos_codigo_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
};
