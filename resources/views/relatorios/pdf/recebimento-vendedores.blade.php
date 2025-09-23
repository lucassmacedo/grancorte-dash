<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Recebimento por Vendedores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        
        .filters {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .filters h3 {
            margin-top: 0;
            color: #333;
        }
        
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        
        .summary {
            background-color: #e8f4f8;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #b3e0f2;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #2c5282;
        }
        
        .summary-grid {
            width: 100%;
            table-layout: fixed;
            border: none !important;
        }
        
        .summary-grid td {
            border: none !important;
            padding: 10px 5px;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-item {
            text-align: center;
            width: 25%;
            vertical-align: top;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background-color: #4472C4;
            color: white;
            padding: 10px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Recebimento por Vendedores</h1>
        <p>Gran Cortes - ERP</p>
    </div>

    <div class="filters">
        <h3>Filtros Aplicados</h3>
        <div class="filter-item"><strong>Período:</strong> {{ $filtros['periodo'] }}</div>
        <div class="filter-item"><strong>Status:</strong> {{ $filtros['status'] }}</div>
        @if($filtros['cod_filial'])
            <div class="filter-item"><strong>Filial:</strong> {{ $filtros['cod_filial'] }}</div>
        @endif
        @if($filtros['cod_vendedor'])
            <div class="filter-item"><strong>Vendedor:</strong> {{ $filtros['cod_vendedor'] }}</div>
        @endif
        @if($filtros['cod_supervisor'])
            <div class="filter-item"><strong>Supervisor:</strong> {{ $filtros['cod_supervisor'] }}</div>
        @endif
    </div>

    <div class="summary">
        <h3>Resumo Geral</h3>
        <table class="summary-grid" style="border: none;">
            <tr class="summary-row">
                <td class="summary-item">
                    <div class="summary-value">{{ formatMoedaReal($dashboard_geral->sum('valor_total'), true) }}</div>
                    <div class="summary-label">Valor Total de Recebimentos</div>
                </td>
                <td class="summary-item">
                    <div class="summary-value">{{ formatMoedaReal($dashboard_geral->sum('valor_medio'), true) }}</div>
                    <div class="summary-label">Valor Médio dos Recebimentos</div>
                </td>
                <td class="summary-item">
                    <div class="summary-value">{{ number_format($dashboard_geral->sum('titulos'), 0, ',', '.') }}</div>
                    <div class="summary-label">Total de Títulos</div>
                </td>
                <td class="summary-item">
                    <div class="summary-value">{{ number_format($dashboard_geral->sum('clientes'), 0, ',', '.') }}</div>
                    <div class="summary-label">Total de Clientes</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th class="text-center">Títulos</th>
                <th class="text-center">Clientes</th>
                <th class="text-right">Valor Total</th>
                <th class="text-right">Valor Médio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dashboard_grouped as $vendedor_data)
                <!-- Linha principal do vendedor -->
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>{{ $vendedor_data['cod_vendedor'] }} - {{ $vendedor_data['vendedor'] }}</td>
                    <td class="text-center">{{ number_format($vendedor_data['totals']['titulos'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($vendedor_data['totals']['clientes'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ formatMoedaReal($vendedor_data['totals']['valor_total'], true) }}</td>
                    <td class="text-right">{{ formatMoedaReal($vendedor_data['totals']['valor_medio'], true) }}</td>
                </tr>
                
                <!-- Sub-linhas das filiais -->
                @foreach($vendedor_data['filiais'] as $filial)
                    <tr style="color: #666; font-size: 11px;">
                        <td style="padding-left: 20px;">→ {{ $filial->cod_filial }} - {{ $filial->nome_filial ?: 'Sem filial' }}</td>
                        <td class="text-center">{{ number_format($filial->titulos, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($filial->clientes, 0, ',', '.') }}</td>
                        <td class="text-right">{{ formatMoedaReal($filial->valor_total, true) }}</td>
                        <td class="text-right">{{ formatMoedaReal($filial->valor_medio, true) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Relatório gerado em {{ date('d/m/Y H:i:s') }} | Sistema Gran Cortes ERP</p>
    </div>
</body>
</html>
