@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <div class="tv-header">
            <h1>Comercial - Produtos</h1>
            <div class="subtitle">
                <span>Dashboard em Tempo Real</span>
                <div class="live-indicator position-absolute" style="right: 1%;top:40px">
                    <div class="pulse-dot"></div>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl col-lg-3 col-md-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-produtos">{{ $dashboard_geral->notas ?? '-' }}</div>
                    <div class="metric-label">Notas Faturadas</div>
                </div>
            </div>
            <div class="col-xl col-lg-3 col-md-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-produtos">{{ $produtos_vendidos->produtos }}</div>
                    <div class="metric-label">Clientes Atendidos</div>
                </div>
            </div>

            <div class="col-xl col-md-3 col-sm-6">
                <div class="metric-card">

                    <div class="metric-value" id="total-clientes">{{ $dashboard_geral->clientes  }}</div>
                    <div class="metric-label">Clientes</div>
                </div>
            </div>
            <div class="col-xl col-md-3 col-sm-6">
                <div class="metric-card">

                    <div class="metric-value ">R$ {{ number_format($dashboard_geral->valor_medio ?? 0, 2, ',', '.') }}</div>
                    <div class="metric-label">Ticket Médio</div>
                </div>
            </div>
            <div class="col-xl col-md-3 col-sm-6">
                <div class="metric-card">

                    <div class="metric-value ">R$ {{ number_format($dashboard_geral->valor_liquido ?? 0, 2, ',', '.') }}</div>
                    <div class="metric-label">Faturamento</div>
                </div>
            </div>
        </div>


        <div class="row g-4 mb-5">

            <!-- Top Produtos -->
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-trophy"></i>
                            Top 5 Produtos
                        </h1>
                    </div>
                    <div class="table-container">
                        <table class="elegant-table">
                            <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Notas</th>
                                <th>QTD</th>
                                <th>Valor Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($produtos_performance as $produto)
                                <tr>
                                    <td>{{ $produto->descricao }}</td>
                                    <td>{{ $produto->notas }}</td>
                                    <td>{{ $produto->quantidade_total }}</td>
                                    <td>R$ {{ number_format($produto->valor_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Produtos -->
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-trophy"></i>
                            Top 5 Produtos Mais Vendidos (Quantidade)
                        </h1>
                    </div>
                    <div class="table-container">
                        <table class="elegant-table">
                            <thead>
                            <tr>
                                <th>Produto</th>
                                <th>QTD</th>
                                <th>Valor Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($produtos_mais_vendidos as $produto)
                                <tr>
                                    <td>{{ $produto->descricao }}</td>
                                    <td>{{ $produto->quantidade_total }}</td>
                                    <td>R$ {{ number_format($produto->valor_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
@endsection