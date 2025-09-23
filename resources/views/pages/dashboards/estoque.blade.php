<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8"/>
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
            padding: 3rem 2rem;
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
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
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
            font-size: 1.3rem;
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
        }

        /* Table Styles */
        .table-container {
            max-height: 400px;
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
                padding: 2rem 1rem;
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

        /* Chart container */
        .chart-content {
            padding: 1rem 2rem 2rem;
            height: 400px;
        }
    </style>
</head>

<body>

<div class="dashboard-container">
    <!-- Header -->
    <div class="tv-header">
        <h1>
            DASHBOARD  - ESTOQUE
        </h1>
        {{--        <div class="subtitle">--}}
        {{--            <span>Dashboard em Tempo Real</span>--}}
        {{--            <div class="live-indicator">--}}
        {{--                <div class="pulse-dot"></div>--}}
        {{--                <span>AO VIVO</span>--}}
        {{--            </div>--}}
        {{--            <span>{{ now()->format('d/m/Y H:i') }}</span>--}}
        {{--        </div>--}}
    </div>

    <!-- Métricas Principais -->
    <div class="row g-4 mb-5">
        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-value" id="total-produtos">{{ number_format($metricas['total_produtos'] ?? 0) }}</div>
                <div class="metric-label">Total Produtos</div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-1">
                <div class="metric-icon">
                    <i class="fas fa-weight-hanging"></i>
                </div>
                <div class="metric-value" id="saldo-total">{{ number_format($metricas['total_saldo_total'] ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Saldo Total (KG)</div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-2">
                <div class="metric-icon">
                    <i class="fas fa-snowflake"></i>
                </div>
                <div class="metric-value" id="saldo-tunel">{{ number_format($metricas['total_saldo_tunel'] ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Em Túnel (KG)</div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-3">
                <div class="metric-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="metric-value" id="saldo-venda">{{ number_format($metricas['total_saldo_p_venda'] ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Disponível Venda (KG)</div>
            </div>
        </div>

        <div class="col-xl col-lg-4 col-md-6">
            <div class="metric-card variant-4">
                <div class="metric-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="metric-value">{{ number_format($metricas['total_saldo_aux'] ?? 0, 2, ',', '.') }}</div>
                <div class="metric-label">Saldo Auxiliar (KG)</div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="row g-4">
        <!-- Gráfico de Distribuição por Grupo -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie"></i>
                        Distribuição por Grupo de Estoque
                    </h3>
                </div>
                <div class="chart-content">
                    <div id="chart-grupos"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Conservação -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-thermometer-half"></i>
                        Distribuição Túnel vs Disponível
                    </h3>
                </div>
                <div class="chart-content">
                    <div id="chart-distribuicao"></div>
                </div>
            </div>
        </div>

        <!-- Top Produtos -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-trophy"></i>
                        Top 10 Produtos - Maior Estoque
                    </h3>
                </div>
                <div class="table-container">
                    <table class="elegant-table">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Grupo</th>
                            <th style="text-align: right;">Estoque (KG)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($top_produtos ?? [] as $produto)
                            <tr>
                                <td><span class="badge-elegant">{{ $produto['codigo'] ?? '' }}</span></td>
                                <td>{{ Str::limit($produto['descricao'] ?? '', 30) }}</td>
                                <td><span class="badge-elegant badge-primary">{{ $produto['grupo'] ?? '' }}</span></td>
                                <td style="text-align: right; font-weight: 600;">{{ number_format($produto['saldo_total'] ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary">Nenhum produto encontrado</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Estoque Baixo -->
        <div class="col-xl-6 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-exclamation-triangle" style="color: #fa709a;"></i>
                        Alerta - Estoque Baixo
                    </h3>
                </div>
                <div class="table-container">
                    <table class="elegant-table">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Grupo</th>
                            <th style="text-align: right;">Estoque (KG)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($estoque_baixo ?? [] as $produto)
                            <tr>
                                <td><span class="badge-elegant">{{ $produto['codigo'] ?? '' }}</span></td>
                                <td>{{ Str::limit($produto['descricao'] ?? '', 30) }}</td>
                                <td><span class="badge-elegant badge-warning">{{ $produto['grupo'] ?? '' }}</span></td>
                                <td style="text-align: right; font-weight: 600; color: #fa709a;">{{ number_format($produto['saldo_total'] ?? 0, 1, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary">Nenhum produto com estoque baixo</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    // Dados simulados para demonstração
    const gruposData = [
        {grupo: 'Bovino', saldo_total: 15678.90},
        {grupo: 'Suíno', saldo_total: 8934.50},
        {grupo: 'Aves', saldo_total: 5432.30},
        {grupo: 'Processados', saldo_total: 3456.20},
        {grupo: 'Outros', saldo_total: 1234.10}
    ];

    const distribuicaoData = {
        em_tunel: 8934.20,
        disponivel: 15633.30,
        auxiliar: 3247.80
    };

    // Gráfico de Grupos (Donut)
    const optionsGrupos = {
        series: gruposData.map(item => item.saldo_total),
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: 'Inter, sans-serif',
            background: 'transparent'
        },
        labels: gruposData.map(item => item.grupo),
        colors: ['#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a'],
        legend: {
            show: true,
            position: 'bottom',
            labels: {
                colors: '#ffffff',
                useSeriesColors: false
            },
            fontSize: '14px',
            fontWeight: 500,
            itemMargin: {
                horizontal: 15,
                vertical: 8
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            color: '#ffffff',
                            fontSize: '18px',
                            fontWeight: 600
                        },
                        value: {
                            show: true,
                            color: '#ffffff',
                            fontSize: '24px',
                            fontWeight: 700,
                            formatter: function (val) {
                                return new Intl.NumberFormat('pt-BR', {
                                    minimumFractionDigits: 1,
                                    maximumFractionDigits: 1
                                }).format(val) + ' KG';
                            }
                        },
                        total: {
                            show: true,
                            label: 'TOTAL',
                            color: '#ffffff',
                            fontSize: '16px',
                            fontWeight: 600,
                            formatter: function (w) {
                                const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                return new Intl.NumberFormat('pt-BR', {
                                    minimumFractionDigits: 1,
                                    maximumFractionDigits: 1
                                }).format(sum) + ' KG';
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 3,
            colors: ['rgba(255,255,255,0.2)']
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '14px',
                fontFamily: 'Inter, sans-serif'
            },
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(val) + ' KG';
                }
            }
        }
    };

    // Gráfico de Distribuição (Barras)
    const optionsDistribuicao = {
        series: [{
            name: 'Estoque (KG)',
            data: [distribuicaoData.em_tunel, distribuicaoData.disponivel, distribuicaoData.auxiliar]
        }],
        chart: {
            type: 'bar',
            height: 350,
            fontFamily: 'Inter, sans-serif',
            toolbar: {show: false},
            background: 'transparent'
        },
        xaxis: {
            categories: ['Em Túnel', 'Disponível', 'Auxiliar'],
            labels: {
                style: {
                    colors: '#ffffff',
                    fontSize: '14px',
                    fontWeight: 500
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#ffffff',
                    fontSize: '14px'
                },
                formatter: function (val) {
                    return new Intl.NumberFormat('pt-BR', {
                        notation: 'compact',
                        compactDisplay: 'short'
                    }).format(val);
                }
            }
        },
        colors: ['#4facfe', '#43e97b', '#fa709a'],
        plotOptions: {
            bar: {
                borderRadius: 12,
                columnWidth: '60%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#ffffff'],
                fontSize: '14px',
                fontWeight: 600
            },
            formatter: function (val) {
                return new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }).format(val);
            },
            offsetY: -20
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)',
            strokeDashArray: 3
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '14px',
                fontFamily: 'Inter, sans-serif'
            },
            y: {
                formatter: function (val) {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(val) + ' KG';
                }
            }
        },
        legend: {
            show: false
        }
    };

    // Inicializar gráficos quando o DOM estiver carregado
    document.addEventListener('DOMContentLoaded', function () {
        // Renderizar gráfico de grupos
        if (document.querySelector("#chart-grupos")) {
            const chartGrupos = new ApexCharts(document.querySelector("#chart-grupos"), optionsGrupos);
            chartGrupos.render();
        }

        // Renderizar gráfico de distribuição
        if (document.querySelector("#chart-distribuicao")) {
            const chartDistribuicao = new ApexCharts(document.querySelector("#chart-distribuicao"), optionsDistribuicao);
            chartDistribuicao.render();
        }

        // Simular atualização de dados em tempo real
        setInterval(() => {
            updateMetrics();
        }, 5000);
    });

    // Função para atualizar métricas
    function updateMetrics() {
        // Simular pequenas variações nos valores
        const totalProdutos = document.getElementById('total-produtos');
        const saldoTotal = document.getElementById('saldo-total');
        const saldoTunel = document.getElementById('saldo-tunel');
        const saldoVenda = document.getElementById('saldo-venda');

        if (totalProdutos) {
            const currentValue = parseInt(totalProdutos.textContent.replace(/[.,]/g, ''));
            const newValue = currentValue + Math.floor(Math.random() * 10 - 5);
            totalProdutos.textContent = new Intl.NumberFormat('pt-BR').format(Math.max(0, newValue));
        }

        // Adicionar pequenas animações aos valores
        animateValue(saldoTotal, 0.5);
        animateValue(saldoTunel, 0.3);
        animateValue(saldoVenda, 0.7);
    }

    // Função para animar valores
    function animateValue(element, variance) {
        if (!element) return;

        element.style.transform = 'scale(1.1)';
        element.style.transition = 'transform 0.3s ease';

        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 300);
    }

    // Adicionar efeito de typing no título
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.innerHTML = '';

        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }

        type();
    }

    // Efeitos visuais adicionais
    document.addEventListener('DOMContentLoaded', function () {
        // Adicionar efeito parallax suave ao fundo
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('body');
            const speed = scrolled * 0.5;
            parallax.style.backgroundPosition = `center ${speed}px`;
        });

        // Adicionar efeito de hover nos cards
        const cards = document.querySelectorAll('.metric-card, .chart-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.zIndex = '10';
            });

            card.addEventListener('mouseleave', function () {
                this.style.zIndex = '1';
            });
        });
    });

    // Auto-refresh simulation
    let refreshCounter = 0;
    setInterval(() => {
        refreshCounter++;
        const refreshElement = document.querySelector('.refresh-indicator span');
        if (refreshElement) {
            refreshElement.textContent = `Atualizado há ${refreshCounter * 5}s`;
        }
    }, 5000);
</script>
</body>
</html>