@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <div class="tv-header">
            <h1>Faturamento</h1>
            <div class="subtitle">
                <span>Dashboard em Tempo Real</span>
                <div class="live-indicator position-absolute" style="right: 1%;top:40px">
                    <div class="pulse-dot"></div>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Métricas Principais -->
        <div class="row g-4 mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-receipt"></i></div>
                    <div class="metric-value" id="total-notas">{{ $dashboard_geral->notas }}</div>
                    <div class="metric-label">Notas Faturadas</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-users"></i></div>
                    <div class="metric-value" id="total-clientes">{{ $dashboard_geral->clientes  }}</div>
                    <div class="metric-label">Clientes Atendidos</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="metric-value" id="vendedores-ativos">{{ $dashboard_geral->vendedores_ativos }}</div>
                    <div class="metric-label">Vendedores</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-box"></i></div>
                    <div class="metric-value" id="produtos-vendidos">{{ $produtos_vendidos->produtos }}</div>
                    <div class="metric-label">Produtos</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-calculator"></i></div>
                    <div class="metric-value" id="valor-medio">R$ {{ number_format($dashboard_geral->valor_medio ?? 0, 2, ',', '.') }}</div>
                    <div class="metric-label">Ticket Médio</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="metric-value" id="valor-total">R$ {{ number_format($dashboard_geral->valor_liquido ?? 0, 2, ',', '.') }}</div>
                    <div class="metric-label">Faturamento</div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Listas -->
        <div class="row g-4 mb-4">
            <!-- Vendas por Hora -->
            <div class="col-xl-12 col-lg-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-clock text-primary"></i>
                            Faturamento Ult. 7 Dias
                        </h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="vendasPorHoraChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mb-4">

            <!-- Performance dos Vendedores -->
            <div class="col-xl-6 col-lg-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-trophy text-warning"></i>
                            Top 5 Vendedores Hoje
                        </h3>
                    </div>
                    <div class="chart-body">
                        <ul class="styled-list">
                            @foreach($vendedores_performance as $vendedor)
                                <li class="styled-list-item">
                                    <div class="list-item-content">
                                        <div class="list-item-title fs-2">{{ $vendedor->vendedor }} | Notas {{ $vendedor->notas }} | Clientes {{ $vendedor->clientes }}</div>
                                    </div>
                                    <div class="list-item-value fs-1">R$ {{ number_format($vendedor->valor_liquido, 0, ',', '.') }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Top Clientes -->
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-star text-success"></i>
                            Top 5 Clientes Hoje
                        </h3>
                    </div>
                    <div class="chart-body">
                        <ul class="styled-list">
                            @foreach($top_clientes->take(5) as $cliente)
                                <li class="styled-list-item">
                                    <div class="list-item-content">
                                        <div class="list-item-title fs-2">{{ Str::limit($cliente->cliente, 50) }} | Notas {{ $cliente->notas }}</div>
                                    </div>
                                    <div class="list-item-value fs-1">R$ {{ number_format($cliente->valor_liquido, 0, ',', '.') }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Produtos Mais Vendidos (KG) -->
            <div class="col-xl-6 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-fire text-danger"></i>
                            Top 5 Produtos Mais Vendidos (KG)
                        </h3>
                    </div>
                    <div class="chart-body">
                        <ul class="styled-list">
                            @foreach($produtos_mais_vendidos->sortByDesc('quantidade_total')->take(8) as $produto)
                                <li class="styled-list-item">
                                    <div class="list-item-content">
                                        <div class="list-item-title fs-2">{{ Str::limit($produto->desc_produto, 50) }}</div>
                                    </div>
                                    <div class="list-item-value fs-1">{{ number_format($produto->quantidade_total, 1, ',', '.') }} kg</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Produtos Mais Vendidos (R$) -->
            <div class="col-xl-6 col-lg-12">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-bolt text-info"></i>
                            Top 5 Produtos Mais Vendidos (R$)
                        </h3>
                    </div>
                    <div class="chart-body">
                        <ul class="styled-list">
                            @foreach($produtos_mais_vendidos->sortByDesc('valor_total')->take(8) as $produto)
                                <li class="styled-list-item">
                                    <div class="list-item-content">
                                        <div class="list-item-title fs-1">{{ Str::limit($produto->desc_produto, 50) }}</div>
                                    </div>
                                    <div class="list-item-value fs-1">R${{ number_format($produto->valor_total, 2, ',', '.') }}</div>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Removido: Vendas por Área --}}
        {{-- @if($vendas_por_area->count() > 0) --}}
        {{-- Removido: Vendas por Área --}}
        {{-- @if($vendas_por_area->count() > 0) ... @endif --}}
        {{-- Removido: dados de áreas --}}
        {{-- const areasData = @json($vendas_por_area); --}}
    </div>
@endsection
@section('scripts')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de Vendas por Hora
            Chart.register(window.ChartDataLabels);
            const vendasUltimos7Dias = @json($vendas_ultimos_7_dias);
            const ctxHoras = document.getElementById('vendasPorHoraChart').getContext('2d');
            const chart = new Chart(ctxHoras, {
                type: 'line',
                data: {
                    labels: Object.keys(vendasUltimos7Dias),
                    datasets: [{
                        label: 'Faturamento (R$)',
                        data: Object.values(vendasUltimos7Dias).map(v => v.valor_liquido),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        datalabels: {
                            color: '#fff',
                            anchor: 'end',
                            align: 'top',
                            font: {weight: 'bold', size: 26},
                            formatter: function (value) {
                                return 'R$ ' + Number(value).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                        }
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: false,
                            mode: 'index',
                            intersect: false
                        },
                        datalabels: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            ticks: {color: '#ffffff',font: {size: 15}},
                            grid: {color: 'rgba(255,255,255,0.1)'}
                        },
                        x: {
                            ticks: {color: '#ffffff', font: {size: 20, weight: 'bold'}},
                            grid: {color: 'rgba(255,255,255,0.1)'},
                            offset: true,
                            padding: 0
                        }
                    }
                },
                plugins: [window.ChartDataLabels]
            });
        });
    </script>
@endsection
