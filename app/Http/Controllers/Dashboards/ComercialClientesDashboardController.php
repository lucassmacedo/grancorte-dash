<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\ClienteNotas;
use App\Models\ClienteNotasItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComercialClientesDashboardController extends Controller
{
    public function index(Request $request)
    {
        $data_hoje = Carbon::today()->subDays(3)->format('Y-m-d');

        $dashboard_geral = ClienteNotas::selectRaw("
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido
        ")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->first();

        $clientes_performance = ClienteNotas::selectRaw("
            cliente_notas.cod_cli_for,
            clientes.nome as cliente,
            count(*) as notas,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido
        ")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->groupBy('cliente_notas.cod_cli_for', 'clientes.nome')
            ->whereDate('data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->orderBy('valor_liquido', 'desc')
            ->take(5)
            ->get();

        // Top 5 Ramo de Atividade
        $top_ramo_atividade = ClienteNotas::selectRaw('clientes.ramo_atividade, sum(valor_liquido) as valor_liquido,count(*) as notas')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupBy('clientes.ramo_atividade')
            ->orderByDesc('valor_liquido')
            ->take(5)
            ->get();

        // Top 5 Areas
        $top_areas = ClienteNotas::selectRaw('clientes.nome_area, sum(valor_liquido) as valor_liquido,count(*) as notas')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupBy('clientes.nome_area')
            ->orderByDesc('valor_liquido')
            ->take(5)
            ->get();

        // Top 5 Cidades
        $top_cidades = ClienteNotas::selectRaw("unaccent(upper(cidade || ' - ' || uf)) as cidade, sum(valor_liquido) as valor_liquido, count(*) as notas")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereDate('cliente_notas.data_mvto', $data_hoje)
            ->where('cancelada', false)
            ->groupByRaw("unaccent(upper(cidade || ' - ' || uf))")
            ->orderByDesc('valor_liquido')
            ->take(5)
            ->get();

        return view('pages.dashboards.comercial-clientes', compact(
            'dashboard_geral',
            'clientes_performance',
            'top_ramo_atividade',
            'top_areas',
            'top_cidades'
        ));
    }
}
