<title>Dashboard Comercial - Vendedores - Gran Cortes</title>
<meta name="description" content="Dashboard Comercial - Vendedores em Tempo Real"/>
<meta name="keywords" content="vendedores, comercial, dashboard, gran cortes"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<!-- Vendor Stylesheets -->
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --dark-bg: #0f0f23;
        --card-bg: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(255, 255, 255, 0.18);
        --text-primary: #ffffff;
        --text-secondary: rgba(255, 255, 255, 0.7);
        --glow-primary: #667eea;
        --glow-secondary: #00ff88;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--dark-bg);
        background-image: radial-gradient(circle at 25% 25%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
        background-attachment: fixed;
        color: var(--text-primary);
        overflow-x: hidden;
        min-height: 100vh;
    }

    .dashboard-container {
        min-height: 100vh;
        padding: 2rem;
        position: relative;
    }

    /* Header Section */
    .tv-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 2rem 2rem;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .tv-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        transform: translateX(-100%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .tv-header h1 {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(45deg, #fff, #667eea, #fff);
        background-size: 200% auto;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradient-text 3s linear infinite;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
    }

    @keyframes gradient-text {
        0% { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }

    .tv-header .subtitle {
        font-size: 1.2rem;
        color: var(--text-secondary);
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(0, 255, 136, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: 1px solid rgba(0, 255, 136, 0.3);
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: var(--glow-secondary);
        border-radius: 50%;
        animation: pulse 2s infinite;
        box-shadow: 0 0 10px var(--glow-secondary);
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* Metric Cards */
    .metric-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: rgba(102, 126, 234, 0.4);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2), 0 0 30px rgba(102, 126, 234, 0.1);
    }

    .metric-card:hover::before { opacity: 1; }

    .metric-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: var(--primary-gradient);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.3));
    }
    .metric-icon i {
        font-size: 3rem;

    }


    .metric-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(45deg, #fff, var(--glow-primary));
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .metric-label {
        font-size: 1.2rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Chart Cards */
    .chart-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 0;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .chart-card:hover {
        border-color: rgba(102, 126, 234, 0.4);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .chart-header {
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, transparent 100%);
    }

    .chart-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .chart-body {
        padding: 2rem;
    }

    /* Lista Estilizada */
    .styled-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .styled-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .styled-list-item:last-child { border-bottom: none; }

    .styled-list-item:hover {
        background: rgba(102, 126, 234, 0.1);
        border-radius: 8px;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .list-item-content { flex: 1; }
    .list-item-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    .list-item-subtitle {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
    .list-item-value {
        font-size: 1.5em;
        font-weight: 700;
        color: var(--glow-primary);
        text-align: right;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .tv-header h1 { font-size: 2rem; }
        .metric-card { padding: 1.5rem; }
        .chart-body { height: 300px; }
    }
</style>

<body>
<div class="dashboard-container">
    <div class="tv-header">
        <h1> Dashboard Faturamento</h1>
        <div class="subtitle">
            <span>Dashboard em Tempo Real</span>
            <div class="live-indicator">
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
                <div class="metric-label">Total de Notas</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-users"></i></div>
                <div class="metric-value" id="total-clientes">{{ $dashboard_geral->clientes  }}</div>
                <div class="metric-label">Clientes</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-user-tie"></i></div>
                <div class="metric-value" id="vendedores-ativos">{{ $dashboard_geral->vendedores_ativos }}</div>
                <div class="metric-label">Vendedores Ativos</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-box"></i></div>
                <div class="metric-value" id="produtos-vendidos">{{ $produtos_vendidos->produtos }}</div>
                <div class="metric-label">Produtos Vendidos</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-calculator"></i></div>
                <div class="metric-value fs-1" id="valor-medio">R$ {{ number_format($dashboard_geral->valor_medio ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Ticket Médio</div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="metric-value fs-1" id="valor-total">R$ {{ number_format($dashboard_geral->valor_liquido ?? 0, 2, ',', '.') }}</div>
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
                    <canvas id="vendasPorHoraChart"></canvas>
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
                        Top 10 Vendedores Hoje
                    </h3>
                </div>
                <div class="chart-body">
                    <ul class="styled-list">
                        @foreach($vendedores_performance as $vendedor)
                            <li class="styled-list-item">
                                <div class="list-item-content">
                                    <div class="list-item-title">{{ $vendedor->vendedor }}</div>
                                    <div class="list-item-subtitle">{{ $vendedor->notas }} notas • {{ $vendedor->clientes }} clientes</div>
                                </div>
                                <div class="list-item-value">R$ {{ number_format($vendedor->valor_liquido, 0, ',', '.') }}</div>
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
                        Top Clientes Hoje
                    </h3>
                </div>
                <div class="chart-body">
                    <ul class="styled-list">
                        @foreach($top_clientes->take(8) as $cliente)
                            <li class="styled-list-item">
                                <div class="list-item-content">
                                    <div class="list-item-title">{{ Str::limit($cliente->cliente, 50) }}</div>
                                    <div class="list-item-subtitle">{{ $cliente->notas }} notas</div>
                                </div>
                                <div class="list-item-value">R$ {{ number_format($cliente->valor_liquido, 0, ',', '.') }}</div>
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
                        Produtos Mais Vendidos (KG)
                    </h3>
                </div>
                <div class="chart-body">
                    <ul class="styled-list">
                        @foreach($produtos_mais_vendidos->sortByDesc('quantidade_total')->take(8) as $produto)
                            <li class="styled-list-item">
                                <div class="list-item-content">
                                    <div class="list-item-title">{{ Str::limit($produto->desc_produto, 50) }}</div>
                                    <div class="list-item-subtitle"></div>
                                </div>
                                <div class="list-item-value">{{ number_format($produto->quantidade_total, 1, ',', '.') }} kg</div>
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
                        Produtos Mais Vendidos (R$)
                    </h3>
                </div>
                <div class="chart-body">
                    <ul class="styled-list">
                        @foreach($produtos_mais_vendidos->sortByDesc('valor_total')->take(8) as $produto)
                            <li class="styled-list-item">
                                <div class="list-item-content">
                                    <div class="list-item-title">{{ Str::limit($produto->desc_produto, 50) }}</div>
                                    <div class="list-item-subtitle"></div>
                                </div>
                                <div class="list-item-value">R${{ number_format($produto->valor_total, 2, ',', '.') }}</div>

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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar relógio
    function updateClock() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString('pt-BR');
    }
    setInterval(updateClock, 1000);

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
                    font: { weight: 'bold', size: 16 },
                    formatter: function(value) {
                        return 'R$ ' + Number(value).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                    }
                }
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#ffffff' } },
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false
                },
                datalabels: {
                    display: true
                }
            },
            scales: {
                y: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    ticks: { color: '#ffffff' },
                    grid: { drawOnChartArea: false }
                },
                x: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    offset: true,
                    padding: 0
                }
            }
        },
        plugins: [window.ChartDataLabels]
    });
    // Mantém o tooltip sempre visível no último ponto
    function showLastTooltip() {
        const points = chart.getDatasetMeta(0).data;
        if (points.length) {
            chart.setActiveElements([
                {datasetIndex: 0, index: points.length - 1},
                {datasetIndex: 1, index: points.length - 1}
            ]);
            chart.tooltip.setActiveElements([
                {datasetIndex: 0, index: points.length - 1},
                {datasetIndex: 1, index: points.length - 1}
            ], {x: points[points.length - 1].x, y: points[points.length - 1].y});
            chart.update();
        }
    }
    chart.on('draw', showLastTooltip);
    showLastTooltip();
});
</script>
</body>
