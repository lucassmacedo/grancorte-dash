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
        Schema::create('produto_precos', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo');
            $table->foreign('codigo')->references('codigo')->on('produtos');

            $table->decimal('preco_minimo', 20);
            $table->decimal('preco', 20);

            $table->integer('cod_filial');
            $table->integer('cod_local');
            $table->integer('cod_lista');

            $table->decimal('saldo_pri');
            $table->decimal('saldo_aux');
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
        Schema::dropIfExists('produto_precos');
    }
};
