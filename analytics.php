<?php
require 'src/Feedback.php';
$feedbackObj = new Feedback();
$stats = $feedbackObj->getEstatisticas();

// Prepara dados para o Chart.js (PHP -> JS)
$labels = [];
$data = [];
$totalCategorias = 0;

// Cores do tema para o gráfico
$colors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
$bgColors = [];

$i = 0;
foreach ($stats['categorias'] as $row) {
    $labels[] = $row['categoria'];
    $data[] = $row['qtd'];
    $totalCategorias += $row['qtd'];
    // Atribui cor ciclicamente
    $bgColors[] = $colors[$i % count($colors)]; 
    $i++;
}
?>

<?php include 'layout/header.php'; ?>

<style>
    /* CSS Específico desta página - LEVE e OTIMIZADO */
    .stat-card {
        background: #1e293b; /* Dark Slate */
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        transition: transform 0.2s ease, border-color 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        border-color: #475569;
    }
    .icon-box {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    .chart-container {
        position: relative;
        height: 300px; /* Altura fixa para evitar CLS (Cumulative Layout Shift) */
        width: 100%;
    }
    .progress-thin {
        height: 6px;
        background-color: #334155;
        border-radius: 3px;
    }
</style>

<div class="d-flex justify-content-between align-items-end mb-4 animate__animated animate__fadeInDown">
    <div>
        <h5 class="text-white fw-bold mb-0">Visão Geral</h5>
        <small class="text-muted">Métricas de ingestão em tempo real</small>
    </div>
    <button class="btn btn-sm btn-outline-secondary border-secondary text-muted" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
    </button>
</div>

<div class="row g-4 mb-4 animate__animated animate__fadeInUp">
    
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-uppercase text-muted small fw-bold mb-1">Total Processado</div>
                    <h2 class="text-white fw-bold mb-0"><?= $stats['total'] ?></h2>
                    <small class="text-success"><i class="bi bi-arrow-up-short"></i> 100% sincronizado</small>
                </div>
                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-hdd-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-uppercase text-muted small fw-bold mb-1">Ingestão Hoje</div>
                    <h2 class="text-white fw-bold mb-0"><?= $stats['hoje'] ?></h2>
                    <small class="text-muted">Desde 00:00h</small>
                </div>
                <div class="icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-uppercase text-muted small fw-bold mb-1">Top Categoria</div>
                    <?php 
                        // Lógica simples para pegar a maior categoria
                        $topCat = !empty($stats['categorias']) ? $stats['categorias'][0]['categoria'] : 'N/A';
                    ?>
                    <h2 class="text-white fw-bold mb-0"><?= $topCat ?></h2>
                    <small class="text-info">Maior volume</small>
                </div>
                <div class="icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-tags"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
    
    <div class="col-lg-8">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="text-white fw-bold mb-0">Distribuição de Tópicos (IA)</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-dark border-secondary text-muted" type="button"><i class="bi bi-filter"></i> Filtro</button>
                </div>
            </div>
            
            <div class="chart-container">
                <canvas id="chartCategorias"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stat-card">
            <h6 class="text-white fw-bold mb-4">Detalhamento</h6>
            
            <div class="d-flex flex-column gap-4">
                <?php 
                $colorIndex = 0;
                foreach ($stats['categorias'] as $cat): 
                    // Calcula porcentagem para a barra de progresso
                    $percent = ($totalCategorias > 0) ? ($cat['qtd'] / $totalCategorias) * 100 : 0;
                    $barColor = $colors[$colorIndex % count($colors)];
                ?>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-light small"><?= $cat['categoria'] ?></span>
                        <span class="text-muted small fw-bold"><?= $cat['qtd'] ?></span>
                    </div>
                    <div class="progress progress-thin bg-dark">
                        <div class="progress-bar" role="progressbar" 
                             style="width: <?= $percent ?>%; background-color: <?= $barColor ?>;" 
                             aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                <?php 
                $colorIndex++;
                endforeach; 
                ?>
                
                <?php if(empty($stats['categorias'])): ?>
                    <div class="text-center text-muted py-4">Sem dados para exibir</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartCategorias').getContext('2d');
    
    // 1. Gradiente "Enterprise" (Roxo brilhante -> Transparente)
    const gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
    gradientFill.addColorStop(0, 'rgba(99, 102, 241, 0.5)'); // Cor Accent (Topo)
    gradientFill.addColorStop(1, 'rgba(99, 102, 241, 0.0)'); // Transparente (Base)

    // Dados vindos do PHP
    const labelsPHP = <?= json_encode($labels) ?>;
    const dataPHP = <?= json_encode($data) ?>;

    new Chart(ctx, {
        type: 'line', // Tipo Linha
        data: {
            labels: labelsPHP,
            datasets: [{
                label: 'Tickets',
                data: dataPHP,
                // Estilo da Linha
                borderColor: '#818cf8', // Roxo claro neon
                borderWidth: 3,
                // Estilo da Curva
                tension: 0.4, // 0.4 = Curva suave (Spline)
                // Estilo do Preenchimento
                backgroundColor: gradientFill,
                fill: true, // Ativa o preenchimento abaixo da linha
                // Estilo dos Pontos
                pointBackgroundColor: '#1e293b', // Cor do fundo do card
                pointBorderColor: '#818cf8',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }, // Sem legenda (limpo)
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#94a3b8',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' Ocorrências';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    grid: {
                        color: '#334155', // Grid sutil
                        drawBorder: false,
                        borderDash: [5, 5] // Linhas tracejadas (moderno)
                    },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Inter', size: 11 },
                        padding: 10,
                        stepSize: 1 // Garante números inteiros
                    }
                },
                x: {
                    grid: { display: false }, // Sem grid vertical
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Inter', weight: '500' }
                    }
                }
            },
            animation: {
                y: {
                    duration: 2000,
                    easing: 'easeOutQuart' // Efeito de "crescimento" suave
                }
            }
        }
    });
</script>
<?php include 'layout/footer.php'; ?>