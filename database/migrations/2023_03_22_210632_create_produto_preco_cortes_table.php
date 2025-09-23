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
        Schema::create('produto_preco_cortes', function (Blueprint $table) {
            $table->id();
            $table->integer('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');

            $table->integer('codigo_produto');
            $table->foreign('codigo_produto')->references('codigo')->on('produtos');

            $table->integer('quantidade');

            $table->boolean('status')->default(FALSE);
            $table->timestamps();
            $table->userstamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_preco_cortes');
    }
};
