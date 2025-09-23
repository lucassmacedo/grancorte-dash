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
        Schema::create('cliente_notas', function (Blueprint $table) {
            $table->id();
            $table->string('chave_acesso', 100);
            $table->string('cod_filial', 100);
            $table->string('serie_seq', 100);
            $table->string('num_docto', 100);
            $table->bigInteger('cod_cli_for')->nullable();
            $table->string('status_nfe', 1)->nullable();
            $table->date('data_mvto')->nullable();
            $table->decimal('valor_liquido', 15, 2)->default(0);
            $table->text('xml_nfe')->nullable();
            $table->text('xml_cancnfe')->nullable();
            $table->bigInteger('cod_vendedor')->nullable();
            $table->bigInteger('cod_gerente')->nullable();
            $table->bigInteger('cod_supervisor')->nullable();
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
        Schema::dropIfExists('cliente_notas');
    }
};
