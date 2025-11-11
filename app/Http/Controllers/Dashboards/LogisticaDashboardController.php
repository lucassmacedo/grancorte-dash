<?php

namespace App\Http\Controllers\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\LogisticaEntrega;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogisticaDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Filtros (opcionais) e data padrão do dia
            $data = [
                'data_entrega'   => $request->input('data_entrega', date('d/m/Y')),
                'cod_filial'     => $request->input('cod_filial'),
                'cod_vendedor'   => $request->input('cod_vendedor'),
                'cod_supervisor' => $request->input('cod_supervisor'),
                'placa'          => $request->input('placa'),
                'cod_cli'        => $request->input('cod_cli'),
            ];

            $dataCarga = Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d');

            // Query base com filtros e regras de acesso
            $base = LogisticaEntrega::query()
                ->where('data_carga', $dataCarga)
                ->when($data['cod_filial'], fn($q, $v) => $q->where('cod_filial', $v))
                ->when($data['cod_vendedor'], fn($q, $v) => $q->where('cod_vendedor', $v))
                ->when($data['cod_supervisor'], fn($q, $v) => $q->where('cod_supervisor', $v))
                ->when($data['placa'], fn($q, $v) => $q->where('placa', 'ilike', sprintf('%%%s%%', strtoupper($v))))
                ->when($data['cod_cli'], fn($q, $v) => $q->where('cod_cli', strtoupper($v)))
                ->when(Auth::user()?->hasRole('vendedor'), fn($q) => $q->where('cod_vendedor', Auth::user()->codigo))
                ->when(Auth::user()?->hasRole('supervisor'), fn($q) => $q->where('cod_supervisor', Auth::user()->codigo));

            // Métricas principais
            $totalNotas       = (clone $base)->count();
            $cargasDistintas  = (clone $base)->distinct('carga')->count('carga');
            $totalEntregues   = (clone $base)->where('acompanhamento', 3)->count();
            $totalAndamento   = (clone $base)->whereIn('acompanhamento', [0, 1, 2])->count();
            $totalProblemas   = (clone $base)->whereNotNull('problemas_entrega')->whereNull('problemas_entrega_resolucao')->count();
            $totalProblemasOk = (clone $base)->whereNotNull('problemas_entrega_resolucao')->count();
            $percentualOK     = $totalNotas > 0 ? round(($totalEntregues / $totalNotas) * 100, 1) : 0;

            // Distribuição por status (0..4)
            $statusCounts = (clone $base)
                ->select('acompanhamento', DB::raw('count(*) as total'))
                ->groupBy('acompanhamento')
                ->pluck('total', 'acompanhamento')
                ->filter(function ($q) {
                    return $q > 0;
                })
                ->toArray();

            $statusLabels = LogisticaEntrega::$status;
            $statusDist   = [];
            foreach ($statusLabels as $k => $label) {
                $statusDist[] = [
                    'status' => $label,
                    'code'   => (int) $k,
                    'total'  => (int) ($statusCounts[$k] ?? 0),
                    'color'  => LogisticaEntrega::$status_color_hex[$k] ?? '#666'
                ];
            }

            $statusDist = array_values(array_filter($statusDist, fn($s) => $s['total'] > 0));

            // Cargas por Filial (pizza)
            $filialCargas = (clone $base)
                ->select('cod_filial', DB::raw('count(distinct carga) as total'))
                ->groupBy('cod_filial')
                ->orderByDesc('total')
                ->get()
                ->map(function ($r) {
                    return [
                        'filial' => (string) $r->cod_filial,
                        'total'  => (int) $r->total
                    ];
                });

            // Problemas x Resolvidos (pizza)
            $problemasPie = [
                'pendentes'  => (int) $totalProblemas,
                'resolvidos' => (int) $totalProblemasOk,
            ];

            // Tempos médios (minutos)
            $avgAguardando = (clone $base)
                ->whereNotNull('data_acompanhamento_descarregando')
                ->whereNotNull('data_acompanhamento_entrega')
                ->selectRaw("avg(EXTRACT(EPOCH FROM (data_acompanhamento_descarregando - data_acompanhamento_entrega)) / 60.0) as avg_min")
                ->value('avg_min');


            $avgDescarrego = (clone $base)
                ->whereNotNull('canhoto_data_upload')
                ->whereNotNull('data_acompanhamento_descarregando')
                ->selectRaw("avg(EXTRACT(EPOCH FROM (canhoto_data_upload - data_acompanhamento_descarregando)) / 60.0) as avg_min")
                ->value('avg_min');

            // Para o tempo médio de trajeto, usamos subconsulta para aplicar a window function e então agregamos fora
            $trajetoInner = LogisticaEntrega::query()
                ->where('data_carga', $dataCarga)
                ->when($data['cod_filial'], fn($q, $v) => $q->where('cod_filial', $v))
                ->when($data['cod_vendedor'], fn($q, $v) => $q->where('cod_vendedor', $v))
                ->when($data['cod_supervisor'], fn($q, $v) => $q->where('cod_supervisor', $v))
                ->when($data['placa'], fn($q, $v) => $q->where('placa', 'ilike', sprintf('%%%s%%', strtoupper($v))))
                ->when($data['cod_cli'], fn($q, $v) => $q->where('cod_cli', strtoupper($v)))
                ->when(Auth::user()?->hasRole('vendedor'), fn($q) => $q->where('cod_vendedor', Auth::user()->codigo))
                ->when(Auth::user()?->hasRole('supervisor'), fn($q) => $q->where('cod_supervisor', Auth::user()->codigo))
                ->whereNotNull('data_acompanhamento_entrega')
                ->leftJoin('logistica_entrega_inicios', function ($join) {
                    $join->on('logistica_entregas.carga', '=', 'logistica_entrega_inicios.carga')
                        ->on('logistica_entregas.placa', '=', 'logistica_entrega_inicios.placa');
                })
                ->selectRaw("CASE WHEN ordem = 1 THEN EXTRACT(EPOCH FROM (data_acompanhamento_entrega - logistica_entrega_inicios.created_at)) / 60.0 ELSE EXTRACT(EPOCH FROM (data_acompanhamento_entrega - LAG(canhoto_data_upload) OVER (PARTITION BY logistica_entregas.carga, logistica_entregas.placa ORDER BY ordem))) / 60.0 END AS minutos");

            $avgTrajeto = DB::query()
                ->fromSub($trajetoInner, 't')
                ->selectRaw('avg(minutos) as avg_min')
                ->value('avg_min');

            $formatMinutes = function ($m) {
                $m = $m ?? 0;
                $m = (int) round($m);

                return CarbonInterval::minutes($m)->cascade()->format('%H:%I');
            };

            $metricas = [
                'total_notas'                => $totalNotas,
                'cargas'                     => $cargasDistintas,
                'entregues'                  => $totalEntregues,
                'andamento'                  => $totalAndamento,
                'problemas'                  => $totalProblemas,
                'percentual_ok'              => $percentualOK,
                'data_formatada'             => Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->translatedFormat('d \\d\\e F, Y'),
                'tempo_medio_aguardando_min' => (int) round($avgAguardando ?? 0),
                'tempo_medio_descarrego_min' => (int) round($avgDescarrego ?? 0),
                'tempo_medio_trajeto_min'    => (int) round($avgTrajeto ?? 0),
                'tempo_medio_aguardando_fmt' => $formatMinutes($avgAguardando),
                'tempo_medio_descarrego_fmt' => $formatMinutes($avgDescarrego),
                'tempo_medio_trajeto_fmt'    => $formatMinutes($avgTrajeto),
            ];

            return view('pages.dashboards.logistica', compact(
                'data',
                'metricas',
                'statusDist',
                'filialCargas',
                'problemasPie'
            ));
        } catch (\Throwable $e) {
            \Log::error('Erro no Dashboard Logística', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
            ]);
            return response()->view('proxy-error', [
                'message' => 'Dashboard Logística temporariamente indisponível',
                'code' => 502
            ], 502);
        }
    }

    public function refresh(Request $request)
    {
        try {
            $dataEntrega = $request->input('data_entrega', date('d/m/Y'));
            $dataCarga   = Carbon::createFromFormat('d/m/Y', $dataEntrega)->format('Y-m-d');

            $base = LogisticaEntrega::query()->where('data_carga', $dataCarga);

            $totalNotas     = (clone $base)->count();
            $totalEntregues = (clone $base)->where('acompanhamento', 3)->count();
            $totalAndamento = (clone $base)->whereIn('acompanhamento', [0, 1, 2])->count();

            return response()->json([
                'timestamp'     => now()->format('d/m/Y H:i:s'),
                'total_notas'   => $totalNotas,
                'entregues'     => $totalEntregues,
                'andamento'     => $totalAndamento,
                'percentual_ok' => $totalNotas > 0 ? round(($totalEntregues / $totalNotas) * 100, 1) : 0,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro no refresh do Dashboard Logística', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Erro ao atualizar dados'
            ], 502);
        }
    }
}

