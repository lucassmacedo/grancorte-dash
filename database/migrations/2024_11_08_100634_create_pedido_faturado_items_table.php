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
        Schema::create('pedido_faturado_items', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nota');
            $table->foreign('id_nota')
                ->references('id')
                ->on('cliente_notas');

            $table->bigInteger("cod_cliente");
//            $table->integer("cod_vendedor");
//            $table->integer("cod_supervisor")->nullable();
//            $table->integer("cod_gerente");

            $table->integer("cod_filial");
            $table->integer("cod_produto");
//            $table->string("nome_produto");

            $table->integer("pedido_qtd_auxiliar");
            $table->decimal("pedido_qtd_principal", 18, 3);

            $table->bigInteger("numero_nota");
            $table->bigInteger("numero_pedido")->nullable();


            $table->integer(column: "nota_qtd_auxiliar");
            $table->decimal("nota_qtd_principal", 18, 3);

            $table->date("data_mvto");

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
        Schema::dropIfExists('pedido_faturado_items');
    }
};
