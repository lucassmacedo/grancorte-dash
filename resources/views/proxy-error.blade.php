<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="dashboard-error" content="true">
    <title>Erro no Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: Arial, sans-serif;
        }
        .error-container {
            text-align: center;
            color: white;
            padding: 40px;
        }
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        h1 {
            font-size: 34px;
            margin: 20px 0;
        }
        p {
            font-size: 26px;
            opacity: 0.9;
        }
        .code {
            margin-top: 20px;
            font-size: 24px;
            opacity: 0.7;
        }
    </style>
    <script>
        // Notifica o parent que houve erro para pular automaticamente
        window.addEventListener('load', function() {
            if (window.parent !== window) {
                window.parent.postMessage({type: 'dashboard-error', code: {{ $code }}}, '*');
            }
        });
    </script>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>{{ $message }}</h1>
        <p>O dashboard será pulado automaticamente...</p>
        <div class="code">Código: {{ $code }}</div>
    </div>
</body>
</html>

