@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <div class="tv-header">
            <h1>Comercial - Clientes ({{ request()->get('atacado',true) ? 'Atacado' :'Varejo' }})</h1>
            <div class="subtitle">
                <span>Dashboard em Tempo Real</span>
                <div class="live-indicator position-absolute" style="right: 1%;top:40px">
                    <div class="pulse-dot"></div>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-5">
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-produtos">{{ $dashboard_geral->clientes ?? '-' }}</div>
                    <div class="metric-label">Total de Clientes </div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-produtos">{{ $dashboard_geral->notas ?? '-' }}</div>
                    <div class="metric-label">Total de Notas</div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-produtos">R$ {{ number_format($dashboard_geral->valor_medio ?? 0, 2, ',', '.') }}</div>
                    <div class="metric-label">Ticket Médio</div>
                </div>
            </div>
            {{--            <div class="col-xl col-lg-4 col-md-6">--}}
            {{--                <div class="metric-card">--}}

            {{--                    <div class="metric-value" id="total-produtos">R$ {{ number_format($dashboard_geral->valor_liquido ?? 0, 2, ',', '.') }}</div>--}}
            {{--                    <div class="metric-label">Faturamento</div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
        <div class="row g-4 mb-5">

            <!-- Tabelas Condicionais -->
            @if($tabela === 'clientes')
                <div class="col-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h1 class="chart-title">
                                <i class="fas fa-trophy"></i>
                                Top 5 Clientes ({{ request()->get('atacado',true) ? 'Atacado' :'Varejo' }})
                            </h1>
                        </div>
                        <div class="table-container">
                            <table class="elegant-table">
                                <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th class="text-center">Notas</th>
                                    <th class="text-center">Vl. Líquido</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clientes_performance as $cliente)
                                    <tr>
                                        <td class="fs-1">{{ $cliente->cliente }}</td>
                                        <td class="text-center" class="fs-1">{{ $cliente->notas }}</td>
                                        <td class="fs-1 text-center">R$ {{ number_format($cliente->valor_liquido, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($tabela === 'ramo')
                <div class="col-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h1 class="chart-title">
                                <i class="fas fa-industry"></i>
                                Top 5 Ramo de Atividade
                            </h1>
                        </div>
                        <div class="table-container">
                            <table class="elegant-table">
                                <thead>
                                <tr>
                                    <th>Ramo de Atividade</th>
                                    <th class="text-center">Notas</th>
                                    <th class="text-center">Vl. Líquido</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($top_ramo_atividade as $ramo)
                                    <tr>
                                        <td>{{ $ramo->ramo_atividade }}</td>
                                        <td class="text-center">{{ $ramo->notas }}</td>
                                        <td class="text-center">R$ {{ number_format($ramo->valor_liquido, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($tabela === 'area')
                <div class="col-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h1 class="chart-title">
                                <i class="fas fa-map-marked-alt"></i>
                                Top 5 Áreas
                            </h1>
                        </div>
                        <div class="table-container">
                            <table class="elegant-table">
                                <thead>
                                <tr>
                                    <th>Área</th>
                                    <th class="text-center">Notas</th>
                                    <th class="text-center">Vl. Líquido</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($top_areas as $area)
                                    <tr>
                                        <td>{{ $area->nome_area }}</td>
                                        <td class="text-center">{{ $area->notas }}</td>
                                        <td class="text-center">R$ {{ number_format($area->valor_liquido, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($tabela === 'cidade')
                <div class="col-12">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h1 class="chart-title">
                                <i class="fas fa-city"></i>
                                Top 20 Cidades
                            </h1>
                        </div>
                        <div class="table-container">
                            <table class="elegant-table">
                                <thead>
                                <tr>
                                    <th>Cidade</th>
                                    <th class="text-center">Notas</th>
                                    <th class="text-center">Vl. Líquido</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($top_cidades as $cidade)
                                    <tr>
                                        <td>{{ $cidade->cidade }}</td>
                                        <td class="text-center">{{ $cidade->notas }}</td>
                                        <td class="text-center">R$ {{ number_format($cidade->valor_liquido, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection