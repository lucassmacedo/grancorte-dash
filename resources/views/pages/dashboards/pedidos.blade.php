@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <!-- Header -->
        <div class="tv-header">
            <h1>Pedidos</h1>
            <div class="subtitle">
                <span>Dashboard em Tempo Real</span>
                <div class="live-indicator position-absolute" style="right: 1%;top:40px">
                    <div class="pulse-dot"></div>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <!-- Métricas Principais -->
        <div class="row g-4 mb-5">
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card">

                    <div class="metric-value">{{ number_format($dashboard['total_pedidos'] ?? 0) }}</div>
                    <div class="metric-label">Pedidos</div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card variant-1">

                    <div class="metric-value">{{ number_format($dashboard['total_kg_pedidos'] ?? 0, 2, ',', '.') }} kg</div>
                    <div class="metric-label">Total KG</div>
                </div>
            </div>
            {{--            <div class="col-xl col-lg-4 col-md-6">--}}
            {{--                <div class="metric-card variant-2">--}}

            {{--                    <div class="metric-value">R$ {{ number_format($dashboard['previsao_faturamento'] ?? 0, 2, ',', '.') }}</div>--}}
            {{--                    <div class="metric-label">Previsão Faturamento</div>--}}
            {{--                </div>--}}
            {{--            </div>--}}


            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card">

                    <div class="metric-value">{{ number_format($dashboard['total_carcacas_vendidas'] ?? 0,0,'.','.') }}</div>
                    <div class="metric-label">Carcaças Vendidas</div>
                </div>
            </div>

            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card variant-4">

                    <div class="metric-value">{{ number_format($dashboard['pedidos_faturados'] ?? 0) }}</div>
                    <div class="metric-label">Faturados</div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-5">

            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card variant-1">

                    <div class="metric-value">{{ number_format($dashboard['pedidos_cancelados'] ?? 0) }}</div>
                    <div class="metric-label">Cancelados</div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">

                <div class="metric-card variant-1">

                    <div class="metric-value">{{ number_format($dashboard['pedidos_bloqueados'] ?? 0) }}</div>
                    <div class="metric-label">Bloqueados</div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card variant-4">

                    <div class="metric-value">{{ number_format($dashboard['pedidos_baixados'] ?? 0) }}</div>
                    <div class="metric-label">Baixados</div>
                </div>
            </div>
            <div class="col-xl col-lg-4 col-md-6">
                <div class="metric-card variant-3">

                    <div class="metric-value">{{ number_format($dashboard['pedidos_em_aberto'] ?? 0) }}</div>
                    <div class="metric-label">Em Aberto</div>
                </div>
            </div>
        </div>
        <!-- Evolução de Vendas -->
        <div class="row g-4 mb-5">
            {{--        <div class="col-xl-6 col-lg-6">--}}
            {{--            <div class="chart-card">--}}
            {{--                <div class="chart-header">--}}
            {{--                    <h3 class="chart-title"><i class="fas fa-chart-line"></i> Evolução de Vendas (7 dias)</h3>--}}
            {{--                </div>--}}
            {{--                <div class="chart-content">--}}
            {{--                    <canvas id="evolucaoVendasChart"></canvas>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--        </div>--}}
            <!-- Ranking Vendedores Valor -->
            <div class="row g-4 mb-5">
                @if($tabela === 'valor')
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title"><i class="fas fa-trophy"></i> Top 10 Vendedores (Valor)</h3>
                            </div>
                            <div class="table-container">
                                <table class="elegant-table">
                                    <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Total Vendido</th>
                                        <th>QTD</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dashboard['ranking_vendedores_valor'] as $vendedor)
                                        <tr>
                                            <td>{{ $vendedor->codigo_vendedor }} - {{ $vendedor->vendedor }}</td>
                                            <td>R$ {{ number_format($vendedor->total, 2, ',', '.') }}</td>
                                            <td>{{ number_format($vendedor->qtd, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($tabela === 'carcaca')
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title"><i class="fas fa-drumstick-bite"></i> Top 10 Vendedores (Carcaça)</h3>
                            </div>
                            <div class="table-container">
                                <table class="elegant-table">
                                    <thead>
                                    <tr>
                                        <th>Vendedor</th>
                                        <th>Total Vendido</th>
                                        <th>QTD</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dashboard['ranking_vendedores_carcaca'] as $vendedor)
                                        <tr>
                                            <td>{{ $vendedor->codigo_vendedor }} - {{ $vendedor->vendedor }}</td>
                                            <td>R$ {{ number_format($vendedor->total, 2, ',', '.') }}</td>
                                            <td>{{ number_format($vendedor->qtd, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($tabela === 'rota')
                    <div class="col-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title"><i class="fas fa-route"></i> Top 10 Pedidos por Rota</h3>
                            </div>
                            <div class="table-container">
                                <table class="elegant-table">
                                    <thead>
                                    <tr>
                                        <th>Rota</th>
                                        <th>Total Vendido</th>
                                        <th>QTD Pedidos</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dashboard['top_pedidos_por_rota'] as $rota)
                                        <tr>
                                            <td>{{ $rota->codigo }} - {{ $rota->rota_nome }}</td>
                                            <td>R$ {{ number_format($rota->total, 2, ',', '.') }}</td>
                                            <td>{{ number_format($rota->qtd_pedidos, 0, ',', '.') }}</td>
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
