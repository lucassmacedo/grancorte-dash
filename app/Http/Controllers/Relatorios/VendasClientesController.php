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

class VendasClientesController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'cod_filial'     => 'nullable|integer',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
            'periodo'        => 'nullable|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4} - [0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
        ]);


        // periodo ex: 31/10/2024 - 29/11/2024
        $data['periodo'] = $request->input('periodo', date('d/m/Y', strtotime(date('Y-m-01'))) . ' - ' . date('d/m/Y', strtotime(date('Y-m-t'))));
        $data_incio      = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[0])->format('Y-m-d');
        $data_fim        = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[1])->format('Y-m-d');

        $data['cod_filial']        = $request->input('cod_filial');
        $data['cod_vendedor']      = $request->input('cod_vendedor');
        $data['cod_supervisor']    = $request->input('cod_supervisor');
        $data['city_id']           = $request->input('city_id');
        $data['cod_cliente']       = $request->input('cod_cliente');
        $data['cod_cliente_grupo'] = $request->input('cod_cliente_grupo');
        $data['tipo_saida']        = $request->input('tipo_saida');
        $data['cod_area']          = $request->input('cod_area');
        $data['cod_ramo']          = $request->input('cod_ramo');


        $dashboard_geral = ClienteNotas::selectRaw("
        count(*) as notas,
        count(distinct cliente_notas.cod_cli_for) as clientes,
        round(sum(valor_liquido) / count(*),2) as valor_medio,
        sum(qtd_pri)                            as qtd_pri,
        json_agg(distinct cliente_notas.cod_vendedor) as vendedores,
        sum(valor_liquido) as valor_liquido")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->leftJoin(DB::raw("(select id_nota,
                           sum(qtd_pri) as qtd_pri
                    from cliente_notas_items
                    group by id_nota) as nota_itens"), 'nota_itens.id_nota', 'cliente_notas.id')
            ->whereBetween('cliente_notas.data_pedido', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_cliente_grupo'], fn($query, $cod_cliente) => $query->whereRaw("cliente_notas.cod_cli_for::text like '$cod_cliente%'"))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('clientes.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('clientes.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('clientes.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->first();


        $clientesProdutos = ClienteNotas::selectRaw("cliente_notas.cod_cli_for, count(distinct cod_produto) as produtos")
            ->join('cliente_notas_items', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_cliente_grupo'], fn($query, $cod_cliente) => $query->whereRaw("cliente_notas.cod_cli_for::text like '$cod_cliente%'"))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('clientes.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('clientes.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('clientes.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->groupBy('cliente_notas.cod_cli_for')
            ->get()
            ->pluck('produtos', 'cod_cli_for');


        $dashboard = ClienteNotas::sortable()->selectRaw("
        clientes.id,
        cliente_notas.cod_cli_for,
        count(*) as notas,
        round(sum(valor_liquido) / count(*),2) as valor_medio,
        sum(valor_liquido) as valor_liquido,
        sum(qtd_pri) as qtd_pri,
        sum(qtd_aux) as qtd_aux,
        apelido as cliente")
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->leftJoin(DB::raw("(select id_nota,
                           sum(qtd_pri) as qtd_pri,
                           sum(qtd_auxiliar) as qtd_aux
                    from cliente_notas_items
                    group by id_nota) as nota_itens"), 'nota_itens.id_nota', 'cliente_notas.id')
            ->groupBy('cliente_notas.cod_cli_for', 'clientes.id', 'apelido')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->orderBy('valor_liquido', 'desc')
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_cliente_grupo'], fn($query, $cod_cliente) => $query->whereRaw("cliente_notas.cod_cli_for::text like '$cod_cliente%'"))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when($data['tipo_saida'], fn($query, $tipo_saida) => $query->whereHas('itens', function ($query) use ($tipo_saida) {
                $query->whereIn('cod_saida', $tipo_saida);
            }))->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('clientes.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('clientes.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('clientes.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
            })
            ->get()
            ->map(function ($item) use ($clientesProdutos) {
                $item->produtos = isset($clientesProdutos[$item->cod_cli_for]) ? $clientesProdutos[$item->cod_cli_for] : 0;

                return $item;
            });

        $cities = \App\Models\City::where("code", $data['city_id'])->search()->pluck('text', 'id')->toArray();

        $clientes = $dashboard->map(function ($item) {
            return [
                'cod_cli' => $item['cod_cli_for'],
                'apelido' => $item['cod_cli_for'] . " - " . $item['cliente']
            ];
        })->pluck('apelido', 'cod_cli')->unique();

        $tipo_saidas = ClienteNotasItem::selectRaw("distinct cod_saida ||'-'|| trim(nome_saida) as nome_saida, cod_saida")
            ->join('cliente_notas', 'cliente_notas_items.id_nota', 'cliente_notas.id')
            ->join('clientes', 'clientes.codigo', 'cliente_notas.cod_cli_for')
            ->whereBetween('data_mvto', [$data_incio, $data_fim])
            ->where('cancelada', false)
            ->whereNotNull("cod_saida")
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_notas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cliente_notas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cliente_notas.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_notas.cod_cli_for', $cod_cliente))
            ->when($data['cod_cliente_grupo'], fn($query, $cod_cliente) => $query->whereRaw("cliente_notas.cod_cli_for::text like '$cod_cliente%'"))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('cod_ramo', $cod_ramo))
            ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                return $query->where('clientes.cod_vendedor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('supervisor'), function ($query) {
                return $query->where('clientes.cod_supervisor', auth()->user()->codigo);
            })->when(auth()->user()->hasRole('Supervisor Vendedores') && auth()->user()->cod_supervisor_vendedores, function ($query) {
                return $query->whereIn('clientes.cod_supervisor', auth()->user()->cod_supervisor_vendedores);
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


        return view('pages.dashboards.comercial-clientes', [
            'dashboard_geral' => $dashboard_geral,
            'periodo' => $data['periodo'],
            // Adicione outros dados necessários
        ]);
    }
}
