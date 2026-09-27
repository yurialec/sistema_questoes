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
            
            {{-- CABEÇALHO DA QUESTÃO --}}
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-danger px-3 py-2 fs-6 fw-bold shadow-sm">
                                Erro em {{ $erro->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span class="text-muted small d-flex align-items-center gap-1 fw-medium">
                                <i class="fas fa-building text-secondary"></i> 
                                {{ $erro->questao->cargo->orgao->nome ?? 'Órgão não informado' }}
                            </span>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-tag text-primary fs-6"></i> 
                            {{ $erro->questao->assunto->nome ?? 'Assunto não informado' }}
                        </h5>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 fw-medium">
                            <i class="fas fa-book me-1"></i> {{ $erro->questao->materia->nome ?? 'Sem matéria' }}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 fw-medium">
                            <i class="fas fa-calendar me-1"></i> {{ $erro->questao->cargo->ano->ano ?? 'Ano' }}
                        </span>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 fw-medium">
                            <i class="fas fa-university me-1"></i> {{ $erro->questao->cargo->banca->nome ?? 'Banca' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                {{-- Imagem do Enunciado --}}
                @if($erro->questao->imagem)
                    <div class="text-center mb-3">
                        <img src="{{ asset('storage/' . $erro->questao->imagem) }}" 
                             alt="Imagem da questão" 
                             class="img-fluid rounded border" 
                             style="max-height: 300px;">
                    </div>
                @endif

                {{-- Texto Complementar --}}
                @if($erro->questao->texto_complementar_id && $erro->questao->textoComplementar)
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-outline-info d-flex align-items-center justify-content-between px-3 py-2" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseTexto{{ $erro->id }}" 
                                aria-expanded="false">
                            <span class="fw-medium">📄 Ver Texto Complementar</span>
                            <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $erro->id }}">+</span>
                        </button>
                    </div>
                    <div class="collapse" id="collapseTexto{{ $erro->id }}">
                        <div class="bg-light p-3 rounded border-start border-4 border-info mb-3">
                            {!! nl2br(e($erro->questao->textoComplementar->conteudo)) !!}
                        </div>
                    </div>
                    <hr class="my-4">
                @endif

                {{-- Tabela HTML --}}
                @if($erro->questao->tabela_html)
                    <div class="table-responsive mb-3">
                        {!! $erro->questao->tabela_html !!}
                    </div>
                @endif

                {{-- Enunciado Completo --}}
                <p class="lead fs-6 mb-4">{!! nl2br(e($erro->questao->enunciado)) !!}</p>

                {{-- Alternativa Marcada Incorretamente --}}
                <fieldset class="mb-4">
                    <legend class="fs-6 text-muted mb-3 fw-semibold">Alternativa que você marcou:</legend>

                    @if($erro->alternativa)
                        <div class="p-3 rounded border border-danger bg-danger bg-opacity-10">
                            <label class="form-check-label w-100 text-danger fw-semibold">
                                {{ $erro->alternativa->descricao }}
                                
                                {{-- Imagens da Alternativa (se houver) --}}
                                @if($erro->alternativa->imagens && is_array($erro->alternativa->imagens))
                                    @foreach($erro->alternativa->imagens as $img)
                                        <br>
                                        <img src="{{ asset('storage/' . $img) }}" 
                                            class="img-fluid mt-2 rounded border" 
                                            style="max-width: 100%; max-height: 200px;">
                                    @endforeach
                                @endif
                            </label>
                        </div>
                    @else
                        <p class="text-muted fst-italic small">Informação da alternativa não encontrada no banco de dados.</p>
                    @endif
                </fieldset>

                <hr class="my-4">

                {{-- SEÇÃO DE ANÁLISE E CORREÇÃO --}}
                <div class="row g-4">
                    <!-- Coluna da Esquerda: O Erro -->
                    <div class="col-md-6">
                        <div class="p-3 rounded border border-danger border-opacity-25 h-100">
                            <h6 class="text-danger fw-bold mb-3">📝 Análise do seu Erro:</h6>
                            <ul class="list-unstyled small mb-3">
                                @if($erro->foi_chute) 
                                    <li class="mb-1"><i class="fas fa-dice me-2"></i>Foi chute</li> 
                                @endif
                                @if($erro->erro_distraido) 
                                    <li class="mb-1"><i class="fas fa-eye-slash me-2"></i>Errei por distração no enunciado</li> 
                                @endif
                            </ul>
                            @if($erro->motivo_erro)
                                <div class="bg-white p-3 rounded border">
                                    <small class="text-muted d-block mb-1 fw-semibold">Motivo que você informou:</small>
                                    <em class="text-dark">"{{ $erro->motivo_erro }}"</em>
                                </div>
                            @else
                                <p class="small text-muted fst-italic">Você não registrou o motivo deste erro.</p>
                            @endif
                        </div>
                </div>

                    <!-- Coluna da Direita: Como Resolver -->
                    <div class="col-md-6">
                        <form action="{{ route('caderno-erros.update-resolver', $erro->id) }}" method="POST" class="h-100 d-flex flex-column">
                            @csrf
                            @method('PATCH')
                            <label class="form-label fw-bold text-success mb-2">💡 Como resolver corretamente esta questão?</label>
                            <textarea name="como_resolver" class="form-control mb-3 flex-grow-1" rows="6" placeholder="Descreva o passo a passo, a regra ou o conceito correto para acertar esta questão na próxima...">{{ old('como_resolver', $erro->como_resolver) }}</textarea>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-1"></i> Salvar Anotação de Resolução
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="fas fa-check-circle fa-3x text-success mb-3 opacity-50"></i>
                <h5 class="text-muted">Nenhum erro registrado nesta data.</h5>
                <p class="text-muted small">Continue estudando! Se errar, o sistema registrará aqui para sua revisão.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- Script para o toggle do texto complementar --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const iconSpan = this.querySelector('.icon-toggle');
                setTimeout(() => {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl && targetEl.classList.contains('show')) {
                        iconSpan.textContent = '-';
                        iconSpan.classList.replace('text-info', 'text-dark');
                    } else {
                        iconSpan.textContent = '+';
                        iconSpan.classList.replace('text-dark', 'text-info');
                    }
                }, 50);
            });
        });
    });
</script>
@endsection