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
        Schema::create('pedido_bloqueios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id')->index();
            $table->string('tipo');
            $table->text('descricao')->nullable();
            $table->text('observacao')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();

            $table->boolean('status')->nullable();


            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_bloqueios');
    }
};
