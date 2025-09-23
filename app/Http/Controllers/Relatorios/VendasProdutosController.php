<?php

namespace App\Http\Controllers\Relatorios;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ClienteLinhas;
use App\Models\ClienteNotas;
use App\Models\ClienteNotasItem;
use App\Models\System\ClienteLinha;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class VendasProdutosController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'cod_filial'     => 'nullable|integer',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
            'cod_local'      => 'nullable|integer',
            'periodo'        => 'nullable|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4} - [0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
        ]);



        // periodo ex: 31/10/2024 - 29/11/2024
        $data['periodo'] = $request->input('periodo', date('d/m/Y', strtotime(date('Y-m-01'))) . ' - ' . date('d/m/Y', strtotime(date('Y-m-t'))));
        $data_incio      = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[0])->format('Y-m-d');
        $data_fim        = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[1])->format('Y-m-d');

        $data['cod_filial']     = $request->input('cod_filial');
        $data['cod_vendedor']   = $request->input('cod_vendedor');
        $data['cod_supervisor'] = $request->input('cod_supervisor');
        $data['city_id']        = $request->input('city_id');
        $data['cod_cliente']    = $request->input('cod_cliente');
        $data['cod_local']      = $request->input('cod_local');
        $data['cod_grupo']      = $request->input('cod_grupo');
        $data['tipo_saida']     = $request->input('tipo_saida');
        $data['cod_area']     = $request->input('cod_area');
        $data['cod_ramo']       = $request->input('cod_ramo');


        $dashboard_geral = ClienteNotasItem::selectRaw("
        count(distinct id_nota) as notas,
        count(distinct cod_cli_for) as clientes,
        round(sum(valor_total) / count(distinct id_nota),2) as valor_medio,
        sum(valor_total) as valor_liquido,
        sum(valor_total) filter (where local = 1) as valor_liquido_01,
        sum(valor_total) filter (where local = 80) as valor_liquido_80,
        sum(qtd_auxiliar) as qtd_aux_total,
        round(sum(valor_total) / sum(qtd_pri) ,2) as qtd_pri_media,
        sum(qtd_pri) as qtd_pri_total
        ")
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->where('cancelada', false)
            ->whereBetween('cliente_notas.data_pedido', [$data_incio, $data_fim])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_local'], fn($query, $cod_local) => $query->where('local', $cod_local))
            ->when($data['cod_grupo'], fn($query, $cod_grupo) => $query->whereIn('cod_grupo', $cod_grupo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereIn('cod_saida', $tipo_saida))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('cliente_notas.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('cliente_notas.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('cliente_notas.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->first();


        $dashboard = ClienteNotasItem::sortable()->selectRaw("
        cliente_notas_items.cod_produto,
        cliente_notas_items.descricao,
        cliente_notas_items.cod_grupo,
        cliente_notas_items.nome_grupo,
        cliente_notas_items.cod_saida,
        cliente_notas_items.nome_saida,
        count(distinct id_nota) as notas,
        count(distinct cod_cli_for) as clientes,
        round(sum(valor_total) / count(distinct cod_cli_for),2) as valor_medio,
        sum(qtd_pri) as qtd_pri,
        round(sum(valor_total) / sum(qtd_pri) ,2) as qtd_pri_media,
        sum(qtd_auxiliar) as qtd_aux_total,
        sum(valor_total) as valor_liquido")
            ->join('cliente_notas', 'cliente_notas.id', 'cliente_notas_items.id_nota')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->groupBy('cod_produto', 'descricao', 'cliente_notas_items.cod_grupo', 'cliente_notas_items.nome_grupo', 'cod_saida', 'nome_saida')
            ->where('cancelada', false)
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_grupo'], fn($query, $cod_grupo) => $query->where('cod_grupo', $cod_grupo))
            ->when($data['cod_local'], fn($query, $cod_local) => $query->where('local', $cod_local))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->where('cod_saida', $tipo_saida))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('cliente_notas.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('cliente_notas.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('cliente_notas.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->orderBy('valor_liquido', 'desc')
            ->get();


        $grupos = $dashboard->pluck('nome_grupo', 'cod_grupo')->unique()->sort();
        $cities = \App\Models\City::where("code", $data['city_id'])->search()->pluck('text', 'id')->toArray();

        $tipo_saidas = ClienteNotasItem::selectRaw("distinct cod_saida ||'-'|| trim(nome_saida) as nome_saida, cod_saida")
            ->join('cliente_notas', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->whereNotNull("cod_saida")
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_grupo'], fn($query, $cod_grupo) => $query->where('cod_grupo', $cod_grupo))
            ->when($data['cod_local'], fn($query, $cod_local) => $query->where('local', $cod_local))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('cliente_notas.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('cliente_notas.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('cliente_notas.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->orderBy('cod_saida')
            ->get()
            ->pluck('nome_saida', 'cod_saida');

        $areas = ClienteNotas::selectRaw("distinct cod_area ||'-'|| trim(nome_area) as nome_area, cod_area")
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->orderBy('cod_area')
            ->get()
            ->pluck('nome_area', 'cod_area');

        $ramo_atividade = ClienteNotas::selectRaw("distinct cod_ramo ||'-'|| trim(ramo_atividade) as ramo_atividade, cod_ramo")
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->orderBy('cod_ramo')
            ->get()
            ->pluck('ramo_atividade', 'cod_ramo');


        $grupos = $dashboard->pluck('nome_grupo', 'cod_grupo')->unique()->sort();


        return view('pages.relatorios.produtos', compact('dashboard', 'dashboard_geral', 'data', 'cities', 'tipo_saidas','areas','ramo_atividade', 'grupos'));
    }
}
