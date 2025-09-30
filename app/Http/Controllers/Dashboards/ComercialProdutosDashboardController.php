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
        $data_hoje = Carbon::today()->subDays(3)->format('Y-m-d');

        $dashboard_geral = ClienteNotasItem::selectRaw("
            count(distinct id_nota) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_total) / count(distinct id_nota),2) as valor_medio,
            sum(valor_total) as valor_liquido
        ")
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
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
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->orderBy('valor_total', 'desc')
            ->take(10)
            ->get();

        $produtos_mais_vendidos       = $produtos_performance->sortByDesc('quantidade_total');
        $produtos_mais_vendidos_valor = $produtos_performance->sortByDesc('valor_total');

        $vendas_ultimos_7_dias = ClienteNotasItem::selectRaw('
            DATE(cliente_notas.data_mvto) as dia,
            sum(valor_total) as valor_liquido
        ')
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->whereBetween('cliente_notas.data_mvto', [Carbon::today()->subDays(6)->format('Y-m-d'), Carbon::today()->format('Y-m-d')])
            ->where('cancelada', false)
            ->groupBy(DB::raw('DATE(cliente_notas.data_mvto)'))
            ->orderBy('dia')
            ->get()
            ->keyBy('dia');

        return view('pages.dashboards.comercial-produtos', compact(
            'dashboard_geral',
            'produtos_performance',
            'produtos_mais_vendidos',
            'produtos_mais_vendidos_valor',
            'vendas_ultimos_7_dias'
        ));
    }
}