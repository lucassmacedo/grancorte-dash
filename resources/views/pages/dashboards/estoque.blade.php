@extends('layout.master')

@section('content')
    <div class="dashboard-container">
        <!-- Header -->
        <div class="tv-header">
            <h1>
                ESTOQUE
            </h1>
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
        </div>

        <!-- Gráficos e Tabelas -->
        <div class="row g-4">

            <!-- Top Produtos -->
            <div class="col-xl-7 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-trophy"></i>
                            Top 10 Produtos - Maior Estoque
                        </h1>
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
                                    <td>{{ Str::limit($produto['descricao'] ?? '', 90) }}</td>
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

            <!-- Estoque por Local -->
            <div class="col-xl-5 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-map-marker-alt me-2 text-info"></i>Estoque por Local
                        </h1>
                    </div>
                    <div class="table-container">
                        <table class="elegant-table">
                            <thead class="bg-dark">
                            <tr class="text-white fw-bold">
                                <th>Local</th>
                                <th class="text-center">Produtos</th>
                                <th class="text-end">Total (KG)</th>
                                <th class="text-end">Túnel (KG)</th>
                                <th class="text-end">Disponível (KG)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($locais as $local)
                                <tr class="text-white">
                                    <td><span class="badge badge-info fs-6">{{ $local['local'] }}</span></td>
                                    <td class="text-center ">{{ number_format($local['quantidade_produtos']) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($local['saldo_total'], 0, ',', '.') }}</td>
                                    <td class="text-end text-primary">{{ number_format($local['saldo_tunel'], 0, ',', '.') }}</td>
                                    <td class="text-end text-success">{{ number_format($local['saldo_venda'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Distribuição por Grupo -->
            <div class="col-xl-7 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Estoque por Grupo (Top 15)
                        </h1>
                    </div>
                    <div class="chart-content" style="height: 500px; overflow-y: auto;">
                        <div id="chart-grupos" style="height: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Conservação -->
            <div class="col-xl-5 col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <h1 class="chart-title">
                            <i class="fas fa-thermometer-half"></i>
                            Distribuição Túnel vs Disponível
                        </h1>
                    </div>
                    <div class="chart-content">
                        <div id="chart-distribuicao"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
@section('scripts')
    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        // Dados para os gráficos com verificação de segurança
        let gruposData = @json($grupos->values());
        let distribuicaoData = @json($distribuicao);

        // Garantir que gruposData seja um array
        if (!Array.isArray(gruposData)) {
            gruposData = Object.values(gruposData || {});
        }

        // Gráfico de Grupos (Barras Horizontais para melhor visualização de 34 grupos)
        const optionsGrupos = {
            series: [{
                name: 'Estoque (KG)',
                data: gruposData.map(item => parseFloat(item.saldo_total || 0))
            }],
            chart: {
                type: 'bar',
                height: gruposData.length * 25 + 100, // Altura dinâmica baseada no número de grupos
                fontFamily: 'Inter, sans-serif',
                toolbar: {show: false},
                background: 'transparent'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true, // Barras horizontais para melhor legibilidade
                    barHeight: '70%',
                    colors: {
                        ranges: [{
                            from: 0,
                            to: Number.MAX_VALUE,
                            color: '#667eea'
                        }],
                        backgroundBarColors: ['rgba(255,255,255,0.1)'],
                        backgroundBarOpacity: 1,
                        backgroundBarRadius: 4
                    }
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#ffffff'],
                    fontSize: '15px',
                    fontWeight: 800
                },
                formatter: function (val) {
                    return new Intl.NumberFormat('pt-BR', {
                        notation: 'compact',
                        compactDisplay: 'short'
                    }).format(val) + ' KG';
                },
                offsetX: 10
            },
            xaxis: {
                categories: gruposData.map(item => item.grupo || 'Não informado'),
                labels: {
                    style: {
                        colors: '#ffffff',
                        fontSize: '15px',
                        fontWeight: 800
                    },
                    formatter: function (val) {
                        return new Intl.NumberFormat('pt-BR', {
                            notation: 'compact',
                            compactDisplay: 'short'
                        }).format(val);
                    }
                },
                axisBorder: {show: false},
                axisTicks: {show: false}
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#ffffff',
                        fontSize: '15px',
                        fontWeight: 800
                    },
                    maxWidth: 500, // Aumentado de 200 para 300px
                    minWidth: 350, // Largura mínima garantida
                    formatter: function (val) {
                        // Não truncar mais - mostrar nome completo
                        return val;
                    }
                }
            },
            grid: {
                borderColor: 'rgba(255,255,255,0.1)',
                strokeDashArray: 3,
                xaxis: {
                    lines: {show: true}
                },
                yaxis: {
                    lines: {show: false}
                },
                padding: {
                    left: 20, // Padding adicional à esquerda
                    right: 20
                }
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
            colors: ['#667eea'],
            legend: {show: false}
        };

        // Gráfico de Distribuição (Barras)
        const optionsDistribuicao = {
            series: [{
                name: 'Estoque (KG)',
                data: [
                    parseFloat(distribuicaoData.em_tunel || 0),
                    parseFloat(distribuicaoData.disponivel || 0),
                ]
            }],
            chart: {
                type: 'bar',
                height: 460,
                fontFamily: 'Inter, sans-serif',
                toolbar: {show: false},
                background: 'transparent'
            },
            xaxis: {
                categories: ['Em Túnel', 'Disponível', 'Auxiliar'],
                labels: {
                    style: {
                        colors: '#ffffff',
                        fontSize: '18px',
                        fontWeight: 800
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
                        fontSize: '18px',
                        fontWeight: 800


                    },
                    minWidth: 50, // Largura mínima garantida
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
                    columnWidth: '80%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#ffffff'],
                    fontSize: '18px',
                    fontWeight: 800,
                    padding: 0
                },
                formatter: function (val) {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 1,
                        maximumFractionDigits: 1
                    }).format(val);
                },
                offset: -50,  // Adjusts the horizontal position of labels
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
            const chartGruposElement = document.querySelector("#chart-grupos");
            if (chartGruposElement) {
                try {
                    const chartGrupos = new ApexCharts(chartGruposElement, optionsGrupos);
                    chartGrupos.render();
                } catch (error) {
                    console.error('Erro ao renderizar gráfico de grupos:', error);
                    chartGruposElement.innerHTML = '<div class="text-center text-white p-5"><i class="fas fa-exclamation-triangle mb-3"></i><br>Erro ao carregar gráfico</div>';
                }
            }

            // Renderizar gráfico de distribuição
            const chartDistribuicaoElement = document.querySelector("#chart-distribuicao");
            if (chartDistribuicaoElement) {
                try {
                    const chartDistribuicao = new ApexCharts(chartDistribuicaoElement, optionsDistribuicao);
                    chartDistribuicao.render();
                } catch (error) {
                    console.error('Erro ao renderizar gráfico de distribuição:', error);
                    chartDistribuicaoElement.innerHTML = '<div class="text-center text-white p-5"><i class="fas fa-exclamation-triangle mb-3"></i><br>Erro ao carregar gráfico</div>';
                }
            }
        });


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
    </script>
@endsection

