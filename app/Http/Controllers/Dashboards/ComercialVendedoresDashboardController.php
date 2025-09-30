<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\ClienteNotas;
use App\Models\ClienteNotasItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComercialVendedoresDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Intervalo desejado: de ontem às 20:00 até hoje às 23:59:59
        $inicio = Carbon::yesterday()->setTime(20, 0, 0);
        $fim    = Carbon::today()->setTime(23, 59, 59);

        // Métricas gerais do dia
        $dashboard_geral = ClienteNotas::selectRaw("
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido,
            count(distinct cliente_notas.cod_vendedor) as vendedores_ativos")
            ->leftJoin('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->first();

        // Produtos distintos vendidos
        $produtos_vendidos = ClienteNotas::selectRaw("count(distinct cod_produto) as produtos")
            ->join('cliente_notas_items', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->first();

        // Performance por vendedor
        $vendedores_performance = ClienteNotas::selectRaw("
            cliente_notas.cod_vendedor,
            users.apelido as vendedor,
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido")
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->groupBy('cliente_notas.cod_vendedor', 'users.apelido')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) <= ?", [$fim->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->orderBy('valor_liquido', 'desc')
            ->take(5)
            ->get();

        // Vendas dos últimos 7 dias (corrigido para PostgreSQL e sintaxe PHP)
        $inicio_7dias = Carbon::today()->subDays(6)->setTime(0, 0, 0);
        $fim_7dias    = Carbon::today()->setTime(23, 59, 59);

        $vendas_ultimos_7_dias = ClienteNotas::selectRaw(
            "DATE(data_mvto || ' ' || hora) as dia, sum(valor_liquido) as valor_liquido"
        )
            ->whereRaw("(data_mvto || ' ' || hora) BETWEEN ? AND ?", [$inicio_7dias->format('Y-m-d H:i:s'), $fim_7dias->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->groupBy(DB::raw("DATE(data_mvto || ' ' || hora)"))
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Top clientes do dia
        $top_clientes = ClienteNotas::selectRaw("
            cod_cli_for,
            clientes.nome as cliente,
            count(*) as notas,
            sum(valor_liquido) as valor_liquido")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) <= ?", [$fim->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->groupBy('cod_cli_for', 'clientes.nome')
            ->orderBy('valor_liquido', 'desc')
            ->take(5)
            ->get();

        // Produtos mais vendidos (quantidade)
        $produtos_mais_vendidos = ClienteNotasItem::selectRaw("
            cod_produto,
            produtos.descricao as desc_produto,
            sum(cliente_notas_items.qtd_auxiliar) as quantidade_total,
            sum(cliente_notas_items.valor_liquido) as valor_total,
            count(*) as ocorrencias")
            ->join('cliente_notas', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('produtos', 'produtos.codigo', 'cliente_notas_items.cod_produto')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) <= ?", [$fim->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->groupBy('cod_produto', 'produtos.descricao')
            ->orderBy('quantidade_total', 'desc')
            ->take(5)
            ->get();

        // Últimas vendas (tempo real)
        $ultimas_vendas = ClienteNotas::select([
            'cliente_notas.id',
            'cliente_notas.num_docto',
            'cliente_notas.data_mvto',
            'cliente_notas.valor_liquido',
            'users.apelido as vendedor',
            'clientes.nome as cliente'
        ])
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) <= ?", [$fim->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->orderBy('data_mvto', 'desc')
            ->take(5)
            ->get();

        return view('pages.dashboards.comercial-vendedores', compact(
            'dashboard_geral',
            'produtos_vendidos',
            'vendedores_performance',
            'vendas_ultimos_7_dias',
            'top_clientes',
            'produtos_mais_vendidos',
            'ultimas_vendas'
        ));
    }
}
