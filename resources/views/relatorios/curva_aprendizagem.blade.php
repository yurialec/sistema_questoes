@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container py-4 mt-4">
    
    {{-- ============================================ --}}
    {{-- CABEÇALHO --}}
    {{-- ============================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-chart-line text-success me-2"></i>
                Curva de Aprendizagem
            </h1>
            <p class="text-muted mb-0">
                Selecione uma matéria e depois um assunto para acompanhar evolução, retenção e distribuição das revisões.
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTROS DE MATÉRIA E ASSUNTO --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.curva') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-muted small">Matéria</label>
                    <select name="materia_id" id="materia_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas as matérias</option>
                        @foreach($materiasComProgresso as $materia)
                            <option value="{{ $materia->id }}" {{ ($materiaSelecionada?->id ?? '') == $materia->id ? 'selected' : '' }}>
                                {{ $materia->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-muted small">Assunto</label>
                    <select name="assunto_id" id="assunto_id" class="form-select" onchange="this.form.submit()" {{ !$materiaSelecionada ? 'disabled' : '' }}>
                        <option value="">Todos os assuntos</option>
                        @foreach($assuntosComProgresso as $assunto)
                            <option value="{{ $assunto->id }}" {{ ($assuntoSelecionado?->id ?? '') == $assunto->id ? 'selected' : '' }}>
                                {{ $assunto->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('relatorios.curva') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-rotate-left me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CARDS DE KPIs --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        {{-- Domínio Médio --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1 fw-semibold">Domínio médio</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $dominioMedio }}%</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-2">
                            <i class="fas fa-trophy text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-arrow-up text-success"></i>
                        Questões nas caixas 4 e 5
                    </small>
                </div>
            </div>
        </div>

        {{-- Cartões Ativos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1 fw-semibold">Cartões ativos</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalCartoes }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                            <i class="fas fa-layer-group text-primary"></i>
                        </div>
                    </div>
                    <small class="text-muted">Total em acompanhamento</small>
                </div>
            </div>
        </div>

        {{-- Revisões Hoje --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1 fw-semibold">Revisões hoje</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $revisoesHoje }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-2">
                            <i class="fas fa-bell text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted">Cartões programados</small>
                </div>
            </div>
        </div>

        {{-- Sequência --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1 fw-semibold">Sequência</p>
                            <h2 class="fw-bold text-dark mb-0">{{ $sequencia }} {{ Str::plural('dia', $sequencia) }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-3 p-2">
                            <i class="fas fa-fire text-danger"></i>
                        </div>
                    </div>
                    <small class="text-muted">Estudo consistente 🔥</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRÁFICOS: LINHA + ROSCA --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        {{-- Curva de Aprendizagem --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Curva de aprendizagem</h5>
                            <small class="text-muted">Evolução percentual nos últimos 30 dias</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-chart-line me-1"></i> Linha suavizada
                        </span>
                    </div>
                    @if(!empty($labelsLinha))
                        <div style="position: relative; height: 320px;">
                            <canvas id="graficoLinha"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Ainda não há dados suficientes para exibir a curva.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Ciclo Leitner (Rosca) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="fw-bold mb-1">Ciclo Leitner</h5>
                        <small class="text-muted">Cartões distribuídos entre as caixas.</small>
                    </div>
                    @if(array_sum($dadosRosca) > 0)
                        <div class="text-center mb-3">
                            <h3 class="fw-bold text-dark mb-0">{{ array_sum($dadosRosca) }}</h3>
                            <small class="text-muted">cartões no ciclo</small>
                        </div>
                        <div style="position: relative; height: 220px;">
                            <canvas id="graficoRosca"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-circle-notch fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Sem cartões no ciclo.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABELA DE DESEMPENHO POR ASSUNTO --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Desempenho por assunto</h5>
                    <small class="text-muted">
                        @if($materiaSelecionada)
                            Resumo da matéria: <strong>{{ $materiaSelecionada->nome }}</strong>
                        @else
                            Resumo de todas as matérias
                        @endif
                    </small>
                </div>
            </div>

            @if(!empty($dadosAssuntos))
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Assunto</th>
                                <th class="text-center">Domínio</th>
                                <th class="text-center">Cartões</th>
                                <th class="text-center">Próxima revisão</th>
                                <th class="text-center pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dadosAssuntos as $assunto)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">{{ $assunto['nome'] }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress" style="width: 80px; height: 6px;">
                                                <div class="progress-bar 
                                                    @if($assunto['porcentagem_dominio'] >= 80) bg-success
                                                    @elseif($assunto['porcentagem_dominio'] >= 50) bg-primary
                                                    @elseif($assunto['porcentagem_dominio'] >= 20) bg-warning
                                                    @else bg-danger
                                                    @endif"
                                                    style="width: {{ $assunto['porcentagem_dominio'] }}%">
                                                </div>
                                            </div>
                                            <span class="small fw-bold">{{ $assunto['porcentagem_dominio'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $assunto['total'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($assunto['proxima_revisao'])
                                            @if($assunto['proxima_revisao']->isPast())
                                                <span class="badge bg-danger">Atrasada</span>
                                            @elseif($assunto['proxima_revisao']->isToday())
                                                <span class="badge bg-warning text-dark">Hoje</span>
                                            @else
                                                <small class="text-muted">{{ $assunto['proxima_revisao']->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        @php
                                            $statusClass = match($assunto['status']) {
                                                'Dominado' => 'bg-success',
                                                'Em progresso' => 'bg-primary',
                                                'Aprendizado' => 'bg-warning text-dark',
                                                default => 'bg-danger'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $assunto['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0">Nenhum assunto encontrado com os filtros selecionados.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LEGENDA DAS CAIXAS LEITNER --}}
    {{-- ============================================ --}}
    <div class="card border-0 bg-light shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-info-circle text-primary me-2"></i>
                Caixas do método Leitner
            </h6>
            <div class="row g-2">
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger">Cx 1</span>
                        <small class="text-muted">1 dia</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark">Cx 2</span>
                        <small class="text-muted">3 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info text-dark">Cx 3</span>
                        <small class="text-muted">7 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">Cx 4</span>
                        <small class="text-muted">15 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success">Cx 5</span>
                        <small class="text-muted">30 dias</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ============================================ --}}
{{-- SCRIPTS DOS GRÁFICOS --}}
{{-- ============================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Segoe UI', Roboto, sans-serif";
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // ==========================================
    // GRÁFICO 1: LINHA (Curva de Aprendizagem)
    // ==========================================
    @if(!empty($labelsLinha))
        const ctxLinha = document.getElementById('graficoLinha').getContext('2d');
        
        const gradiente = ctxLinha.createLinearGradient(0, 0, 0, 320);
        gradiente.addColorStop(0, 'rgba(13, 110, 253, 0.3)');
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
                    tension: 0.4,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.85)',
                        padding: 12,
                        cornerRadius: 8,
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
                        ticks: { color: '#6c757d', maxRotation: 0 },
                        grid: { display: false }
                    }
                }
            }
        });
    @endif

    // ==========================================
    // GRÁFICO 2: ROSCA (Ciclo Leitner)
    // ==========================================
    @if(array_sum($dadosRosca) > 0)
        const ctxRosca = document.getElementById('graficoRosca').getContext('2d');
        new Chart(ctxRosca, {
            type: 'doughnut',
            data: {
                labels: @json($labelsRosca),
                datasets: [{
                    data: @json($dadosRosca),
                    backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#0d6efd', '#198754'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.85)',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = ((context.parsed / total) * 100).toFixed(1);
                                return ` ${context.label}: ${context.parsed} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    @endif
});
</script>

@endsection