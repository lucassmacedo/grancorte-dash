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
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->integer('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');

            $table->integer('codigo_produto');
            $table->foreign('codigo_produto')->references('codigo')->on('produtos');

            $table->integer('quantidade');
            $table->decimal('valor_unitario_original', 20);
            $table->decimal('valor_unitario', 20);
            $table->decimal('valor_total', 20);
            $table->decimal('peso_total', 20);
            $table->boolean('preco_alterado')->default(FALSE);

            $table->decimal('desconto', 10)->default(0);
            $table->userstamps();
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
        Schema::dropIfExists('pedido_items');
    }
};
