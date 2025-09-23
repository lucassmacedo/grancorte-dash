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
        Schema::create('prospect_cliente_observacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_cliente_id')->constrained('prospect_clientes');
            $table->text('observacao');
            $table->string('tipo');
            $table->string('file')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('prospect_cliente_observacoes');
    }
};
