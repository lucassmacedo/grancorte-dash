<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logistica_roterizacaos', function (Blueprint $table) {
            $table->id();
            $table->date('data_entrega');
            $table->foreignId('caminhao_id')->constrained('logistica_caminhoes');
            $table->integer('armazem_id');
            $table->integer('cod_filial')->nullable();
            $table->integer('cod_local')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('mostrar_nao_roterizados')->default(true);
            $table->json('options')->nullable();
            $table->json('tipo_conservacao')->nullable();
            $table->decimal('km_aproximado', 10, 2)->nullable();
            $table->json('pedagios')->nullable();
            $table->json('coordenadas')->nullable();
            $table->jsonb('rotas')->nullable();
            $table->decimal('valor_descarga', 10, 2)->nullable();
            $table->decimal('valor_escolta', 10, 2)->nullable();
            $table->decimal('valor_despesa_extra', 10, 2)->nullable();
            $table->decimal('valor_acrescimo', 10, 2)->nullable();
            $table->text('observacao')->nullable();
            $table->decimal('valor_desconto', 10, 2)->nullable();
            $table->string('tipo_frete', 1)->nullable();
            $table->string('numero_lacre', 255)->nullable();
            $table->decimal('valor_unitario_transporte', 10, 2)->nullable();
            $table->decimal('valor_total_carga', 10, 2)->nullable();

            $table->dateTime('data_integracao')->nullable();
            $table->userstamps();
            $table->softDeletes();
            $table->timestamps();
        });

        // roterizacao_pedidos
        Schema::create('logistica_roterizacao_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roterizacao_id')->constrained('logistica_roterizacaos');
            $table->foreignId('pedido_id')->constrained('pedidos');
            $table->foreignId('rota_id')->constrained('logistica_rotas');
            $table->unsignedBigInteger('cod_cliente');
            $table->foreign('cod_cliente')->references('codigo')->on('clientes');
            $table->boolean('status')->default(false);
            $table->integer('sequencia');
            $table->string('observacoes', 191)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('logistica_roterizacao_pedidos', function (Blueprint $table) {
            $table->index('roterizacao_id', 'idx_logistica_roterizacao_pedidos_roterizacao_id');
            $table->index('pedido_id', 'idx_logistica_roterizacao_pedidos_pedido_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistica_roterizacao_pedidos');
        Schema::dropIfExists('logistica_roterizacaos');
    }
};
