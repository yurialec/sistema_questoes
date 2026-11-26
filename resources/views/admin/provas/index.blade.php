@extends('layouts.app')

@section('title', 'Admin - Provas')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1">Provas cadastradas</h1>
            <p class="text-body-secondary small mb-0">Provas agrupadas por cargo, ano e banca.</p>
        </div>
        <a href="{{ route('admin.provas.cadastrar') }}" class="btn btn-primary">
            <i class="fas fa-file-circle-plus me-1"></i>
            Cadastrar prova
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Órgão</th>
                        <th>Cargo</th>
                        <th>Banca</th>
                        <th>Ano</th>
                        <th class="text-end">Questões</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($provas as $prova)
                        <tr>
                            <td>{{ $prova->cargo->orgao->nome ?? '—' }}</td>
                            <td>{{ $prova->cargo->nome ?? '—' }}</td>
                            <td>{{ $prova->banca->nome ?? '—' }}</td>
                            <td>{{ $prova->ano->ano ?? '—' }}</td>
                            <td class="text-end">
                                {{ $prova->questoes_count }}
                                {{ $prova->questoes_count === 1 ? 'questão' : 'questões' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-5">
                                Nenhuma prova cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
