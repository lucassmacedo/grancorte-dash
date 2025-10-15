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
        // Intervalo desejado: de ontem às 20:00 até hoje às 23:59:59
        $inicio = Carbon::yesterday()->setTime(20, 0, 0);


        $dashboard_geral = ClienteNotas::selectRaw("
            count(*) as notas,
            count(distinct cod_cli_for) as clientes,
            round(sum(valor_liquido) / count(*),2) as valor_medio,
            sum(valor_liquido) as valor_liquido
        ")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->whereNotIn('cliente_notas.cod_filial',[30201])
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
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->whereNotIn('cliente_notas.cod_filial',[30201])
            ->orderBy('valor_liquido', 'desc')
            ->take(5)
            ->get();

        // Top 5 Ramo de Atividade
        $top_ramo_atividade = ClienteNotas::selectRaw('clientes.ramo_atividade, sum(valor_liquido) as valor_liquido,count(*) as notas')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->whereNotIn('cliente_notas.cod_filial',[30201])
            ->groupBy('clientes.ramo_atividade')
            ->orderByDesc('valor_liquido')
            ->take(5)
            ->get();

        // Top 5 Areas
        $top_areas = ClienteNotas::selectRaw('clientes.nome_area, sum(valor_liquido) as valor_liquido,count(*) as notas')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->whereNotIn('cliente_notas.cod_filial',[30201])
            ->groupBy('clientes.nome_area')
            ->orderByDesc('valor_liquido')
            ->take(5)
            ->get();

        // Top 5 Cidades
        $top_cidades = ClienteNotas::selectRaw("unaccent(upper(cidade || ' - ' || uf)) as cidade, sum(valor_liquido) as valor_liquido, count(*) as notas")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereRaw("CONCAT(cliente_notas.data_mvto, ' ', cliente_notas.hora) >= ?", [$inicio->format('Y-m-d H:i:s')])
            ->where('cancelada', false)
            ->whereNotIn('cliente_notas.cod_filial',[30201])
            ->groupByRaw("unaccent(upper(cidade || ' - ' || uf))")
            ->orderByDesc('valor_liquido')
            ->take(20)
            ->get();

        $tabela = $request->get('tabela', 'clientes'); // valor padrão: 'clientes'
        return view('pages.dashboards.comercial-clientes', compact(
            'dashboard_geral',
            'clientes_performance',
            'top_ramo_atividade',
            'top_areas',
            'top_cidades',
            'tabela'
        ));
    }
}
