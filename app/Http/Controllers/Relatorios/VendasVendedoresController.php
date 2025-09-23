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

class VendasVendedoresController extends Controller
{

    public function index(Request $request)
    {
        abort_if(auth()->user()->hasRole('vendedor'), 403);

        $request->validate([
            'cod_filial'     => 'nullable|array',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
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
        $data['tipo_saida']     = $request->input('tipo_saida');
        $data['cod_area']       = $request->input('cod_area');
        $data['cod_ramo']       = $request->input('cod_ramo');

        $dashboard_geral = ClienteNotas::selectRaw("
        count(*) as notas,
        count(distinct cod_cli_for) as clientes,
        round(sum(valor_liquido) / count(*),2) as valor_medio,
        sum(valor_liquido) as valor_liquido")
            ->leftJoin('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('cliente_notas.data_pedido', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->whereIn('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->get();


        $clientesProdutos = ClienteNotas::selectRaw("cliente_notas.cod_vendedor, count(distinct cod_produto) as produtos")
            ->join('cliente_notas_items', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->whereIn('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->groupBy('cliente_notas.cod_vendedor')
            ->get();

        $tipo_saidas = ClienteNotasItem::selectRaw("distinct cod_saida ||'-'|| trim(nome_saida) as nome_saida, cod_saida")
            ->join('cliente_notas', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->whereNotNull("cod_saida")
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->whereIn('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->orderBy('cod_saida')
            ->get()
            ->pluck('nome_saida', 'cod_saida');

        $dashboard = ClienteNotas::sortable()->selectRaw("
        cliente_notas.cod_vendedor,
        count(*) as notas,
        count(distinct cod_cli_for) as clientes,
        round(sum(valor_liquido) / count(*),2) as valor_medio,
        sum(valor_liquido) as valor_liquido,
        sum(valor_liquido) filter (where cancelada is true) as valor_cancelada,
        users.apelido as vendedor")
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->groupBy('cliente_notas.cod_vendedor', 'users.apelido')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->whereIn('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->get()
            ->map(function ($item) use ($clientesProdutos) {
                $item->produtos = $clientesProdutos->where('cod_vendedor', $item->cod_vendedor)->first()->produtos;

                return $item;
            });

        $cities = \App\Models\City::where("code", $data['city_id'])->search()->pluck('text', 'id')->toArray();

        $areas = ClienteNotas::selectRaw("distinct cod_area ||'-'|| trim(nome_area) as nome_area, cod_area")
            ->join('users', 'users.codigo', 'cliente_notas.cod_vendedor')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->whereIn('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
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
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))
            ->orderBy('cod_ramo')
            ->get()
            ->pluck('ramo_atividade', 'cod_ramo');


        return view('pages.relatorios.vendedores', compact('dashboard', 'dashboard_geral', 'data', 'cities', 'tipo_saidas', 'areas', 'ramo_atividade'));
    }

    public function notas_search(Request $request)
    {
        $request->validate([
            'search' => 'required'
        ]);

        $notas = ClienteNotas::select('cliente_notas.*', 'clientes.id as codigo_cli')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->where(function ($query) {
                return $query
                    ->where('chave_acesso', request()->input('search'))
                    ->orWhere('num_docto', 'like', '%' . (int) request()->input('search') . '%');
            })
            ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('clientes.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('clientes.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('clientes.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->limit(10)
            ->get();


        return view('pages.atalhos.notas_result', compact('notas'));
    }
}
