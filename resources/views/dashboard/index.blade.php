@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .study-dashboard {
        --sd-surface: var(--bs-body-bg, #fff);
        --sd-muted: var(--bs-secondary-color, #64748b);
        --sd-line: var(--bs-border-color, #e2e8f0);
        --sd-track: var(--bs-tertiary-bg, #f1f5f9);
        --sd-accent: var(--bs-primary, #4f46e5);
        --sd-green: var(--bs-success-text-emphasis, #146c43);
        --sd-red: var(--bs-danger-text-emphasis, #b02a37);
        max-width: 1440px; margin: 0 auto; color: var(--bs-body-color, #172033);
    }
    .sd-heading { font-size: clamp(1.55rem, 2.5vw, 2rem); font-weight: 750; letter-spacing: -.035em; margin: 0; }
    .sd-subtitle { font-size: .875rem; color: var(--sd-muted); margin: .35rem 0 0; }
    .sd-card { background: var(--sd-surface); border: 1px solid var(--sd-line); border-radius: 16px; padding: 1.25rem; min-width: 0; }
    .sd-grid { display: grid; gap: 1rem; margin-bottom: 1.25rem; }
    .sd-hero-grid { grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); }
    .sd-hero-grid > :only-child { grid-column: 1 / -1; }
    .sd-kpis { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .sd-analysis { grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); }
    .sd-bank-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
    .sd-hero { position: relative; overflow: hidden; color: #fff; background: linear-gradient(120deg, #25365e, #344d90); border: 0; display: flex; flex-direction: column; align-items: flex-start; justify-content: center; min-height: 208px; padding: 1.5rem; }
    .sd-hero::after { content: ''; position: absolute; width: 260px; height: 260px; right: -85px; top: -120px; border: 42px solid rgba(255,255,255,.07); border-radius: 50%; pointer-events: none; }
    .sd-hero > * { position: relative; z-index: 1; }
    .sd-eyebrow { font-size: .72rem; font-weight: 650; letter-spacing: .08em; text-transform: uppercase; margin: 0 0 .6rem; }
    .sd-hero .sd-eyebrow, .sd-hero .sd-subtitle { color: #e2e9fb; }
    .sd-hero h2 { font-size: clamp(1.3rem, 2.3vw, 1.85rem); font-weight: 750; margin: 0; overflow-wrap: anywhere; }
    .sd-hero .btn { margin-top: 1.25rem; background: #fff; color: #25365e; border-color: #fff; font-weight: 650; padding: .65rem 1rem; border-radius: 10px; }
    .sd-hero .btn:hover { background: #e8efff; border-color: #e8efff; }
    .sd-title { font-size: 1rem; font-weight: 700; letter-spacing: -.015em; margin: 0; }
    .sd-label { color: var(--sd-muted); font-size: .8rem; font-weight: 550; }
    .sd-value { font-size: clamp(1.5rem, 2.8vw, 2rem); font-weight: 750; line-height: 1.2; letter-spacing: -.04em; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .sd-icon { width: 36px; height: 36px; display: inline-grid; place-items: center; border-radius: 10px; color: var(--sd-accent); background: var(--sd-track); flex-shrink: 0; }
    .sd-progress { height: 8px; background: var(--sd-track); border-radius: 99px; overflow: hidden; }
    .sd-progress .progress-bar { border-radius: inherit; }
    .sd-goal { display: flex; flex-direction: column; justify-content: space-between; gap: .8rem; }
    .sd-status { font-size: .75rem; font-weight: 600; border-radius: 6px; padding: .28rem .55rem; background: var(--sd-track); color: var(--sd-muted); }
    .sd-status[data-rank="aprovado"], .sd-status[data-rank="excelente"] { color: var(--sd-green); }
    .sd-status[data-rank="ruim"] { color: var(--sd-red); }
    .sd-quiet { color: var(--sd-muted); font-size: .8rem; }
    .sd-chart { position: relative; height: 260px; width: 100%; }
    .sd-rhythm-row { display: flex; align-items: center; gap: .75rem; padding: .85rem 0; border-bottom: 1px solid var(--sd-line); }
    .sd-rhythm-row:last-child { border-bottom: 0; padding-bottom: 0; }
    .sd-bank { border: 1px solid var(--sd-line); border-radius: 12px; padding: 1rem; min-width: 0; }
    .sd-bank h3 { font-size: .95rem; font-weight: 650; overflow-wrap: anywhere; margin: 0; }
    .sd-empty { border: 1px dashed var(--sd-line); border-radius: 12px; padding: 1.5rem; text-align: center; }
    .sd-delete { min-width: 36px; min-height: 36px; }
    .study-dashboard :is(a, button):focus-visible { outline: 3px solid var(--sd-accent); outline-offset: 3px; }
    .sd-hero a:focus-visible { outline-color: #fff; }
    @media (max-width: 1199.98px) { .sd-analysis { grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr); } .sd-bank-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 991.98px) { .sd-hero-grid, .sd-analysis { grid-template-columns: 1fr; } .sd-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575.98px) { .sd-card { padding: 1rem; border-radius: 12px; } .sd-grid { gap: .75rem; } .sd-bank-grid { grid-template-columns: 1fr; } .sd-chart { height: 230px; } .sd-value { font-size: 1.5rem; } }
    @media (prefers-reduced-motion: reduce) { .study-dashboard .progress-bar { transition: none; } }
    @media (max-width: 575.98px) { .sd-kpis .sd-icon { width: 28px; height: 28px; } .sd-kpis .sd-label { font-size: .74rem; overflow-wrap: anywhere; } .sd-rhythm-row { flex-wrap: wrap; } }
    @media (max-width: 359.98px) { .sd-kpis { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="study-dashboard container-fluid px-3 px-lg-4 py-4">
    <header class="mb-4">
        <h1 class="sd-heading">Meu painel de estudos</h1>
        <p class="sd-subtitle">Acompanhe seu desempenho e organize os próximos passos da sua preparação.</p>
    </header>

    @foreach(['success', 'error'] as $tipo)
        @if(session($tipo))
            <div class="alert alert-{{ $tipo === 'error' ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">
                {{ session($tipo) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif
    @endforeach

    @php
        $materiasComResposta = $materias->where('questoes_respondidas', '>', 0)->count();
        $aproveitamento = max(0, min(100, (float) $percentual));
        $nivelPct = 0;
        $nivelRestante = max(0, ($nivel['meta'] ?? 0) - $total);
        if (!empty($nivel['meta'])) {
            $nivelInicio = match($nivel['titulo']) {
                'Novato' => 0, 'Iniciante' => 10, 'Estudioso' => 50,
                'Dedicado' => 200, 'Avançado' => 500, default => 0,
            };
            $nivelProg = $nivel['meta'] - $nivelInicio;
            $nivelPct = $nivelProg > 0 ? max(0, min(100, round(($total - $nivelInicio) / $nivelProg * 100))) : 100;
        }
        $temDesempenhoDiario = collect($acertosData)->sum() + collect($errosData)->sum() > 0;
    @endphp

    <section class="sd-grid sd-hero-grid" aria-label="Plano de estudo">
        @if($sugestaoHoje)
            @php
                $filtrosEstudarAgora = ($metaAtiva && $metaAtiva->filtroSalvo) ? $metaAtiva->filtroSalvo->filtros : [];
                $filtrosEstudarAgora['materia_id'] = [$sugestaoHoje->id];
            @endphp
            <article class="sd-card sd-hero">
                <p class="sd-eyebrow"><i class="fa-solid fa-bullseye me-2" aria-hidden="true"></i>Seu próximo passo</p>
                <h2>{{ $sugestaoHoje->nome }}</h2>
                <p class="sd-subtitle">Sugestão de estudo para hoje</p>
                <a href="{{ route('responder', $filtrosEstudarAgora) }}" class="btn">
                    <i class="fa-solid fa-play me-2" aria-hidden="true"></i>Estudar agora
                </a>
            </article>
        @endif
        <article class="sd-card sd-goal">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <h2 class="sd-title"><i class="fa-solid fa-bullseye me-2 text-primary" aria-hidden="true"></i>Meta de aprovação</h2>
                @if($metaAtiva)
                    <form action="{{ route('metas-aprovacao.destroy') }}" method="POST" class="m-0" onsubmit="return confirm('Tem certeza que deseja excluir sua meta atual? Todo o progresso será perdido.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-secondary sd-delete" aria-label="Excluir meta" title="Excluir meta"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                    </form>
                @endif
            </div>
            @if($metaAtiva)
                @php
                    $rankConfig = [
                        'ruim' => ['texto' => 'Ruim', 'status' => 'Priorize os pontos que precisam de revisão.', 'barra' => 'bg-danger'],
                        'regular' => ['texto' => 'Regular', 'status' => 'Continue praticando para avançar.', 'barra' => 'bg-warning'],
                        'bom' => ['texto' => 'Bom', 'status' => 'Mantenha o ritmo de estudos.', 'barra' => 'bg-info'],
                        'excelente' => ['texto' => 'Excelente', 'status' => 'Você está perto da meta.', 'barra' => 'bg-success'],
                        'aprovado' => ['texto' => 'Aprovado', 'status' => 'Meta batida! Continue revisando.', 'barra' => 'bg-primary'],
                    ];
                    $config = $rankConfig[$metaAtiva->rank] ?? $rankConfig['ruim'];
                    $metaPct = max(0, min(100, (float) $metaAtiva->porcentagem));
                @endphp
                <div><div class="sd-label mb-1">Cargo-alvo</div><div class="fw-semibold text-break">{{ $metaAtiva->cargo->nome }}</div></div>
                <div>
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <span class="sd-value">{{ $metaAtiva->porcentagem }}<span class="fs-6">%</span></span>
                        <span class="sd-status" data-rank="{{ $metaAtiva->rank }}">{{ $config['texto'] }}</span>
                    </div>
                    <div class="progress sd-progress" role="progressbar" aria-label="Progresso da meta de aprovação" aria-valuenow="{{ $metaPct }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar {{ $config['barra'] }}" style="width: {{ $metaPct }}%"></div></div>
                    <p class="sd-quiet mb-0 mt-2">{{ $config['status'] }}</p>
                </div>
            @else
                <div><p class="mb-2 fw-semibold">Defina seu objetivo de estudo</p><p class="sd-quiet mb-3">Acompanhe seu progresso em direção ao cargo desejado.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalMetaAprovacao"><i class="fa-solid fa-plus me-2" aria-hidden="true"></i>Definir meta</button>
                </div>
            @endif
        </article>
    </section>

    <section aria-labelledby="sd-overview">
        <h2 id="sd-overview" class="sd-title mb-3">Visão geral</h2>
        <div class="sd-grid sd-kpis">
            <article class="sd-card">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3"><span class="sd-label">Aproveitamento</span><span class="sd-icon"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></span></div>
                <div class="sd-value">{{ $total > 0 ? $percentual . '%' : '—' }}</div>
                <div class="progress sd-progress mt-2" role="progressbar" aria-label="Aproveitamento" aria-valuenow="{{ $total > 0 ? $aproveitamento : 0 }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-primary" style="width: {{ $total > 0 ? $aproveitamento : 0 }}%"></div></div>
                <div class="sd-quiet mt-2">{{ $total > 0 ? 'Taxa de acertos' : 'Sem respostas ainda' }}</div>
            </article>
            <article class="sd-card">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3"><span class="sd-label">Questões respondidas</span><span class="sd-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span></div>
                <div class="sd-value">{{ number_format($total, 0, ',', '.') }}</div><div class="sd-quiet mt-2">Total acumulado</div>
            </article>
            <article class="sd-card">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3"><span class="sd-label">Acertos</span><span class="sd-icon" style="color:var(--sd-green)"><i class="fa-solid fa-check" aria-hidden="true"></i></span></div>
                <div class="sd-value" style="color:var(--sd-green)">{{ number_format($acertos, 0, ',', '.') }}</div><div class="sd-quiet mt-2">{{ $total > 0 ? round($acertos / $total * 100) . '% do total' : 'Sem respostas' }}</div>
            </article>
            <article class="sd-card">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-3"><span class="sd-label">Erros</span><span class="sd-icon" style="color:var(--sd-red)"><i class="fa-solid fa-xmark" aria-hidden="true"></i></span></div>
                <div class="sd-value" style="color:var(--sd-red)">{{ number_format($erros, 0, ',', '.') }}</div><div class="sd-quiet mt-2">{{ $total > 0 ? round($erros / $total * 100) . '% do total' : 'Sem respostas' }}</div>
            </article>
        </div>
    </section>

    <section class="sd-grid sd-analysis" aria-label="Desempenho e evolução">
        <article class="sd-card">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                <div><h2 class="sd-title">Desempenho diário</h2><p class="sd-subtitle">Questões respondidas nos últimos 14 dias</p></div>
                <span class="sd-status">Últimos 14 dias</span>
            </div>
            @if($temDesempenhoDiario)
                <div class="sd-chart"><canvas id="desempenhoChart" role="img" aria-label="Gráfico de acertos e erros por dia, nos últimos 14 dias"></canvas></div>
                <details class="mt-3 sd-quiet"><summary>Consultar dados do gráfico</summary>
                    <div class="table-responsive mt-2"><table class="table table-sm mb-0"><thead><tr><th scope="col">Dia</th><th scope="col">Acertos</th><th scope="col">Erros</th></tr></thead><tbody>
                        @foreach($datasFormatadas as $i => $dia)
                            <tr><th scope="row">{{ $dia }}</th><td>{{ $acertosData[$i] ?? 0 }}</td><td>{{ $errosData[$i] ?? 0 }}</td></tr>
                        @endforeach
                    </tbody></table></div>
                </details>
            @else
                <div class="sd-empty py-5"><span class="sd-icon mb-3"><i class="fa-solid fa-chart-column" aria-hidden="true"></i></span><p class="fw-semibold mb-1">Nenhum estudo nos últimos 14 dias</p><p class="sd-quiet mb-0">Responda questões para acompanhar sua evolução aqui.</p></div>
            @endif
        </article>
        <article class="sd-card">
            <h2 class="sd-title mb-3">Minha evolução</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="sd-icon"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
                <div class="flex-grow-1"><div class="sd-label">Nível do estudante</div><div class="fw-bold text-break">{{ $nivel['titulo'] }}</div></div>
            </div>
            @if($nivel['meta'])
                <div class="progress sd-progress mt-3" role="progressbar" aria-label="Progresso para o próximo nível" aria-valuenow="{{ $nivelPct }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-primary" style="width: {{ $nivelPct }}%"></div></div>
            @endif
            <p class="sd-quiet mt-2 mb-2">@if($nivel['proximo']) Faltam {{ $nivelRestante }} questões para <strong>{{ $nivel['proximo'] }}</strong>. @else Nível máximo alcançado! @endif</p>
            <div class="sd-rhythm-row"><span class="sd-icon" style="color:var(--bs-warning-text-emphasis, #805300)"><i class="fa-solid fa-fire" aria-hidden="true"></i></span><div class="flex-grow-1"><div class="sd-label">Sequência atual</div><div class="fw-bold">{{ $streak }} {{ $streak === 1 ? 'dia' : 'dias' }}</div></div><span class="sd-quiet text-end">{{ $streak === 0 ? 'Comece hoje' : ($streak < 7 ? 'Continue assim' : ($streak < 30 ? 'Incrível!' : 'Lendário!')) }}</span></div>
            <div class="sd-rhythm-row"><span class="sd-icon"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></span><div class="flex-grow-1"><div class="sd-label">Último estudo</div><div class="fw-bold text-break">{{ $ultimaRespostaLabel }}</div></div><span class="sd-quiet text-end">{{ $diasSemResponder === 0 ? 'Em dia' : ($diasSemResponder === 1 ? 'Mantenha o ritmo' : ($diasSemResponder !== null && $diasSemResponder > 1 ? 'Retome os estudos' : 'Sem respostas')) }}</span></div>
            <div class="sd-rhythm-row"><span class="sd-icon"><i class="fa-solid fa-book" aria-hidden="true"></i></span><div class="flex-grow-1"><div class="sd-label">Matérias cadastradas</div><div class="fw-bold">{{ $materias->count() }}</div></div><span class="sd-quiet text-end">{{ $materiasComResposta }} iniciada{{ $materiasComResposta !== 1 ? 's' : '' }}</span></div>
        </article>
    </section>

    <section class="sd-card" aria-labelledby="sd-banks">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div><h2 id="sd-banks" class="sd-title">Domínio da banca</h2><p class="sd-subtitle">Acompanhe seu desempenho por banca examinadora.</p></div>
            @unless($dominiosBanca->isEmpty())
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDominioBanca"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i>Cadastrar banca</button>
            @endunless
        </div>
        @if($dominiosBanca->isEmpty())
            <div class="sd-empty"><p class="fw-semibold mb-1">Nenhuma banca acompanhada</p><p class="sd-quiet mb-3">Selecione as bancas que deseja acompanhar.</p><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDominioBanca"><i class="fa-solid fa-plus me-1" aria-hidden="true"></i>Cadastrar banca</button></div>
        @else
            <div class="sd-bank-grid">
                @foreach($dominiosBanca as $dominio)
                    @php $porcentagemDominio = $dominio->porcentagemDominio(); $dominioPct = max(0, min(100, (float) $porcentagemDominio)); @endphp
                    <article class="sd-bank">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <h3>{{ $dominio->banca->nome }}</h3>
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('dashboard.dominio-bancas.destroy', $dominio) }}" method="POST" class="m-0" onsubmit="return confirm('Tem certeza que deseja excluir o domínio desta banca?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary sd-delete" aria-label="Excluir domínio de {{ $dominio->banca->nome }}" title="Excluir domínio"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                                </form>
                                <strong class="text-primary">{{ $porcentagemDominio }}%</strong>
                            </div>
                        </div>
                        <div class="progress sd-progress mb-3" role="progressbar" aria-label="Domínio de {{ $dominio->banca->nome }}" aria-valuenow="{{ $dominioPct }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-primary" style="width: {{ $dominioPct }}%"></div></div>
                        <div class="d-flex flex-wrap justify-content-between gap-2 sd-quiet"><span>Acertos: <strong style="color:var(--sd-green)">{{ $dominio->qtd_acertos }}</strong></span><span>Erros: <strong style="color:var(--sd-red)">{{ $dominio->qtd_erros }}</strong></span></div>
                        <div class="sd-quiet mt-2">Total de questões da banca: {{ $dominio->total_questoes_banca }}</div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>

{{-- Modal Domínio da Banca --}}
<div class="modal fade" id="modalDominioBanca" tabindex="-1"
     aria-labelledby="modalDominioBancaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('dashboard.dominio-bancas.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="modalDominioBancaLabel">
                    Cadastrar bancas para acompanhar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                @if($errosBancas->isNotEmpty())
                    <div class="alert alert-danger py-2">
                        @foreach($errosBancas as $erroBanca)
                            <div>{{ $erroBanca }}</div>
                        @endforeach
                    </div>
                @endif

                @forelse($bancas as $banca)
                    @php
                        $jaAcompanhada = in_array($banca->id, $bancasAcompanhadasIds);
                    @endphp
                    <div class="form-check py-2 border-bottom">
                        <input class="form-check-input" type="checkbox"
                               name="bancas[]" value="{{ $banca->id }}"
                               id="dominio-banca-{{ $banca->id }}"
                               @checked($jaAcompanhada) @disabled($jaAcompanhada)>
                        <label class="form-check-label w-100" for="dominio-banca-{{ $banca->id }}">
                            <span class="fw-medium">{{ $banca->nome }}</span>
                            <small class="d-block text-body-secondary">
                                {{ $banca->questoes_count }} questões
                                @if($jaAcompanhada)
                                    · já acompanhada
                                @endif
                            </small>
                        </label>
                    </div>
                @empty
                    <p class="text-body-secondary mb-0">Nenhuma banca cadastrada.</p>
                @endforelse
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" @disabled($todasBancasAcompanhadas)>
                    Acompanhar selecionadas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($abrirModalDominioBanca)
        const modalDominioBanca = document.getElementById('modalDominioBanca');
        if (modalDominioBanca && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(modalDominioBanca).show();
        }
    @endif

    const canvas = document.getElementById('desempenhoChart');
    if (!canvas || typeof Chart === 'undefined') return;

    const style = getComputedStyle(canvas);
    const colorSuccess = style.getPropertyValue('--bs-success').trim() || '#198754';
    const colorDanger = style.getPropertyValue('--bs-danger').trim() || '#dc3545';
    const colorBody = style.getPropertyValue('--bs-body-color').trim() || '#64748b';
    const colorGrid = style.getPropertyValue('--bs-border-color').trim() || '#e2e8f0';

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($datasFormatadas),
            datasets: [
                {
                    label: 'Acertos',
                    data: @json($acertosData),
                    backgroundColor: colorSuccess,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Erros',
                    data: @json($errosData),
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
            animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : undefined,
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
                    ticks: { color: colorBody, font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 14 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: colorGrid, drawBorder: false },
                    border: { display: false },
                    ticks: { color: colorBody, maxTicksLimit: 6, precision: 0, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush