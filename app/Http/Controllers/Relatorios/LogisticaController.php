<?php

namespace App\Http\Controllers\Relatorios;

use App\Exports\PendenciasExport;
use App\Exports\Relatorios\LogisticaRateioExport;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ClienteLinhas;
use App\Models\ClienteNotas;
use App\Models\ClienteNotasItem;
use App\Models\System\ClienteLinha;
use App\Models\VLogisticaRoterizacaoRateio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LogisticaController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
//            'cod_filial'     => 'nullable|integer',
//            'cod_vendedor'   => 'nullable|integer',
//            'cod_supervisor' => 'nullable|integer',
            'periodo' => 'nullable|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4} - [0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
        ]);


        // periodo ex: 31/10/2024 - 29/11/2024
        $data['periodo']       = $request->input('periodo', date('d/m/Y', strtotime(date('Y-m-01'))) . ' - ' . date('d/m/Y', strtotime(date('Y-m-t'))));
        $data['cod_filial']    = $request->input('cod_filial');
        $data['cod_agrupador'] = $request->filled('cod_agrupador') ? $request->input('cod_agrupador', 'filial') : 'filial';
        $data_incio            = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[0])->format('Y-m-d');
        $data_fim              = Carbon::createFromFormat('d/m/Y', explode(' - ', $data['periodo'])[1])->format('Y-m-d');

        $data['items'] = VLogisticaRoterizacaoRateio::sortable()
            ->selectRaw(sprintf("
               %s as agrupador,
               sum(peso_total)                as peso_total,
               sum(valor_descarga)            as valor_descarga,
               sum(valor_pedagio)             as valor_pedagio,
               sum(valor_escolta)             as valor_escolta,
               sum(valor_despesa_extra)       as valor_despesa_extra,
               sum(valor_acrescimo)           as valor_acrescimo,
               sum(valor_desconto)            as valor_desconto,
               sum(valor_total_carga)         as valor_total_carga", $data['cod_agrupador']))
            ->whereBetween('data_entrega', [$data_incio, $data_fim])
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cod_filial', $cod_filial))
            ->groupBy($data['cod_agrupador'])
            ->get();


        if ($request->input('exportar') == 'excel') {
            $fileName = sprintf('relatorio_logistica_%s_%s.xlsx', $data['cod_agrupador'], date('YmdHis'));


            return Excel::download(new LogisticaRateioExport($data['items'], $data['cod_agrupador']), $fileName);
        }

        return view('pages.relatorios.logistica.rateio', compact('data'));
    }
}
