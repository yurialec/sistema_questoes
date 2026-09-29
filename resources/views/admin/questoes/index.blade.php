@extends('layouts.app')

@section('title', 'Admin - Editar Questões')

@section('content')
<div class="container py-4">
    
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">✏️ Editar Questões</h2>
            <p class="text-muted small mb-0">Selecione uma questão para editar o enunciado, texto complementar ou alternativas.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulário de Filtros -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.questoes.index') }}" class="row g-3">
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted">Órgão</label>
                    <select name="orgao_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($orgaos as $orgao)
                            <option value="{{ $orgao->id }}" {{ request('orgao_id') == $orgao->id ? 'selected' : '' }}>{{ $orgao->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted">Banca</label>
                    <select name="banca_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($bancas as $banca)
                            <option value="{{ $banca->id }}" {{ request('banca_id') == $banca->id ? 'selected' : '' }}>{{ $banca->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-bold text-muted">Ano</label>
                    <select name="ano_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" {{ request('ano_id') == $ano->id ? 'selected' : '' }}>{{ $ano->ano }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small fw-bold text-muted">Cargo</label>
                    <select name="cargo_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-bold text-muted">Matéria</label>
                    <select name="materia_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4"><i class="fas fa-filter me-1"></i> Filtrar</button>
                    <a href="{{ route('admin.questoes.index') }}" class="btn btn-outline-secondary btn-sm px-4"><i class="fas fa-eraser me-1"></i> Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Listagem de Questões -->
    @forelse($questoes as $questao)
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Informações da Questão -->
                    <div class="col-md-9">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-2 py-1">Questão {{ $questao->numero ?? $questao->id }}</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 small">
                                {{ $questao->materia->nome ?? 'Sem matéria' }}
                            </span>
                            <span class="text-muted small">
                                <i class="fas fa-building me-1"></i> {{ $questao->cargo->orgao->nome ?? '' }}
                                <span class="mx-1">|</span>
                                <i class="fas fa-calendar me-1"></i> {{ $questao->cargo->ano->ano ?? '' }}
                                <span class="mx-1">|</span>
                                <i class="fas fa-university me-1"></i> {{ $questao->cargo->banca->nome ?? '' }}
                            </span>
                        </div>
                        
                        <h6 class="fw-bold text-dark mb-1">
                            {{ $questao->assunto->nome ?? 'Assunto não definido' }}
                        </h6>
                        
                        <p class="text-muted small mb-0 text-truncate" style="max-width: 100%;">
                            {!! Str::limit(strip_tags($questao->enunciado), 150) !!}
                        </p>
                    </div>

                    <!-- Botão de Ação -->
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('admin.questoes.edit', $questao->id) }}" class="btn btn-warning btn-sm px-4 shadow-sm">
                            <i class="fas fa-edit me-1"></i> Editar Questão
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0 text-center py-5 bg-light">
            <div class="card-body">
                <i class="fas fa-search fa-3x text-muted mb-3 opacity-50"></i>
                <h5 class="text-muted">Nenhuma questão encontrada</h5>
                <p class="text-muted small">Tente ajustar os filtros acima ou importe novas questões via JSON.</p>
            </div>
        </div>
    @endforelse

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
        {{ $questoes->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection