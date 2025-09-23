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
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo')->nullable();
            $table->string('nome');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('apelido')->nullable();
            $table->string('cpf_cgc', 14)->nullable();
            $table->string('rg_ie', 14)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cidade', 50)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('telefone')->nullable();
            $table->boolean('status')->nullable();

            $table->boolean('is_admin')->default(false);
            $table->boolean('vendas_clientes_com_debitos')->default(true);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
