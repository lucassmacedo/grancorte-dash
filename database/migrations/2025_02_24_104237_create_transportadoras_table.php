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
        Schema::create('logistica_transportadoras', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social');
            $table->string('nome_fantasia');
            $table->string('cnpj');
            $table->string('inscricao_estadual')->nullable();

            $table->string('cep', 8)->nullable();
            $table->string('endereco', 100)->nullable();
            $table->string('numero', 50)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->integer("city_id")->nullable();

            $table->string('fone')->nullable();
            $table->string('email')->nullable();
            $table->string('nome_contato')->nullable();
            $table->string('fone_contato')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('logistica_transportadoras_locais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transportadora_id')->constrained('logistica_transportadoras');

            $table->string('cod_filial');
            $table->integer('cod_local');
            $table->string('pagamento_por', 20);
            $table->decimal('valor_unitario', 10, 2);
//            $table->string('observacao', 255)->nullable();
//            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistica_transportadoras_locais');
        Schema::dropIfExists('logistica_transportadoras');
    }
};
