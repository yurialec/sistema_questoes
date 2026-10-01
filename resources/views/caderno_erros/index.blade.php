@extends('layouts.app')

@section('title', 'Caderno de Erros')

@section('content')
<div class="container py-4">
    
    <h2 class="mb-4 fw-bold text-body">Caderno de Erros</h2>

    <!-- Filtro de Data -->
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="card-body">
            <form method="GET" action="{{ route('caderno-erros.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">Filtrar por data:</label>
                    <input type="date" name="data" class="form-control" value="{{ $dataFiltro }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @forelse($erros as $erro)
        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-danger">
            
            {{-- CABEÇALHO DA QUESTÃO --}}
            <div class="card-header bg-body-tertiary border-bottom border-secondary-subtle py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-danger px-3 py-2 fs-6 fw-bold shadow-sm">
                                Erro em {{ $erro->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span class="text-body-secondary small fw-medium">
                                {{ $erro->questao->cargo->orgao->nome ?? 'Órgão não informado' }}
                            </span>
                        </div>
                        <h5 class="mb-0 fw-bold text-body">
                            {{ $erro->questao->assunto->nome ?? 'Assunto não informado' }}
                        </h5>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-2 fw-medium">
                            {{ $erro->questao->materia->nome ?? 'Sem matéria' }}
                        </span>
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 fw-medium">
                            {{ $erro->questao->cargo->ano->ano ?? 'Ano' }}
                        </span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 fw-medium">
                            {{ $erro->questao->cargo->banca->nome ?? 'Banca' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4">
                {{-- Imagem do Enunciado --}}
                @if($erro->questao->imagem)
                    <div class="text-center mb-4">
                        <img src="{{ asset('storage/' . $erro->questao->imagem) }}" 
                             alt="Imagem da questão" 
                             class="img-fluid rounded border border-secondary-subtle" 
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
                            <span class="fw-medium small">Ver Texto Complementar</span>
                            <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $erro->id }}">+</span>
                        </button>
                    </div>
                    <div class="collapse" id="collapseTexto{{ $erro->id }}">
                        <div class="bg-body-tertiary p-3 rounded border-start border-4 border-info-subtle mb-4">
                            {!! $erro->questao->textoComplementar->conteudo !!}
                        </div>
                    </div>
                @endif

                {{-- Tabela HTML --}}
                @if($erro->questao->tabela_html)
                    <div class="table-responsive mb-4">
                        {!! $erro->questao->tabela_html !!}
                    </div>
                @endif

                {{-- Enunciado Completo --}}
                <div class="fs-5 text-body mb-4" style="line-height: 1.7;">
                    {!! $erro->questao->enunciado !!}
                </div>

                {{-- Alternativa Marcada Incorretamente --}}
                <fieldset class="mb-4">
                    <legend class="fs-6 text-body-secondary mb-3 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Alternativa que você marcou</legend>

                    @if($erro->alternativa)
                        <div class="p-3 rounded border border-danger-subtle bg-danger-subtle">
                            <label class="form-check-label w-100 text-danger-emphasis fw-semibold">
                                {{ $erro->alternativa->letra }}) {!! $erro->alternativa->descricao !!}
                                
                                {{-- Imagens da Alternativa (se houver) --}}
                                @if($erro->alternativa->imagens && is_array($erro->alternativa->imagens))
                                    @foreach($erro->alternativa->imagens as $img)
                                        <br>
                                        <img src="{{ asset('storage/' . $img) }}" 
                                            class="img-fluid mt-2 rounded border border-secondary-subtle" 
                                            style="max-width: 100%; max-height: 200px;">
                                    @endforeach
                                @endif
                            </label>
                        </div>
                    @else
                        <p class="text-body-secondary fst-italic small">Informação da alternativa não encontrada no banco de dados.</p>
                    @endif
                </fieldset>

                <hr class="border-secondary-subtle my-4">

                {{-- SEÇÃO DE ANÁLISE E CORREÇÃO --}}
                <div class="row g-4">
                    <!-- Coluna da Esquerda: O Erro -->
                    <div class="col-md-6">
                        <div class="p-3 rounded border border-secondary-subtle bg-body-tertiary h-100">
                            <h6 class="text-danger-emphasis fw-bold mb-3">Análise do seu Erro</h6>
                            
                            @if($erro->foi_chute || $erro->erro_distraido)
                                <ul class="list-unstyled small mb-3 text-body-secondary">
                                    @if($erro->foi_chute) 
                                        <li class="mb-1">• Foi chute</li> 
                                    @endif
                                    @if($erro->erro_distraido) 
                                        <li class="mb-1">• Errei por distração no enunciado</li> 
                                    @endif
                                </ul>
                            @endif

                            @if($erro->motivo_erro)
                                <div class="bg-body p-3 rounded border border-secondary-subtle">
                                    <small class="text-body-secondary d-block mb-1 fw-semibold">Motivo que você informou:</small>
                                    <em class="text-body">"{{ $erro->motivo_erro }}"</em>
                                </div>
                            @else
                                <p class="small text-body-secondary fst-italic mb-0">Você não registrou o motivo deste erro.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Coluna da Direita: Como Resolver -->
                    <div class="col-md-6">
                        <form action="{{ route('caderno-erros.update-resolver', $erro->id) }}" method="POST" class="h-100 d-flex flex-column">
                            @csrf
                            @method('PATCH')
                            <label class="form-label fw-semibold text-success-emphasis mb-2">Como resolver corretamente esta questão?</label>
                            <textarea name="como_resolver" class="form-control mb-3 flex-grow-1" rows="6" placeholder="Descreva o passo a passo, a regra ou o conceito correto para acertar esta questão na próxima...">{{ old('como_resolver', $erro->como_resolver) }}</textarea>
                            
                            <button type="submit" class="btn btn-success w-100 fw-semibold">
                                <i class="fas fa-save me-1"></i> Salvar Anotação de Resolução
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm text-center py-5 bg-body-tertiary">
            <div class="card-body">
                <h5 class="text-body fw-bold mb-2">Nenhum erro registrado nesta data.</h5>
                <p class="text-body-secondary small mb-4">Continue estudando! Se errar, o sistema registrará aqui para sua revisão.</p>
                <a href="{{ route('responder') }}" class="btn btn-primary px-4 fw-semibold">
                    <i class="fas fa-play me-1"></i> Voltar a responder
                </a>
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
                        iconSpan.textContent = '−';
                        iconSpan.classList.replace('text-info', 'text-body');
                    } else {
                        iconSpan.textContent = '+';
                        iconSpan.classList.replace('text-body', 'text-info');
                    }
                }, 50);
            });
        });
    });
</script>
@endsection