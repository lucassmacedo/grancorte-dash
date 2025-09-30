<title>Dashboard Logística - Gran Cortes</title>
<meta name="description" content="Dashboard de Logística em Tempo Real"/>
<meta name="keywords" content="logistica, dashboard, gran cortes"/>
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
        inset: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        transform: translateX(-100%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%)
        }
        100% {
            transform: translateX(100%)
        }
    }

    .tv-header h1 {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(45deg, #fff, #667eea, #fff);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradient-text 3s linear infinite;
        margin-bottom: .5rem;
        text-shadow: 0 0 30px rgba(102, 126, 234, .5);
    }

    @keyframes gradient-text {
        0% {
            background-position: 0% 50%
        }
        100% {
            background-position: 200% 50%
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
        gap: .5rem;
        background: rgba(0, 255, 136, .2);
        padding: .5rem 1rem;
        border-radius: 50px;
        border: 1px solid rgba(0, 255, 136, .3);
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
            transform: scale(1)
        }
        50% {
            opacity: .5;
            transform: scale(1.2)
        }
    }

    .metric-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all .4s cubic-bezier(.175, .885, .32, 1.275);
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
        transition: opacity .3s ease;
    }

    .metric-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: rgba(102, 126, 234, .4);
        box-shadow: 0 20px 40px rgba(102, 126, 234, .2), 0 0 30px rgba(102, 126, 234, .1);
    }

    .metric-card:hover::before {
        opacity: 1;
    }

    .metric-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 0 10px rgba(102, 126, 234, .3));
    }

    .metric-icon i {
        font-size: 3rem;

    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: .5rem;
        background: linear-gradient(45deg, #fff, var(--glow-primary));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .metric-label {
        font-size: 1.2rem;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .chart-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 0;
        overflow: hidden;
        transition: all .3s ease;
        height: 100%;
    }

    .chart-header {
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, .1);
        background: linear-gradient(135deg, rgba(102, 126, 234, .1) 0%, transparent 100%);
    }

    .chart-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 0;
    }

    .table-container {
        max-height: 420px;
        overflow-y: auto;
        padding: 1rem;
    }

    .elegant-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .elegant-table th {
        background: rgba(102, 126, 234, .2);
        color: var(--text-primary);
        font-weight: 600;
        padding: 1rem;
        text-align: left;
        font-size: .9rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .elegant-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, .1);
        color: var(--text-primary);
        font-weight: 400;
        transition: background .3s ease;
    }

    .elegant-table tr:hover td {
        background: rgba(102, 126, 234, .1);
    }

    .badge-elegant {
        padding: .5rem 1rem;
        border-radius: 50px;
        font-size: .8rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, .2);
        background: rgba(255, 255, 255, .1);
        color: var(--text-primary);
    }

    .progress {
        height: 10px;
        background: rgba(255, 255, 255, .12);
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        transition: width .6s ease;
    }


    @media (max-width: 1200px) {
        .tv-header h1 {
            font-size: 2.5rem
        }

        .metric-value {
            font-size: 2rem
        }

        .metric-icon {
            font-size: 2.5rem
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem
        }

        .tv-header {
            padding: 1.5rem .5rem
        }

        .tv-header h1 {
            font-size: 2rem
        }

        .metric-card {
            padding: 1.5rem
        }

        .chart-header {
            padding: 1.5rem 1.5rem 1rem
        }
    }
</style>

<div class="dashboard-container">
    <div class="tv-header">
        <h1>Dashboard Logística</h1>
        <div class="subtitle">
            <span>Dashboard em Tempo Real</span>
            <div class="live-indicator">
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
                        fontsize: 16,
                        textStrokeColor: 'rgba(0,0,0,0.6)',
                        textStrokeWidth: 3,
                        align: 'end',
                        textAlign: 'center',
                        clamp: true,
                        clip: false
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#ddd', font: { size: 18 } }
                    },
                    y: {
                        ticks: { color: '#ddd', font: { size: 18 } },
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
