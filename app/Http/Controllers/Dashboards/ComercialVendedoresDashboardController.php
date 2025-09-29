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
        // Data atual (hoje)
        $data_hoje = Carbon::today()->subDays(3)->format('Y-m-d');

        // Métricas gerais do dia
        $dashboard_geral = ClienteNotas::selectRaw("
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido,
            count(distinct cliente_notas.cod_vendedor) as vendedores_ativos")
            ->leftJoin('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->first();

        // Produtos distintos vendidos hoje
        $produtos_vendidos = ClienteNotas::selectRaw("count(distinct cod_produto) as produtos")
            ->join('cliente_notas_items', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->whereDate('data_mvto', $data_hoje)
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
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->orderBy('valor_liquido', 'desc')
            ->take(10)
            ->get();

        // Vendas por hora do dia
        $vendas_por_hora = ClienteNotas::selectRaw("
            EXTRACT(HOUR FROM data_mvto) as hora,
            count(*) as notas,
            sum(valor_liquido) as valor_liquido")
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupBy(DB::raw('EXTRACT(HOUR FROM data_mvto)'))
            ->orderBy('hora')
            ->get()
            ->keyBy('hora');

        // Preencher horas vazias (0-23)
        $horas_completas = collect(range(0, 23))->map(function ($hora) use ($vendas_por_hora) {
            return [
                'hora' => $hora,
                'notas' => $vendas_por_hora->get($hora)?->notas ?? 0,
                'valor_liquido' => $vendas_por_hora->get($hora)?->valor_liquido ?? 0,
            ];
        });

        // Top clientes do dia
        $top_clientes = ClienteNotas::selectRaw("
            cod_cli_for,
            clientes.nome as cliente,
            count(*) as notas,
            sum(valor_liquido) as valor_liquido")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupBy('cod_cli_for', 'clientes.nome')
            ->orderBy('valor_liquido', 'desc')
            ->take(10)
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
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupBy('cod_produto', 'produtos.descricao')
            ->orderBy('quantidade_total', 'desc')
            ->take(10)
            ->get();

        // Distribuição por tipo de saída
        $tipos_saida = ClienteNotasItem::selectRaw("
            cod_saida,
            nome_saida,
            count(*) as quantidade,
            sum(cliente_notas_items.valor_liquido) as valor_liquido")
            ->join('cliente_notas', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->whereNotNull('cod_saida')
            ->groupBy('cod_saida', 'nome_saida')
            ->orderBy('valor_liquido', 'desc')
            ->get();

        // Vendas por área
        $vendas_por_area = ClienteNotas::selectRaw("
            cod_area,
            nome_area,
            count(*) as notas,
            sum(valor_liquido) as valor_liquido,
            count(distinct cliente_notas.cod_vendedor) as vendedores")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->whereNotNull('cod_area')
            ->groupBy('cod_area', 'nome_area')
            ->orderBy('valor_liquido', 'desc')
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
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->orderBy('data_mvto', 'desc')
            ->take(15)
            ->get();

        return view('pages.dashboards.comercial-vendedores', compact(
            'dashboard_geral',
            'produtos_vendidos',
            'vendedores_performance',
            'horas_completas',
            'top_clientes',
            'produtos_mais_vendidos',
            'tipos_saida',
            'vendas_por_area',
            'ultimas_vendas'
        ));
    }

    public function refresh()
    {
        $data_hoje = Carbon::today()->format('Y-m-d');

        $dashboard_geral = ClienteNotas::selectRaw("
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            sum(valor_liquido) as valor_liquido")
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->first();

        return response()->json([
            'timestamp' => now()->format('d/m/Y H:i:s'),
            'metricas' => [
                'total_notas' => number_format($dashboard_geral->notas ?? 0),
                'total_clientes' => number_format($dashboard_geral->clientes ?? 0),
                'valor_total' => 'R$ ' . number_format($dashboard_geral->valor_liquido ?? 0, 2, ',', '.'),
            ]
        ]);
    }
}
