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
        Schema::create('dashboard_recebimento', function (Blueprint $table) {
            $table->id();
            $table->string('ano');
            $table->string('mes');
            $table->string('cod_vendedor');
            $table->string('cod_filial');
            $table->decimal('recebimento', 20, 2)->default(0);
            $table->decimal('comissao', 20, 2)->default(0);
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
        Schema::dropIfExists('dashboard_recebimento');
    }
};
