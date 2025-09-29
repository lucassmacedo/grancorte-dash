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
        // Buscar dados principais do estoque
        $estoque = DB::connection('sqlsrv')->table('vw_pdv_estoque')->get()
            ->map(function ($item) {
                $item->GRUPO_ESTOQUE = str_replace(['MERCADO', '  '], ['MER.', ''], $item->GRUPO_ESTOQUE);

                return $item;
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
            ->take(15);// Ordenar por saldo total (maior para menor)
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
        $top_produtos = $estoque->sortByDesc('SALDO_TOTAL')->take(10)->map(function ($item) {
            return [
                'codigo'           => $item->COD_PRODUTO,
                'descricao'        => substr($item->DESC_PRODUTO ?? '', 0, 40) . '...',
                'grupo'            => $item->GRUPO_ESTOQUE,
                'saldo_total'      => $item->SALDO_TOTAL,
                'saldo_tunel'      => $item->SALDO_TUNEL,
                'saldo_venda'      => $item->SALDO_P_VENDA ?? 0,
                'tipo_conservacao' => $item->TIPO_CONSERVACAO,
            ];
        });

        // Produtos com estoque baixo (menos de 10kg)
        $estoque_baixo = $estoque->where('SALDO_TOTAL', '>', 0)
            ->where('SALDO_TOTAL', '<', 10)
            ->sortBy('SALDO_TOTAL')
            ->take(10)
            ->map(function ($item) {
                return [
                    'codigo'      => $item->COD_PRODUTO,
                    'descricao'   => substr($item->DESC_PRODUTO ?? '', 0, 35) . '...',
                    'saldo_total' => $item->SALDO_TOTAL,
                    'grupo'       => $item->GRUPO_ESTOQUE,
                ];
            });

        // Distribuição túnel vs disponível
        $distribuicao = [
            'em_tunel'   => $metricas['total_saldo_tunel'],
            'disponivel' => $metricas['total_saldo_p_venda'],
            'auxiliar'   => $metricas['total_saldo_aux'],
        ];

        return view('pages.dashboards.estoque', compact(
            'metricas',
            'grupos',
            'conservacao',
            'locais',
            'top_produtos',
            'estoque_baixo',
            'distribuicao'
        ));
    }

    public function refresh()
    {
        // Endpoint para atualização automática dos dados via AJAX
        $estoque = DB::connection('sqlsrv')->table('vw_pdv_estoque')->get();

        return response()->json([
            'timestamp' => now()->format('d/m/Y H:i:s'),
            'metricas'  => [
                'total_produtos'      => number_format($estoque->count()),
                'total_saldo_total'   => number_format($estoque->sum('SALDO_TOTAL'), 0, ',', '.'),
                'total_saldo_tunel'   => number_format($estoque->sum('SALDO_TUNEL'), 0, ',', '.'),
                'total_saldo_p_venda' => number_format($estoque->sum(function ($item) {
                    return $item->SALDO_P_VENDA ?? 0;
                }), 0, ',', '.'),
                'valor_total_estoque' => 'R$ ' . number_format($estoque->sum(function ($item) {
                        $saldo = $item->SALDO_TOTAL ?? 0;
                        $preco = $item->PRECO_VENDA ?? 0;

                        return $saldo * $preco;
                    }), 0, ',', '.'),
            ]
        ]);
    }
}
