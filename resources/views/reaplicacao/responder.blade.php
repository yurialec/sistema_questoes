@extends('layouts.app')

@section('title', 'Modo Reaplicação')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-warning fw-bold">🔄 Modo Reaplicação</h2>
        <a href="{{ route('reaplicacao.index') }}" class="btn btn-outline-secondary btn-sm">Sair do Modo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('resultado') === false)
        <div class="alert alert-warning border-warning mb-4">
            <h5 class="alert-heading fw-bold">❌ Você errou novamente.</h5>
            <p class="mb-0">Revise suas anotações abaixo para entender onde está a falha antes de tentar a próxima.</p>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3">
            <h5 class="mb-0 fw-semibold">Questão {{ $erro->questao->numero }}</h5>
        </div>
        
        <div class="card-body">
            @if($erro->questao->textoComplementar)
                <div class="bg-light p-3 rounded border-start border-4 border-info mb-3">
                    {!! nl2br(e($erro->questao->textoComplementar->conteudo)) !!}
                </div>
            @endif

            <p class="lead fs-6 mb-4">{!! nl2br(e($erro->questao->enunciado)) !!}</p>

            <form method="POST" action="{{ route('reaplicacao.verificar', $erro->id) }}">
                @csrf
                <fieldset>
                    @foreach($erro->questao->alternativas->shuffle() as $alternativa)
                        <div class="form-check mb-2 p-2 rounded hover-bg-light">
                            <input class="form-check-input" type="radio" name="alternativa_id" 
                                   id="alt_{{ $alternativa->id }}" value="{{ $alternativa->id }}" required>
                            <label class="form-check-label w-100 ps-2" for="alt_{{ $alternativa->id }}">
                                <strong>{{ $alternativa->letra }})</strong> {{ $alternativa->descricao }}
                            </label>
                        </div>
                    @endforeach
                </fieldset>

                <div class="d-grid gap-2 d-md-block mt-4">
                    <button type="submit" class="btn btn-warning px-4 fw-bold">
                        Responder Reaplicação
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- BOX DE REVISÃO (Só aparece se errou de novo neste modo) --}}
    @if(isset($mostrar_anotacoes) && $mostrar_anotacoes)
        <div class="card border-danger shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">
                📓 Suas Anotações do Erro Anterior
            </div>
            <div class="card-body bg-danger bg-opacity-10">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold text-danger">Por que você errou antes:</h6>
                        <ul class="list-unstyled small">
                            @if($erro->foi_chute) <li>• Foi chute</li> @endif
                            @if($erro->erro_distraido) <li>• Distração no enunciado</li> @endif
                        </ul>
                        @if($erro->motivo_erro)
                            <p class="small fst-italic">"{{ $erro->motivo_erro }}"</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-success">Como resolver corretamente:</h6>
                        @if($erro->como_resolver)
                            <p class="small bg-white p-2 rounded border">{!! nl2br(e($erro->como_resolver)) !!}</p>
                        @else
                            <p class="small text-muted fst-italic">Você ainda não registrou como resolver esta questão. Vá ao Caderno de Erros e preencha!</p>
                        @endif
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('reaplicacao.iniciar') }}" class="btn btn-outline-danger btn-sm">
                        Entendi, tentar a próxima questão →
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection