@extends('layouts.app')

@section('title', 'Admin - Editar Questões')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulário de Filtros -->
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.questoes.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Órgão</label>
                    <select name="orgao_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($orgaos as $orgao)
                            <option value="{{ $orgao->id }}" {{ request('orgao_id') == $orgao->id ? 'selected' : '' }}>{{ $orgao->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Banca</label>
                    <select name="banca_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($bancas as $banca)
                            <option value="{{ $banca->id }}" {{ request('banca_id') == $banca->id ? 'selected' : '' }}>{{ $banca->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Ano</label>
                    <select name="ano_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" {{ request('ano_id') == $ano->id ? 'selected' : '' }}>{{ $ano->ano }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">Cargo</label>
                    <select name="cargo_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">Matéria</label>
                    <select name="materia_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>{{ $materia->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 mt-2 border-top border-secondary-subtle pt-3">
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.questoes.index') }}" class="btn btn-outline-secondary btn-sm px-4">
                        <i class="fas fa-eraser me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Listagem de Questões -->
    @forelse($questoes as $questao)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center">
                    <!-- Informações da Questão -->
                    <div class="col-md-9">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-2 py-1 fw-semibold">#{{ $questao->numero ?? $questao->id }}</span>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-2 py-1 small">
                                {{ $questao->materia->nome ?? 'Sem matéria' }}
                            </span>
                            <span class="text-body-secondary small fw-medium">
                                {{ $questao->cargo->orgao->nome ?? 'Órgão' }} 
                                <span class="mx-1 text-body-tertiary">•</span>
                                {{ $questao->ano->ano ?? 'Ano' }}
                                <span class="mx-1 text-body-tertiary">•</span>
                                {{ $questao->banca->nome ?? 'Banca' }}
                            </span>
                        </div>
                        
                        <h6 class="fw-bold text-body mb-1">
                            {{ $questao->assunto->nome ?? 'Assunto não definido' }}
                        </h6>
                        
                        <p class="text-body-secondary small mb-0 text-truncate" style="max-width: 100%;">
                            {!! Str::limit(strip_tags($questao->enunciado), 150) !!}
                        </p>
                    </div>

                    <!-- Botão de Ação -->
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('admin.questoes.edit', $questao->id) }}" class="btn btn-warning text-dark fw-semibold btn-sm px-4 shadow-sm">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm text-center py-5 bg-body-tertiary">
            <div class="card-body">
                <h5 class="text-body fw-bold mb-2">Nenhuma questão encontrada</h5>
                <p class="text-body-secondary small mb-0">Tente ajustar os filtros acima ou importe novas questões via JSON.</p>
            </div>
        </div>
    @endforelse

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $questoes->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection