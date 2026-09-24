@extends('layouts.app')

@section('title', 'Caderno de Erros')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-danger fw-bold">📓 Caderno de Erros</h2>

    <!-- Filtro de Data -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('caderno-erros.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Filtrar por data:</label>
                    <input type="date" name="data" class="form-control" value="{{ $dataFiltro }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    @forelse($erros as $erro)
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-secondary">{{ $erro->questao->materia->nome ?? 'Sem matéria' }}</span>
                    <small class="text-muted">{{ $erro->created_at->format('d/m/Y H:i') }}</small>
                </div>

                <h5 class="fw-semibold mb-3">Questão {{ $erro->questao->numero }}</h5>
                <p class="text-muted small mb-4">{!! nl2br(e(Str::limit($erro->questao->enunciado, 200))) !!}</p>

                <div class="row g-4">
                    <!-- Coluna da Esquerda: O Erro -->
                    <div class="col-md-6">
                        <div class="p-3 bg-danger bg-opacity-10 rounded border border-danger border-opacity-25">
                            <h6 class="text-danger fw-bold mb-2">❌ Sua resposta incorreta:</h6>
                            <p class="mb-2"><strong>{{ $erro->alternativa->letra }})</strong> {{ $erro->alternativa->descricao }}</p>
                            
                            <hr class="my-3 border-danger border-opacity-25">
                            
                            <h6 class="fw-bold mb-2">📝 Análise do Erro:</h6>
                            <ul class="list-unstyled small mb-3">
                                @if($erro->foi_chute) <li class="text-danger">• Foi chute</li> @endif
                                @if($erro->erro_distraido) <li class="text-danger">• Erro por distração no enunciado</li> @endif
                            </ul>
                            @if($erro->motivo_erro)
                                <div class="bg-white p-2 rounded border">
                                    <small class="text-muted d-block mb-1">Motivo informado:</small>
                                    <em>"{{ $erro->motivo_erro }}"</em>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coluna da Direita: Como Resolver -->
                    <div class="col-md-6">
                        <form action="{{ route('caderno-erros.update-resolver', $erro->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label fw-bold text-success">💡 Como resolver corretamente esta questão?</label>
                            <textarea name="como_resolver" class="form-control mb-3" rows="5" placeholder="Descreva o passo a passo ou o conceito correto para acertar esta questão...">{{ old('como_resolver', $erro->como_resolver) }}</textarea>
                            
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-save me-1"></i> Salvar Anotação
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
            <h5 class="text-muted">Nenhum erro registrado nesta data.</h5>
            <p class="text-muted small">Continue estudando! Se errar, o sistema registrará aqui.</p>
        </div>
    @endforelse
</div>
@endsection