<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotacionador de Dashboards</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #1a1a1a;
            overflow: hidden;
        }

        .container {
            width: 100vw;
            height: 100vh;
            position: relative;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 20px;
            display: none;
        }

        .loading.show {
            display: block;
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1001;
        }

        .progress-fill {
            height: 100%;
            background: #4CAF50;
            width: 0%;
            transition: width 1s linear;
        }

        .controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 25px;
            border-radius: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
            z-index: 1000;
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #444;
            cursor: pointer;
            transition: all 0.3s;
        }

        .indicator.active {
            background: #4CAF50;
            transform: scale(1.3);
        }

        .timer {
            color: white;
            font-size: 14px;
            min-width: 40px;
            text-align: center;
        }

        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        button:hover {
            background: #45a049;
        }

        button.paused {
            background: #f44336;
        }
    </style>
</head>
<body>
<div class="progress-bar">
    <div class="progress-fill" id="progressFill"></div>
</div>

<div class="container">
    <div class="loading">Carregando dashboard...</div>
    <iframe id="currentDashboard"></iframe>
    <iframe id="preloadDashboard" style="display:none;"></iframe>
</div>

<div class="controls">
    <div class="indicator active" onclick="goToDashboard(0)"></div>
    <div class="indicator" onclick="goToDashboard(1)"></div>
    <div class="indicator" onclick="goToDashboard(2)"></div>
    <div class="indicator" onclick="goToDashboard(3)"></div>
    <div class="indicator" onclick="goToDashboard(4)"></div>
    <div class="indicator" onclick="goToDashboard(5)"></div>
    <span class="timer"></span>
    <button onclick="togglePause()">Pausar</button>
</div>

<script>
    // Substitua os URLs abaixo pelos seus dashboards
    const dashboardUrls = [
        '{{ route('dashboard.estoque') }}',
        '{{ route('dashboard.logistica') }}',
        '{{ route('dashboard.vendedores') }}',
        '{{ route('dashboard.produtos') }}',
        '{{ route('dashboard.clientes') }}',
        '{{ route('dashboard.pedidos') }}',
        '{{ route('proxy.dashboard') }}?url=http://104.236.233.129/grancorte/oeetv/index.php'
    ];

    const iframe = document.getElementById('currentDashboard');
    const preloadIframe = document.getElementById('preloadDashboard');
    const indicators = document.querySelectorAll('.indicator');
    const timerDisplay = document.querySelector('.timer');
    const pauseBtn = document.querySelector('button');
    const loading = document.querySelector('.loading');
    const progressFill = document.getElementById('progressFill');

    let currentIndex = 0;
    let timeLeft = 30;
    let isPaused = false;
    let interval;
    let preloadStarted = false;

    function startTimer() {
        clearInterval(interval);
        interval = setInterval(updateTimer, 1000);
    }

    function loadDashboard(index) {
        loading.classList.add('show');
        iframe.style.opacity = '0';
        clearInterval(interval);
        preloadStarted = false;

        // Carrega o dashboard atual
        iframe.src = dashboardUrls[index];
        iframe.onload = () => {
            loading.classList.remove('show');
            iframe.style.opacity = '1';
            timeLeft = 30;
            timerDisplay.textContent = timeLeft + 's';
            progressFill.style.width = '0%';
            startTimer();
        };

        // Atualiza indicadores
        indicators.forEach((ind, i) => {
            ind.classList.toggle('active', i === index);
        });
        currentIndex = index;
    }

    function preloadNextDashboard() {
        const nextIndex = (currentIndex + 1) % dashboardUrls.length;
        preloadIframe.src = dashboardUrls[nextIndex];
    }

    function swapToPreloadedDashboard() {
        clearInterval(interval);
        iframe.src = preloadIframe.src;
        preloadIframe.src = '';
        preloadStarted = false;
        currentIndex = (currentIndex + 1) % dashboardUrls.length;
        indicators.forEach((ind, i) => {
            ind.classList.toggle('active', i === currentIndex);
        });
        // Quando o novo iframe carregar, reinicia o timer
        iframe.onload = () => {
            loading.classList.remove('show');
            iframe.style.opacity = '1';
            timeLeft = 30;
            timerDisplay.textContent = timeLeft + 's';
            progressFill.style.width = '0%';
            startTimer();
        };
    }

    function nextDashboard() {
        if (!isPaused) {
            swapToPreloadedDashboard();
        }
    }

    function goToDashboard(index) {
        loadDashboard(index);
    }

    function togglePause() {
        isPaused = !isPaused;
        pauseBtn.textContent = isPaused ? 'Retomar' : 'Pausar';
        pauseBtn.classList.toggle('paused', isPaused);
    }

    function updateTimer() {
        if (!isPaused) {
            timeLeft--;
            timerDisplay.textContent = timeLeft + 's';
            const progress = ((30 - timeLeft) / 30) * 100;
            progressFill.style.width = progress + '%';
            // 15s antes do fim, inicia preload se ainda não iniciado
            if (timeLeft === 15 && !preloadStarted) {
                preloadNextDashboard();
                preloadStarted = true;
            }
            if (timeLeft <= 0) {
                nextDashboard();
            }
        }
    }

    // Carrega o primeiro dashboard
    loadDashboard(0);
</script>
</body>
</html>
