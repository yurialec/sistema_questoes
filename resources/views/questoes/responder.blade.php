@extends('layouts.app')

@section('content')

<h2>Responder Questão</h2>

@if(session('resultado') !== null)

    @if(session('resultado'))
        <p>✅ Resposta correta.</p>
    @else
        <p>❌ Resposta incorreta.</p>
    @endif

@endif

<div>

    <h3>Questão {{ $questao->numero ?? $questao->id }}</h3>

    @if($questao->imagem)
        <img
            src="{{ asset('storage/' . $questao->imagem) }}"
            alt="Imagem da questão"
        >
    @endif

    @if($questao->tabela_html)
        {!! $questao->tabela_html !!}
    @endif

    @if($questao->texto_complementar)
        <div>
            {!! nl2br(e($questao->texto_complementar)) !!}
        </div>
        <hr>
    @endif

    <p>
        {!! nl2br(e($questao->enunciado)) !!}
    </p>

</div>

<form
    method="POST"
    action="{{ url('/questao/verificar') }}"
>

    @csrf

    <fieldset>

        <legend>Alternativas</legend>

        @foreach($questao->alternativas as $alternativa)

            <div>

                <label>

                    <input
                        type="radio"
                        name="alternativa_id"
                        value="{{ $alternativa->id }}"
                        required
                    >

                    <strong>
                        {{ $alternativa->letra }})
                    </strong>

                    {{ $alternativa->descricao }}

                </label>

            </div>

        @endforeach

    </fieldset>

    <br>

    <button type="submit">
        Responder
    </button>

</form>

@endsection
