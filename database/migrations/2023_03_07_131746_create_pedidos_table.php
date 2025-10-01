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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->integer('cod_filial');
            $table->integer('cod_local');
            $table->bigInteger("codigo_cliente");
            $table->integer("codigo_vendedor");
            $table->integer("total_itens");

            $table->decimal('desconto', 10, 2)->default(0);
            $table->decimal('valor_total', 20, 2);
            $table->decimal('peso_total', 20, 2)->nullable();

            $table->integer('status')->default(0);

            $table->longText("observacoes")->nullable();
            $table->string("pedido_compra", 191)->nullable();

            $table->integer("tipo_descarga")->nullable();
            $table->decimal('valor_descarga', 20, 2)->nullable();
            $table->integer('sequencia_entrega')->nullable();
            $table->date("data_entrega")->nullable();

            $table->longText("observacoes_cancelamento")->nullable();
            $table->decimal('debitos_cliente', 20, 2)->nullable();
            $table->integer("tipo_pedido")->nullable();
            $table->integer("numero_veiculo")->nullable();

            $table->integer('export')->default(0);
            $table->timestamp("exported_at")->nullable();
            $table->boolean("faturado")->default(false);

            $table->userstamps();
            $table->timestamps();
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
