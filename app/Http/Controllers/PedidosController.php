<?php

namespace App\Http\Controllers;

use App\Exports\PedidosExport;
use App\Http\Requests\PedidoCreateRequest;
use App\Http\Requests\PedidoStoreRequest;
use App\Http\Requests\PedidoUpdateRequest;
use App\Models\Cliente;
use App\Models\Filial;
use App\Models\LogisticaRota;
use App\Models\Pedido;
use App\Models\PedidoBloqueio;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\ProdutoPreco;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class PedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Pedido::sortable(['id' => 'desc'])
            ->select(
                "pedidos.id",
                "clientes.nome as cliente_nome",
                "clientes.cpf_cgc as cliente_cnpj",
                "pedidos.status",
                "pedidos.cod_filial",
                "pedidos.cod_local",
                "pedidos.total_itens",
                "pedidos.valor_total",
                "pedidos.exported_at",
                "clientes.cidade as cidade",
                "clientes.uf",
                "pedidos.sequencia_entrega",
                "pedidos.data_entrega",
                "pedidos.numero_veiculo",
                "pedidos.faturado",
                "pedidos.observacoes_cancelamento",
                "users.codigo as codigo_vendedor",
                "rota_id",
                DB::raw("users.codigo || ' - ' || split_part(users.nome, ' ', 1) as vendedor"),
                DB::raw("users.codigo || ' - ' || split_part(users.apelido, ' ', 1) as vendedor_apelido"),
            )
            ->join('clientes', 'clientes.codigo', 'pedidos.codigo_cliente')
            ->join("users", "users.codigo", "pedidos.codigo_vendedor")
            ->withCount('bloqueios')
            ->when(!auth()->user()->hasRole(['admin', 'supervisor', 'Supervisor Vendedores']), function ($q) {
                $q->where('pedidos.codigo_vendedor', auth()->user()->codigo);
            })
            ->when($request->filled('data_pedido'), function ($q) use ($request) {
                $date = Carbon::createFromFormat('d/m/Y', $request->input('data_pedido'))->format('Y-m-d');
                $q->whereDate('pedidos.created_at', $date);
            })
            ->when($request->filled('cod_vendedores'), function ($q) use ($request) {
                $q->whereIn('pedidos.codigo_vendedor', $request->input('cod_vendedores'));
            })
            ->when($request->filled('city_id'), function ($q) use ($request) {
                $q->where('clientes.codigo_municipio', $request->input('city_id'));
            })
            ->when($request->filled('exported_at'), function ($q) use ($request) {
                $q->where('pedidos.exported_at', $request->input('exported_at', 1) ? '!=' : '=', null);
            })
            ->when($request->filled('rota_id'), function ($q) use ($request) {
                $q->where('clientes.rota_id', $request->input('rota_id'));
            })
            ->when($request->filled('data_entrega'), function ($q) use ($request) {
                $date = Carbon::createFromFormat('d/m/Y', $request->input('data_entrega'))->format('Y-m-d');
                $q->where('pedidos.data_entrega', $date);
            }, function ($q) {
                $q->where('pedidos.data_entrega', Carbon::now()->addDay(1)->format('Y-m-d'));
            })->when($request->filled('status'), function ($q) use ($request) {
                $q->where('pedidos.status', $request->input('status'));
            }, function ($q) {
                $q->whereNotIn('pedidos.status', [Pedido::STATUS_CANCELADO]);
            })
            ->when($request->filled('faturado'), function ($q) use ($request) {
                $q->where('pedidos.faturado', (bool)$request->input('faturado'));
            });

        $query->when($request->filled('cod_filial'), fn($q) => $q->where('pedidos.cod_filial', $request->input('cod_filial')));
        $query->when($request->filled('cod_local'), fn($q) => $q->where('pedidos.cod_local', $request->input('cod_local')));
        $query->when($request->filled('numero_veiculo'), fn($q) => $q->where('pedidos.numero_veiculo', $request->input('numero_veiculo')));

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($subq) use ($request) {
                $search = trim($request->input('search'));
                if ((int) $search > 0) {
                    $subq->where('pedidos.id', (int) $search);
                    $subq->orWhere('clientes.cpf_cgc', 'like', strtoupper("%{$search}%"));
                }
                $subq->orWhere(DB::raw('upper(clientes.nome)'), 'like', strtoupper("%{$search}%"));
            });
        });

        // Filtro por grupo de produto(s)
        $query->when($request->filled('grupo_produto'), function ($q) use ($request) {
            $grupos = is_array($request->input('grupo_produto')) ? $request->input('grupo_produto') : [$request->input('grupo_produto')];
            $q->whereExists(function ($subQuery) use ($grupos) {
                $subQuery->select(DB::raw(1))
                    ->from('pedido_items')
                    ->join('produtos', 'produtos.codigo', '=', 'pedido_items.codigo_produto')
                    ->whereColumn('pedido_items.pedido_id', 'pedidos.id')
                    ->whereIn('produtos.cod_grupo', $grupos);
            });
        });

        // Filtro por rota_id
        $query->when($request->filled('rota_id'), function ($q) use ($request) {
            $q->whereIn('clientes.rota_id', $request->input('rota_id'));
        });

        // Get available product groups for current filtered orders
        $pedidoIds = clone $query;
        $pedidoIds->getQuery()->orders = [];
        $pedidoGrupoProdutos = PedidoItem::selectRaw("produtos.cod_grupo||' - '||produtos.desc_grupo as grupo, produtos.cod_grupo")
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->join('produtos', 'produtos.codigo', '=', 'pedido_items.codigo_produto')
            ->whereIn('pedidos.id', $pedidoIds->select('pedidos.id'))
            ->groupBy('produtos.cod_grupo', 'produtos.desc_grupo')
            ->get()
            ->pluck('grupo', 'cod_grupo');

        // Get available rotas for current filtered orders

        $pedidosRotas = LogisticaRota::selectRaw("logistica_rotas.id, logistica_rotas.nome, logistica_rotas.codigo")
            ->whereIn('logistica_rotas.id', $pedidoIds->select('clientes.rota_id'))
            ->get()
            ->pluck('nome', 'id');

        $vendedores = $query->get()->pluck('vendedor_apelido', 'codigo_vendedor')->unique();
        $pedidos = $query->paginate($request->get('per_page', 20));

        if ($request->ajax()) {
            return view('pages.pedidos.table-list', compact('pedidos'))->render();
        }

        $filiais  = Filial::listFiliais();
        $locais   = Produto::$cod_local;
        $veiculos = Pedido::$tipo_veiculo;

        $cities = $request->has('city_id') ? \App\Models\City::where("code", $request->input('city_id'))->search()->pluck('text', 'id') : [];

        return view('pages.pedidos.index', compact('pedidos', 'filiais', 'locais', 'veiculos', 'cities', 'vendedores', 'pedidoGrupoProdutos', 'pedidosRotas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(PedidoCreateRequest $request)
    {
        $cliente  = null;
        $vendedor = null;
        $ultimoEnderecoAlterado = null;

        if ($request->has('cliente')) {
            $cliente = Cliente::selectRaw("clientes.codigo,
            latitude,
            longitude,
            clientes.cod_lista,
            clientes.nome,
            clientes.apelido as fantasia,
            clientes.Cod_Sisant as codigo_anterior,
            clientes.cpf_cgc as cnpj_cpf,
            clientes.endereco,
            clientes.numero,
            clientes.bairro,
            clientes.cidade,
            clientes.uf,
            clientes.cep,
            lower(clientes.cod_situacao) as cod_situacao,
            users.codigo || ' - ' || users.nome as vendedor,
            limite_credito,
            limite_consumido,
            limite_disponivel,
            debitos_grupo,
            obs_venda")
                ->where('clientes.codigo', $request->input('cliente'))
                ->leftjoin('users', 'users.codigo', 'clientes.cod_vendedor')
                ->first();

            $vendedor = $cliente->vendedor;

            // Buscar o último endereço alterado do cliente em pedidos anteriores
            $ultimoEnderecoAlterado = DB::table('pedidos_endereco')
                ->join('pedidos', 'pedidos.id', '=', 'pedidos_endereco.pedido_id')
                ->where('pedidos.codigo_cliente', $request->input('cliente'))
                ->orderBy('pedidos_endereco.created_at', 'desc')
                ->select('pedidos_endereco.*')
                ->first();
        }

        $produtos['data']       = [];
        $produtos['attributes'] = [];

        if ($request->has('cod_filial') && $request->has('cod_local') && $cliente) {
            $filial = Filial::where('codigo', $request->input('cod_filial'))->first();
            $day    = Carbon::now()->dayOfWeek;
            if (Carbon::now()->isAfter(Carbon::createFromFormat('H:i', $filial->horarios_pedidos[$day]['fim'])) || Carbon::now()->isBefore(Carbon::createFromFormat('H:i', $filial->horarios_pedidos[$day]['inicio']))) {
                return redirect()->back()
                    ->withInput([
                        'cod_filial' => $request->input('cod_filial'),
                        'cod_local'  => $request->input('cod_local'),
                        'cliente'    => $request->input('cliente'),
                    ])
                    ->withErrors(['cod_filial' => sprintf("Horário de pedidos para filial é %s - %s", $filial->horarios_pedidos[$day]['inicio'], $filial->horarios_pedidos[$day]['fim'])]);
            }

            $produtos = ProdutoPreco::listaFiltrada(
                $request->input('cod_filial'),
                $request->input('cod_local'),
                $cliente->cod_lista,
                $cliente->perc_desconto
            );
        }


        $filters = [
            'cod_filial' => $request->input('cod_filial'),
            'cod_local'  => $request->input('cod_local'),
            'cliente'    => $request->input('cliente'),
        ];

        $cities = [];

        return view('pages.pedidos.edit', compact('produtos', 'cliente', 'vendedor', 'filters', 'cities', 'ultimoEnderecoAlterado'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PedidoStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $cliente = Cliente::where('codigo', $request->input('cliente'))->first();

            $data_pedido = [
                "cod_local"         => $request->input('cod_local'),
                "cod_filial"        => $request->input('cod_filial'),
                "codigo_cliente"    => $request->input('cliente'),
                "codigo_vendedor"   => $cliente->cod_vendedor,
                "observacoes"       => $request->input('observacoes'),
                "total_itens"       => 0,
                "valor_total"       => 0,
                "data_entrega"      => Carbon::createFromFormat('d/m/Y', $request->input('data_entrega'))->format('Y-m-d'),
                "desconto"          => $cliente->perc_desconto,
                "pedido_compra"     => $request->input('pedido_compra'),
                "tipo_descarga"     => $request->input('tipo_descarga'),
                "valor_descarga"    => realToFloat($request->input('valor_descarga')),
                "tipo_pedido"       => $request->input('tipo_pedido'),
                'sequencia_entrega' => $request->input('sequencia_entrega'),
                'numero_veiculo'    => $request->input('numero_veiculo'),
            ];

            $data_items         = [];
            $produtos_alterados = [];
            foreach ($request->input('produtos') as $item) {
                $produto = ProdutoPreco::with('produto')
                    ->where([
                        'cod_filial' => $request->input('cod_filial'),
                        'cod_local'  => $request->input('cod_local'),
                        'cod_lista'  => $cliente->cod_lista,
                    ])
                    ->where("codigo", $item['codigo'])
                    ->first();

                $preco = realToFloat($item['unitario']);
                $peso_padrao = $produto->produto->peso_padrao;
                if ($peso_padrao) {
                    // Calcular por KG
                    $peso_total = $item['quantidade'] * $produto->produto->peso_medio;
                    $valor_total = $peso_total * $preco;
                } else {
                    // Calcular por unidade
                    $peso_total = $item['quantidade'];
                    $valor_total = $item['quantidade'] * $preco;
                }

                if ((float) $produto->preco > (float) $preco) {
                    $produtos_alterados[] = sprintf(
                        "Produto: %s | Preço Original: %s | Preço Alterado: %s | QTD: %s",
                        $produto->produto->descricao,
                        formatMoedaReal($produto->preco),
                        formatMoedaReal($preco),
                        $item['quantidade']
                    );
                }

                $data_items[] = [
                    "peso_total"              => $peso_total,
                    "codigo_produto"          => $item['codigo'],
                    "quantidade"              => $item['quantidade'],
                    "valor_unitario"          => $preco,
                    "valor_unitario_original" => (float) $produto->preco,
                    "valor_total"             => $valor_total,
                    'preco_alterado'          => (float) $produto->preco <> $preco,
                ];
            }

            $data_pedido['total_itens'] = array_sum(array_column($data_items, 'quantidade'));
            $data_pedido['valor_total'] = array_sum(array_column($data_items, 'valor_total'));
            $data_pedido['peso_total']  = array_sum(array_column($data_items, 'peso_total'));

            // if tipo_descarga == 1 valor_descarga receive = ceil peso_total / 1000 * valor_descarga
            if ($request->input('tipo_descarga') == 1 && ceil($data_pedido['peso_total'] / 1000) > 1) {
                $data_pedido['valor_descarga'] = (int) ceil($data_pedido['peso_total'] / 1000) * realToFloat($request->input('valor_descarga'));
            }
            $data_pedido['debitos_cliente'] = $cliente->debitos_grupo;

            $pedido = Pedido::create($data_pedido);
            $pedido->items()->createMany($data_items);

            // Salvar endereço personalizado se fornecido
            if ($request->filled('endereco.cep')) {
                $pedido->endereco()->create([
                    'cep' => str_replace('-', '', $request->input('endereco.cep')),
                    'endereco' => $request->input('endereco.endereco'),
                    'numero' => $request->input('endereco.numero'),
                    'bairro' => $request->input('endereco.bairro'),
                    'cidade' => $request->input('endereco.cidade'),
                    'city_id' => $request->input('endereco.city_id'),
                    'uf' => $request->input('endereco.uf'),
                    'complemento' => $request->input('endereco.complemento'),
                    'latitude' => $request->input('endereco.latitude'),
                    'longitude' => $request->input('endereco.longitude'),
                ]);
            }


            $this->setBloqueios($produtos_alterados, $pedido, $cliente);

            DB::commit();
            flash('Pedido criado com sucesso!', 'success');
            return redirect()->route('pedidos.index');
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            DB::rollBack();
            flash('Erro ao criar pedido!', 'error');

            return redirect()->route('pedidos.index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Pedido $pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Pedido $pedido)
    {
        $this->authorize('update', $pedido);

        $pedido = $pedido->load('items.produto', 'vendedor');

        return view('pages.pedidos.show', compact('pedido'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Pedido $pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Pedido $pedido)
    {
        $this->authorize('update', $pedido);

        $pedido->load('cliente');
        $pedido->load('endereco');
        $produtos['data']       = [];
        $produtos['attributes'] = [];


        $produtos = ProdutoPreco::listaFiltrada(
            $pedido->cod_filial,
            $pedido->cod_local,
            $pedido->cliente->cod_lista,
            $pedido->cliente->perc_desconto
        );

        $vendedor = null;

        $cliente = Cliente::selectRaw("latitude,
            longitude,
            clientes.nome,
            clientes.apelido as fantasia,
            clientes.codigo,
            clientes.Cod_Sisant as codigo_anterior,
            clientes.cpf_cgc as cnpj_cpf,
            clientes.endereco,
            clientes.numero,
            clientes.bairro,
            clientes.cidade,
            clientes.uf,
            clientes.cep,
            limite_credito,
            limite_consumido,
            limite_disponivel,
            debitos_grupo
        ")
            ->where('clientes.codigo', $pedido->codigo_cliente)
            ->leftjoin('users', 'users.codigo', 'clientes.cod_vendedor')
            ->first();

        $filters = [
            'cod_filial' => $pedido->cod_filial,
            'cod_local'  => $pedido->cod_local,
            'cliente'    => $pedido->codigo_cliente,
        ];

        // Buscar o último endereço alterado do cliente em pedidos anteriores (excluindo o pedido atual)
        $ultimoEnderecoAlterado = DB::table('pedidos_endereco')
            ->join('pedidos', 'pedidos.id', '=', 'pedidos_endereco.pedido_id')
            ->where('pedidos.codigo_cliente', $pedido->codigo_cliente)
            ->where('pedidos.id', '!=', $pedido->id) // Excluir o pedido atual
            ->orderBy('pedidos_endereco.created_at', 'desc')
            ->select('pedidos_endereco.*')
            ->first();


        $pedido->produtos = $pedido->items->map(function ($item) {
            $item->produto->preco = $item->valor_unitario;

            return [
                'codigo'     => $item->codigo_produto,
                'unitario'   => $item->valor_unitario,
                'quantidade' => $item->quantidade,
            ];
        });

        $pedido->valor_descarga = formatMoedaReal($pedido->valor_descarga);
        $vendedor               = sprintf("%s - %s", $pedido->vendedor->codigo, $pedido->vendedor->nome);


        $cities = $pedido->endereco ? ($pedido->endereco->city_id ? \App\Models\City::where("code", $pedido->endereco->city_id)->pluck('name', 'code') : []) : [];

        return view('pages.pedidos.edit', compact('produtos', 'pedido', 'cliente', 'vendedor', 'filters', 'ultimoEnderecoAlterado', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Pedido $pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PedidoUpdateRequest $request, Pedido $pedido)
    {
        $this->authorize('update', $pedido);

        try {
            DB::beginTransaction();

            $pedido->load('cliente');

            $data_pedido = [
                "observacoes"       => $request->input('observacoes'),
                "data_entrega"      => Carbon::createFromFormat('d/m/Y', $request->input('data_entrega'))->format('Y-m-d'),
                "desconto"          => $pedido->cliente->perc_desconto,
                "pedido_compra"     => $request->input('pedido_compra'),
                "tipo_descarga"     => $request->input('tipo_descarga'),
                "valor_descarga"    => realToFloat($request->input('valor_descarga')),
                "tipo_pedido"       => $request->input('tipo_pedido'),
                'sequencia_entrega' => $request->input('sequencia_entrega'),
                'numero_veiculo'    => $request->input('numero_veiculo'),
            ];

            $data_items         = [];
            $produtos_alterados = [];

            foreach ($request->input('produtos') as $item) {
                $produto = ProdutoPreco::with('produto')
                    ->where([
                        'cod_filial' => $pedido->cod_filial,
                        'cod_local'  => $pedido->cod_local,
                        'cod_lista'  => $pedido->cliente->cod_lista,
                    ])
                    ->where("codigo", $item['codigo'])
                    ->first();

                $preco = realToFloat($item['unitario']);
                $peso_padrao = $produto->produto->peso_padrao;
                if ($peso_padrao) {
                    // Calcular por KG
                    $peso_total = $item['quantidade'] * $produto->produto->peso_medio;
                    $valor_total = $peso_total * $preco;
                } else {
                    // Calcular por unidade
                    $peso_total = $item['quantidade'];
                    $valor_total = $item['quantidade'] * $preco;
                }

                $data_items[] = [
                    "peso_total"              => $peso_total,
                    "codigo_produto"          => $item['codigo'],
                    "quantidade"              => $item['quantidade'],
                    "valor_unitario"          => $preco,
                    "valor_unitario_original" => (float) $produto->preco,
                    "valor_total"             => $valor_total,
                    'preco_alterado'          => (float) $produto->preco <> $preco,
                ];
                if ((float) $produto->preco > (float) $preco) {
                    $produtos_alterados[] = sprintf(
                        "Produto: %s | Preço Original: %s | Preço Alterado: %s | QTD: %s",
                        $produto->produto->descricao,
                        formatMoedaReal($produto->preco),
                        formatMoedaReal($preco),
                        $item['quantidade']
                    );
                }
            }

            $data_pedido['total_itens'] = array_sum(array_column($data_items, 'quantidade'));
            $data_pedido['valor_total'] = array_sum(array_column($data_items, 'valor_total'));
            $data_pedido['peso_total']  = array_sum(array_column($data_items, 'peso_total'));


            if ($request->input('tipo_descarga') == 1 && ceil($data_pedido['peso_total'] / 1000) > 1) {
                $data_pedido['valor_descarga'] = (int) ceil($data_pedido['peso_total'] / 1000) * realToFloat($request->input('valor_descarga'));
            }

            $data_pedido['debitos_cliente'] = $pedido->cliente->debitos_grupo;

            $pedido->update($data_pedido);
            foreach ($data_items as $item) {
                $pedido->items()->updateOrCreate(
                    [
                        'codigo_produto' => $item['codigo_produto'],
                    ],
                    $item
                );
            }
            // Delete items not in request data
            $pedido->items()->whereNotIn('codigo_produto', array_column($data_items, 'codigo_produto'))->delete();

            // Atualizar ou criar endereço personalizado se fornecido
            if ($request->filled('endereco_entrega.cep')) {
                $pedido->endereco()->updateOrCreate([], [
                    'cep' => str_replace('-', '', $request->input('endereco_entrega.cep')),
                    'endereco' => $request->input('endereco_entrega.endereco'),
                    'numero' => $request->input('endereco_entrega.numero'),
                    'bairro' => $request->input('endereco_entrega.bairro'),
                    'cidade' => $request->input('endereco_entrega.cidade'),
                    'city_id' => $request->input('endereco_entrega.city_id'),
                    'uf' => $request->input('endereco_entrega.uf'),
                    'complemento' => $request->input('endereco_entrega.complemento'),
                    'latitude' => $request->input('endereco_entrega.latitude'),
                    'longitude' => $request->input('endereco_entrega.longitude'),
                ]);
            } else {
                // Se não há endereço personalizado, remover se existir
                $pedido->endereco()->delete();
            }

            $this->setBloqueios($produtos_alterados, $pedido, $pedido->cliente);


            DB::commit();
            flash('Pedido atualizado com sucesso!', 'success');
            return redirect()->route('pedidos.edit', $pedido);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getLine());
            flash('Erro ao criar pedido!', 'error');

            return redirect()->route('pedidos.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Pedido $pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pedido $pedido)
    {
        //
    }

    public function cancelar(Pedido $pedido, Request $request)
    {
        $request->validate([
            'observacoes_cancelamento' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $pedido->status                   = Pedido::STATUS_CANCELADO;
            $pedido->observacoes_cancelamento = $request->input('observacoes_cancelamento');
            $pedido->save();
            DB::commit();
            flash('Pedido cancelado com sucesso!', 'success');

            return redirect()->route('pedidos.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Bugsnag::notifyException($e);
            flash('Erro ao cancelar pedido!', 'error');

            return redirect()->route('pedidos.index');
        }
    }

    public function updateSequencia(Request $request)
    {
        $request->validate([
            'pedido_id'       => 'required|exists:pedidos,id',
            'sequencia'       => 'required|integer',
            'reordenar_todos' => 'required',
        ]);
        try {
            $pedido                    = Pedido::find($request->input('pedido_id'));
            $pedido->sequencia_entrega = $request->input('sequencia');
            $pedido->save();


            $pedidos = [];
            if ($request->input('reordenar_todos')) {
                $pedidos = Pedido::select('sequencia_entrega', 'id')
                    ->where('data_entrega', $pedido->data_entrega)
                    ->where('numero_veiculo', $pedido->numero_veiculo)
                    ->orderBy('sequencia_entrega', 'ASC')
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                foreach ($pedidos as $key => $pedido) {
                    $pedido->sequencia_entrega = $key + 1;
                    $pedido->save();
                }
                $pedidos = $pedidos->toArray();
            }

            return response()->json(['message' => 'Sequência atualizada com sucesso!', 'pedidos' => $pedidos]);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function exportar(Request $request)
    {
        $request->validate([
            'exportado' => 'required|in:0,1',
        ]);

        $query = PedidoItem::sortable()
            ->selectRaw("
       pedidos.id                                                          as ID,
       '0' || produto_preco_cortes.cod_filial                              as FILIAL,
       null                                                                as OPER,
       pedidos.id                                                          as PEDIDO,
       null                                                                as DTENTREGA,
       substr(clientes.codigo::text, 1, length(clientes.codigo::text) - 4) as CODCLI,
       substr(clientes.codigo::text, 7, length(clientes.codigo::text) - 4) as CODLOJA,
       clientes.cpf_cgc                                                    as CNPJ,
       regexp_replace(clientes.nome, '[^a-zA-Z0-9\s]', '', 'g')            as NOME,
       produtos.codigo                                                     as CODPROD,
       regexp_replace(produtos.descricao, '[^a-zA-Z0-9\s]', '', 'g')       as DESCPROD,
       produto_preco_cortes.quantidade                                     as QTDE,
       produto_preco_cortes.valor_unitario                                 as VALOR,
       case when pedidos.pedido_compra is not null then pedidos.pedido_compra else '0' end                                 as PEDIDO_COMPRA,
       CASE 
        WHEN pedidos_endereco.id IS NOT NULL THEN 
          CONCAT(
            CASE WHEN observacoes IS NOT NULL THEN regexp_replace(observacoes, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g') ELSE '' END,
            CASE WHEN observacoes IS NOT NULL THEN ' | ' ELSE '' END,
            'Endereco de Entrega: ',
            regexp_replace(pedidos_endereco.endereco, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g'), ' ',
            pedidos_endereco.numero, ', ',
            regexp_replace(pedidos_endereco.bairro, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g'), ', ',
            regexp_replace(pedidos_endereco.cidade, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g'), ' - ',
            pedidos_endereco.uf, ', CEP: ',
            pedidos_endereco.cep,
            CASE WHEN pedidos_endereco.complemento IS NOT NULL AND pedidos_endereco.complemento != '' 
              THEN CONCAT(', Complemento: ', regexp_replace(pedidos_endereco.complemento, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g'))
              ELSE '' 
            END
          )
        ELSE 
          CASE WHEN observacoes IS NOT NULL THEN regexp_replace(observacoes, E'[\\n\\r]+|[^a-zA-Z0-9\s]', ' ', 'g') ELSE '' END
        END as OBS,
       codigo_vendedor                                                     as VENDEDOR,
       null                                                                as CONDPGTO,
       null                                                                as TABPRECO,
       CASE 
        WHEN tipo_pedido = 1 
            THEN filiais.locais -> pedidos.cod_local::text ->> 'tipo_pedido'
        ELSE LPAD(pedidos.tipo_pedido::text, 2, '0') 
       END                                                                 as TES,
      LPAD(
        (case 
            when produto_preco_cortes.cod_local = 15 then 1 
            else produto_preco_cortes.cod_local 
            end)::text, 
        2, '0' ) as LOCAL,
       case when exported_at is not null then 1 else 0 end                 as IMPORT
        ")
            ->join('pedidos', 'pedidos.id', 'pedido_items.pedido_id')
            ->join('clientes', 'clientes.codigo', 'pedidos.codigo_cliente')
            ->join('filiais', 'filiais.codigo', 'pedidos.cod_filial')
            ->join('produto_preco_cortes', function ($join) {
                $join->on('produto_preco_cortes.pedido_id', 'pedidos.id')
                    ->on('produto_preco_cortes.codigo_produto', 'pedido_items.codigo_produto');
            })
            ->join('produtos', 'produtos.codigo', DB::raw('coalesce(produto_preco_cortes.cod_produto_substituido, produto_preco_cortes.codigo_produto)'))
            ->leftJoin('pedidos_endereco', 'pedidos_endereco.pedido_id', 'pedidos.id')
            ->where('produto_preco_cortes.status', true)
            ->where('pedido_items.deleta_do_corte', false)
            ->whereNotIn("pedidos.status", [Pedido::STATUS_BLOQUEADO, Pedido::STATUS_CANCELADO])
            ->when($request->input('exportado') == 0, function ($q) use ($request) {
                $q->whereNull('pedidos.exported_at');
            })
            ->when(!auth()->user()->hasRole(['admin', 'supervisor']), function ($q) {
                $q->where('pedidos.codigo_vendedor', auth()->user()->codigo);
            })
            ->when($request->filled('data_pedido'), function ($q) use ($request) {
                $date = Carbon::createFromFormat('d/m/Y', $request->input('data_pedido'))->format('Y-m-d');
                $q->whereDate('pedidos.created_at', $date);
            })
            ->when($request->filled('data_entrega'), function ($q) use ($request) {
                $date = Carbon::createFromFormat('d/m/Y', $request->input('data_entrega'))->format('Y-m-d');
                $q->where('pedidos.data_entrega', $date);
            }, function ($q) {
                $q->where('pedidos.data_entrega', Carbon::now()->addDay(1)->format('Y-m-d'));
            })
            ->when($request->filled('cod_vendedores'), function ($q) use ($request) {
                $q->whereIn('pedidos.codigo_vendedor', $request->input('cod_vendedores'));
            })
            ->when($request->filled('rota_id'), function ($q) use ($request) {
                $q->where('clientes.rota_id', $request->input('rota_id'));
            })
            ->when($request->filled('cod_filial'), fn($q) => $q->where('pedidos.cod_filial', $request->input('cod_filial')))
            ->when($request->filled('cod_local'), fn($q) => $q->where('pedidos.cod_local', $request->input('cod_local')))
            ->when($request->filled('numero_veiculo'), fn($q) => $q->where('pedidos.numero_veiculo', $request->input('numero_veiculo')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($subq) use ($request) {
                    $search = trim($request->input('search'));
                    if ((int) $search > 0) {
                        $subq->where('pedidos.id', (int) $search);
                        $subq->orWhere('clientes.cpf_cgc', 'like', strtoupper("%{$search}%"));
                    }
                    $subq->orWhere(DB::raw('upper(clientes.nome)'), 'like', strtoupper("%{$search}%"));
                });
            })->get();


        // update all pedidos exportados
        if (!auth()->user()->hasRole(['vendedor'])) {
            Pedido::whereIn('id', $query->pluck('id'))->update(['exported_at' => now()]);
        }

        return Excel::download(new PedidosExport($query), sprintf("pedidos_%s_%s.csv", formatDate($request->input('data_entrega')), time()), \Maatwebsite\Excel\Excel::TSV);
    }

    /**
     * @param array $produtos_alterados
     * @param $pedido
     * @param $cliente
     * @return void
     */
    public function setBloqueios(array $produtos_alterados, $pedido, $cliente): void
    {
        // se a data de entrega é posterior a "amanhã"
        if (Carbon::parse($pedido->data_entrega)->gt(Carbon::now()->addDay(1))) {
            $pedido->bloqueios()->updateOrCreate(
                [
                    'tipo' => PedidoBloqueio::PEDIDO_PROGRAMADO
                ],
                [
                    'descricao' => sprintf('Pedido programado para %s', formatDate($pedido->data_entrega))
                ]
            );


            $pedido->status = Pedido::STATUS_BLOQUEADO;
            $pedido->save();
            return ;
        }


        if (count($produtos_alterados) > 0) {
            $pedido->bloqueios()->updateOrCreate(
                [
                    'tipo' => PedidoBloqueio::PRECO_ALTERADO
                ],
                [
                    'descricao' => implode("<br>", $produtos_alterados)
                ]
            );


            $pedido->status = Pedido::STATUS_BLOQUEADO;
            $pedido->save();
        }

        if ($cliente->limite_disponivel <  $pedido->valor_total) {
            $pedido->bloqueios()->updateOrCreate(
                [
                    'tipo' => PedidoBloqueio::LIMITE_CREDITO_EXCEDIDO
                ],
                [
                    'descricao' => sprintf(
                        'Limite de %s | Consumido %s | Disponível %s | Valor do Pedido %s |  Excedido %s',
                        formatMoedaReal($cliente->limite_credito),
                        formatMoedaReal($cliente->limite_consumido),
                        formatMoedaReal($cliente->limite_disponivel),
                        formatMoedaReal($pedido->valor_total),
                        formatMoedaReal($cliente->limite_disponivel - $pedido->valor_total)
                    )
                ]
            );

            $pedido->status = Pedido::STATUS_BLOQUEADO;
            $pedido->save();
        }

        if ($cliente->debitos_grupo > 0) {
            $pedido->bloqueios()->updateOrCreate(
                [
                    'tipo' => PedidoBloqueio::DEBITO_ABERTO
                ],
                [
                    'descricao' => sprintf('Cliente com %s em débitos em aberto', formatMoedaReal($cliente->debitos_grupo))
                ]
            );

            $pedido->status = Pedido::STATUS_BLOQUEADO;
            $pedido->save();
        }

    }
}
