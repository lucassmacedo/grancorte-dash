<?php

namespace App\Http\Controllers\Relatorios;

use App\Exports\EstoqueSaldoTunelExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EstoqueController extends Controller
{

    public function index(Request $request)
    {
        // Filtros de busca
        $filtros = [
            'grupo_estoque' => $request->input('grupo_estoque'),
            'cod_produto' => $request->input('cod_produto'),
            'desc_produto' => $request->input('desc_produto'),
            'tipo_conservacao' => $request->input('tipo_conservacao'),
            'local_estoque' => $request->input('local_estoque'),
            'ean' => $request->input('ean'),
        ];

        // Query base
        $query = DB::connection('sqlsrv')->table('vw_pdv_estoque')
            ->when($filtros['grupo_estoque'], function ($query) use ($filtros) {
                return $query->where('GRUPO_ESTOQUE', 'like', '%' . $filtros['grupo_estoque'] . '%');
            })
            ->when($filtros['cod_produto'], function ($query) use ($filtros) {
                return $query->where('COD_PRODUTO', 'like', '%' . $filtros['cod_produto'] . '%');
            })
            ->when($filtros['desc_produto'], function ($query) use ($filtros) {
                return $query->where('DESC_PRODUTO', 'like', '%' . $filtros['desc_produto'] . '%');
            })
            ->when($filtros['tipo_conservacao'], function ($query) use ($filtros) {
                return $query->where('TIPO_CONSERVACAO', $filtros['tipo_conservacao']);
            })
            ->when($filtros['local_estoque'], function ($query) use ($filtros) {
                return $query->where('LOCAL_ESTOQUE', 'like', '%' . $filtros['local_estoque'] . '%');
            })
            ->when($filtros['ean'], function ($query) use ($filtros) {
                return $query->where('EAN', 'like', '%' . $filtros['ean'] . '%');
            });

        // Buscar dados
        $data = $query
            ->orderBy('GRUPO_ESTOQUE', 'asc')
            ->get();

        // Calcular totais
        $totais = [
            'total_saldo_total' => $data->sum('SALDO_TOTAL'),
            'total_saldo_tunel' => $data->sum('SALDO_TUNEL'),
            'total_saldo_p_venda' => $data->sum('SALDO_P_VENDA'),
            'total_saldo_aux' => $data->sum('SALDO_AUX'),
            'total_saldo_aux_p_venda' => $data->sum('SALDO_AUX_P_VENDA'),
            'total_saldo_tunel_aux' => $data->sum('SALDO_TUNEL_AUX'),
            'total_registros' => $data->count(),
        ];

        // Buscar opções para filtros
        $grupos_estoque = $data->pluck('GRUPO_ESTOQUE', 'GRUPO_ESTOQUE')->unique()->sort();
        $tipos_conservacao = $data->pluck('TIPO_CONSERVACAO', 'TIPO_CONSERVACAO')->unique()->sort();
        $locais_estoque = $data->pluck('LOCAL_ESTOQUE', 'LOCAL_ESTOQUE')->unique()->sort();

        // Verificar se é exportação para Excel
        if ($request->get('exportar') === 'excel') {
            return Excel::download(new EstoqueSaldoTunelExport($data), 'relatorio-estoque-saldo-tunel-' . date('Y-m-d-H-i-s') . '.xlsx');
        }

        return view('pages.relatorios.estoque.saldo-tunel', compact(
            'data',
            'filtros',
            'totais',
            'grupos_estoque',
            'tipos_conservacao',
            'locais_estoque'
        ));
    }
}
