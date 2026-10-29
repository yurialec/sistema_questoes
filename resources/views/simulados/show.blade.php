@extends('layouts.app')

@section('title', $simulado->nome)

@section('content')
<div class="container py-4">

    {{-- Cabeçalho --}}
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h4 class="mb-1 fw-semibold">{{ $simulado->nome }}</h4>
                    <div class="d-flex flex-wrap gap-2 small text-body-secondary">
                        <span><i class="fas fa-list-ol me-1"></i>{{ $simulado->total_questoes }} questões</span>
                        <span><i class="fas fa-calendar me-1"></i>{{ $simulado->created_at->format('d/m/Y H:i') }}</span>
                        @if($simulado->iniciado_em)
                            <span><i class="fas fa-play me-1"></i>Iniciado {{ $simulado->iniciado_em->format('d/m/Y H:i') }}</span>
                        @endif
                        @if($simulado->finalizado_em)
                            <span><i class="fas fa-flag-checkered me-1"></i>Finalizado {{ $simulado->finalizado_em->format('d/m/Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if(in_array($simulado->status, ['em_andamento', 'pausado']))
                        <div id="cronometro" class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 py-2 fs-6"
                             data-iniciado-em="{{ $simulado->iniciado_em->toIso8601String() }}"
                             data-rodando="{{ $simulado->status === 'em_andamento' ? '1' : '0' }}">
                            <i class="fas fa-stopwatch me-1"></i><span id="cronometroTexto">00:00:00</span>
                        </div>
                    @endif

                    <a href="{{ route('simulados.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         ESTADO: NÃO INICIADO
    ═══════════════════════════════════════════ --}}
    @if($simulado->status === 'nao_iniciado')
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-hourglass-start fa-3x text-primary mb-3"></i>
                <h5 class="fw-semibold mb-2">Pronto para começar?</h5>
                <p class="text-body-secondary mb-4">
                    O cronômetro começa a contar assim que você clicar em iniciar.
                </p>
                <form action="{{ route('simulados.iniciar', $simulado) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary px-5 fw-semibold">
                        <i class="fas fa-play me-1"></i> Iniciar simulado
                    </button>
                </form>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════
         ESTADO: EM ANDAMENTO OU PAUSADO
    ═══════════════════════════════════════════ --}}
    @elseif(in_array($simulado->status, ['em_andamento', 'pausado']))
        @if($simulado->status === 'pausado')
            <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><i class="fas fa-pause-circle me-2"></i>Simulado pausado.</div>
                <form action="{{ route('simulados.retomar', $simulado) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning fw-semibold">
                        <i class="fas fa-play me-1"></i> Retomar
                    </button>
                </form>
            </div>
        @endif

        <form action="{{ route('simulados.finalizar', $simulado) }}" method="POST" id="formSimulado">
            @csrf

            {{-- Abas de matérias --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body py-2">
                    <ul class="nav nav-pills flex-nowrap overflow-auto gap-2" id="abasMaterias" role="tablist">
                        @foreach($questoesPorMateria as $index => $bloco)
                            @php
                                $totalMateria = $bloco['questoes']->count();
                                $materiaId = $bloco['materia']->id ?? 'sem';
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link aba-materia {{ $index === 0 ? 'active' : '' }}"
                                        id="aba-{{ $materiaId }}"
                                        data-bs-toggle="pill"
                                        data-bs-target="#painel-{{ $materiaId }}"
                                        data-materia="{{ $materiaId }}"
                                        data-total="{{ $totalMateria }}"
                                        type="button"
                                        role="tab">
                                    <span class="nome-materia">{{ $bloco['materia']->nome ?? 'Sem matéria' }}</span>
                                    <span class="badge ms-2 badge-progresso bg-secondary-subtle text-secondary-emphasis">
                                        0/{{ $totalMateria }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Painéis de questões --}}
            <fieldset @disabled($simulado->status === 'pausado')>
                <div class="tab-content" id="conteudoMaterias">
                    @foreach($questoesPorMateria as $index => $bloco)
                        @php
                            $materiaId = $bloco['materia']->id ?? 'sem';
                        @endphp
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                             id="painel-{{ $materiaId }}"
                             role="tabpanel">

                            @foreach($bloco['questoes'] as $sq)
                                @php $questao = $sq->questao; @endphp
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-4">
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                                            <span class="badge bg-primary px-3 py-2 fw-bold">
                                                #{{ $sq->ordem }}
                                            </span>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                                    {{ $questao->ano->ano ?? '' }}
                                                </span>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                    {{ $questao->banca->nome ?? '' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($questao->imagem)
                                            <div class="text-center mb-3">
                                                <img src="{{ asset('storage/' . $questao->imagem) }}"
                                                     class="img-fluid rounded border border-secondary-subtle"
                                                     style="max-height: 300px;">
                                            </div>
                                        @endif

                                        @if($questao->texto_complementar_id && $questao->textoComplementar)
                                            <div class="mb-3">
                                                <button class="btn btn-outline-info btn-sm w-100 d-flex justify-content-between align-items-center"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#texto{{ $sq->id }}">
                                                    <span>Ver Texto Complementar</span>
                                                    <span class="fw-bold">+</span>
                                                </button>
                                                <div class="collapse mt-2" id="texto{{ $sq->id }}">
                                                    <div class="bg-body-tertiary p-3 rounded border-start border-4 border-info-subtle">
                                                        {!! $questao->textoComplementar->conteudo !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($questao->tabela_html)
                                            <div class="table-responsive mb-3">{!! $questao->tabela_html !!}</div>
                                        @endif

                                        <div class="fs-5 text-body mb-3" style="line-height: 1.7;">
                                            {!! $questao->enunciado !!}
                                        </div>

                                        @foreach($questao->alternativas->sortBy('letra') as $alternativa)
                                            <div class="form-check p-3 mb-2 rounded border border-secondary-subtle bg-body-tertiary">
                                                <input class="form-check-input mt-1 alternativa-radio"
                                                       type="radio"
                                                       name="respostas[{{ $questao->id }}]"
                                                       id="alt_{{ $sq->id }}_{{ $alternativa->letra }}"
                                                       value="{{ $alternativa->id }}"
                                                       data-questao="{{ $questao->id }}"
                                                       data-materia="{{ $materiaId }}"
                                                       {{ $sq->alternativa_id == $alternativa->id ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 ps-2"
                                                       for="alt_{{ $sq->id }}_{{ $alternativa->letra }}">
                                                    <strong class="text-primary me-1">{{ $alternativa->letra }})</strong>
                                                    {!! $alternativa->descricao !!}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </fieldset>

            {{-- Barra fixa de ações --}}
            @if($simulado->status === 'em_andamento')
                <div class="card border-0 shadow-sm position-sticky bottom-0 mb-4">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-body-secondary">
                            <i class="fas fa-check-circle me-1"></i>
                            Total: <span id="contadorRespondidas">0</span> de {{ $simulado->total_questoes }} respondidas
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    formaction="{{ route('simulados.pausar', $simulado) }}"
                                    formnovalidate
                                    class="btn btn-outline-warning btn-sm fw-semibold px-4">
                                <i class="fas fa-pause me-1"></i> Pausar
                            </button>

                            <button type="submit"
                                    id="btnFinalizar"
                                    class="btn btn-success btn-sm fw-semibold px-4"
                                    disabled>
                                <i class="fas fa-flag-checkered me-1"></i> Finalizar
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </form>

    {{-- ═══════════════════════════════════════════
         ESTADO: FINALIZADO
    ═══════════════════════════════════════════ --}}
    @elseif($simulado->status === 'finalizado')
    @php
        $totalQuestoes = $simulado->total_questoes;
        $totalAcertos  = $simulado->total_acertos ?? 0;
        $totalErros    = $simulado->total_erros ?? 0;
        $aproveitamento = $totalQuestoes > 0
            ? round($totalAcertos / $totalQuestoes * 100, 1)
            : 0;
    @endphp

    {{-- Resumo geral --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100 bg-body-tertiary">
                <div class="card-body">
                    <i class="fas fa-list-ol fa-2x text-primary mb-2"></i>
                    <h3 class="fw-bold mb-0">{{ $totalQuestoes }}</h3>
                    <div class="text-body-secondary small">Questões</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100 bg-body-tertiary">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="fw-bold mb-0 text-success">{{ $totalAcertos }}</h3>
                    <div class="text-body-secondary small">Acertos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100 bg-body-tertiary">
                <div class="card-body">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h3 class="fw-bold mb-0 text-danger">{{ $totalErros }}</h3>
                    <div class="text-body-secondary small">Erros</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center h-100 bg-body-tertiary">
                <div class="card-body">
                    <i class="fas fa-percent fa-2x text-info mb-2"></i>
                    <h3 class="fw-bold mb-0">{{ $aproveitamento }}%</h3>
                    <div class="text-body-secondary small">Aproveitamento</div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center text-body-secondary small mb-4">
        <i class="fas fa-stopwatch me-1"></i>
        Tempo total: {{ gmdate('H:i:s', $simulado->tempo_total_segundos ?? 0) }}
    </div>

    {{-- Desempenho por matéria --}}
    <h5 class="fw-semibold mb-3">
        <i class="fas fa-chart-simple me-2 text-primary"></i> Desempenho por matéria
    </h5>

    @foreach($questoesPorMateria as $bloco)
        @php
            $materia       = $bloco['materia'];
            $questoesMat   = $bloco['questoes'];
            $totalMat      = $questoesMat->count();
            $acertosMat    = $questoesMat->where('correta', true)->count();
            $errosMat      = $totalMat - $acertosMat;
            $aprovMat      = $totalMat > 0 ? round($acertosMat / $totalMat * 100, 1) : 0;
            $collapseId    = 'detalhes-materia-' . ($materia->id ?? 'sem');
        @endphp

        <div class="card border-0 shadow-sm mb-3 bg-body-tertiary">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h6 class="fw-semibold mb-1">
                            {{ $materia->nome ?? 'Sem matéria' }}
                        </h6>
                        <div class="small text-body-secondary">
                            {{ $totalMat }} {{ \Illuminate\Support\Str::plural('questão', $totalMat) }}:
                            <span class="text-success fw-semibold">{{ $acertosMat }} {{ \Illuminate\Support\Str::plural('acerto', $acertosMat) }}</span>,
                            <span class="text-danger fw-semibold">{{ $errosMat }} {{ \Illuminate\Support\Str::plural('erro', $errosMat) }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-3 py-2">
                            {{ $aprovMat }}%
                        </span>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary btn-toggle-detalhes"
                                data-target="#{{ $collapseId }}"
                                aria-expanded="false"
                                aria-controls="{{ $collapseId }}"
                                title="Ver questões">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>

                {{-- Quadradinhos --}}
                <div class="d-flex flex-wrap gap-1">
                    @foreach($questoesMat->sortBy('ordem') as $sq)
                        <div class="quadradinho {{ $sq->correta ? 'quadradinho-acerto' : 'quadradinho-erro' }}"
                             title="Questão #{{ $sq->ordem }} — {{ $sq->correta ? 'Acerto' : 'Erro' }}">
                        </div>
                    @endforeach
                </div>

                {{-- Detalhes das questões (expandível) --}}
                <div class="mt-3 detalhes-materia" id="{{ $collapseId }}" style="display: none;">
                    @foreach($questoesMat->sortBy('ordem') as $sq)
                        @php
                            $questao            = $sq->questao;
                            $alternativaMarcada = $questao->alternativas->firstWhere('id', $sq->alternativa_id);
                            $alternativaCorreta = $questao->alternativas->firstWhere('correta', true);
                        @endphp
                        <div class="card border-0 shadow-sm mb-2 bg-body">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                    <span class="badge {{ $sq->correta ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                                        #{{ $sq->ordem }} — {{ $sq->correta ? 'Acerto' : 'Erro' }}
                                    </span>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($questao->ano)
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                                {{ $questao->ano->ano }}
                                            </span>
                                        @endif
                                        @if($questao->banca)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                {{ $questao->banca->nome }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="small text-body mb-2" style="line-height: 1.5;">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($questao->enunciado), 220) !!}
                                </div>

                                <div class="small">
                                    <div class="mb-1">
                                        <i class="fas fa-hand-pointer me-1 text-body-secondary"></i>
                                        <strong>Sua resposta:</strong>
                                        @if($alternativaMarcada)
                                            <span class="{{ $sq->correta ? 'text-success' : 'text-danger' }} fw-semibold">
                                                {{ $alternativaMarcada->letra }}) {{ \Illuminate\Support\Str::limit(strip_tags($alternativaMarcada->descricao), 120) }}
                                            </span>
                                        @else
                                            <span class="text-body-secondary fst-italic">Não respondida</span>
                                        @endif
                                    </div>

                                    @if(!$sq->correta && $alternativaCorreta)
                                        <div>
                                            <i class="fas fa-check me-1 text-success"></i>
                                            <strong>Resposta correta:</strong>
                                            <span class="text-success fw-semibold">
                                                {{ $alternativaCorreta->letra }}) {{ \Illuminate\Support\Str::limit(strip_tags($alternativaCorreta->descricao), 120) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="text-center mt-4 mb-5 d-flex flex-wrap justify-content-center gap-2">
        <a href="{{ route('simulados.index') }}" class="btn btn-outline-secondary btn-sm px-4">
            <i class="fas fa-arrow-left me-1"></i> Voltar para a lista
        </a>
        <form action="{{ route('simulados.exportarJson', $simulado) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary btn-sm px-4">
                <i class="fas fa-file-export me-1"></i> Exportar JSON
            </button>
        </form>
    </div>
@endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Cronômetro =====
    const cronEl = document.getElementById('cronometro');
    if (cronEl) {
        const iniciadoEm = new Date(cronEl.dataset.iniciadoEm);
        const rodando = cronEl.dataset.rodando === '1';
        const texto = document.getElementById('cronometroTexto');

        function render() {
            const diff = Math.max(0, Math.floor((new Date() - iniciadoEm) / 1000));
            const h = String(Math.floor(diff / 3600)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            const s = String(diff % 60).padStart(2, '0');
            texto.textContent = `${h}:${m}:${s}`;
        }

        render();
        if (rodando) setInterval(render, 1000);
    }

    // ===== Contadores, abas e botão Finalizar =====
    const form = document.getElementById('formSimulado');
    if (form) {
        const total = {{ $simulado->total_questoes }};
        const btnFinalizar = document.getElementById('btnFinalizar');
        const contador = document.getElementById('contadorRespondidas');
        const abas = document.querySelectorAll('.aba-materia');

        function atualizar() {
            const respondidasPorMateria = {};
            const questoesRespondidas = new Set();

            document.querySelectorAll('.alternativa-radio:checked').forEach(el => {
                const materia = el.dataset.materia;
                const questao = el.dataset.questao;
                questoesRespondidas.add(questao);

                if (!respondidasPorMateria[materia]) {
                    respondidasPorMateria[materia] = new Set();
                }
                respondidasPorMateria[materia].add(questao);
            });

            contador.textContent = questoesRespondidas.size;
            btnFinalizar.disabled = questoesRespondidas.size < total;

            abas.forEach(aba => {
                const materia = aba.dataset.materia;
                const totalMateria = parseInt(aba.dataset.total, 10);
                const respondidas = respondidasPorMateria[materia]?.size ?? 0;

                const badge = aba.querySelector('.badge-progresso');
                badge.textContent = `${respondidas}/${totalMateria}`;

                aba.classList.remove('aba-concluida', 'aba-pendente');
                badge.classList.remove(
                    'bg-success-subtle', 'text-success-emphasis',
                    'bg-warning-subtle', 'text-warning-emphasis',
                    'bg-secondary-subtle', 'text-secondary-emphasis'
                );

                if (respondidas === totalMateria && totalMateria > 0) {
                    aba.classList.add('aba-concluida');
                    badge.classList.add('bg-success-subtle', 'text-success-emphasis');
                } else if (respondidas > 0) {
                    aba.classList.add('aba-pendente');
                    badge.classList.add('bg-warning-subtle', 'text-warning-emphasis');
                } else {
                    badge.classList.add('bg-secondary-subtle', 'text-secondary-emphasis');
                }
            });
        }

        document.querySelectorAll('.alternativa-radio').forEach(el => {
            el.addEventListener('change', atualizar);
        });

        atualizar();
    }

    // ===== TOGGLE CHEVRON (JS puro, sem depender do Bootstrap Collapse) =====
    document.querySelectorAll('.btn-toggle-detalhes').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const seletor = btn.getAttribute('data-target');
            const alvo = document.querySelector(seletor);
            if (!alvo) {
                console.warn('Alvo não encontrado:', seletor);
                return;
            }

            const icon = btn.querySelector('i');
            const visivel = alvo.style.display !== 'none';

            if (visivel) {
                alvo.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                alvo.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .aba-materia {
        white-space: nowrap;
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        display: flex;
        align-items: center;
    }
    .aba-materia.active {
        background-color: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }
    .aba-materia.aba-concluida:not(.active) {
        border-color: var(--bs-success-border-subtle);
    }
    .aba-materia.aba-pendente:not(.active) {
        border-color: var(--bs-warning-border-subtle);
    }
    .aba-materia .badge-progresso {
        font-size: 0.7rem;
    }
    .aba-materia.active .badge-progresso {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #fff !important;
    }
    .quadradinho {
        width: 22px;
        height: 22px;
        border-radius: 4px;
        border: 1px solid transparent;
        cursor: default;
        transition: transform 0.1s ease;
    }
    .quadradinho:hover {
        transform: scale(1.15);
    }
    .quadradinho-acerto {
        background-color: var(--bs-success);
        border-color: var(--bs-success);
    }
    .quadradinho-erro {
        background-color: var(--bs-danger);
        border-color: var(--bs-danger);
    }
    .btn-toggle-detalhes {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-toggle-detalhes i {
        font-size: 0.8rem;
        transition: transform 0.15s ease;
    }
</style>
@endpush