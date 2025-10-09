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

        @if(request()->has('tv'))
        .controls {
            display: none;
        }
        @endif
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

<div class="controls" id="controls">
</div>

<script>
    // Substitua os URLs abaixo pelos seus dashboards
    const dashboardConfigsRaw = [
        // Estoque
        {url: '{{ route('dashboard.estoque') }}', tempo: 30},
        {url: '{{ route('dashboard.estoque', ['tabela' => 'produtos']) }}', tempo: 30},
        {url: '{{ route('dashboard.estoque', ['tabela' => 'local']) }}', tempo: 30},

        // Pedidos
        {url: '{{ route('dashboard.pedidos', ['tabela' => 'valor']) }}', tempo: 30},
        {url: '{{ route('dashboard.pedidos', ['tabela' => 'carcaca']) }}', tempo: 30},
        {url: '{{ route('dashboard.pedidos', ['tabela' => 'rota']) }}', tempo: 30},

        // Comercial Clientes
        {url: '{{ route('dashboard.clientes', ['tabela' => 'clientes']) }}', tempo: 30},
        {url: '{{ route('dashboard.clientes', ['tabela' => 'ramo']) }}', tempo: 30},
        {url: '{{ route('dashboard.clientes', ['tabela' => 'area']) }}', tempo: 30},
        {url: '{{ route('dashboard.clientes', ['tabela' => 'cidade']) }}', tempo: 30},

        // Comercial Produtos
        {url: '{{ route('dashboard.produtos', ['tabela' => 'performance']) }}', tempo: 30},
        {url: '{{ route('dashboard.produtos', ['tabela' => 'quantidade']) }}', tempo: 30},

        // Comercial Vendedores
        {url: '{{ route('dashboard.vendedores', ['tabela' => 'vendedores']) }}', tempo: 30},
        {url: '{{ route('dashboard.vendedores', ['tabela' => 'clientes']) }}', tempo: 30},
        {url: '{{ route('dashboard.vendedores', ['tabela' => 'produtos']) }}', tempo: 30},

        {url: '{{ route('dashboard.logistica') }}', tempo: 30},

        {url: '{{ route('proxy.dashboard') }}?url=http://104.236.233.129/grancorte/oeetv/index.php', tempo: 30},
        {url: '{{ route('proxy.dashboard') }}?url=http://104.236.233.129/grancorte/oeetv/status_area_limpa.php', tempo: 10}
    ];

    function isPeriodoPermitido(periodo) {
        if (!periodo) return true;
        const agora = new Date();
        const hora = agora.getHours();
        return hora >= periodo.inicio && hora < periodo.fim;
    }

    // Filtra dashboards pelo período permitido
    const dashboardConfigs = dashboardConfigsRaw.filter(cfg => isPeriodoPermitido(cfg.periodo));

    // Renderiza indicadores dinamicamente
    function renderIndicators(activeIndex = 0) {
        const indicatorsContainer = document.getElementById('controls');
        indicatorsContainer.innerHTML = '';

        // Cria os indicadores
        dashboardConfigs.forEach((cfg, i) => {
            const div = document.createElement('div');
            div.className = 'indicator' + (i === activeIndex ? ' active' : '');
            div.onclick = function () {
                goToDashboard(i);
            };
            indicatorsContainer.appendChild(div);
        });

        // Cria o timer
        const timer = document.createElement('span');
        timer.className = 'timer';
        timer.textContent = '0s';
        indicatorsContainer.appendChild(timer);

        // Cria o botão
        const button = document.createElement('button');
        button.textContent = 'Pausar';
        button.onclick = togglePause;
        indicatorsContainer.appendChild(button);
    }

    renderIndicators();

    const iframe = document.getElementById('currentDashboard');
    const preloadIframe = document.getElementById('preloadDashboard');
    const indicators = document.querySelectorAll('.indicator');
    const timerDisplay = document.querySelector('.timer');
    const pauseBtn = document.querySelector('button');
    const loading = document.querySelector('.loading');
    const progressFill = document.getElementById('progressFill');

    let currentIndex = 0;
    let timeLeft = dashboardConfigs[0]?.tempo || 0;
    let isPaused = false;
    let interval;
    let preloadStarted = false;

    function startTimer() {
        clearInterval(interval);
        interval = setInterval(updateTimer, 1000);
    }

    function loadDashboard(index) {
        if (!dashboardConfigs[index]) return;
        loading.classList.add('show');
        iframe.style.opacity = '0';
        clearInterval(interval);
        preloadStarted = false;

        iframe.src = dashboardConfigs[index].url;
        iframe.onload = () => {
            loading.classList.remove('show');
            iframe.style.opacity = '1';
            timeLeft = dashboardConfigs[index].tempo;
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
        const nextIndex = (currentIndex + 1) % dashboardConfigs.length;
        if (!dashboardConfigs[nextIndex]) return;
        preloadIframe.src = dashboardConfigs[nextIndex].url;
    }

    function swapToPreloadedDashboard() {
        clearInterval(interval);
        iframe.src = preloadIframe.src;
        preloadIframe.src = '';
        preloadStarted = false;
        currentIndex = (currentIndex + 1) % dashboardConfigs.length;
        indicators.forEach((ind, i) => {
            ind.classList.toggle('active', i === currentIndex);
        });
        iframe.onload = () => {
            loading.classList.remove('show');
            iframe.style.opacity = '1';
            timeLeft = dashboardConfigs[currentIndex].tempo;
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
            const progress = ((dashboardConfigs[currentIndex].tempo - timeLeft) / dashboardConfigs[currentIndex].tempo) * 100;
            progressFill.style.width = progress + '%';
            if (timeLeft === Math.floor(dashboardConfigs[currentIndex].tempo / 2) && !preloadStarted) {
                preloadNextDashboard();
                preloadStarted = true;
            }
            if (timeLeft <= 0) {
                nextDashboard();
            }
        }
    }

    // Carrega o primeiro dashboard
    if (dashboardConfigs.length > 0) {
        loadDashboard(0);
    } else {
        loading.textContent = 'Nenhum dashboard disponível neste horário.';
        loading.classList.add('show');
    }
</script>
</body>
</html>
