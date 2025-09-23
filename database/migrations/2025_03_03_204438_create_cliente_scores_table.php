<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cliente_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cliente');
            $table->date('data_score');
            $table->integer('notas_fiscais')->default(0);
            $table->integer('notas_canceladas')->default(0);
            $table->decimal('total_compras', 15, 2)->default(0);
            $table->integer('titulos')->default(0);
            $table->integer('titulos_liquidados')->default(0);
            $table->integer('titulos_pagos_em_dia')->default(0);
            $table->integer('titulos_pagos_atraso')->default(0);
            $table->integer('titulos_atrasados')->default(0);
            $table->integer('titulos_pagos_adiantados')->default(0);
            $table->integer('titulos_em_aberto')->default(0);
            $table->decimal('media_dias_atraso', 10, 2)->default(0);
            $table->decimal('pontos_taxa_cancelamento', 10, 2)->default(0);
            $table->decimal('pontos_pagamentos_em_dia', 10, 2)->default(0);
            $table->decimal('pontos_titulos_liquidados', 10, 2)->default(0);
            $table->decimal('pontos_pagamentos_adiantados', 10, 2)->default(0);
            $table->decimal('pontos_titulos_atrasados', 10, 2)->default(0);
            $table->decimal('pontos_media_atraso', 10, 2)->default(0);
            $table->decimal('score_cliente', 10, 2)->default(0);
            $table->string('classificacao_cliente', 1);
            $table->timestamps();
        });
        Schema::create('cliente_grupo_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cod_grupo');
            $table->date('data_score');
            $table->integer('notas_fiscais')->default(0);
            $table->integer('notas_canceladas')->default(0);
            $table->decimal('total_compras', 15, 2)->default(0);
            $table->integer('titulos')->default(0);
            $table->integer('titulos_liquidados')->default(0);
            $table->integer('titulos_pagos_em_dia')->default(0);
            $table->integer('titulos_pagos_atraso')->default(0);
            $table->integer('titulos_atrasados')->default(0);
            $table->integer('titulos_pagos_adiantados')->default(0);
            $table->integer('titulos_em_aberto')->default(0);
            $table->decimal('media_dias_atraso', 10, 2)->default(0);
            $table->decimal('pontos_taxa_cancelamento', 10, 2)->default(0);
            $table->decimal('pontos_pagamentos_em_dia', 10, 2)->default(0);
            $table->decimal('pontos_titulos_liquidados', 10, 2)->default(0);
            $table->decimal('pontos_pagamentos_adiantados', 10, 2)->default(0);
            $table->decimal('pontos_titulos_atrasados', 10, 2)->default(0);
            $table->decimal('pontos_media_atraso', 10, 2)->default(0);
            $table->decimal('score_cliente', 10, 2)->default(0);
            $table->string('classificacao_cliente', 1);
            $table->timestamps();
        });
        // exec view ./sql/cliente_scores.sql
        DB::unprepared(file_get_contents(database_path('migrations/sqls/v_clientes_score.sql')));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_clientes_frequencia');
        DB::unprepared('DROP VIEW IF EXISTS v_cliente_grupo_score');
        DB::unprepared('DROP VIEW IF EXISTS v_cliente_score');

        Schema::dropIfExists('cliente_grupo_scores');
        Schema::dropIfExists('cliente_scores');
    }
};
