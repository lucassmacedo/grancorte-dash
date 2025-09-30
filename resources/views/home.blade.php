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
</div>

<div class="controls">
{{--    <div class="indicator active" onclick="goToDashboard(0)"></div>--}}
{{--    <div class="indicator" onclick="goToDashboard(1)"></div>--}}
    <span class="timer"></span>
{{--    <button onclick="togglePause()">Pausar</button>--}}
</div>

<script>
    // Substitua os URLs abaixo pelos seus dashboards
    const dashboardUrls = [
        '{{ route('dashboard.estoque') }}',
        '{{ route('dashboard.logistica') }}',
        '{{ route('dashboard.gcomercial-vendedores') }}',
    ];


    const iframe = document.getElementById('currentDashboard');
    const indicators = document.querySelectorAll('.indicator');
    const timerDisplay = document.querySelector('.timer');
    const pauseBtn = document.querySelector('button');
    const loading = document.querySelector('.loading');
    const progressFill = document.getElementById('progressFill');

    let currentIndex = 0;
    let timeLeft = 30;
    let isPaused = false;
    let interval;

    function loadDashboard(index) {
        loading.classList.add('show');
        iframe.style.opacity = '0';

        // Carrega o novo dashboard
        iframe.src = dashboardUrls[index];

        iframe.onload = () => {
            loading.classList.remove('show');
            iframe.style.opacity = '1';
        };

        // Atualiza indicadores
        indicators.forEach((ind, i) => {
            ind.classList.toggle('active', i === index);
        });

        currentIndex = index;
        timeLeft = 30;
    }

    function nextDashboard() {
        if (!isPaused) {
            currentIndex = (currentIndex + 1) % dashboardUrls.length;
            loadDashboard(currentIndex);
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

            // Atualiza a barra de progresso
            const progress = ((30 - timeLeft) / 30) * 100;
            progressFill.style.width = progress + '%';

            if (timeLeft <= 0) {
                nextDashboard();
            }
        }
    }

    // Carrega o primeiro dashboard
    loadDashboard(0);

    // Inicia o timer
    interval = setInterval(updateTimer, 1000);
</script>
</body>
</html>