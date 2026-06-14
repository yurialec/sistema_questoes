@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Questões')

@section('content')
    <div class="container mt-4">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold text-primary">Painel Administrativo</h1>
                <p class="text-muted">Bem-vindo ao sistema de gerenciamento de questões e provas.</p>
            </div>
        </div>

        <!-- Cards de Resumo -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 bg-success-subtle">
                    <div class="card-body">
                        <h5 class="card-title text-success-emphasis">Acertos</h5>
                        <p class="card-text display-4 fw-light">{{ $acertos ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 bg-danger-subtle">
                    <div class="card-body">
                        <h5 class="card-title text-danger-emphasis">Erros</h5>
                        <p class="card-text display-4 fw-light">{{ $erros ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 bg-info-subtle">
                    <div class="card-body">
                        <h5 class="card-title text-info-emphasis">Matérias Cadastradas</h5>
                        <p class="card-text display-4 fw-light">{{ count($materias) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <a href="{{route('reponder')}}">Responder</a>
        </div>

        <!-- Tabela de Matérias -->
        {{-- <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Relação de Matérias</h5>
                <span class="badge bg-secondary rounded-pill">{{ count($materias) }} registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">#</th>
                                <th scope="col">Nome da Matéria</th>
                                <th scope="col">Tipo</th>
                                <th scope="col" class="text-end pe-4">Total de Questões</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materias as $materia)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $materia->id }}</td>
                                    <td class="fw-medium">{{ $materia->nome }}</td>
                                    <td>
                                        @if($materia->tipo === 'especifica')
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Específica</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Básica</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="fw-bold">{{ $materia->total_questoes }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Nenhuma matéria cadastrada no sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}
    </div>
@endsection