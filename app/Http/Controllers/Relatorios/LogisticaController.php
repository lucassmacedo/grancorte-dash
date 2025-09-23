<?php

namespace App\Http\Controllers\Relatorios;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\LogisticaEntrega;
use App\Models\LogisticaEntregaPausa;
use App\Models\VLogisticaEntregaLog;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogisticaController extends Controller
{
    public function index(Request $request)
    {
        $data['cod_filial']     = $request->input('cod_filial', null);
        $data['data_entrega']   = $request->filled('data_entrega') ? $request->input('data_entrega', date('d/m/Y')) : date('d/m/Y');
        $data['cod_vendedor']   = $request->input('cod_vendedor', null);
        $data['cod_supervisor'] = $request->input('cod_supervisor', null);
        $data['placa']          = $request->input('placa', null);
        $data['cod_cli']        = $request->input('cod_cli', null);


        // Primeiro obtemos todas as cargas distintas que atendem aos critérios
        $cargasQuery = LogisticaEntrega::select('carga')
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', 'ilike', sprintf("%%%s%%", strtoupper($placa))))
            ->when($data['cod_cli'], fn($query, $cliente) => $query->where('cod_cli', strtoupper($cliente)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('cod_supervisor', Auth::user()->codigo))
            ->distinct('carga')
            ->orderBy('carga');

        // Paginamos as cargas
        $paginator  = $cargasQuery->clone()->paginate($request->input('per_page', 10));
        $cargas_ids = $paginator->getCollection()->pluck('carga')->toArray();

        // Agora buscamos os dados completos apenas para as cargas da página atual
        $entregas = LogisticaEntrega::select(
            'logistica_entregas.*',
            DB::raw('clientes.id as cliente_id'),
            'clientes.apelido',
            'clientes.latitude',
            'clientes.longitude',
            DB::raw("users.codigo || ' - ' || users.nome as vendedor"),
            DB::raw("'Por: '||coalesce(canhoto_user.nome,'Motorista') || ' em ' || coalesce(to_char(canhoto_data_upload, 'DD/MM/YYYY HH24:MI'),to_char(data_carga, 'DD/MM/YYYY')) as canhoto_user"),
            DB::raw('coalesce(logistica_entrega_pausas.total,0) as total_pausas'),
            DB::raw('logistica_entrega_inicios.created_at as inicio_entrega'),

            // Tempo de Deslocamento
            DB::raw("CASE WHEN ordem = 1 THEN 
                                ROUND((EXTRACT(EPOCH FROM (data_acompanhamento_entrega - logistica_entrega_inicios.created_at)) / 60)::numeric, 0)
                            ELSE 
                                ROUND((EXTRACT(EPOCH FROM (data_acompanhamento_entrega - LAG(canhoto_data_upload) OVER (PARTITION BY logistica_entregas.carga, logistica_entregas.placa ORDER BY ordem))) / 60)::numeric, 0)
                        END as tempo_deslocamento"),
            // Tempo Aguardando Entrega
            DB::raw("ROUND((EXTRACT(EPOCH FROM (data_acompanhamento_descarregando - data_acompanhamento_entrega)) / 60)::numeric, 0) as tempo_aguardando"),
            // Tempo Descarrego
            DB::raw("ROUND((EXTRACT(EPOCH FROM (canhoto_data_upload - data_acompanhamento_descarregando)) / 60)::numeric, 0) as tempo_descarrego"),
        )
            ->whereIn('logistica_entregas.carga', $cargas_ids)
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->join('users', 'users.codigo', 'logistica_entregas.cod_vendedor')
            ->leftjoin('users as canhoto_user', 'canhoto_user.id', 'logistica_entregas.canhoto_entrega_user_upload')
            ->leftJoin(DB::raw("(select count(*) as total, carga, placa
                    from logistica_entrega_pausas
                    group by carga, placa) as logistica_entrega_pausas"), function ($join) {
                $join->on('logistica_entrega_pausas.carga', 'logistica_entregas.carga')
                    ->on('logistica_entrega_pausas.placa', 'logistica_entregas.placa');
            })
            ->leftJoin('logistica_entrega_inicios', function ($join) {
                $join->on('logistica_entregas.carga', '=', 'logistica_entrega_inicios.carga')
                    ->on('logistica_entregas.placa', '=', 'logistica_entrega_inicios.placa');
            })
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->orderBy('carga')
            ->orderBy('ordem')
            ->get();

        // Agrupamos as entregas por carga
        $cargas = $entregas->groupBy('carga')->map(function ($carga) {
            $carga->porcentagem = round($carga->filter(fn($nota) => $nota->canhoto_entrega)->count() / $carga->count() * 100, 2);
            $localizacoes       = $carga->sortBy('ordem')
                ->map(fn($nota) => ['lat' => (float) $nota->latitude, 'lng' => (float) $nota->longitude])
                ->reject(fn($nota) => $nota['lat'] == '0.00000000' || $nota['lng'] == '0.00000000');

            $data['localizacoes']             = [
                'origin'      => $localizacoes->first(),
                'destination' => $localizacoes->last(),
                'waypoints'   => $localizacoes->slice(1, -1)->map(fn($nota) => ["location" => $nota])->toArray()
            ];
            $data['items']                    = $carga->sortBy('ordem');
            $data['porcentagem']              = $carga->porcentagem;
            $data['inicio_entrega']           = $carga->first()->inicio_entrega ? $carga->first()->inicio_entrega->format('H:i') : null;
            $data['tempo_total_deslocamento'] = \Carbon\CarbonInterval::minutes($carga->sum(fn($nota) => $nota->tempo_deslocamento))->cascade()->format('%H:%I');
            $data['tempo_aguardando_entrega'] = \Carbon\CarbonInterval::minutes($carga->sum(fn($nota) => $nota->tempo_aguardando))->cascade()->format('%H:%I');
            $data['tempo_descarrego']         = \Carbon\CarbonInterval::minutes($carga->sum(fn($nota) => $nota->tempo_descarrego))->cascade()->format('%H:%I');

            return $data;
        });


        // Para obter todos os clientes para o filtro, mantemos a consulta original
        $todos_clientes = LogisticaEntrega::select('logistica_entregas.cod_cli', 'clientes.apelido')
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', strtoupper($placa)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->get();

        $clientes = $todos_clientes->map(function ($item) {
            return [
                'cod_cli' => $item['cod_cli'],
                'apelido' => $item['cod_cli'] . " - " . $item['apelido']
            ];
        })->pluck('apelido', 'cod_cli')->unique();


        // Primeiro obtemos todas as cargas distintas que atendem aos critérios
        $pausas = LogisticaEntregaPausa::selectRaw('*')
            ->join('logistica_entregas', function ($join) {
                $join->on('logistica_entregas.carga', '=', 'logistica_entrega_pausas.carga')
                    ->on('logistica_entregas.placa', '=', 'logistica_entrega_pausas.placa');
            })
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('logistica_entregas.placa', strtoupper($placa)))
            ->when($data['cod_cli'], fn($query, $cliente) => $query->where('logistica_entregas.cod_cli', strtoupper($cliente)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->whereDate('logistica_entrega_pausas.created_at', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->orderByRaw('logistica_entrega_pausas.tipo,logistica_entrega_pausas.id desc')
            ->get();

        // Primeiro obtemos todas as cargas distintas que atendem aos critérios
        $problemas_entrega = LogisticaEntrega::select(
            'logistica_entregas.*',
            'clientes.apelido',
            DB::raw("TO_CHAR(INTERVAL '1 second' * EXTRACT(EPOCH FROM (now() - (problemas_entrega ->> 'data_problema')::timestamp)), 'HH24:MI') || ' min' as minutos_problema"),
            DB::raw("users.codigo || ' - ' || users.nome as vendedor")
        )
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->join('users', 'users.codigo', 'logistica_entregas.cod_vendedor')
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', 'ilike', sprintf("%%%s%%", strtoupper($placa))))
            ->when($data['cod_cli'], fn($query, $cliente) => $query->where('cod_cli', strtoupper($cliente)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->whereNotNull('problemas_entrega')
            ->whereNull('problemas_entrega_resolucao')
            ->orderByRaw("EXTRACT(EPOCH FROM (now() - (problemas_entrega ->> 'data_problema')::timestamp)) desc")
            ->get();

        // Primeiro obtemos todas as cargas distintas que atendem aos critérios
        $entregas_dia_andamento = LogisticaEntrega::select(
            'logistica_entregas.*',
            'clientes.apelido',
            DB::raw("TO_CHAR(INTERVAL '1 second' * EXTRACT(EPOCH FROM (now() - data_acompanhamento_entrega::timestamp)), 'HH24:MI') || ' min' as minutos_andamento"),
            DB::raw("TO_CHAR(INTERVAL '1 second' * EXTRACT(EPOCH FROM (now() - data_acompanhamento_descarregando::timestamp)), 'HH24:MI') || ' min' as minutos_descarregando"),
            DB::raw("users.codigo || ' - ' || users.nome as vendedor")
        )
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->join('users', 'users.codigo', 'logistica_entregas.cod_vendedor')
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', 'ilike', sprintf("%%%s%%", strtoupper($placa))))
            ->when($data['cod_cli'], fn($query, $cliente) => $query->where('cod_cli', strtoupper($cliente)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->whereIn('acompanhamento', [1, 2])
            ->orderByRaw("EXTRACT(EPOCH FROM (now() - data_acompanhamento_entrega::timestamp)) desc")
            ->get();

        return view('pages.logistica.entregas.index', compact('cargas', 'paginator', 'data', 'clientes', 'pausas', 'problemas_entrega', 'entregas_dia_andamento'));
    }

    public function indexByStatus(Request $request)
    {
        $request->validate([
            'data_entrega'   => 'nullable|date_format:d/m/Y',
            'cod_filial'     => 'nullable|exists:filiais,codigo',
            'cod_vendedor'   => 'nullable|exists:users,codigo',
            'cod_supervisor' => 'nullable|exists:users,codigo',
            'placa'          => 'nullable|string|max:10',
            'cod_cli'        => 'nullable|string|max:10',
            'status'         => 'required|in:' . implode(',', array_keys(LogisticaEntrega::$status))
        ]);

        $data['cod_filial']     = $request->input('cod_filial', null);
        $data['data_entrega']   = $request->input('data_entrega', date('d/m/Y'));
        $data['cod_vendedor']   = $request->input('cod_vendedor', null);
        $data['cod_supervisor'] = $request->input('cod_supervisor', null);
        $data['placa']          = $request->input('placa', null);
        $data['cod_cli']        = $request->input('cod_cli', null);
        $data['status']         = $request->input('status', 1);


        // Agora buscamos os dados completos apenas para as cargas da página atual
        $entregas = LogisticaEntrega::sortable()->select(
            'logistica_entregas.*',
            DB::raw('clientes.id as cliente_id'),
            'clientes.apelido',
            'clientes.latitude',
            'clientes.longitude',
            DB::raw("users.codigo || ' - ' || users.nome as vendedor"),
            DB::raw("'Por: '||canhoto_user.nome || ' em ' || coalesce(to_char(canhoto_data_upload, 'DD/MM/YYYY HH24:MI'),to_char(data_carga, 'DD/MM/YYYY')) as canhoto_user"),
            DB::raw('coalesce(logistica_entrega_pausas.total,0) as total_pausas')
        )
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('logistica_entregas.placa', 'ilike', sprintf("%%%s%%", strtoupper($placa))))
            ->when($data['cod_cli'], fn($query, $cliente) => $query->where('logistica_entregas.cod_cli', strtoupper($cliente)))
            ->where('logistica_entregas.acompanhamento', $data['status'])
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->join('users', 'users.codigo', 'logistica_entregas.cod_vendedor')
            ->leftjoin('users as canhoto_user', 'canhoto_user.id', 'logistica_entregas.canhoto_entrega_user_upload')
            ->leftJoin(DB::raw("(select count(*) as total, carga, placa
                    from logistica_entrega_pausas
                    group by carga, placa) as logistica_entrega_pausas"), function ($join) {
                $join->on('logistica_entrega_pausas.carga', 'logistica_entregas.carga')
                    ->on('logistica_entrega_pausas.placa', 'logistica_entregas.placa');
            })
            ->orderBy('carga')
            ->orderBy('ordem')
            ->paginate($request->input('per_page', 20));


        if ($request->ajax()) {
            return view('pages.logistica.entregas.status-table-list', compact('entregas', 'data'))->render();
        }


        // Para obter todos os clientes para o filtro, mantemos a consulta original
        $todos_clientes = LogisticaEntrega::select('logistica_entregas.cod_cli', 'clientes.apelido')
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('logistica_entregas.cod_filial', $cod_filial))
            ->when($data['cod_vendedor'], fn($query, $cod_vendedor) => $query->where('logistica_entregas.cod_vendedor', $cod_vendedor))
            ->when($data['cod_supervisor'], fn($query, $cod_supervisor) => $query->where('logistica_entregas.cod_supervisor', $cod_supervisor))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', strtoupper($placa)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
            ->get();

        $clientes = $todos_clientes->map(function ($item) {
            return [
                'cod_cli' => $item['cod_cli'],
                'apelido' => $item['cod_cli'] . " - " . $item['apelido']
            ];
        })->pluck('apelido', 'cod_cli')->unique();


        return view('pages.logistica.entregas.status', compact('data', 'clientes', 'entregas'));
    }

    public function mapa(Request $request)
    {
        $data['cod_filial']     = $request->input('cod_filial', null);
        $data['data_entrega']   = $request->input('data_entrega', date('d/m/Y'));
        $data['cod_vendedor']   = $request->input('cod_vendedor', null);
        $data['cod_supervisor'] = $request->input('cod_supervisor', null);
        $data['placa']          = $request->input('placa', null);
        $data['cod_cli']        = $request->input('cod_cli', null);

        // Agora buscamos os dados completos apenas para as cargas da página atual
        $entregas = LogisticaEntrega::select(
            'logistica_entregas.id',
            'logistica_entregas.ordem',
            'logistica_entregas.acompanhamento',
            'logistica_entregas.cod_filial',
            'logistica_entregas.cod_cli',
            'logistica_entregas.numero_nota',
            'logistica_entregas.cidade',
            'logistica_entregas.uf',
            'logistica_entregas.carga',
            'logistica_entregas.placa',
            'logistica_entregas.canhoto_entrega',
            DB::raw('clientes.id as cliente_id'),
            'clientes.cpf_cgc',
            'clientes.apelido',
            'clientes.latitude',
            'clientes.longitude',
            DB::raw("users.codigo || ' - ' || users.nome as vendedor"),
        )
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
            ->join('users', 'users.codigo', 'logistica_entregas.cod_vendedor')
            ->leftjoin('users as canhoto_user', 'canhoto_user.id', 'logistica_entregas.canhoto_entrega_user_upload')
            ->where('data_carga', Carbon::createFromFormat('d/m/Y', $data['data_entrega'])->format('Y-m-d'))
//            ->when($data['cod_filial'], fn($query, $cod_filial) => $query->where('cod_filial', $cod_filial))
            ->when($data['placa'], fn($query, $placa) => $query->where('placa', strtoupper($placa)))
            ->when(Auth::user()->hasRole('vendedor'), fn($query) => $query->where('logistica_entregas.cod_vendedor', Auth::user()->codigo))
            ->when(Auth::user()->hasRole('supervisor'), fn($query) => $query->where('logistica_entregas.cod_supervisor', Auth::user()->codigo))
//            ->where('carga', '018895')
            ->get()
            ->reject(fn($nota) => round($nota->latitude, 2) == 0.00 || round($nota->longitude, 2) == 0.00)
            ->map(function ($nota) {
                if (abs($nota->latitude) > 90) {
                    $nota->latitude = $nota->latitude / 10000000;
                }
                if (abs($nota->longitude) > 180) {
                    $nota->longitude = $nota->longitude / 10000000;
                }

                // se o total de digitos for menor que 8, multiplica por 10000000
                if (strlen($nota->longitude) < 11) {
                    $multiplica_por  = (int) 11 - strlen($nota->longitude) . '0';
                    $nota->longitude = $nota->longitude * 10;
                }

                // Formata com precisão de 7 casas decimais
                $nota->latitude  = (float) number_format($nota->latitude, 7, '.', '');
                $nota->longitude = (float) number_format($nota->longitude, 7, '.', '');
                $nota->status    = !is_null($nota->canhoto_entrega);
                $nota->title     = $nota->cod_cli . ' - ' . $nota->apelido;

                return $nota;
            });


        $cargas = $entregas->mapWithKeys(function ($nota) {
            return [
                $nota->carga => $nota->carga . " - " . $nota->placa
            ];
        })
            ->unique()
            ->toArray();


        return view('pages.logistica.mapa', compact('data', 'entregas', 'cargas'));
    }

    public function localizacoes(Request $request)
    {
        $request->validate([
            'carga' => 'required|numeric'
        ]);


        $cargas = LogisticaEntrega::select('numero_nota', 'ordem', 'carga', 'canhoto_entrega', 'nome', 'codigo', 'clientes.latitude', 'clientes.longitude')
            ->join('clientes', 'clientes.codigo', 'logistica_entregas.cod_cli')
//            ->where('carga', '009242')
            ->when($request->filled('carga'), fn($query) => $query->where('carga', $request->input('carga')))
            ->where('latitude', '<>', '0.00000000')
            ->where('longitude', '<>', '0.00000000')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy("ordem")
            ->get()
            ->map(function ($nota) {
                if (abs($nota->latitude) > 90) {
                    $nota->latitude = $nota->latitude / 10000000;
                }
                if (abs($nota->longitude) > 180) {
                    $nota->longitude = $nota->longitude / 10000000;
                }

                // se o total de digitos for menor que 8, multiplica por 10000000
                if (strlen($nota->longitude) < 11) {
                    $multiplica_por  = (int) 11 - strlen($nota->longitude) . '0';
                    $nota->longitude = $nota->longitude * 10;
                }

                // Formata com precisão de 7 casas decimais
                $nota->latitude  = number_format($nota->latitude, 7, '.', '');
                $nota->longitude = number_format($nota->longitude, 7, '.', '');

                // Valida se as coordenadas são válidas antes de retornar
                $lat = (float) $nota->latitude;
                $lng = (float) $nota->longitude;

                // Verifica se as coordenadas estão dentro dos limites válidos
                // Latitude deve estar entre -90 e 90
                // Longitude deve estar entre -180 e 180
                // E não devem ser valores muito próximos de zero (coordenadas inexistentes)
                if (abs($lat) < 0.001 || abs($lng) < 0.001 ||
                    $lat < -90 || $lat > 90 ||
                    $lng < -180 || $lng > 180) {
                    return null; // Retorna null para coordenadas inválidas
                }

                return [
                    'ordem'     => $nota->ordem,
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'title'     => $nota->codigo . ' - ' . $nota->nome,
                    'nota'      => $nota->numero_nota,
                    'carga'     => $nota->carga,
                    'status'    => !is_null($nota->canhoto_entrega),
                    'type'      => !is_null($nota->canhoto_entrega) ? 'home' : 'store-alt'
                ];
            })
            ->filter() // Remove valores null (coordenadas inválidas)
            ->values(); // Reindexar o array

        return $cargas;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'nota'  => 'required|numeric',
            'carga' => 'required|numeric',
            'file'  => 'required|mimes:pdf,png,jpg,jpeg,gif'
        ]);

        try {
            DB::beginTransaction();
            $logistica = LogisticaEntrega::where('numero_nota', $request->input('nota'))->where('carga', $request->input('carga'))->first();

            $file     = $request->file('file');
            $fileName = $request->input('nota') . time() . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/canhotos'), $fileName);

            $logistica->update([
                'canhoto_entrega'                   => $fileName,
                'canhoto_entrega_user_upload'       => auth()->user()->id,
                'canhoto_data_upload'               => now(),
                'data_acompanhamento_entrega'       => now(),
                'data_acompanhamento_descarregando' => now(),
                'acompanhamento'                    => 3
            ]);

            flash('Canhoto enviado com sucesso');
            DB::commit();

            return response()->json([
                'success'      => true,
                'file'         => asset('storage/canhotos/' . $fileName),
                'nota'         => $request->input('nota'),
                'canhoto_user' => sprintf("Por: %s em %s", auth()->user()->name, now()->format('d/m/Y H:i')),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Bugsnag::notifyException($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addObservacoes(Request $request, $nota)
    {
        $nota = LogisticaEntrega::where('numero_nota', $nota)->first();

        $disable = auth()->user()->hasRole('vendedor');

        return view('pages.logistica.addObservacoes', compact('nota', 'disable'));
    }

    public function addObservacoesStore(Request $request, $nota)
    {
        $request->validate([
            'observacao' => 'required|string'
        ]);

        try {
            $nota = LogisticaEntrega::where('numero_nota', $nota)->first();
            $nota->update([
                'observacao' => $request->input('observacao')
            ]);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return redirect()->back()->with('error', 'Erro ao adicionar observação');
        }
    }

    public function getPausas(Request $request)
    {
        $request->validate([
            'carga' => 'required',
            'placa' => 'required'
        ]);

        $pausas = LogisticaEntregaPausa::selectRaw("*, round((6371 * acos(cos(radians(latitude_inicio))
                              * cos(radians(latitude_fim))
                              * cos(radians(longitude_fim) - radians(longitude_inicio))
           + sin(radians(latitude_inicio))
                              * sin(radians(latitude_fim))))::numeric, 2) AS distancia_km")
            ->where('carga', $request->input('carga'))
            ->where('placa', $request->input('placa'))
            ->orderBy('id', 'asc')
            ->get();


        return view('pages.logistica.entregas.pausas', compact('pausas'));
    }

    public function resolverProblema(Request $request, $nota)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'arquivo'   => 'nullable|file|mimes:pdf,png,jpg,jpeg,gif|max:2048',
        ]);

        try {
            $nota = LogisticaEntrega::where('numero_nota', $nota)->firstOrFail();

            $nota->problemas_entrega_resolucao = [
                'descricao' => $request->input('descricao'),
                'data'      => now()->format('Y-m-d H:i:s'),
                'user_id'   => auth()->user()->id,
            ];

            if ($request->filled('arquivi')) {
                $file     = $request->file('arquivo');
                $fileName = 'problema_' . $nota->numero_nota . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path('app/public/problemas_entrega_solucoes'), $fileName);
                $nota->problemas_entrega_resolucao['arquivo'] = $fileName;
            }

            $nota->save();

            flash('Resolução do problema registrada com sucesso');
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            flash('Erro ao registrar problema: ' . $e->getMessage(), 'danger');
        }
    }

    public function logs(Request $request)
    {
        $request->validate([
            'data_carga' => 'required',
        ]);

        // Para obter todos os clientes para o filtro, mantemos a consulta original
        $logs = VLogisticaEntregaLog::sortable(['data_evento' => 'desc', 'orderm' => 'asc'])
            ->whereDate('data_evento', Carbon::createFromFormat('d/m/Y', $request->input('data_carga'))->format('Y-m-d'))
            ->paginate($request->input('per_page', 20));

        if ($request->ajax()) {
            return view('pages.logistica.entregas.logs.table-list', compact('logs'))->render();
        }

        return view('pages.logistica.entregas.logs.index', compact('logs'));
    }
}
