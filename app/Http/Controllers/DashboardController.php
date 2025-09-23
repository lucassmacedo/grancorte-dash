<?php

namespace App\Http\Controllers;

use App\Models\DashboardFaturamentoTotal;
use App\Models\LogisticaEntrega;
use App\Models\System\DashboardFaturamento;
use App\Models\System\DashboardRecebimento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'ano' => 'nullable|integer|min:2021|max:' . date('Y'),
        ]);

        $ano = $request->input('ano', date('Y'));

        $recebimento = DashboardRecebimento::selectRaw("cod_filial, mes, round(sum(recebimento),2) as recebimento, round(sum(comissao),2) as comissao")
            ->where("ano", $ano)->groupByRaw("cod_filial,mes")->orderByRaw("mes::numeric asc");

        $faturamento = DashboardFaturamentoTotal::selectRaw("EXTRACT(MONTH FROM periodo) as mes, sum(faturamento) as faturamento")
            ->whereYear("periodo", $ano)
            ->groupByRaw("EXTRACT(MONTH FROM periodo) ")
            ->orderByRaw("EXTRACT(MONTH FROM periodo)  asc");

        // if is not admin filter DashboardRecebimento by cod_vendedor
        $recebimento = $recebimento->get();
        $faturamento = $faturamento->get();


        $data["faturamento"] = $faturamento->pluck("faturamento")->toArray();

        $data["faturamento_mes"] = $faturamento->map(function ($item) {
            return getMonths()[$item->mes - 1];
        })->unique();


        $data["100"]["recebimento"] = $recebimento->where("cod_filial", '010101')->pluck("recebimento")->toArray();
        $data["200"]["recebimento"] = $recebimento->where("cod_filial", '020101')->pluck("recebimento")->toArray();


        $data["100"]["comissao"] = $recebimento->where("cod_filial", '010101')->pluck("comissao")->toArray();
        $data["200"]["comissao"] = $recebimento->where("cod_filial", '020101')->pluck("comissao")->toArray();

        $data["geral"]["comissao"]    = $recebimento->groupBy('mes')->map(fn($item) => $item->sum('comissao'))->values();
        $data["geral"]["recebimento"] = $recebimento->groupBy('mes')->map(fn($item) => $item->sum('recebimento'))->values();

        $data['total'] = [
            'recebimento' => $recebimento->sum('recebimento'),
            'comissao'    => $recebimento->sum('comissao'),
            'faturamento' => $faturamento->sum('faturamento'),
        ];

        $data["mes"] = $recebimento->map(  fn($item) => getMonths()[$item->mes - 1])->unique()->values();


        $entregas = LogisticaEntrega::selectRaw("
         count(*) FILTER ( WHERE canhoto_entrega IS NOT NULL) as entregas_com_canhoto,
         count(*) as entregas,
         coalesce((count(*) FILTER ( WHERE canhoto_entrega IS NOT NULL ) * 100) / NULLIF(count(*), 0), 0) as porcentagem")
            ->where("data_carga", date('Y-m-d'));


        $entregas = $entregas->first();

        return view('pages.dashboards.index', compact('data', 'ano', 'entregas'));
    }
}
