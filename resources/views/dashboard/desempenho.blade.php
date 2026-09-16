<!-- resources/views/dashboard/desempenho_materia.blade.php -->
@extends('layouts.app')

@section('title', 'Desempenho por Matéria e Assunto')

@push('styles')
<style>
    .progress {
        height: 10px;
    }

    .subject-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .subject-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .topic-row {
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }

    .topic-row:last-child {
        border-bottom: 0;
    }

    .small-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="mb-4">
        <h3 class="mb-1 fw-bold">Desempenho por Matéria</h3>
        <p class="text-muted mb-0">Análise detalhada de acertos e erros por matéria e assunto</p>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
    </div>

    @forelse($materiasData as $materia)
    @php
    $borderColor = $materia['tipo'] === 'especifica' ? '#6366F1' : '#0d6efd';
    $badgeClass = $materia['aproveitamento'] >= 70 ? 'bg-success' : ($materia['aproveitamento'] >= 50 ? 'bg-warning text-dark' : 'bg-danger');
    @endphp

    <!-- MATÉRIA -->
    <div class="card shadow-sm mb-4 subject-card" style="border-left-color: {{ $borderColor }};">
        <div class="card-body">

            <!-- Título -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1 fw-semibold">{{ $materia['nome'] }}</h4>
                    <span class="text-muted small">
                        {{ $materia['total_respondidas'] }} questões respondidas
                    </span>
                </div>
                <div class="text-end">
                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                        {{ $materia['aproveitamento'] }}% de aproveitamento
                    </span>
                </div>
            </div>

            <!-- Indicadores -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="small-label fw-semibold text-uppercase">Respondidas</div>
                        <div class="fs-3 fw-bold">{{ $materia['total_respondidas'] }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="small-label fw-semibold text-uppercase text-success">Acertos</div>
                        <div class="fs-3 fw-bold text-success">{{ $materia['total_acertos'] }}</div>
                        <small class="text-success fw-semibold">
                            {{ $materia['total_respondidas'] > 0 ? round(($materia['total_acertos'] / $materia['total_respondidas']) * 100) : 0 }}%
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-light h-100">
                        <div class="small-label fw-semibold text-uppercase text-danger">Erros</div>
                        <div class="fs-3 fw-bold text-danger">{{ $materia['total_erros'] }}</div>
                        <small class="text-danger fw-semibold">
                            {{ $materia['total_respondidas'] > 0 ? round(($materia['total_erros'] / $materia['total_respondidas']) * 100) : 0 }}%
                        </small>
                    </div>
                </div>
            </div>

            <!-- Barra geral -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small-label fw-semibold">Aproveitamento geral</span>
                    <span class="small-label fw-semibold">{{ $materia['total_acertos'] }} / {{ $materia['total_respondidas'] }}</span>
                </div>
                <div class="progress" style="height: 14px; border-radius: 99px;">
                    @if($materia['total_respondidas'] > 0)
                    <div class="progress-bar bg-success" style="width: {{ ($materia['total_acertos'] / $materia['total_respondidas']) * 100 }}%"></div>
                    <div class="progress-bar bg-danger" style="width: {{ ($materia['total_erros'] / $materia['total_respondidas']) * 100 }}%"></div>
                    @else
                    <div class="progress-bar bg-secondary" style="width: 100%"></div>
                    @endif
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-success fw-semibold">● Acertos: {{ $materia['total_acertos'] }}</small>
                    <small class="text-danger fw-semibold">● Erros: {{ $materia['total_erros'] }}</small>
                </div>
            </div>

            <!-- ASSUNTOS -->
            <h5 class="mb-3 fw-semibold border-bottom pb-2">Desempenho por assunto</h5>

            @forelse($materia['assuntos'] as $assunto)
            @php
            $assuntoBadgeClass = $assunto['aproveitamento'] >= 70 ? 'bg-success' : ($assunto['aproveitamento'] >= 50 ? 'bg-warning text-dark' : 'bg-danger');
            $acertosPct = $assunto['respondidas'] > 0 ? round(($assunto['acertos'] / $assunto['respondidas']) * 100, 1) : 0;
            $errosPct = $assunto['respondidas'] > 0 ? round(($assunto['erros'] / $assunto['respondidas']) * 100, 1) : 0;
            @endphp

            <div class="topic-row">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <strong class="d-block">{{ $assunto['nome'] }}</strong>
                        <div class="small text-muted">
                            {{ $assunto['respondidas'] }} de {{ $assunto['total_questoes'] }} questões
                        </div>
                    </div>

                    <div class="col-md-7">
                        <!-- Acertos -->
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-success fw-semibold">Acertos</small>
                            <small class="fw-semibold">{{ $assunto['acertos'] }} ({{ $acertosPct }}%)</small>
                        </div>
                        <div class="progress mb-3" style="height: 8px; border-radius: 99px;">
                            <div class="progress-bar bg-success" style="width: {{ $acertosPct }}%"></div>
                        </div>

                        <!-- Erros -->
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-danger fw-semibold">Erros</small>
                            <small class="fw-semibold">{{ $assunto['erros'] }} ({{ $errosPct }}%)</small>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 99px;">
                            <div class="progress-bar bg-danger" style="width: {{ $errosPct }}%"></div>
                        </div>
                    </div>

                    <div class="col-md-2 text-md-end mt-3 mt-md-0">
                        <span class="badge {{ $assuntoBadgeClass }} fs-6 px-3 py-2">
                            {{ $assunto['aproveitamento'] }}%
                        </span>
                        <div class="small text-muted mt-1 fw-semibold">aproveitamento</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="fas fa-chart-bar fa-2x mb-2 d-block opacity-50"></i>
                <p class="small mb-0">Nenhum assunto respondido nesta matéria ainda.</p>
            </div>
            @endforelse

        </div>
    </div>
    @empty
    <div class="card shadow-sm border-0 text-center py-5">
        <div class="card-body">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="text-muted">Nenhum dado encontrado</h5>
            <p class="text-muted small">Responda algumas questões para visualizar seu desempenho por matéria e assunto.</p>
            <a href="{{ route('responder') }}" class="btn btn-primary mt-2">
                <i class="fas fa-play me-1"></i> Começar a responder
            </a>
        </div>
    </div>
    @endforelse

    <!-- LEGENDA -->
    <div class="text-muted small bg-light p-3 rounded border mt-4">
        <span class="text-success fw-bold">●</span> Acertos
        &nbsp;&nbsp;&nbsp;
        <span class="text-danger fw-bold">●</span> Erros
        <span class="float-end text-muted fst-italic">* Percentuais calculados com base no total de questões respondidas por assunto.</span>
    </div>

</div>
@endsection