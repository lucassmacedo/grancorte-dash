<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\ClienteNotas;
use App\Models\ClienteNotasItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComercialProdutosDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Intervalo desejado: de ontem às 20:00 até hoje às 23:59:59
        $inicio = Carbon::yesterday()->setTime(20, 0, 0);
        $fim    = Carbon::today()->setTime(23, 59, 59);

        $dashboard_geral = ClienteNotasItem::selectRaw("
            count(distinct id_nota) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_total) / count(distinct id_nota),2) as valor_medio,
            sum(valor_total) as valor_liquido
        ")
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->first();

        $produtos_performance = ClienteNotasItem::selectRaw("
            cod_produto,
            descricao,
            count(distinct id_nota) as notas,
            sum(qtd_auxiliar) as quantidade_total,
            sum(valor_total) as valor_total
        ")
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->groupBy('cod_produto', 'descricao')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->orderBy('valor_total', 'desc')
            ->take(10)
            ->get();

        $produtos_mais_vendidos       = $produtos_performance->sortByDesc('quantidade_total');
        $produtos_mais_vendidos_valor = $produtos_performance->sortByDesc('valor_total');

        $inicio_7dias          = Carbon::today()->subDays(6)->setTime(0, 0, 0);
        $fim_7dias             = Carbon::today()->setTime(23, 59, 59);
        $vendas_ultimos_7_dias = ClienteNotasItem::selectRaw('
            DATE(cliente_notas.data_mvto) as dia,
            sum(valor_total) as valor_liquido
        ')
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->whereRaw("(data_mvto || ' ' || hora) BETWEEN ? AND ?", [$inicio_7dias->format('Y-m-d H:i:s'), $fim_7dias->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->groupBy(DB::raw('DATE(cliente_notas.data_mvto)'))
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        // Produtos distintos vendidos
        $produtos_vendidos = ClienteNotas::selectRaw("count(distinct cod_produto) as produtos")
            ->join('cliente_notas_items', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->first();


        return view('pages.dashboards.comercial-produtos', compact(
            'dashboard_geral',
            'produtos_performance',
            'produtos_mais_vendidos',
            'produtos_mais_vendidos_valor',
            'vendas_ultimos_7_dias',
            'produtos_vendidos',
        ));
    }
}