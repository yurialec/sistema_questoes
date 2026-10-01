@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Animação suave do anel SVG (usando variáveis do Bootstrap) */
    .accuracy-ring circle.ring-fill {
        transition: stroke-dashoffset 1s ease;
    }

    /* Animação minimalista do Streak */
    @keyframes pulse-fire {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .streak-icon {
        animation: pulse-fire 2s ease-in-out infinite;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════
         SUGESTÃO DE ESTUDO DE HOJE
    ═══════════════════════════════════════════════ --}}
    @if($sugestaoHoje)
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-bullseye text-primary"></i>
                        <h5 class="mb-0 fw-bold">Sugestão de Estudo para Hoje</h5>
                    </div>
                    <h2 class="fw-bold mb-2 text-body">{{ $sugestaoHoje->nome }}</h2>
                    <p class="mb-0 text-body-secondary">
                        @php
                            $errosPendentes = \App\Models\CadernoErro::join('questoes', 'questoes.id', '=', 'caderno_erros.questao_id')
                                ->where('caderno_erros.user_id', auth()->id())
                                ->where('questoes.materia_id', $sugestaoHoje->id)
                                ->where('caderno_erros.status', 'pendente')
                                ->count();
                            
                            $motivo = $errosPendentes > 0 
                                ? "Você tem {$errosPendentes} erro(s) pendente(s) para revisar nesta matéria." 
                                : "Esta é a matéria com menor progresso ou próxima no seu ciclo de estudos.";
                        @endphp
                        {{ $motivo }}
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('responder', ['materia_id' => [$sugestaoHoje->id]]) }}" class="btn btn-primary fw-semibold px-4">
                        <i class="fas fa-play me-2"></i> Estudar Agora
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <p class="text-uppercase text-body-secondary small fw-semibold mb-3" style="letter-spacing:.08em">Visão geral</p>

    <div class="row g-3 mb-4">
        {{-- Taxa de acerto (anel SVG adaptado para Dark Mode) --}}
        <div class="col-12 col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-4 p-4">
                    @php
                    $circumference = 2 * M_PI * 40;
                    $offset = $circumference - ($percentual / 100) * $circumference;
                    
                    // Usa classes do Bootstrap para definir a cor via variável CSS
                    $ringColorClass = match(true) {
                        $percentual >= 70 => 'success',
                        $percentual >= 50 => 'primary',
                        default => 'danger',
                    };
                    @endphp

                    <div class="flex-shrink-0">
                        <svg width="110" height="110" viewBox="0 0 100 100" class="accuracy-ring" aria-label="Taxa de acerto: {{ $percentual }}%">
                            <!-- Fundo do anel usa a cor de borda padrão do tema -->
                            <circle cx="50" cy="50" r="40" fill="none" stroke="var(--bs-border-color)" stroke-width="8" />
                            <!-- Preenchimento usa a variável de cor do Bootstrap (success, primary ou danger) -->
                            <circle cx="50" cy="50" r="40" fill="none" stroke="var(--bs-{{ $ringColorClass }})" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference }}" data-offset="{{ $offset }}" class="ring-fill" transform="rotate(-90 50 50)" />
                            
                            <text x="50" y="48" text-anchor="middle" font-size="20" font-weight="700" fill="var(--bs-body-color)">{{ $percentual }}%</text>
                            <text x="50" y="64" text-anchor="middle" font-size="10" fill="var(--bs-secondary-color)">acertos</text>
                        </svg>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-body-secondary small fw-semibold text-uppercase mb-1" style="letter-spacing:.05em">Questões respondidas</p>
                        <h2 class="fw-bold mb-0 lh-1 text-body">{{ number_format($total) }}</h2>
                        <p class="text-body-secondary small mb-3">no total</p>
                        <div class="d-flex gap-4">
                            <div>
                                <span class="fs-5 fw-bold text-success">{{ $acertos }}</span>
                                <span class="badge rounded-pill ms-1 bg-success-subtle text-success fw-semibold">{{ $total > 0 ? round($acertos/$total*100) : 0 }}%</span>
                                <p class="text-body-secondary small mb-0 mt-1">acertos</p>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <span class="fs-5 fw-bold text-danger">{{ $erros }}</span>
                                <span class="badge rounded-pill ms-1 bg-danger-subtle text-danger fw-semibold">{{ $total > 0 ? round($erros/$total*100) : 0 }}%</span>
                                <p class="text-body-secondary small mb-0 mt-1">erros</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPIs rápidos --}}
        <div class="col-12 col-md-7">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-2 p-2 bg-primary-subtle text-primary"><i class="fas fa-check-circle"></i></span>
                                <span class="small fw-semibold text-uppercase text-body-secondary" style="letter-spacing:.05em">Acertos</span>
                            </div>
                            <p class="fs-2 fw-bold mb-0 text-success lh-1">{{ $acertos }}</p>
                            <p class="small text-body-secondary mt-1 mb-0">de {{ $total }} tentativas</p>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-2 p-2 bg-danger-subtle text-danger"><i class="fas fa-times-circle"></i></span>
                                <span class="small fw-semibold text-uppercase text-body-secondary" style="letter-spacing:.05em">Erros</span>
                            </div>
                            <p class="fs-2 fw-bold mb-0 text-danger lh-1">{{ $erros }}</p>
                            <p class="small text-body-secondary mt-1 mb-0">{{ $erros > 0 ? 'revise os conteúdos' : 'nenhum erro ainda!' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="small fw-semibold text-uppercase text-body-secondary mb-1" style="letter-spacing:.05em">Nível do estudante</p>
                                    <p class="fs-5 fw-bold mb-0 text-primary">{{ $nivel['titulo'] }}</p>
                                    @if($nivel['proximo'])
                                    <p class="small text-body-secondary mb-0">Próximo: <strong class="text-body">{{ $nivel['proximo'] }}</strong> em {{ $nivel['meta'] - $total }} questões</p>
                                    @else
                                    <p class="small text-body-secondary mb-0">Nível máximo alcançado! 🏆</p>
                                    @endif
                                </div>
                                <i class="fas fa-graduation-cap fa-2x text-body-tertiary"></i>
                            </div>

                            @if($nivel['meta'])
                            @php
                            $nivelInicio = match($nivel['titulo']) {
                                'Novato' => 0, 'Iniciante' => 10, 'Estudioso' => 50,
                                'Dedicado' => 200, 'Avançado' => 500, default => 0,
                            };
                            $nivelProg = $nivel['meta'] - $nivelInicio;
                            $nivelAtual = max(0, $total - $nivelInicio);
                            $nivelPct = min(100, round(($nivelAtual / $nivelProg) * 100));
                            @endphp
                            <div class="progress mt-3" style="height:6px; border-radius:99px; background-color: var(--bs-secondary-bg);">
                                <div class="progress-bar bg-primary" role="progressbar" style="width:{{ $nivelPct }}%" aria-valuenow="{{ $nivelPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         DESEMPENHO DIÁRIO - GRÁFICO
    ══════════════════════════════════════════════ --}}
    <p class="text-uppercase text-body-secondary small fw-semibold mb-3" style="letter-spacing:.08em">Desempenho diário (últimos 14 dias)</p>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="chart-container" style="position: relative; height:300px; width:100%;">
                <canvas id="desempenhoChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         SEQUÊNCIA E RITMO
    ══════════════════════════════════════════════ --}}
    <p class="text-uppercase text-body-secondary small fw-semibold mb-3" style="letter-spacing:.08em">Sequência e ritmo</p>

    <div class="row g-3 mb-4">
        {{-- Streak --}}
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    @if($streak >= 2)
                    <span class="streak-icon d-block mb-1 text-warning" style="font-size:1.6rem">🔥</span>
                    @else
                    <i class="fas fa-calendar-day mb-2 d-block text-warning" style="font-size:1.4rem"></i>
                    @endif
                    <p class="fs-3 fw-bold mb-0 lh-1 text-body">{{ $streak }} <span class="small fw-normal text-body-secondary">{{ $streak === 1 ? 'dia' : 'dias' }}</span></p>
                    <p class="small text-body-secondary mb-2">sequência atual</p>
                    
                    @if($streak === 0)
                        <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold">Comece hoje!</span>
                    @elseif($streak < 7)
                        <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold">Continue assim!</span>
                    @elseif($streak < 30)
                        <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Incrível! 🏅</span>
                    @else
                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">Lendário! 🏆</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Último estudo --}}
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <i class="fas fa-calendar-check mb-2 d-block text-success" style="font-size:1.4rem"></i>
                    <p class="fs-3 fw-bold mb-0 lh-1 text-body">{{ $ultimaRespostaLabel }}</p>
                    <p class="small text-body-secondary mb-2">último estudo</p>
                    
                    @if($diasSemResponder === 0)
                        <span class="badge rounded-pill bg-success-subtle text-success fw-semibold">Você estudou hoje! ✅</span>
                    @elseif($diasSemResponder === 1)
                        <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold">Não perca o ritmo!</span>
                    @elseif($diasSemResponder !== null && $diasSemResponder > 1)
                        <span class="badge rounded-pill bg-danger-subtle text-danger fw-semibold">Volte a estudar!</span>
                    @else
                        <span class="badge rounded-pill bg-body-tertiary text-body-secondary fw-semibold">Nenhuma resposta ainda</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Matérias cadastradas --}}
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <i class="fas fa-book mb-2 d-block text-primary" style="font-size:1.4rem"></i>
                    <p class="fs-3 fw-bold mb-0 lh-1 text-body">{{ $materias->count() }}</p>
                    <p class="small text-body-secondary mb-2">matérias cadastradas</p>
                    @php $materiasComResposta = $materias->where('questoes_respondidas', '>', 0)->count(); @endphp
                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold">{{ $materiasComResposta }} iniciada{{ $materiasComResposta !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('desempenhoChart');
    if (!canvas) return;

    // Lê as cores diretamente das variáveis CSS do Bootstrap para garantir compatibilidade total com Dark Mode
    const style = getComputedStyle(document.body);
    const colorSuccess = style.getPropertyValue('--bs-success').trim() || '#198754';
    const colorDanger = style.getPropertyValue('--bs-danger').trim() || '#dc3545';
    const colorBody = style.getPropertyValue('--bs-body-color').trim() || '#dee2e6';
    const colorGrid = style.getPropertyValue('--bs-border-color').trim() || 'rgba(255, 255, 255, 0.1)';

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($datasFormatadas) !!},
            datasets: [
                {
                    label: 'Acertos',
                    data: {!! json_encode($acertosData) !!},
                    backgroundColor: colorSuccess,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Erros',
                    data: {!! json_encode($errosData) !!},
                    backgroundColor: colorDanger,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        color: colorBody,
                        font: { size: 12, weight: '500' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    borderColor: colorGrid,
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' questão(ões)';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: colorBody, font: { size: 11 }, maxRotation: 0 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: colorGrid, drawBorder: false },
                    border: { display: false },
                    ticks: { color: colorBody, stepSize: 1, precision: 0, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush