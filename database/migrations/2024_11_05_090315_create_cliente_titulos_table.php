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
        Schema::create('cliente_titulos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cliente');
            $table->string('cnpj_cliente');
            $table->string('cod_filial');
            $table->string('nome_filial');
            $table->string('e1_prefixo')->nullable();
            $table->string('numero_titulo')->nullable();
            $table->string('numero_parcela')->nullable();
            $table->string('nossonumero')->nullable();
            $table->decimal('percentual_desconto_financeiro', 15, 2)->nullable();
            $table->decimal('valor_desconto_financeiro', 15, 2)->nullable();
            $table->decimal('valor', 15, 2);
            $table->decimal('saldo', 15, 2)->nullable();
            $table->decimal('acrescimo_financeiro', 15, 2)->nullable();
            $table->decimal('decrescimo_financeiro', 15, 2)->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->date('data_baixa')->nullable();
            $table->integer('hist_dias_atraso')->nullable();
            $table->string('status')->nullable();
            $table->boolean('status_protesto')->nullable();
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
        Schema::dropIfExists('cliente_titulos');
    }
};
