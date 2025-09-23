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
        Schema::create('cliente_notas_items', function (Blueprint $table) {
            $table->id();
            $table->integer('id_nota');
            $table->foreign('id_nota')
                ->references('id')
                ->on('cliente_notas');

            $table->integer("cod_produto");
            $table->string("descricao");
            $table->integer('qtd_auxiliar');
            $table->decimal('qtd_pri', 15, 2);
            $table->decimal('valor_unitario', 15, 2);
            $table->decimal('valor_total', 15, 2);
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
        Schema::dropIfExists('cliente_notas_items');
    }
};
