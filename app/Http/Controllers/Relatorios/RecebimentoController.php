<?php

namespace App\Http\Controllers\Relatorios;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ClienteTitulos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class RecebimentoController extends Controller
{

    public function vendedores(Request $request)
    {
        abort_if(auth()->user()->hasRole('vendedor'), 403);

        $request->validate([
            'cod_filial'     => 'nullable|integer',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
            'status'         => 'nullable|string|in:ABERTO,LIQUIDADO,VENCIDO',
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
        $data['cod_area']       = $request->input('cod_area');
        $data['cod_ramo']       = $request->input('cod_ramo');
        $data['status']         = $request->input('status', 'LIQUIDADO'); // Default: LIQUIDADO

        // Dashboard geral - agora baseado em títulos com status selecionado
        $dashboard_geral = ClienteTitulos::selectRaw("
        count(*) as titulos,
        count(distinct cliente) as clientes,
        round(sum(valor) / count(*),2) as valor_medio,
        sum(valor) as valor_total")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->leftJoin('users', 'users.codigo', 'clientes.cod_vendedor')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status']) // Filtro para títulos com status selecionado
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->get();

        // Dashboard por vendedor - baseado em títulos com status selecionado
        $dashboard = ClienteTitulos::sortable()
            ->selectRaw("
        clientes.cod_vendedor,
        cliente_titulos.cod_filial,
        filiais.nome as nome_filial,
        count(*) as titulos,
        count(distinct cliente_titulos.cliente) as clientes,
        round(sum(cliente_titulos.valor) / count(*),2) as valor_medio,
        sum(cliente_titulos.valor) as valor_total,
        users.apelido as vendedor")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->join('users', 'users.codigo', 'clientes.cod_vendedor')
            ->leftJoin('filiais', 'filiais.codigo', DB::raw('(cliente_titulos.cod_filial)::integer'))
            ->groupBy('clientes.cod_vendedor', 'users.apelido', 'cliente_titulos.cod_filial', 'filiais.nome')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status']) // Filtro para títulos com status selecionado
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->get();

        // Agrupar dados por vendedor para melhor exibição
        $dashboard_grouped = $dashboard->groupBy('cod_vendedor')->map(function ($filiais, $cod_vendedor) {
            $vendedor_data = $filiais->first();
            return [
                'cod_vendedor' => $cod_vendedor,
                'vendedor' => $vendedor_data->vendedor,
                'filiais' => $filiais,
                'totals' => [
                    'titulos' => $filiais->sum('titulos'),
                    'clientes' => $filiais->sum('clientes'),
                    'valor_total' => $filiais->sum('valor_total'),
                    'valor_medio' => $filiais->avg('valor_medio')
                ]
            ];
        });

        $cities = \App\Models\City::where("code", $data['city_id'])->search()->pluck('text', 'id')->toArray();

        // Áreas baseadas nos clientes com títulos do status selecionado
        $areas = ClienteTitulos::selectRaw("distinct clientes.cod_area ||'-'|| trim(clientes.nome_area) as nome_area, clientes.cod_area")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->join('users', 'users.codigo', 'clientes.cod_vendedor')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status'])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->orderBy('clientes.cod_area')
            ->get()
            ->pluck('nome_area', 'cod_area');

        // Ramos de atividade baseados nos clientes com títulos do status selecionado
        $ramo_atividade = ClienteTitulos::selectRaw("distinct clientes.cod_ramo ||'-'|| trim(clientes.ramo_atividade) as ramo_atividade, clientes.cod_ramo")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->join('users', 'users.codigo', 'clientes.cod_vendedor')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status'])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->orderBy('clientes.cod_ramo')
            ->get()
            ->pluck('ramo_atividade', 'cod_ramo');

        $status_list = ClienteTitulos::$status;

        return view('pages.relatorios.recebimento-vendedores', compact('dashboard_grouped', 'dashboard_geral', 'data', 'cities', 'areas', 'ramo_atividade', 'status_list'));
    }

    public function exportExcel(Request $request)
    {
        abort_if(auth()->user()->hasRole('vendedor'), 403);

        $request->validate([
            'cod_filial'     => 'nullable|integer',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
            'status'         => 'nullable|string|in:ABERTO,LIQUIDADO,VENCIDO',
            'periodo'        => 'nullable|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4} - [0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
        ]);

        // Reutilizar a mesma lógica do método vendedores para obter os dados
        $data = $this->getDadosRelatorio($request);

        $filtros = [
            'periodo' => $data['filtros']['periodo'],
            'cod_filial' => $data['filtros']['cod_filial'],
            'cod_vendedor' => $data['filtros']['cod_vendedor'],
            'cod_supervisor' => $data['filtros']['cod_supervisor'],
            'city_id' => $data['filtros']['city_id'],
            'status' => $data['filtros']['status'],
        ];

        $filename = 'relatorio_recebimento_vendedores_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new \App\Exports\Relatorios\RecebimentoVendedoresExport($data['dashboard_grouped'], $filtros), $filename);
    }

    public function exportPdf(Request $request)
    {
        abort_if(auth()->user()->hasRole('vendedor'), 403);

        $request->validate([
            'cod_filial'     => 'nullable|integer',
            'cod_vendedor'   => 'nullable|integer',
            'cod_supervisor' => 'nullable|integer',
            'city_id'        => 'nullable|integer',
            'status'         => 'nullable|string|in:ABERTO,LIQUIDADO,VENCIDO',
            'periodo'        => 'nullable|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4} - [0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
        ]);

        // Reutilizar a mesma lógica do método vendedores para obter os dados
        $data = $this->getDadosRelatorio($request);

        $viewData = [
            'dashboard_grouped' => $data['dashboard_grouped'],
            'dashboard_geral' => $data['dashboard_geral'],
            'filtros' => $data['filtros'],
        ];

        $pdf = Pdf::loadView('pages.relatorios.pdf.recebimento-vendedores', $viewData);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'relatorio_recebimento_vendedores_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    private function getDadosRelatorio(Request $request)
    {
        // periodo ex: 31/10/2024 - 29/11/2024
        $data['periodo'] = $request->input('periodo', date('d/m/Y', strtotime(date('Y-m-01'))) . ' - ' . date('d/m/Y', strtotime(date('Y-m-t'))));
        $data_incio      = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[0])->format('Y-m-d');
        $data_fim        = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[1])->format('Y-m-d');

        $data['cod_filial']     = $request->input('cod_filial');
        $data['cod_vendedor']   = $request->input('cod_vendedor');
        $data['cod_supervisor'] = $request->input('cod_supervisor');
        $data['city_id']        = $request->input('city_id');
        $data['cod_cliente']    = $request->input('cod_cliente');
        $data['cod_area']       = $request->input('cod_area');
        $data['cod_ramo']       = $request->input('cod_ramo');
        $data['status']         = $request->input('status', 'LIQUIDADO'); // Default: LIQUIDADO

        // Dashboard geral - agora baseado em títulos com status selecionado
        $dashboard_geral = ClienteTitulos::selectRaw("
        count(*) as titulos,
        count(distinct cliente) as clientes,
        round(sum(valor) / count(*),2) as valor_medio,
        sum(valor) as valor_total")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->leftJoin('users', 'users.codigo', 'clientes.cod_vendedor')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status']) // Filtro para títulos com status selecionado
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->get();

        // Dashboard por vendedor - baseado em títulos com status selecionado
        $dashboard = ClienteTitulos::sortable()->selectRaw("
        clientes.cod_vendedor,
        cliente_titulos.cod_filial,
        filiais.nome as nome_filial,
        count(*) as titulos,
        count(distinct cliente_titulos.cliente) as clientes,
        round(sum(cliente_titulos.valor) / count(*),2) as valor_medio,
        sum(cliente_titulos.valor) as valor_total,
        users.apelido as vendedor")
            ->join('clientes', 'clientes.codigo', 'cliente_titulos.cliente')
            ->join('users', 'users.codigo', 'clientes.cod_vendedor')
            ->leftJoin('filiais', 'filiais.codigo', 'cliente_titulos.cod_filial')
            ->groupBy('clientes.cod_vendedor', 'users.apelido', 'cliente_titulos.cod_filial', 'filiais.nome')
            ->whereBetween('cliente_titulos.data_baixa', [$data_incio, $data_fim])
            ->where('cliente_titulos.status', $data['status']) // Filtro para títulos com status selecionado
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cliente_titulos.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('clientes.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('clientes.cod_supervisor', $cod_supervisor))
            ->when($data['city_id'], fn($query, $city_id) => $query->where('clientes.codigo_municipio', $city_id))
            ->when($data['cod_cliente'], fn($query, $cod_cliente) => $query->where('cliente_titulos.cliente', $cod_cliente))
            ->when($data['cod_area'], fn($query, $cod_area) => $query->whereIn('clientes.cod_area', $cod_area))
            ->when($data['cod_ramo'], fn($query, $cod_ramo) => $query->whereIn('clientes.cod_ramo', $cod_ramo))
            ->orderBy('clientes.cod_vendedor')
            ->orderBy('cliente_titulos.cod_filial')
            ->get();

        // Agrupar dados por vendedor para melhor exibição
        $dashboard_grouped = $dashboard->groupBy('cod_vendedor')->map(function ($filiais, $cod_vendedor) {
            $vendedor_data = $filiais->first();
            return [
                'cod_vendedor' => $cod_vendedor,
                'vendedor' => $vendedor_data->vendedor,
                'filiais' => $filiais,
                'totals' => [
                    'titulos' => $filiais->sum('titulos'),
                    'clientes' => $filiais->sum('clientes'),
                    'valor_total' => $filiais->sum('valor_total'),
                    'valor_medio' => $filiais->avg('valor_medio')
                ]
            ];
        });

        return [
            'dashboard' => $dashboard,
            'dashboard_grouped' => $dashboard_grouped,
            'dashboard_geral' => $dashboard_geral,
            'filtros' => $data,
        ];
    }
}
