@extends('layouts.app')

@section('content')
{{-- Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container py-4">
    
    {{-- Cabeçalho --}}
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark">
            <i class="fas fa-chart-line text-success me-2"></i>
            Relatório: Curva de Aprendizagem
        </h1>
        <p class="text-muted mb-0">
            Acompanhe sua evolução e identifique os pontos que precisam de mais atenção.
        </p>
    </div>

    @if(empty($dadosAssuntos) && empty($labelsLinha))
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
            <div>
                <h5 class="alert-heading mb-1">Nenhum dado encontrado</h5>
                <p class="mb-0">Você ainda não respondeu questões suficientes para gerar o relatório. Comece a resolver questões!</p>
            </div>
        </div>
    @else

        {{-- ============================================ --}}
        {{-- GRÁFICO 1: EVOLUÇÃO (LINHA) --}}
        {{-- ============================================ --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-area text-primary me-2"></i>
                    Evolução do Desempenho (Últimos 30 dias)
                </h5>
                <small class="text-muted">Percentual de acertos por dia</small>
            </div>
            <div class="card-body">
                @if(!empty($labelsLinha))
                    <div style="position: relative; height: 300px;">
                        <canvas id="graficoLinha"></canvas>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Ainda não há dados suficientes para exibir o gráfico de evolução.</p>
                @endif
            </div>
        </div>

        {{-- Linha com 2 gráficos lado a lado --}}
        <div class="row g-4 mb-4">
            
            {{-- ============================================ --}}
            {{-- GRÁFICO 2: ROSCA (Distribuição das caixas) --}}
            {{-- ============================================ --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-chart-pie text-info me-2"></i>
                            Distribuição das Caixas
                        </h5>
                        <small class="text-muted">Situação atual do sistema Leitner</small>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        @if(array_sum($dadosRosca) > 0)
                            <div style="position: relative; width: 100%; max-width: 300px;">
                                <canvas id="graficoRosca"></canvas>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Sem dados.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- GRÁFICO 3: BARRAS (Top 5 piores assuntos) --}}
            {{-- ============================================ --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-triangle-exclamation text-warning me-2"></i>
                            Assuntos que Precisam de Atenção
                        </h5>
                        <small class="text-muted">Os 5 assuntos com menor % de domínio</small>
                    </div>
                    <div class="card-body">
                        @if(!empty($labelsBarras))
                            <div style="position: relative; height: 300px;">
                                <canvas id="graficoBarras"></canvas>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Sem dados.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TABELA DETALHADA --}}
        {{-- ============================================ --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-table text-dark me-2"></i>
                    Detalhamento por Assunto
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Assunto</th>
                                <th class="text-center">Total</th>
                                <th class="text-center"><span class="badge bg-danger">Cx 1</span></th>
                                <th class="text-center"><span class="badge bg-warning text-dark">Cx 2</span></th>
                                <th class="text-center"><span class="badge bg-info text-dark">Cx 3</span></th>
                                <th class="text-center"><span class="badge bg-primary">Cx 4</span></th>
                                <th class="text-center"><span class="badge bg-success">Cx 5</span></th>
                                <th class="pe-3" style="min-width: 200px;">Domínio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dadosAssuntos as $assunto)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $assunto['nome'] }}</td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $assunto['total'] }}</span></td>
                                    <td class="text-center fw-bold text-danger">{{ $assunto['caixas'][1] }}</td>
                                    <td class="text-center fw-bold text-warning">{{ $assunto['caixas'][2] }}</td>
                                    <td class="text-center fw-bold text-info">{{ $assunto['caixas'][3] }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $assunto['caixas'][4] }}</td>
                                    <td class="text-center fw-bold text-success">{{ $assunto['caixas'][5] }}</td>
                                    <td class="pe-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $assunto['porcentagem_dominio'] }}%">
                                                </div>
                                            </div>
                                            <span class="fw-bold text-dark small">{{ $assunto['porcentagem_dominio'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Card de Explicação --}}
        <div class="card border-0 bg-light shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Como interpretar os gráficos:
                </h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-chart-area text-primary me-2"></i>
                        <strong>Evolução:</strong> Mostra sua curva de aprendizado real. Uma linha subindo indica que você está melhorando.
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        <strong>Distribuição:</strong> Idealmente, a maior parte deve estar nas caixas 4 e 5 (verde/azul).
                    </li>
                    <li>
                        <i class="fas fa-triangle-exclamation text-warning me-2"></i>
                        <strong>Atenção:</strong> Foque seus estudos nos assuntos que aparecem aqui com % baixo.
                    </li>
                </ul>
            </div>
        </div>

    @endif
</div>

{{-- ============================================ --}}
{{-- SCRIPTS DOS GRÁFICOS --}}
{{-- ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Configuração global do Chart.js (fonte e responsividade)
    Chart.defaults.font.family = "'Segoe UI', Roboto, sans-serif";
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // ==========================================
    // GRÁFICO 1: LINHA (Evolução)
    // ==========================================
    @if(!empty($labelsLinha))
        const ctxLinha = document.getElementById('graficoLinha').getContext('2d');
        
        // Cria gradiente para o preenchimento da linha
        const gradiente = ctxLinha.createLinearGradient(0, 0, 0, 300);
        gradiente.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
        gradiente.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: @json($labelsLinha),
                datasets: [{
                    label: '% de Acertos',
                    data: @json($dadosLinha),
                    borderColor: '#0d6efd',
                    backgroundColor: gradiente,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Curva suave
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return ' Acertos: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; },
                            color: '#6c757d'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: { color: '#6c757d' },
                        grid: { display: false }
                    }
                }
            }
        });
    @endif

    // ==========================================
    // GRÁFICO 2: ROSCA (Distribuição das caixas)
    // ==========================================
    @if(array_sum($dadosRosca) > 0)
        const ctxRosca = document.getElementById('graficoRosca').getContext('2d');
        new Chart(ctxRosca, {
            type: 'doughnut',
            data: {
                labels: @json($labelsRosca),
                datasets: [{
                    data: @json($dadosRosca),
                    backgroundColor: [
                        '#dc3545', // Cx 1 - Vermelho
                        '#ffc107', // Cx 2 - Amarelo
                        '#0dcaf0', // Cx 3 - Ciano
                        '#0d6efd', // Cx 4 - Azul
                        '#198754'  // Cx 5 - Verde
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = ((context.parsed / total) * 100).toFixed(1);
                                return ` ${context.label}: ${context.parsed} questões (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    @endif

    // ==========================================
    // GRÁFICO 3: BARRAS (Top 5 piores assuntos)
    // ==========================================
    @if(!empty($labelsBarras))
        const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
        new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: @json($labelsBarras),
                datasets: [{
                    label: '% de Domínio',
                    data: @json($dadosBarras),
                    backgroundColor: function(context) {
                        const value = context.parsed.y;
                        if (value < 20) return '#dc3545';
                        if (value < 50) return '#ffc107';
                        if (value < 80) return '#0dcaf0';
                        return '#198754';
                    },
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y', // Barras horizontais
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' Domínio: ' + context.parsed.x + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; },
                            color: '#6c757d'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        ticks: { 
                            color: '#212529',
                            font: { weight: 'bold' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    @endif

});
</script>

@endsection