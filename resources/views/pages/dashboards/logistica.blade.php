@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <div class="tv-header">
            <h1>Logística</h1>
            <div class="subtitle">
{{--                <span>Dashboard em Tempo Real</span>--}}
                <div class="live-indicator position-absolute" style="right: 1%;top:40px">
                    <div class="pulse-dot"></div>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                        <div id="metric-cargas" class="metric-value">{{ number_format($metricas['cargas'] ?? 0, 0, ',', '.') }}</div>
                        <div class="metric-label">Cargas</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div id="metric-total-notas" class="metric-value">{{ number_format($metricas['total_notas'] ?? 0, 0, ',', '.') }}</div>
                        <div class="metric-label">Notas do Dia</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div id="metric-entregues" class="metric-value">{{ number_format($metricas['entregues'] ?? 0, 0, ',', '.') }}</div>
                        <div class="metric-label">Entregues</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-route"></i></div>
                        <div id="metric-andamento" class="metric-value">{{ number_format($metricas['andamento'] ?? 0, 0, ',', '.') }}</div>
                        <div class="metric-label">Em Andamento</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div id="metric-problemas" class="metric-value">{{ number_format($metricas['problemas'] ?? 0, 0, ',', '.') }}</div>
                        <div class="metric-label">Problemas</div>
                    </div>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-percent"></i></div>
                        <div id="metric-percentual" class="metric-value">{{ number_format($metricas['percentual_ok'] ?? 0, 1, ',', '.') }}%</div>
                        <div class="metric-label">Concluído</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-12 col-xl-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title"><i class="fa-solid fa-chart-pie"></i> Distribuição por Status</h3>
                        </div>
                        <div class="p-4">
                            <canvas id="statusPie" height="240"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title"><i class="fa-solid fa-warehouse"></i> Cargas por Filial</h3>
                        </div>
                        <div class="p-4">
                            <canvas id="filialPie" height="240"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title"><i class="fa-solid fa-triangle-exclamation"></i> Problemas x Resolvidos</h3>
                        </div>
                        <div class="p-4">
                            <canvas id="problemasPie" height="240"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-regular fa-clock"></i></div>
                        <div class="metric-value">{{ $metricas['tempo_medio_aguardando_fmt'] ?? '00:00' }}</div>
                        <div class="metric-label">Tempo Médio Aguardando</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-dolly"></i></div>
                        <div class="metric-value">{{ $metricas['tempo_medio_descarrego_fmt'] ?? '00:00' }}</div>
                        <div class="metric-label">Tempo Médio Descarregamento</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="metric-card">
                        <div class="metric-icon"><i class="fa-solid fa-route"></i></div>
                        <div class="metric-value">{{ $metricas['tempo_medio_trajeto_fmt'] ?? '00:00' }}</div>
                        <div class="metric-label">Tempo Médio Trajeto</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        const statusDist = @json($statusDist ?? []);
        const filialCargas = @json($filialCargas ?? []);
        const problemasPie = @json($problemasPie ?? []);

        // Register datalabels plugin globally
        if (window.Chart && window.ChartDataLabels) {
            Chart.register(ChartDataLabels);
        }

        const makeBar = (ctx, labels, data, colors) => {
            if (!ctx) return;
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderColor: '#222',
                        borderWidth: 1,
                        minBarLength: 5 // altura mínima da barra
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: '#fff',
                            font:{
                                weight: 'bold',
                                size: 22
                            },
                            anchor: 'end',
                            textStrokeColor: 'rgba(0,0,0,0.6)',
                            textStrokeWidth: 3,
                            align: 'end',
                            textAlign: 'center',
                            clamp: true,
                            clip: false,
                            padding: -10
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#ddd', font: { size: 22 } }
                        },
                        y: {
                            ticks: { color: '#ddd', font: { size: 22 } },
                            beginAtZero: true
                        }
                    }
                }
            });
        };

        const renderBars = () => {
            // Status Bar
            const sLabels = statusDist.map(s => s.status);
            const sData = statusDist.map(s => s.total);
            const sColors = statusDist.map(s => s.color || '#888');
            makeBar(document.getElementById('statusPie'), sLabels, sData, sColors);

            // Filial Bar
            const fLabels = (filialCargas || []).map(f => (f.filial || '-'));
            const fData = (filialCargas || []).map(f => f.total);
            const palette = ['#4facfe', '#00f2fe', '#43e97b', '#38f9d7', '#f5576c', '#f093fb', '#fee140', '#fa709a', '#764ba2', '#667eea'];
            const fColors = fLabels.map((_, i) => palette[i % palette.length]);
            makeBar(document.getElementById('filialPie'), fLabels, fData, fColors);

            // Problemas Bar
            const pLabels = ['Pendentes', 'Resolvidos'];
            const pData = [problemasPie.pendentes || 0, problemasPie.resolvidos || 0];
            const pColors = ['#dc3545', '#28a745'];
            makeBar(document.getElementById('problemasPie'), pLabels, pData, pColors);
        };

        document.addEventListener('DOMContentLoaded', () => {
            renderBars();
        });
    </script>

@endsection
