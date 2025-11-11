<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstoqueDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Buscar dados principais do estoque
            $estoque = \Cache::remember('estoque_dashboard_data', now()->addMinutes(5), function () {
                return DB::connection('sqlsrv')->table('vw_pdv_estoque')->get()
                    ->map(function ($item) {
                        $item->GRUPO_ESTOQUE = str_replace(['MERCADO', '  '], ['MER.', ''], $item->GRUPO_ESTOQUE);

                        return $item;
                    });
            });

            // Métricas principais com verificações de segurança
            $metricas = [
                'total_produtos'      => $estoque->count(),
                'total_saldo_total'   => $estoque->sum('SALDO_TOTAL'),
                'total_saldo_tunel'   => $estoque->sum('SALDO_TUNEL'),
                'total_saldo_p_venda' => $estoque->sum(function ($item) {
                    return $item->SALDO_DISPONIVEL_VENDA_KG ?? 0;
                }),
                'total_saldo_aux'     => $estoque->sum(function ($item) {
                    return $item->SALDO_DISPONIVEL_VENDA_AUX ?? 0;
                }),
            ];


            // Dados por grupo de estoque - TODOS os grupos, sem limitação
            $grupos = $estoque->groupBy('GRUPO_ESTOQUE')->map(function ($items, $grupo) {
                return [
                    'grupo'               => $grupo,
                    'quantidade_produtos' => $items->count(),
                    'saldo_total'         => $items->sum('SALDO_TOTAL'),
                    'saldo_tunel'         => $items->sum('SALDO_TUNEL'),
                    'saldo_venda'         => $items->sum('SALDO_DISPONIVEL_VENDA_KG')
                ];
            })->sortByDesc('saldo_total')
                ->take(5);// Ordenar por saldo total (maior para menor)
            // Dados por tipo de conservação
            $conservacao = $estoque->groupBy('TIPO_CONSERVACAO')->map(function ($items, $tipo) {
                return [
                    'tipo'                => $tipo,
                    'quantidade_produtos' => $items->count(),
                    'saldo_total'         => $items->sum('SALDO_TOTAL'),
                    'percentual'          => 0, // Será calculado no frontend
                ];
            })->sortByDesc('saldo_total');

            // Dados por local de estoque
            $locais = $estoque->groupBy('LOCAL_ESTOQUE')->map(function ($items, $local) {
                return [
                    'local'               => $local,
                    'quantidade_produtos' => $items->count(),
                    'saldo_total'         => $items->sum('SALDO_TOTAL'),
                    'saldo_tunel'         => $items->sum('SALDO_TUNEL'),
                    'saldo_venda'         => $items->sum('SALDO_DISPONIVEL_VENDA_KG')
                ];
            })->sortByDesc('saldo_total');

            // Top produtos com maior estoque
            $top_produtos = $estoque->sortByDesc('SALDO_TOTAL')->take(5)->map(function ($item) {
                return [
                    'codigo'           => $item->COD_PRODUTO,
                    'descricao'        => $item->DESC_PRODUTO,
                    'grupo'            => $item->GRUPO_ESTOQUE,
                    'saldo_total'      => $item->SALDO_TOTAL,
                    'saldo_tunel'      => $item->SALDO_TUNEL,
                    'saldo_venda'      => $item->SALDO_P_VENDA ?? 0,
                    'tipo_conservacao' => $item->TIPO_CONSERVACAO
                ];
            });


            // Distribuição túnel vs disponível
            $distribuicao = [
                'em_tunel'   => $metricas['total_saldo_tunel'],
                'disponivel' => $metricas['total_saldo_p_venda'],
            ];

            $tabela = $request->get('tabela', 'graficos'); // valor padrão: 'produtos'

            return view('pages.dashboards.estoque', compact(
                'metricas',
                'grupos',
                'conservacao',
                'locais',
                'top_produtos',
                'distribuicao',
                'tabela'
            ));
        } catch (\Throwable $e) {
            \Log::error('Erro no Dashboard de Estoque', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
            ]);
            return response()->view('proxy-error', [
                'message' => 'Dashboard de Estoque temporariamente indisponível',
                'code' => 502
            ], 502);
        }
    }
}
