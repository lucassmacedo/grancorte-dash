<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bandwidth_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->integer('response_size'); // Tamanho da resposta em bytes
            $table->string('method'); // GET, POST, etc.
            $table->string('ip'); // IP do usuário
            $table->string('user_agent')->nullable(); // Informações do navegador
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('band_with_logs');
    }
};
