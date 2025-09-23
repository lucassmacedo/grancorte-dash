<x-default-layout>
    @section('breadcrumbs-actions')
        <div>
            <a class="btn btn-success btn-sm" id="btn-exportar" href="{{ route('relatorios.estoque.saldo-tunel',array_merge(request()->query(), ['exportar' => 'excel'])) }}">
                <i class="fa fa-file-excel"></i>
                Exportar EXCEL
            </a>
        </div>
    @endsection

    @section('title')
        Relatório Estoque - Saldo em TúnelC
    @endsection

    <div class="card card-flush mb-5">
        <div class="card-header">
            <h3 class="card-title">Filtros de Busca</h3>
        </div>
        <div class="card-body pt-0">
            <form action="" method="get">
                <div class="row">
                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="grupo_estoque">Grupo Estoque</label>
                        {{ Form::select('grupo_estoque', $grupos_estoque->prepend('Todos', ''), $filtros['grupo_estoque'], ['class' => 'form-control', 'data-control' => 'select2', 'placeholder' => 'Selecione um grupo']) }}
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="cod_produto">Código Produto</label>
                        {{ Form::text('cod_produto', $filtros['cod_produto'], ['class' => 'form-control', 'placeholder' => 'Digite o código']) }}
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="desc_produto">Descrição Produto</label>
                        {{ Form::text('desc_produto', $filtros['desc_produto'], ['class' => 'form-control', 'placeholder' => 'Digite a descrição']) }}
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="tipo_conservacao">Tipo Conservação</label>
                        {{ Form::select('tipo_conservacao', $tipos_conservacao->prepend('Todos', ''), $filtros['tipo_conservacao'], ['class' => 'form-control', 'data-control' => 'select2', 'placeholder' => 'Selecione o tipo']) }}
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="local_estoque">Local Estoque</label>
                        {{ Form::select('local_estoque', $locais_estoque->prepend('Todos', ''), $filtros['local_estoque'], ['class' => 'form-control', 'data-control' => 'select2', 'placeholder' => 'Selecione o local']) }}
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6">
                        <label for="ean">EAN</label>
                        {{ Form::text('ean', $filtros['ean'], ['class' => 'form-control', 'placeholder' => 'Digite o EAN']) }}
                    </div>

                    <div class="form-group col-lg-6 col-md-4 col-sm-12 pt-6">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                        @if (request()->query())
                            <a class="btn btn-danger" href="{{ route('relatorios.estoque.saldo-tunel') }}">
                                <i class="fa fa-trash"></i> Limpar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-5">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-info">
                <div class="card-body text-center p-3">
                    <i class="fa fa-boxes fs-2x text-info mb-3"></i>
                    <h3 class="text-info">{{ number_format($totais['total_registros']) }}</h3>
                    <p class="text-muted mb-0">Total de Produtos</p>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-primary">
                <div class="card-body text-center p-3">
                    <i class="fa fa-weight fs-2x text-primary mb-3"></i>
                    <h3 class="text-primary">{{ number_format($totais['total_saldo_total'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Saldo Total (KG)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-warning">
                <div class="card-body text-center p-3">
                    <i class="fa fa-warehouse fs-2x text-warning mb-3"></i>
                    <h3 class="text-warning">{{ number_format($totais['total_saldo_tunel'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Saldo Túnel (KG)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-success">
                <div class="card-body text-center p-3">
                    <i class="fa fa-shopping-cart fs-2x text-success mb-3"></i>
                    <h3 class="text-success">{{ number_format($totais['total_saldo_p_venda'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Saldo P/ Venda (KG)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-secondary">
                <div class="card-body text-center p-3">
                    <i class="fa fa-plus-circle fs-2x text-secondary mb-3"></i>
                    <h3 class="text-secondary">{{ number_format($totais['total_saldo_aux'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Saldo Auxiliar (KG)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="card bg-light-dark">
                <div class="card-body text-center p-3">
                    <i class="fa fa-exchange-alt fs-2x text-dark mb-3"></i>
                    <h3 class="text-dark">{{ number_format($totais['total_saldo_tunel_aux'], 2, ',', '.') }}</h3>
                    <p class="text-muted mb-0">Túnel Auxiliar (KG)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Dados -->
    <div class="card card-flush">
        <div class="card-header">
            <h3 class="card-title">Relatório Estoque - Saldo em Túnel</h3>
            <div class="card-toolbar">
                <span class="badge badge-light-primary fs-6">
                    Emissão: {{ date('d/m/Y H:i') }}
                </span>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover align-middle fs-6 gy-2" id="kt_estoque_saldo_tunel_table" style="position: relative;">
                    <thead class="bg-light" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa !important;">
                        <tr class="text-start text-gray-600 fw-bold fs-7 text-uppercase gs-0" style="background-color: #f8f9fa !important;">
                            <th class="min-w-80px">Produto</th>
                            <th class="min-w-300px">Descrição</th>
                            <th class="min-w-150px">Tipo Conservação</th>
                            <th class="min-w-80px text-center">EAN</th>
                            <th class="min-w-100px text-center">Disponível</th>
                            <th class="min-w-80px text-center">Em Túnel</th>
                            <th class="min-w-80px text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @php
                            $currentGroup = '';
                            $groupTotals = [
                                'saldo_total' => 0,
                                'saldo_tunel' => 0,
                                'saldo_p_venda' => 0,
                                'saldo_aux' => 0,
                                'saldo_disponivel_venda_aux' => 0,
                                'saldo_tunel_aux' => 0,
                            ];
                        @endphp

                        @foreach ($data as $item)
                            @php
                                // Detectar mudança de grupo
                                if ($currentGroup !== $item->GRUPO_ESTOQUE) {
                                    // Exibir totais do grupo anterior
                                    if ($currentGroup !== '') {
                                        echo '<tr class="bg-light-primary">
                                                <td colspan="4" class="fw-bold">Total: ' .
                                            '</td>
                                                <td class="text-center fw-bold">' .
                                            number_format($groupTotals['saldo_p_venda'], 2, ',', '.') .
                                            ' KG</td>
                                                <td class="text-center fw-bold">' .
                                            number_format($groupTotals['saldo_tunel'], 2, ',', '.') .
                                            ' KG</td>
                                                <td class="text-center fw-bold">' .
                                            number_format($groupTotals['saldo_total'], 2, ',', '.') .
                                            ' KG</td>
                                              </tr>';
                                    }

                                    // Resetar totais para novo grupo
                                    $groupTotals = [
                                        'saldo_total' => 0,
                                        'saldo_tunel' => 0,
                                        'saldo_p_venda' => 0,
                                        'saldo_aux' => 0,
                                        'saldo_disponivel_venda_aux' => 0,
                                        'saldo_tunel_aux' => 0,
                                    ];

                                    // Novo cabeçalho de grupo
                                    $currentGroup = $item->GRUPO_ESTOQUE;
                                    echo '<tr class="bg-primary">
                                            <td colspan="7" class="fw-bold text-white fs-6">' .
                                        $item->GRUPO_ESTOQUE .
                                        '</td>
                                          </tr>';
                                }

                                // Acumular totais do grupo
                                $groupTotals['saldo_total'] += $item->SALDO_TOTAL ?? 0;
                                $groupTotals['saldo_tunel'] += $item->SALDO_TUNEL ?? 0;
                                $groupTotals['saldo_p_venda'] += $item->SALDO_DISPONIVEL_VENDA_KG?? 0;
                                $groupTotals['saldo_aux'] += $item->SALDO_AUX ?? 0;
                                $groupTotals['saldo_disponivel_venda_aux'] += $item->SALDO_DISPONIVEL_VENDA_AUX ?? 0;
                                $groupTotals['saldo_tunel_aux'] += $item->SALDO_TUNEL_AUX ?? 0;
                            @endphp

                            <tr>
                                <td>{{ $item->COD_PRODUTO }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $item->DESC_PRODUTO }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted fs-7">{{ $item->TIPO_CONSERVACAO }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $item->EAN ?: '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-muted">{{ number_format($item->SALDO_DISPONIVEL_VENDA_KG?? 0, 2, ',', '.') }}
                                        {{ $item->COD_UNIDADE_PRI }}</span>
                                    @if ($item->SALDO_DISPONIVEL_VENDA_AUX > 0)
                                        <div class="text-muted">
                                            <small>{{ number_format($item->SALDO_DISPONIVEL_VENDA_AUX, 2, ',', '.') }}
                                                {{ $item->COD_UNIDADE_AUX }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->SALDO_TUNEL > 0)
                                        <span
                                            class="fw-bold text-warning">{{ number_format($item->SALDO_TUNEL, 2, ',', '.') }}
                                            {{ $item->COD_UNIDADE_PRI }}</span>
                                    @else
                                        <span class="text-muted">0,00</span>
                                    @endif
                                    @if ($item->SALDO_TUNEL_AUX > 0)
                                        <div class="text-warning">
                                            <small>{{ number_format($item->SALDO_TUNEL_AUX, 2, ',', '.') }}
                                                {{ $item->COD_UNIDADE_AUX }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ number_format($item->SALDO_TOTAL ?? 0, 2, ',', '.') }}
                                        {{ $item->COD_UNIDADE_PRI }}</span>
                                    @if ($item->SALDO_AUX > 0)
                                        <div class="text-info">
                                            <small>{{ number_format($item->SALDO_AUX, 2, ',', '.') }}
                                                {{ $item->COD_UNIDADE_AUX }}</small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @php
                            // Exibir total do último grupo
                            if ($currentGroup !== '') {
                                echo '<tr class="bg-light-primary">
                                        <td colspan="4" class="fw-bold">Total: ' .
                                    '</td>
                                        <td class="text-center fw-bold">' .
                                    number_format($groupTotals['saldo_p_venda'], 2, ',', '.') .
                                    ' KG</td>
                                        <td class="text-center fw-bold">' .
                                    number_format($groupTotals['saldo_tunel'], 2, ',', '.') .
                                    ' KG</td>
                                        <td class="text-center fw-bold">' .
                                    number_format($groupTotals['saldo_total'], 2, ',', '.') .
                                    ' KG</td>
                                      </tr>';
                            }
                        @endphp

                        <!-- Total Geral -->
                        <tr class="bg-dark">
                            <td colspan="4" class="fw-bold text-white">TOTAL GERAL</td>
                            <td class="text-center fw-bold text-white">
                                {{ number_format($totais['total_saldo_p_venda'], 2, ',', '.') }} KG</td>
                            <td class="text-center fw-bold text-white">
                                {{ number_format($totais['total_saldo_tunel'], 2, ',', '.') }} KG</td>
                            <td class="text-center fw-bold text-white">
                                {{ number_format($totais['total_saldo_total'], 2, ',', '.') }} KG</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Cabeçalho fixo da tabela */
            #kt_estoque_saldo_tunel_table thead th {
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: #f8f9fa !important;
                border-bottom: 2px solid #dee2e6;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            }

            /* Container da tabela com altura fixa */
            .table-responsive {
                max-height: 70vh;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 0.475rem;
            }

            /* Ajustar cores dos grupos */
            .bg-primary td {
                background-color: #009ef7 !important;
                color: white !important;
            }

            .bg-light-primary td {
                background-color: #f1faff !important;
                color: #009ef7 !important;
            }

            .bg-dark td {
                background-color: #1e1e2d !important;
                color: white !important;
            }

            /* Melhorar visualização da tabela */
            #kt_estoque_saldo_tunel_table tbody tr:hover {
                background-color: #f8f9fa !important;
            }

            /* Evitar hover nos totais */
            #kt_estoque_saldo_tunel_table tbody tr.bg-light-primary:hover,
            #kt_estoque_saldo_tunel_table tbody tr.bg-primary:hover,
            #kt_estoque_saldo_tunel_table tbody tr.bg-dark:hover {
                background-color: inherit !important;
            }

            /* Scrollbar customizada */
            .table-responsive::-webkit-scrollbar {
                width: 8px;
            }

            .table-responsive::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }

            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Melhor separação visual */
            #kt_estoque_saldo_tunel_table tbody tr {
                border-bottom: 1px solid #eff2f5;
            }
        </style>
    @endpush
</x-default-layout>
