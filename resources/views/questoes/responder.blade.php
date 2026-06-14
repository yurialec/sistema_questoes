@extends('layouts.app')

@section('title', 'Responder Questões')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold">Banco de Questões</h2>
    <h4>
        <a href="{{route('dashboard')}}" class="text-decoration-none text-dark">Retornar a dashboard</a>
    </h4>
    {{-- Feedback de Resposta --}}
    @if(session('resultado') !== null)
        <div class="alert {{ session('resultado') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-4" role="alert">
            @if(session('resultado'))
                ✅ Resposta correta!
            @else
                ❌ Resposta incorreta. Tente novamente.
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Loop de Questões Paginadas --}}
    @forelse($questoes as $questao)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Questão {{ $questao->numero ?? $questao->id }}</h5>
                <span class="badge bg-secondary">{{ $questao->materia->nome ?? 'Matéria não definida' }}</span>
            </div>
            
            <div class="card-body">
                {{-- Imagem do Enunciado --}}
                @if($questao->imagem)
                    <div class="text-center mb-3">
                        <img src="{{ asset('storage/' . $questao->imagem) }}" 
                             alt="Imagem da questão {{ $questao->numero }}" 
                             class="img-fluid rounded border" 
                             style="max-height: 300px;">
                    </div>
                @endif

                {{-- Botão Toggle para Texto Complementar --}}
                @if($questao->texto_complementar_id && $questao->textoComplementar)
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-outline-info d-flex align-items-center justify-content-between px-3 py-2" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseTexto{{ $questao->id }}" 
                                aria-expanded="false" 
                                aria-controls="collapseTexto{{ $questao->id }}">
                            <span class="fw-medium">📄 Ver Texto Complementar</span>
                            <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $questao->id }}">+</span>
                        </button>
                    </div>

                    {{-- Área Colapsável --}}
                    <div class="collapse" id="collapseTexto{{ $questao->id }}">
                        <div class="bg-light p-3 rounded border-start border-4 border-info mb-3">
                            {!! nl2br(e($questao->textoComplementar->conteudo)) !!}
                        </div>
                    </div>
                    
                    <hr class="my-4">
                @endif

                {{-- Tabela HTML --}}
                @if($questao->tabela_html)
                    <div class="table-responsive mb-3">
                        {!! $questao->tabela_html !!}
                    </div>
                @endif

                {{-- Enunciado --}}
                <p class="lead fs-6">{!! nl2br(e($questao->enunciado)) !!}</p>

                {{-- Formulário Individual --}}
                <form method="POST" action="{{ url('/questao/verificar') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="questao_id" value="{{ $questao->id }}">

                    <fieldset>
                        <legend class="fs-6 text-muted mb-3">Alternativas:</legend>
                        
                        @foreach($questao->alternativas->shuffle() as $alternativa)
                            <div class="form-check mb-2 p-2 rounded hover-bg-light">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="alternativa_id" 
                                       id="alt_{{ $questao->id }}_{{ $alternativa->letra }}"
                                       value="{{ $alternativa->id }}" 
                                       required>
                                
                                <label class="form-check-label w-100 ps-2" for="alt_{{ $questao->id }}_{{ $alternativa->letra }}">
                                    <strong>{{ $alternativa->letra }})</strong> 
                                    {{ $alternativa->descricao }}
                                    
                                    {{-- Imagens das Alternativas --}}
                                    @if($alternativa->imagens && is_array($alternativa->imagens))
                                        @foreach($alternativa->imagens as $img)
                                            <br>
                                            <img src="{{ asset('storage/' . $img) }}" 
                                                 alt="Imagem alternativa {{ $alternativa->letra }}" 
                                                 class="img-fluid mt-2 rounded border" 
                                                 style="max-width: 100%; max-height: 200px;">
                                        @endforeach
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </fieldset>

                    <div class="d-grid gap-2 d-md-block mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            Responder Questão
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-warning text-center py-5">
            Nenhuma questão encontrada para exibição.
        </div>
    @endforelse

    {{-- Paginação --}}
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $questoes->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Script Inline para alternar ícones + / - --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleciona todos os botões de toggle dentro dos cards
        const toggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                // Pega o ID do alvo (ex: #collapseTexto123)
                const targetId = this.getAttribute('data-bs-target');
                const iconSpan = this.querySelector('.icon-toggle');
                
                // Pequeno delay para permitir que o Bootstrap atualize o estado antes de trocar o ícone
                setTimeout(() => {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl && targetEl.classList.contains('show')) {
                        iconSpan.textContent = '-';
                        iconSpan.classList.remove('text-info');
                        iconSpan.classList.add('text-dark');
                    } else {
                        iconSpan.textContent = '+';
                        iconSpan.classList.remove('text-dark');
                        iconSpan.classList.add('text-info');
                    }
                }, 50);
            });
        });
    });
</script>
@endsection