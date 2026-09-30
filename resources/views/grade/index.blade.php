@extends('layouts.app')

@section('title', 'Minha Grade de Estudos')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">📅 Minha Grade de Estudos</h2>
            <p class="text-muted small mb-0">Organize seus grupos de matérias e defina o que estudar em cada dia da semana.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        
        {{-- COLUNA 1: GRUPOS E MATÉRIAS --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-layer-group me-2 text-primary"></i> 1. Gerenciar Grupos e Matérias
                </div>
                <div class="card-body">
                    {{-- Criar Novo Grupo --}}
                    <form action="{{ route('grade.grupos.store') }}" method="POST" class="d-flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="nome" class="form-control" placeholder="Nome do novo grupo (ex: Foco em TI)" required>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Criar</button>
                    </form>

                    <hr class="my-4">

                    {{-- Lista de Grupos --}}
                    @forelse($grupos as $grupo)
                        <div class="card mb-3 border-light bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark">{{ $grupo->nome }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary">{{ $grupo->materias->count() }} matérias</span>
                                    
                                    {{-- Botão de Excluir Grupo --}}
                                    <form action="{{ route('grade.grupos.destroy', $grupo->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o grupo \'{{ $grupo->nome }}\'? Os dias da semana associados a ele também serão limpos.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir grupo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                                {{-- Matérias do Grupo --}}
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @forelse($grupo->materias as $materia)
                                        <span class="badge bg-white text-dark border p-2 d-flex align-items-center gap-2">
                                            {{ $materia->nome }}
                                            <form action="{{ route('grade.materias.remove', [$grupo->id, $materia->id]) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-close" style="font-size: 0.5rem;" aria-label="Remover"></button>
                                            </form>
                                        </span>
                                    @empty
                                        <small class="text-muted fst-italic">Nenhuma matéria adicionada.</small>
                                    @endforelse
                                </div>

                                {{-- Adicionar Matéria --}}
                                <form action="{{ route('grade.materias.add', $grupo->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <select name="materia_id" class="form-select form-select-sm" required>
                                        <option value="">Adicionar matéria...</option>
                                        @foreach($todasMaterias as $m)
                                            @if(!$grupo->materias->contains('id', $m->id))
                                                <option value="{{ $m->id }}">{{ $m->nome }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Nenhum grupo criado ainda. Crie seu primeiro grupo acima!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- COLUNA 2: GRADE SEMANAL --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-calendar-week me-2 text-success"></i> 2. Montar Grade Semanal
                </div>
                <div class="card-body">
                    @php
                        $diasSemana = [
                            1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 
                            4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado', 7 => 'Domingo'
                        ];
                    @endphp

                    @foreach($diasSemana as $numero => $nomeDia)
                        @php $gradeDoDia = $grade->get($numero); @endphp
                        <div class="row align-items-center mb-3 pb-3 border-bottom {{ $loop->last ? 'border-0 mb-0' : '' }}">
                            <div class="col-4 fw-semibold text-muted">{{ $nomeDia }}</div>
                            <div class="col-8">
                                @if($gradeDoDia)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success flex-grow-1 py-2">
                                            {{ $gradeDoDia->grupo->nome }}
                                        </span>
                                        <form action="{{ route('grade.dias.destroy', $gradeDoDia->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Limpar dia">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('grade.dias.store') }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="dia_semana" value="{{ $numero }}">
                                        <select name="grupo_id" class="form-select form-select-sm" required {{ $grupos->isEmpty() ? 'disabled' : '' }}>
                                            <option value="">Selecionar grupo...</option>
                                            @foreach($grupos as $g)
                                                <option value="{{ $g->id }}">{{ $g->nome }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm" {{ $grupos->isEmpty() ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection