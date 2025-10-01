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

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->onDelete('cascade');

            $table->integer('codigo_produto');
            $table->foreign('codigo_produto')
                ->references('codigo')
                ->on('produtos');

            $table->integer('quantidade');
            $table->decimal('valor_unitario_original', 20, 2);
            $table->decimal('valor_unitario', 20, 2);
            $table->decimal('valor_total', 20, 2);
            $table->decimal('peso_total', 20, 2);
            $table->boolean('preco_alterado')->default(false);

            $table->decimal('desconto', 10, 2)->default(0);

            $table->date('data_pedido')->nullable();
            $table->boolean('deleta_do_corte')->default(false);

            $table->userstamps();
            $table->timestamps();

            // Índices
            $table->index('codigo_produto', 'idx_pedido_items_codigo_produto');
            $table->index('pedido_id', 'idx_pedido_items_pedido_id');
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
