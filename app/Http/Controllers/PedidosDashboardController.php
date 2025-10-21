<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidosDashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoje            = now()->addDay()->toDateString();
        $statusCancelado = \App\Models\Pedido::STATUS_CANCELADO;

        // Consulta única para totais e métricas simples
        $totais = \App\Models\Pedido::whereDate('data_entrega', $hoje)
            ->selectRaw('
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN status <> 4 THEN peso_total ELSE 0 END) as total_kg_pedidos,
                SUM(CASE WHEN status IN (0,1,2) THEN valor_total ELSE 0 END) as previsao_faturamento,
                SUM(CASE WHEN status IN (0,1) THEN 1 ELSE 0 END) as abertos,
                SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as cancelados,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as baixados,
                SUM(CASE WHEN status = 1 and faturado = false THEN 1 ELSE 0 END) as bloqueados,
                SUM(CASE WHEN status = 2 and faturado = true THEN 1 ELSE 0 END) as faturados
                ')
            ->first();

        // Evolução diária de vendas (apenas hoje, não cancelados)
        $evolucao_vendas = \App\Models\Pedido::selectRaw('DATE(created_at) as data, SUM(valor_total) as total')
            ->whereDate('data_entrega', '>=', now()->subDays(7))
            ->where('status', '!=', $statusCancelado)
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        // Ranking Top 10 Vendedores em Valor (não cancelados)
        $ranking_vendedores_valor = \App\Models\Pedido::whereDate('data_entrega', $hoje)
            ->leftJoin('users', 'users.codigo', 'pedidos.codigo_vendedor')
            ->where('pedidos.status', '!=', $statusCancelado)
            ->select('codigo_vendedor', DB::raw('SUM(valor_total) as total'), DB::raw('SUM(total_itens) as qtd'), 'users.apelido as vendedor')
            ->groupBy('codigo_vendedor', 'users.apelido')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Ranking Top 10 Vendedores em Venda de carcaça UN e total de carcaças vendidas (não cancelados)
        $ranking_vendedores_carcaca = \App\Models\PedidoItem::join('produtos', 'pedido_items.codigo_produto', 'produtos.codigo')
            ->where('produtos.cod_grupo', 5001)
            ->join('pedidos', function ($join) use ($hoje, $statusCancelado) {
                $join->on('pedido_items.pedido_id', '=', 'pedidos.id')
                    ->whereDate('pedidos.data_entrega', $hoje)
                    ->where('pedidos.status', '!=', $statusCancelado);
            })
            ->leftJoin('users', 'users.codigo', 'pedidos.codigo_vendedor')
            ->select('pedidos.codigo_vendedor', DB::raw('SUM(pedidos.valor_total) as total'), DB::raw('SUM(pedido_items.quantidade) as qtd'), 'users.apelido as vendedor')
            ->groupBy('pedidos.codigo_vendedor', 'users.apelido')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $total_carcacas_vendidas = \App\Models\PedidoItem::join('produtos', 'pedido_items.codigo_produto', 'produtos.codigo')
            ->where('produtos.cod_grupo', 5001)
            ->join('pedidos', function ($join) use ($hoje, $statusCancelado) {
                $join->on('pedido_items.pedido_id', '=', 'pedidos.id')
                    ->whereDate('pedidos.data_entrega', $hoje)
                    ->where('pedidos.status', '!=', $statusCancelado);
            })
            ->sum('pedido_items.quantidade');

        // Top 10 Pedidos por Rota (não cancelados)
        $top_pedidos_por_rota = \App\Models\Pedido::whereDate('data_entrega', $hoje)
            ->where('pedidos.status', '!=', $statusCancelado)
            ->join('clientes', 'clientes.codigo', '=', 'pedidos.codigo_cliente')
            ->join('logistica_rotas', 'logistica_rotas.id', '=', 'clientes.rota_id')
            ->select(
                'clientes.rota_id',
                'logistica_rotas.codigo',
                'logistica_rotas.nome as rota_nome',
                DB::raw('SUM(pedidos.valor_total) as total'),
                DB::raw('COUNT(pedidos.id) as qtd_pedidos')
            )
            ->groupBy('clientes.rota_id', 'logistica_rotas.codigo', 'logistica_rotas.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
        
        // add total KG por rota
        $top_rotas_por_kg = \App\Models\Pedido::whereDate('data_entrega', $hoje)
            ->where('pedidos.status', '!=', $statusCancelado)
            ->join('clientes', 'clientes.codigo', '=', 'pedidos.codigo_cliente')
            ->join('logistica_rotas', 'logistica_rotas.id', '=', 'clientes.rota_id')
            ->select(
                'clientes.rota_id',
                'logistica_rotas.nome as rota_nome',
                DB::raw('SUM(pedidos.peso_total) as total_kg'),
                DB::raw('COUNT(pedidos.id) as qtd_pedidos')
            )
            ->groupBy('clientes.rota_id', 'logistica_rotas.nome')
            ->orderByDesc('total_kg')
            ->limit(10)
            ->get();

        $dashboard = [
            'total_pedidos'              => $totais->total_pedidos ?? 0,
            'total_kg_pedidos'           => $totais->total_kg_pedidos ?? 0,
            'previsao_faturamento'       => $totais->previsao_faturamento ?? 0,
            'pedidos_em_aberto'          => $totais->abertos ?? 0,
            'pedidos_faturados'          => $totais->faturados ?? 0,
            'pedidos_cancelados'         => $totais->cancelados ?? 0,
            'pedidos_bloqueados'         => $totais->bloqueados ?? 0,
            'pedidos_baixados'           => $totais->baixados,
            'evolucao_vendas'            => $evolucao_vendas,
            'ranking_vendedores_valor'   => $ranking_vendedores_valor,
            'ranking_vendedores_carcaca' => $ranking_vendedores_carcaca,
            'total_carcacas_vendidas'    => $total_carcacas_vendidas,
            'top_pedidos_por_rota'       => $top_pedidos_por_rota,
            'top_rotas_por_kg'           => $top_rotas_por_kg,
        ];
        $tabela    = $request->get('tabela', 'valor');

        return view('pages.dashboards.pedidos', compact('dashboard', 'tabela'));
    }
}
