@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── Animação do anel SVG ─────────────────────────────── */
    .accuracy-ring circle.ring-fill {
        transition: stroke-dashoffset 1s ease;
    }

    /* ── Cards padrão ────────────────────────────────────── */
    .stat-card {
        border: 1px solid #f0f0f0;
        transition: box-shadow .15s ease;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06) !important; }

    /* ── Progress bar matérias ───────────────────────────── */
    .materia-progress .progress {
        height: 5px;
        border-radius: 99px;
    }

    /* ── Streak fire pulse ───────────────────────────────── */
    @keyframes pulse-fire {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.15); }
    }
    .streak-icon { animation: pulse-fire 1.4s ease-in-out infinite; display: inline-block; }
</style>
@endpush

@section('content')
<div class="container py-4 mt-4">

    {{-- ══════════════════════════════════════════════
         CABEÇALHO
    ══════════════════════════════════════════════ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-semibold mb-0">Dashboard</h4>
            <p class="text-muted small mb-0">Acompanhe seu desempenho nos estudos</p>
        </div>
        <a href="{{ route('responder') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-play me-1"></i> Responder questões
        </a>

        
    </div>

    {{-- ══════════════════════════════════════════════
         LINHA 1 — VISÃO GERAL
    ══════════════════════════════════════════════ --}}
    <p class="text-uppercase text-muted small fw-semibold mb-2" style="letter-spacing:.06em">Visão geral</p>

    <div class="row g-3 mb-4">

        {{-- Taxa de acerto (anel SVG) --}}
        <div class="col-12 col-md-5">
            <div class="card stat-card border shadow-none h-100">
                <div class="card-body d-flex align-items-center gap-4 p-4">

                    {{-- Anel SVG --}}
                    @php
                        $circumference = 2 * M_PI * 40; // ≈ 251.33
                        $offset = $circumference - ($percentual / 100) * $circumference;
                        $ringColor = match(true) {
                            $percentual >= 70 => '#10B981',
                            $percentual >= 50 => '#6366F1',
                            default           => '#EF4444',
                        };
                    @endphp

                    <div class="flex-shrink-0">
                        <svg width="110" height="110" viewBox="0 0 100 100"
                             class="accuracy-ring" aria-label="Taxa de acerto: {{ $percentual }}%">
                            <circle cx="50" cy="50" r="40"
                                    fill="none" stroke="#f0f0f0" stroke-width="10"/>
                            <circle cx="50" cy="50" r="40"
                                    fill="none"
                                    stroke="{{ $ringColor }}"
                                    stroke-width="10"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $circumference }}"
                                    data-offset="{{ $offset }}"
                                    class="ring-fill"
                                    transform="rotate(-90 50 50)"/>
                            <text x="50" y="46" text-anchor="middle"
                                  font-size="18" font-weight="600"
                                  fill="#1e293b">{{ $percentual }}%</text>
                            <text x="50" y="59" text-anchor="middle"
                                  font-size="9" fill="#94a3b8">acertos</text>
                        </svg>
                    </div>

                    <div class="flex-grow-1">
                        <p class="text-muted small fw-semibold text-uppercase mb-1"
                           style="letter-spacing:.05em">questões respondidas</p>
                        <h2 class="fw-semibold mb-0 lh-1">{{ number_format($total) }}</h2>
                        <p class="text-muted small mb-3">no total</p>

                        <div class="d-flex gap-3">
                            <div>
                                <span class="fs-5 fw-semibold text-success">{{ $acertos }}</span>
                                <span class="badge rounded-pill ms-1"
                                      style="background:#D1FAE5;color:#065F46;font-size:.7rem">
                                    {{ $total > 0 ? round($acertos/$total*100) : 0 }}%
                                </span>
                                <p class="text-muted small mb-0">acertos</p>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <span class="fs-5 fw-semibold text-danger">{{ $erros }}</span>
                                <span class="badge rounded-pill ms-1"
                                      style="background:#FEE2E2;color:#7F1D1D;font-size:.7rem">
                                    {{ $total > 0 ? round($erros/$total*100) : 0 }}%
                                </span>
                                <p class="text-muted small mb-0">erros</p>
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
                    <div class="card stat-card border shadow-none h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-2 p-2"
                                      style="background:#EEF2FF">
                                    <i class="fas fa-check-circle text-indigo"
                                       style="color:#6366F1;font-size:.85rem"></i>
                                </span>
                                <span class="small fw-semibold text-uppercase text-muted"
                                      style="letter-spacing:.05em">Acertos</span>
                            </div>
                            <p class="fs-2 fw-semibold mb-0 text-success lh-1">{{ $acertos }}</p>
                            <p class="small text-muted mt-1 mb-0">
                                de {{ $total }} tentativas
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card stat-card border shadow-none h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge rounded-2 p-2"
                                      style="background:#FEF2F2">
                                    <i class="fas fa-times-circle"
                                       style="color:#EF4444;font-size:.85rem"></i>
                                </span>
                                <span class="small fw-semibold text-uppercase text-muted"
                                      style="letter-spacing:.05em">Erros</span>
                            </div>
                            <p class="fs-2 fw-semibold mb-0 text-danger lh-1">{{ $erros }}</p>
                            <p class="small text-muted mt-1 mb-0">
                                {{ $erros > 0 ? 'revise os conteúdos' : 'nenhum erro ainda!' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card stat-card border shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="small fw-semibold text-uppercase text-muted mb-1"
                                       style="letter-spacing:.05em">Nível do estudante</p>
                                    <p class="fs-5 fw-semibold mb-0" style="color:#6366F1">
                                        {{ $nivel['titulo'] }}
                                    </p>
                                    @if($nivel['proximo'])
                                        <p class="small text-muted mb-0">
                                            Próximo: <strong>{{ $nivel['proximo'] }}</strong>
                                            em {{ $nivel['meta'] - $total }} questões
                                        </p>
                                    @else
                                        <p class="small text-muted mb-0">Nível máximo alcançado! 🏆</p>
                                    @endif
                                </div>
                                <i class="fas fa-graduation-cap fa-2x"
                                   style="color:#C7D2FE"></i>
                            </div>

                            @if($nivel['meta'])
                                @php
                                    $nivelInicio = match($nivel['titulo']) {
                                        'Novato'     => 0,
                                        'Iniciante'  => 10,
                                        'Estudioso'  => 50,
                                        'Dedicado'   => 200,
                                        'Avançado'   => 500,
                                        default      => 0,
                                    };
                                    $nivelProg = $nivel['meta'] - $nivelInicio;
                                    $nivelAtual = $total - $nivelInicio;
                                    $nivelPct = min(100, round(($nivelAtual / $nivelProg) * 100));
                                @endphp
                                <div class="progress mt-2" style="height:4px;border-radius:99px">
                                    <div class="progress-bar"
                                         role="progressbar"
                                         style="width:{{ $nivelPct }}%;background:#6366F1"
                                         aria-valuenow="{{ $nivelPct }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         LINHA 2 — SEQUÊNCIA E RITMO
    ══════════════════════════════════════════════ --}}
    <p class="text-uppercase text-muted small fw-semibold mb-2" style="letter-spacing:.06em">
        Sequência e ritmo
    </p>

    <div class="row g-3 mb-4">

        {{-- Streak --}}
        <div class="col-12 col-sm-4">
            <div class="card stat-card border shadow-none h-100">
                <div class="card-body p-3">
                    @if($streak >= 2)
                        <span class="streak-icon d-block mb-1" style="font-size:1.6rem">🔥</span>
                    @else
                        <i class="fas fa-calendar-day mb-2 d-block"
                           style="font-size:1.4rem;color:#F59E0B"></i>
                    @endif

                    <p class="fs-3 fw-semibold mb-0 lh-1">
                        {{ $streak }}
                        <span class="small fw-normal text-muted">
                            {{ $streak === 1 ? 'dia' : 'dias' }}
                        </span>
                    </p>
                    <p class="small text-muted mb-1">sequência atual</p>

                    @if($streak === 0)
                        <span class="badge rounded-pill" style="background:#FEF9C3;color:#713F12;font-size:.7rem">
                            Comece hoje!
                        </span>
                    @elseif($streak < 7)
                        <span class="badge rounded-pill" style="background:#FEF3C7;color:#92400E;font-size:.7rem">
                            Continue assim!
                        </span>
                    @elseif($streak < 30)
                        <span class="badge rounded-pill" style="background:#D1FAE5;color:#065F46;font-size:.7rem">
                            Incrível! 🏅
                        </span>
                    @else
                        <span class="badge rounded-pill" style="background:#EEF2FF;color:#3730A3;font-size:.7rem">
                            Lendário! 🏆
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Último estudo --}}
        <div class="col-12 col-sm-4">
            <div class="card stat-card border shadow-none h-100">
                <div class="card-body p-3">
                    <i class="fas fa-calendar-check mb-2 d-block"
                       style="font-size:1.4rem;color:#10B981"></i>

                    <p class="fs-3 fw-semibold mb-0 lh-1">{{ $ultimaRespostaLabel }}</p>
                    <p class="small text-muted mb-1">último estudo</p>

                    @if($diasSemResponder === 0)
                        <span class="badge rounded-pill" style="background:#D1FAE5;color:#065F46;font-size:.7rem">
                            Você estudou hoje! ✅
                        </span>
                    @elseif($diasSemResponder === 1)
                        <span class="badge rounded-pill" style="background:#FEF3C7;color:#92400E;font-size:.7rem">
                            Não perca o ritmo!
                        </span>
                    @elseif($diasSemResponder !== null && $diasSemResponder > 1)
                        <span class="badge rounded-pill" style="background:#FEE2E2;color:#7F1D1D;font-size:.7rem">
                            Volte a estudar!
                        </span>
                    @else
                        <span class="badge rounded-pill" style="background:#F1F5F9;color:#475569;font-size:.7rem">
                            Nenhuma resposta ainda
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Matérias cadastradas --}}
        <div class="col-12 col-sm-4">
            <div class="card stat-card border shadow-none h-100">
                <div class="card-body p-3">
                    <i class="fas fa-book mb-2 d-block"
                       style="font-size:1.4rem;color:#6366F1"></i>

                    <p class="fs-3 fw-semibold mb-0 lh-1">{{ $materias->count() }}</p>
                    <p class="small text-muted mb-1">matérias cadastradas</p>

                    @php
                        $materiasComResposta = $materias->where('questoes_respondidas', '>', 0)->count();
                    @endphp

                    <span class="badge rounded-pill" style="background:#EEF2FF;color:#3730A3;font-size:.7rem">
                        {{ $materiasComResposta }} iniciada{{ $materiasComResposta !== 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════
         LINHA 3 — PROGRESSO POR MATÉRIA
    ══════════════════════════════════════════════ --}}
    <p class="text-uppercase text-muted small fw-semibold mb-2" style="letter-spacing:.06em">
        Progresso por matéria
    </p>

    <div class="card border shadow-none">
        <div class="card-header d-flex align-items-center gap-2 bg-white border-bottom py-3">
            <span class="badge rounded-2 p-2" style="background:#EEF2FF">
                <i class="fas fa-layer-group" style="color:#6366F1;font-size:.85rem"></i>
            </span>
            <div>
                <p class="fw-semibold mb-0 small">Matérias</p>
                <p class="text-muted mb-0" style="font-size:.75rem">
                    Questões respondidas e taxa de acerto por matéria
                </p>
            </div>
        </div>

        <div class="card-body p-0">
            @forelse($materias->sortByDesc('questoes_respondidas') as $materia)
                <div class="materia-progress px-4 py-3
                            {{ !$loop->last ? 'border-bottom' : '' }}">

                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $materia->nome }}</span>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">
                                {{ $materia->questoes_respondidas }} / {{ $materia->total_questoes }}
                                questão{{ $materia->total_questoes !== 1 ? 'ões' : '' }}
                            </span>

                            @if($materia->total_tentativas > 0)
                                @php
                                    $accColor = match(true) {
                                        $materia->percentual_acertos >= 70 => 'background:#D1FAE5;color:#065F46',
                                        $materia->percentual_acertos >= 50 => 'background:#EEF2FF;color:#3730A3',
                                        default                            => 'background:#FEE2E2;color:#7F1D1D',
                                    };
                                @endphp
                                <span class="badge rounded-pill"
                                      style="font-size:.7rem;{{ $accColor }}">
                                    {{ $materia->percentual_acertos }}% acertos
                                </span>
                            @else
                                <span class="badge rounded-pill"
                                      style="font-size:.7rem;background:#F1F5F9;color:#64748B">
                                    não iniciado
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="progress">
                        @php
                            $barColor = match(true) {
                                $materia->percentual_progresso >= 70 => '#10B981',
                                $materia->percentual_progresso >= 30 => '#6366F1',
                                $materia->percentual_progresso > 0   => '#F59E0B',
                                default                              => '#E2E8F0',
                            };
                        @endphp
                        <div class="progress-bar"
                             role="progressbar"
                             style="width:{{ max($materia->percentual_progresso, 0) }}%;
                                    background:{{ $barColor }};
                                    border-radius:99px"
                             aria-valuenow="{{ $materia->percentual_progresso }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-book-open fa-2x mb-2 d-block" style="opacity:.3"></i>
                    <p class="small mb-0">Nenhuma matéria cadastrada ainda.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Anima o anel SVG ao carregar a página
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.ring-fill').forEach(circle => {
            const targetOffset = parseFloat(circle.dataset.offset);
            // Começa no valor "vazio" e anima para o valor real
            requestAnimationFrame(() => {
                circle.style.strokeDashoffset = targetOffset;
            });
        });
    });
</script>
@endpush