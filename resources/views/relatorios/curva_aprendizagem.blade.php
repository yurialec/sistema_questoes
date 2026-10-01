@extends('layouts.app')

@section('title', 'Curva de Aprendizagem')

@section('content')
<div class="container py-4">
    
    {{-- ============================================ --}}
    {{-- CABEÇALHO --}}
    {{-- ============================================ --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-body mb-1">Curva de Aprendizagem</h1>
            <p class="text-body-secondary mb-0">
                Selecione uma matéria e um assunto para acompanhar evolução, retenção e distribuição das revisões.
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTROS DE MATÉRIA E ASSUNTO --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.curva') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-body-secondary small">Matéria</label>
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
                    <label class="form-label fw-semibold text-body-secondary small">Assunto</label>
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
    {{-- CARDS DE KPIs (Minimalistas, sem ícones decorativos) --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body">
                    <p class="text-body-secondary small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Domínio médio</p>
                    <h2 class="fw-bold text-body mb-2">{{ $dominioMedio }}%</h2>
                    <small class="text-body-secondary">Questões nas caixas 4 e 5</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body">
                    <p class="text-body-secondary small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Cartões ativos</p>
                    <h2 class="fw-bold text-body mb-2">{{ $totalCartoes }}</h2>
                    <small class="text-body-secondary">Total em acompanhamento</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body">
                    <p class="text-body-secondary small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Revisões hoje</p>
                    <h2 class="fw-bold text-warning-emphasis mb-2">{{ $revisoesHoje }}</h2>
                    <small class="text-body-secondary">Cartões programados</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                <div class="card-body">
                    <p class="text-body-secondary small mb-1 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Sequência</p>
                    <h2 class="fw-bold text-body mb-2">{{ $sequencia }} <span class="fs-6 fw-normal text-body-secondary">{{ Str::plural('dia', $sequencia) }}</span></h2>
                    <small class="text-body-secondary">Estudo consistente</small>
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
                            <h5 class="fw-bold mb-1 text-body">Curva de aprendizagem</h5>
                            <small class="text-body-secondary">Evolução percentual nos últimos 30 dias</small>
                        </div>
                    </div>
                    @if(!empty($labelsLinha))
                        <div style="position: relative; height: 320px;">
                            <canvas id="graficoLinha"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-body-secondary">
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
                        <h5 class="fw-bold mb-1 text-body">Ciclo Leitner</h5>
                        <small class="text-body-secondary">Cartões distribuídos entre as caixas.</small>
                    </div>
                    @if(array_sum($dadosRosca) > 0)
                        <div class="text-center mb-3">
                            <h3 class="fw-bold text-body mb-0">{{ array_sum($dadosRosca) }}</h3>
                            <small class="text-body-secondary">cartões no ciclo</small>
                        </div>
                        <div style="position: relative; height: 220px;">
                            <canvas id="graficoRosca"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-body-secondary">
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
                    <h5 class="fw-bold mb-1 text-body">Desempenho por assunto</h5>
                    <small class="text-body-secondary">
                        @if($materiaSelecionada)
                            Resumo da matéria: <strong class="text-body">{{ $materiaSelecionada->nome }}</strong>
                        @else
                            Resumo de todas as matérias
                        @endif
                    </small>
                </div>
            </div>

            @if(!empty($dadosAssuntos))
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-body-secondary small text-uppercase" style="letter-spacing: 0.05em;">
                                <th class="ps-3 border-bottom border-secondary-subtle">Assunto</th>
                                <th class="text-center border-bottom border-secondary-subtle">Domínio</th>
                                <th class="text-center border-bottom border-secondary-subtle">Cartões</th>
                                <th class="text-center border-bottom border-secondary-subtle">Próxima revisão</th>
                                <th class="text-center pe-3 border-bottom border-secondary-subtle">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dadosAssuntos as $assunto)
                                <tr>
                                    <td class="ps-3 fw-semibold text-body">{{ $assunto['nome'] }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress" style="width: 80px; height: 6px; background-color: var(--bs-body-tertiary);">
                                                <div class="progress-bar 
                                                    @if($assunto['porcentagem_dominio'] >= 80) bg-success
                                                    @elseif($assunto['porcentagem_dominio'] >= 50) bg-primary
                                                    @elseif($assunto['porcentagem_dominio'] >= 20) bg-warning
                                                    @else bg-danger
                                                    @endif"
                                                    style="width: {{ $assunto['porcentagem_dominio'] }}%">
                                                </div>
                                            </div>
                                            <span class="small fw-bold text-body">{{ $assunto['porcentagem_dominio'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-body-tertiary text-body-secondary border border-secondary-subtle">{{ $assunto['total'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($assunto['proxima_revisao'])
                                            @if($assunto['proxima_revisao']->isPast())
                                                <span class="badge bg-danger-subtle text-danger-emphasis">Atrasada</span>
                                            @elseif($assunto['proxima_revisao']->isToday())
                                                <span class="badge bg-warning-subtle text-warning-emphasis">Hoje</span>
                                            @else
                                                <small class="text-body-secondary">{{ $assunto['proxima_revisao']->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <small class="text-body-secondary">-</small>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        @php
                                            $statusClass = match($assunto['status']) {
                                                'Dominado' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
                                                'Em progresso' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
                                                'Aprendizado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                default => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle'
                                            };
                                        @endphp
                                        <span class="badge rounded-pill {{ $statusClass }}">{{ $assunto['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-body-secondary">
                    <p class="mb-0">Nenhum assunto encontrado com os filtros selecionados.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LEGENDA DAS CAIXAS LEITNER --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm bg-body-tertiary">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-body">Caixas do método Leitner</h6>
            <div class="row g-2">
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Cx 1</span>
                        <small class="text-body-secondary">1 dia</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Cx 2</span>
                        <small class="text-body-secondary">3 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Cx 3</span>
                        <small class="text-body-secondary">7 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">Cx 4</span>
                        <small class="text-body-secondary">15 dias</small>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Cx 5</span>
                        <small class="text-body-secondary">30 dias</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lê as cores do Bootstrap dinamicamente para garantir compatibilidade total com Dark Mode
    const style = getComputedStyle(document.body);
    const colorPrimary = style.getPropertyValue('--bs-primary').trim() || '#0d6efd';
    const colorBody = style.getPropertyValue('--bs-body-color').trim() || '#dee2e6';
    const colorGrid = style.getPropertyValue('--bs-border-color').trim() || 'rgba(255, 255, 255, 0.1)';
    
    // Cores específicas para o gráfico de rosca (usando as cores padrão do Bootstrap)
    const colorDanger = style.getPropertyValue('--bs-danger').trim() || '#dc3545';
    const colorWarning = style.getPropertyValue('--bs-warning').trim() || '#ffc107';
    const colorInfo = style.getPropertyValue('--bs-info').trim() || '#0dcaf0';
    const colorSuccess = style.getPropertyValue('--bs-success').trim() || '#198754';

    Chart.defaults.font.family = "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;
    Chart.defaults.color = colorBody;

    // ==========================================
    // GRÁFICO 1: LINHA (Curva de Aprendizagem)
    // ==========================================
    @if(!empty($labelsLinha))
        const ctxLinha = document.getElementById('graficoLinha').getContext('2d');
        
        // Gradiente adaptável (usa a cor primária com opacidade)
        const gradiente = ctxLinha.createLinearGradient(0, 0, 0, 320);
        gradiente.addColorStop(0, colorPrimary.replace(')', ', 0.3)').replace('rgb', 'rgba')); 
        gradiente.addColorStop(1, colorPrimary.replace(')', ', 0.0)').replace('rgb', 'rgba'));

        new Chart(ctxLinha, {
            type: 'line',
            data: {
                labels: @json($labelsLinha),
                datasets: [{
                    label: '% de Acertos',
                    data: @json($dadosLinha),
                    borderColor: colorPrimary,
                    backgroundColor: gradiente,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colorPrimary,
                    pointBorderColor: style.getPropertyValue('--bs-body-bg').trim() || '#212529',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: style.getPropertyValue('--bs-body-bg').trim() || '#212529',
                        titleColor: colorBody,
                        bodyColor: colorBody,
                        borderColor: colorGrid,
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 6,
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
                            color: colorBody
                        },
                        grid: { color: colorGrid }
                    },
                    x: {
                        ticks: { color: colorBody, maxRotation: 0 },
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
                    backgroundColor: [colorDanger, colorWarning, colorInfo, colorPrimary, colorSuccess],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: colorBody,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: style.getPropertyValue('--bs-body-bg').trim() || '#212529',
                        titleColor: colorBody,
                        bodyColor: colorBody,
                        borderColor: colorGrid,
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 6,
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
@endpush