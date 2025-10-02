<!DOCTYPE HTML>
<html lang="pt-br" class>
<head>
    <title>Dashboard Estoque - Gran Cortes</title>
    <meta name="description" content="Dashboard de Estoque em Tempo Real"/>
    <meta name="keywords" content="estoque, dashboard, gran cortes"/>
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
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
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
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 200% 50%;
            }
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
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
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
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2),
            0 0 30px rgba(102, 126, 234, 0.1);
        }

        .metric-card:hover::before {
            opacity: 1;
        }

        .metric-icon i {
            font-size: 3rem;
        }

        .metric-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.3));
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

        .chart-title i {
            background: var(--primary-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;

        }

        /* Table Styles */
        .table-container {
            max-height: 550px;
            overflow-y: auto;
            padding: 1rem;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 3px;
        }

        .elegant-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .elegant-table th {
            background: rgba(102, 126, 234, 0.2);
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .elegant-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            font-weight: 400;
            transition: background 0.3s ease;
        }

        .elegant-table tr:hover td {
            background: rgba(102, 126, 234, 0.1);
        }

        .badge-elegant {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        .badge-primary {
            background: var(--primary-gradient);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .badge-warning {
            background: var(--warning-gradient);
            border-color: rgba(250, 112, 154, 0.3);
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glow-secondary);
            border-radius: 50px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1000;
            font-weight: 500;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }

        .refresh-icon {
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .tv-header h1 {
                font-size: 2.5rem;
            }

            .metric-value {
                font-size: 2rem;
            }

            .metric-icon {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .tv-header {
                padding: 1.5rem 0.5rem;
            }

            .tv-header h1 {
                font-size: 2rem;
            }

            .metric-card {
                padding: 1.5rem;
            }

            .chart-header {
                padding: 1.5rem 1.5rem 1rem;
            }
        }

        /* Special metric card variants */
        .metric-card.variant-1 .metric-icon {
            background: var(--secondary-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .metric-card.variant-2 .metric-icon {
            background: var(--accent-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .metric-card.variant-3 .metric-icon {
            background: var(--success-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .metric-card.variant-4 .metric-icon {
            background: var(--warning-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

    </style>
</head>

<body>
<div class="dashboard-container">
    <!-- Header -->
    <div class="tv-header">
        <h1>Pedidos</h1>
        <div class="subtitle">
            <span>Dashboard em Tempo Real</span>
            <div class="live-indicator">
                <div class="pulse-dot"></div>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
    <!-- Métricas Principais -->
    <div class="row g-4 mb-5">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-file-invoice"></i></div>
                <div class="metric-value">{{ number_format($dashboard['total_pedidos'] ?? 0) }}</div>
                <div class="metric-label">Pedidos</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-1">
                <div class="metric-icon"><i class="fas fa-weight-hanging"></i></div>
                <div class="metric-value">{{ number_format($dashboard['total_kg_pedidos'] ?? 0, 2, ',', '.') }} kg</div>
                <div class="metric-label">Total KG</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-2">
                <div class="metric-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="metric-value">R$ {{ number_format($dashboard['previsao_faturamento'] ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Previsão Faturamento</div>
            </div>
        </div>


        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon"><i class="fas fa-drumstick-bite"></i></div>
                <div class="metric-value">{{ number_format($dashboard['total_carcacas_vendidas'] ?? 0,0,'.','.') }}</div>
                <div class="metric-label">Carcaças Vendidas</div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-4">
                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                <div class="metric-value">{{ number_format($dashboard['pedidos_faturados'] ?? 0) }}</div>
                <div class="metric-label">Faturados</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-1">
                <div class="metric-icon"><i class="fas fa-times-circle"></i></div>
                <div class="metric-value">{{ number_format($dashboard['pedidos_cancelados'] ?? 0) }}</div>
                <div class="metric-label">Cancelados</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">

            <div class="metric-card variant-1">
                <div class="metric-icon"><i class="fas fa-ban"></i></div>
                <div class="metric-value">{{ number_format($dashboard['pedidos_bloqueados'] ?? 0) }}</div>
                <div class="metric-label">Bloqueados</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-4">
                <div class="metric-icon"><i class="fas fa-arrow-down"></i></div>
                <div class="metric-value">{{ number_format($dashboard['pedidos_baixados'] ?? 0) }}</div>
                <div class="metric-label">Baixados</div>
            </div>
        </div>
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-3">
                <div class="metric-icon"><i class="fas fa-hourglass-half"></i></div>
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
        <div class="col-xl-4 col-lg-4">
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
        <div class="col-xl-4 col-lg-4">
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
        <div class="col-xl-4 col-lg-4">
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
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const evolucaoVendas = @json($dashboard['evolucao_vendas']);
    const ctx = document.getElementById('evolucaoVendasChart').getContext('2d');
    const labels = evolucaoVendas.map(item => item.data);
    const data = evolucaoVendas.map(item => item.total);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Vendas',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102,126,234,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {display: false}
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>

