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
        Schema::create('prospect_clientes', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo_vendedor');
            $table->integer('codigo_cliente');
            $table->integer("status")->default(0);

            // Informações Cadastrais
            $table->string('razao_social');
            $table->string('nome_fantasia', 100)->nullable();
            $table->string('cnpj', 20);
            $table->char('tipo_pessoa', 1)->nullable();
            $table->string('inscricao_estadual', 100)->nullable();
            $table->string('cliente_tipo', 100)->nullable();

            $table->string('endereco', 100)->nullable();
            $table->string('numero', 50)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->integer('natureza')->nullable();
            $table->integer("city_id")->nullable();
            $table->string('telefone_responsavel', 20)->nullable();
            $table->string('telefone_whatsapp', 20)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('telefone_compras', 20)->nullable();
            $table->date('fundacao')->nullable();
            $table->string('telefone_financeiro', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('email_xml', 50)->nullable();

            // Financeiro
            $table->integer('prazo')->nullable();
            $table->integer('area_atuacao')->nullable();
            $table->decimal('limite_credito', 15, 2)->nullable();
            $table->string('atividade', 100)->nullable();
            $table->boolean('rede')->default(false)->nullable();
            $table->boolean('simples_nacional')->default(false);
            $table->decimal('faturamento', 15, 2)->nullable();

            $table->json('contatos_referencia')->nullable();

            // Logística
            $table->string('rota')->nullable();
            $table->string('telefone_recebimento', 20)->nullable();
            $table->text('observacoes_entrega')->nullable();
            $table->string('endereco_entrega', 100)->nullable();

            // Comercial
            $table->boolean('contrato_fornecedor_cliente')->default(false);

            // Informações Adicionais
            $table->text('informacoes_adicionais')->nullable();

            // Anexos de Documentos
            $table->string('anexo_cnpj')->nullable();
            $table->string('anexo_inscricao_estadual')->nullable();
            $table->string('anexo_comprovante_endereco')->nullable();
            $table->string('anexo_contrato_social')->nullable();
            $table->string('anexo_alvara')->nullable();
            $table->string('anexo_irpj')->nullable();
            $table->string('anexo_comprovante_bancario')->nullable();
            $table->string('anexo_fotos')->nullable();
            $table->string('anexo_1')->nullable();
            $table->string('anexo_2')->nullable();
            $table->string('anexo_3')->nullable();
            $table->string('localizacao')->nullable();
            $table->softDeletes();

            // Campos de auditoria
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
        Schema::dropIfExists('prospect_clientes');
    }
};
