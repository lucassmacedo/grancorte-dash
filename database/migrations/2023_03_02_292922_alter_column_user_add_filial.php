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
        // create timezone column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->jsonb('cod_filial')->nullable();
            $table->jsonb('cod_local')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cod_filial');
            $table->dropColumn('cod_local');
        });
    }
};
