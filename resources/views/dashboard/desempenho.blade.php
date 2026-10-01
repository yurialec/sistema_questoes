@extends('layouts.app')

@section('title', 'Desempenho por Matéria e Assunto')

@section('content')
<div class="container py-4">

    @if(count($materiasData) > 0)

        <!-- ABAS DAS MATÉRIAS (Estilo Minimalista com nav-underline) -->
        <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
            <div class="card-body pb-0">
                <ul class="nav nav-underline overflow-auto flex-nowrap mb-3" id="materiasTab" role="tablist" style="scrollbar-width: thin;">
                    @foreach($materiasData as $index => $materia)
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link text-body-secondary {{ $index === 0 ? 'active text-primary fw-semibold' : '' }}"
                                id="materia-tab-{{ $index }}"
                                data-bs-toggle="tab"
                                data-bs-target="#materia-{{ $index }}"
                                type="button"
                                role="tab"
                                aria-controls="materia-{{ $index }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                            >
                                {{ $materia['nome'] }}
                                <span class="badge bg-body text-body-secondary border border-secondary-subtle ms-1 fw-normal">
                                    {{ $materia['total_respondidas'] }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- CONTEÚDO DAS ABAS -->
        <div class="tab-content" id="materiasTabContent">
            @foreach($materiasData as $index => $materia)
                @php
                    // Mantemos a cor dinâmica, mas usamos variáveis ou classes do Bootstrap quando possível
                    $borderClass = $materia['tipo'] === 'especifica' ? 'border-primary' : 'border-info';
                    
                    $badgeClass = $materia['aproveitamento'] >= 70
                        ? 'bg-success-subtle text-success-emphasis border border-success-subtle'
                        : ($materia['aproveitamento'] >= 50
                            ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
                            : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');
                @endphp

                <div
                    class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                    id="materia-{{ $index }}"
                    role="tabpanel"
                    aria-labelledby="materia-tab-{{ $index }}"
                    tabindex="0"
                >
                    <div class="card border-0 shadow-sm mb-4 border-start border-4 {{ $borderClass }}">
                        <div class="card-body p-4">

                            <!-- Cabeçalho da Matéria -->
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                                <div>
                                    <h4 class="mb-1 fw-bold text-body">{{ $materia['nome'] }}</h4>
                                    <span class="text-body-secondary small">{{ $materia['total_respondidas'] }} questões respondidas</span>
                                </div>
                                <div>
                                    <span class="badge rounded-pill fs-6 px-3 py-2 fw-semibold {{ $badgeClass }}">
                                        {{ $materia['aproveitamento'] }}% de aproveitamento
                                    </span>
                                </div>
                            </div>

                            <!-- Indicadores Rápidos (KPIs) -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="border border-secondary-subtle rounded-3 p-3 bg-body-tertiary h-100">
                                        <div class="text-body-secondary small fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Respondidas</div>
                                        <div class="fs-3 fw-bold text-body">{{ $materia['total_respondidas'] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border border-success-subtle rounded-3 p-3 bg-success-subtle h-100">
                                        <div class="text-success-emphasis small fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Acertos</div>
                                        <div class="fs-3 fw-bold text-success-emphasis">{{ $materia['total_acertos'] }}</div>
                                        <small class="text-success-emphasis fw-semibold">
                                            {{ $materia['total_respondidas'] > 0 ? round(($materia['total_acertos'] / $materia['total_respondidas']) * 100) : 0 }}%
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border border-danger-subtle rounded-3 p-3 bg-danger-subtle h-100">
                                        <div class="text-danger-emphasis small fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Erros</div>
                                        <div class="fs-3 fw-bold text-danger-emphasis">{{ $materia['total_erros'] }}</div>
                                        <small class="text-danger-emphasis fw-semibold">
                                            {{ $materia['total_respondidas'] > 0 ? round(($materia['total_erros'] / $materia['total_respondidas']) * 100) : 0 }}%
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Barra de Progresso Geral -->
                            <div class="mb-5">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-body-secondary small fw-semibold">Aproveitamento geral</span>
                                    <span class="text-body-secondary small fw-semibold">{{ $materia['total_acertos'] }} / {{ $materia['total_respondidas'] }}</span>
                                </div>
                                <div class="progress" style="height: 12px; border-radius: 99px; background-color: var(--bs-body-tertiary);">
                                    @if($materia['total_respondidas'] > 0)
                                        <div class="progress-bar bg-success" style="width: {{ ($materia['total_acertos'] / $materia['total_respondidas']) * 100 }}%"></div>
                                        <div class="progress-bar bg-danger" style="width: {{ ($materia['total_erros'] / $materia['total_respondidas']) * 100 }}%"></div>
                                    @else
                                        <div class="progress-bar bg-secondary-subtle" style="width: 100%"></div>
                                    @endif
                                </div>
                            </div>

                            <!-- Desempenho por Assunto -->
                            <h5 class="mb-3 fw-bold text-body border-bottom border-secondary-subtle pb-2">Desempenho por assunto</h5>

                            @forelse($materia['assuntos'] as $assunto)
                                @php
                                    $assuntoBadgeClass = $assunto['aproveitamento'] >= 70
                                        ? 'bg-success-subtle text-success-emphasis border border-success-subtle'
                                        : ($assunto['aproveitamento'] >= 50
                                            ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
                                            : 'bg-danger-subtle text-danger-emphasis border border-danger-subtle');

                                    $acertosPct = $assunto['respondidas'] > 0 ? round(($assunto['acertos'] / $assunto['respondidas']) * 100, 1) : 0;
                                    $errosPct = $assunto['respondidas'] > 0 ? round(($assunto['erros'] / $assunto['respondidas']) * 100, 1) : 0;
                                @endphp

                                <div class="row align-items-center py-3 border-bottom border-secondary-subtle last-no-border">
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <strong class="d-block text-body mb-1">{{ $assunto['nome'] }}</strong>
                                        <div class="small text-body-secondary">{{ $assunto['respondidas'] }} de {{ $assunto['total_questoes'] }} questões</div>
                                    </div>

                                    <div class="col-md-7">
                                        <!-- Barra de Acertos -->
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-success-emphasis fw-semibold">Acertos</small>
                                            <small class="fw-semibold text-body">{{ $assunto['acertos'] }} ({{ $acertosPct }}%)</small>
                                        </div>
                                        <div class="progress mb-3" style="height: 6px; border-radius: 99px; background-color: var(--bs-body-tertiary);">
                                            <div class="progress-bar bg-success" style="width: {{ $acertosPct }}%"></div>
                                        </div>

                                        <!-- Barra de Erros -->
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-danger-emphasis fw-semibold">Erros</small>
                                            <small class="fw-semibold text-body">{{ $assunto['erros'] }} ({{ $errosPct }}%)</small>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 99px; background-color: var(--bs-body-tertiary);">
                                            <div class="progress-bar bg-danger" style="width: {{ $errosPct }}%"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                        <span class="badge rounded-pill fs-6 px-3 py-2 fw-semibold {{ $assuntoBadgeClass }}">
                                            {{ $assunto['aproveitamento'] }}%
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-body-secondary">
                                    <p class="small mb-0">Nenhum assunto respondido nesta matéria ainda.</p>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <!-- Estado Vazio (Empty State) -->
        <div class="card border-0 shadow-sm text-center py-5 bg-body-tertiary">
            <div class="card-body">
                <h5 class="text-body fw-bold mb-2">Nenhum dado encontrado</h5>
                <p class="text-body-secondary small mb-4">Responda algumas questões para visualizar seu desempenho detalhado por matéria e assunto.</p>
                <a href="{{ route('responder') }}" class="btn btn-primary px-4 fw-semibold">
                    <i class="fas fa-play me-1"></i> Começar a responder
                </a>
            </div>
        </div>
    @endif

    <!-- LEGENDA MINIMALISTA -->
    <div class="d-flex flex-wrap justify-content-between align-items-center bg-body-tertiary border border-secondary-subtle rounded-3 p-3 mt-4 small text-body-secondary">
        <div class="d-flex gap-3">
            <span class="d-flex align-items-center gap-1"><div class="rounded-circle bg-success" style="width: 8px; height: 8px;"></div> Acertos</span>
            <span class="d-flex align-items-center gap-1"><div class="rounded-circle bg-danger" style="width: 8px; height: 8px;"></div> Erros</span>
        </div>
        <span class="fst-italic mt-2 mt-md-0">* Percentuais calculados com base no total de questões respondidas por assunto.</span>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* Apenas para remover a borda do último item da lista de assuntos, mantendo o visual limpo */
    .last-no-border {
        border-bottom: none !important;
    }
    /* Estilização fina da scrollbar das abas no Firefox/Chrome */
    #materiasTab::-webkit-scrollbar {
        height: 4px;
    }
    #materiasTab::-webkit-scrollbar-thumb {
        background-color: var(--bs-secondary-bg);
        border-radius: 4px;
    }
</style>
@endpush