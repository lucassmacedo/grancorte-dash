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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("codigo")->unsigned();

            $table->string("tipo_cadastro", 1)->nullable();
            $table->string("nome", 100)->nullable();
            $table->string("apelido", 100)->nullable();
            $table->string("cpf_cgc", 20)->nullable();
            $table->string("rg_ie", 20)->nullable();
            $table->string("uf", 2)->nullable();
            $table->string("cidade", 100)->nullable();
            $table->string("bairro", 100)->nullable();
            $table->string("endereco", 100)->nullable();
            $table->string("numero", 10)->nullable();
            $table->string("telefone", 20)->nullable();
            $table->longText("email")->nullable();

            $table->string("latitude", 20)->nullable();
            $table->string("longitude", 20)->nullable();
            $table->string("cod_situacao", 2)->nullable();
            $table->integer("cod_lista")->nullable();
            $table->string("desc_lista", 100)->nullable();
            $table->integer("cod_vendedor")->nullable();
            $table->string("cod_forma_cob")->nullable();
            $table->string("desc_forma_cob", 100)->nullable();
            $table->string("cod_cond_pgto", 10)->nullable();
            $table->string("desc_cond_pgto", 100)->nullable();
            $table->integer("prazo_medio")->nullable();
            $table->integer("cod_area")->nullable();
            $table->string("nome_area", 100)->nullable();
            $table->integer("cod_ramo")->nullable();
            $table->string("ramo_atividade", 100)->nullable();
            $table->decimal("debitos_individuais", 20)->nullable();
            $table->decimal("debitos_grupo", 20)->nullable();
            $table->decimal("limite_credito", 20)->nullable();
            $table->string("limite_disponivel")->nullable();
//            $table->string("adiantamentos_grupo")->nullable();
//            $table->string("adiantamentos_CNPJ")->nullable();
            $table->integer("cod_grupo_limite")->nullable();
            $table->string("nome_grupo", 100)->nullable();
            $table->string("credito_limitado", 1)->nullable();
            $table->decimal("perc_desconto", 20)->nullable();
            $table->date("data_ultima_compra")->nullable();
            $table->integer("dias_sem_compra")->nullable();
            $table->decimal("valor_ultima_compra", 20)->nullable();
            $table->decimal("valor_maior_compra", 20)->nullable();
            $table->bigInteger("Cod_Sisant")->nullable();


            // add index to improve performance
            $table->index('codigo');
            $table->index('cpf_cgc');
            $table->index(['cod_vendedor']);
            $table->index(['cod_vendedor', 'cpf_cgc']);
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
        Schema::dropIfExists('clientes');
    }
};
