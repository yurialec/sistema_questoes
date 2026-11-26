@extends('layouts.app')

@section('title', 'Filtrar Questões')

@push('styles')
<style>
    .study-responder {
        --sr-surface: var(--bs-body-bg, #212529);
        --sr-muted: var(--bs-secondary-color, #94a3b8);
        --sr-line: var(--bs-border-color, #343a40);
        --sr-track: var(--bs-tertiary-bg, #2b3035);
        --sr-accent: var(--bs-primary, #4f46e5);
        max-width: 1440px;
        margin: 0 auto;
        color: var(--bs-body-color);
    }
    .sr-heading { font-size: clamp(1.55rem, 2.5vw, 2rem); font-weight: 750; letter-spacing: -.035em; margin: 0; }
    .sr-subtitle { color: var(--sr-muted); font-size: .875rem; margin: .35rem 0 0; }
    .sr-card { background: var(--sr-surface); border: 1px solid var(--sr-line); border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,.10); }
    .sr-filter-card { margin-bottom: 1.25rem; overflow: hidden; }
    .sr-filter-card .card-body { padding: 1.25rem; }
    .sr-filter-card .form-label { color: var(--sr-muted); font-size: .78rem; letter-spacing: .01em; margin-bottom: .45rem; }
    .sr-filter-card .border-top { border-color: var(--sr-line) !important; }
    .sr-question { border: 1px solid var(--sr-line) !important; border-radius: 16px; overflow: hidden; box-shadow: 0 12px 30px rgba(0,0,0,.10); background: var(--sr-surface); transition: border-color .18s ease, box-shadow .18s ease; }
    .sr-question:hover { border-color: rgba(var(--bs-primary-rgb), .42) !important; box-shadow: 0 16px 36px rgba(0,0,0,.16); }
    .sr-question .card-header { background: var(--sr-track) !important; border-bottom: 1px solid var(--sr-line) !important; }
    .sr-question-number { min-width: 48px; border-radius: 10px; background: linear-gradient(120deg, #25365e, #344d90) !important; }
    .sr-question .card-body { padding: clamp(1rem, 2.5vw, 1.75rem); }
    .sr-complementary { border: 1px solid var(--sr-line); border-radius: 12px; background: var(--sr-track); color: var(--bs-body-color); }
    .sr-complementary:hover { border-color: rgba(var(--bs-primary-rgb), .45); color: var(--bs-primary); }
    .sr-complementary-content { border: 1px solid var(--sr-line); border-radius: 12px; background: var(--sr-track); }
    .sr-alternative { position: relative; border: 1px solid var(--sr-line) !important; border-radius: 12px !important; background: var(--sr-track) !important; cursor: pointer; transition: border-color .16s ease, background .16s ease, transform .16s ease; }
    .sr-alternative:hover { border-color: rgba(var(--bs-primary-rgb), .55) !important; background: rgba(var(--bs-primary-rgb), .08) !important; transform: translateY(-1px); }
    .sr-alternative.selected { border-color: var(--bs-primary) !important; background: rgba(var(--bs-primary-rgb), .12) !important; box-shadow: inset 3px 0 0 var(--bs-primary); }
    .sr-alternative .form-check-input { width: 1.1em; height: 1.1em; margin-top: .18em; }
    .sr-alternative .form-check-label { cursor: pointer; line-height: 1.55; }
    .sr-submit { min-height: 46px; border-radius: 10px; font-weight: 700; }
    .sr-results-count { border: 1px solid var(--sr-line) !important; border-radius: 10px; background: var(--sr-track) !important; color: var(--sr-muted) !important; }
    .sr-empty { border: 1px dashed var(--sr-line); border-radius: 16px; background: var(--sr-surface); }
    .study-responder .form-control, .study-responder .form-select,
    .study-responder .ts-control { border-color: var(--sr-line); border-radius: 10px; background: var(--sr-track); color: var(--bs-body-color); }
    .study-responder .form-control:focus, .study-responder .form-select:focus,
    .study-responder .ts-control.focus { border-color: var(--bs-primary); box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .12); }
    .study-responder .modal-content { border: 1px solid var(--sr-line); border-radius: 16px; background: var(--sr-surface); }
    .study-responder .modal-header, .study-responder .modal-footer { border-color: var(--sr-line) !important; background: var(--sr-track); }
    .study-responder .pagination { gap: .35rem; }
    .study-responder .page-link { border: 1px solid var(--sr-line); border-radius: 8px !important; background: var(--sr-surface); color: var(--bs-body-color); }
    .study-responder .page-link:hover { background: var(--sr-track); color: var(--bs-primary); }
    @media (max-width: 991.98px) { .study-responder { padding-left: .25rem; padding-right: .25rem; } .sr-filter-card .card-body { padding: 1rem; } }
    @media (max-width: 575.98px) {
        .sr-question { border-radius: 12px; }
        .sr-question .card-header { padding: .9rem 1rem !important; }
        .sr-question .card-body { padding: 1rem; }
        .sr-question .enunciado-content { font-size: 1rem !important; }
        .sr-submit { width: 100%; }
        .sr-filter-card .btn { min-height: 42px; }
    }
</style>
@endpush

@section('content')
<div class="study-responder container-fluid px-3 px-lg-4 py-4">
    <header class="mb-4">
        <h1 class="sr-heading">Banco de questões</h1>
        <p class="sr-subtitle">Filtre, pratique e acompanhe sua evolução por assunto.</p>
    </header>
    
    {{-- ══════════════════════════════════════════════
         FORMULÁRIO DE FILTROS
    ═══════════════════════════════════════════════ --}}
    <div class="card sr-card sr-filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('responder') }}" id="formFiltros" class="row g-3">

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Órgão</label>
                    <select name="orgao_id[]" id="selectOrgao" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($orgaos as $orgao)
                            <option value="{{ $orgao->id }}" {{ in_array($orgao->id, request('orgao_id', [])) ? 'selected' : '' }}>
                                {{ $orgao->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Banca</label>
                    <select name="banca_id[]" id="selectBanca" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($bancas as $banca)
                            <option value="{{ $banca->id }}" {{ in_array($banca->id, request('banca_id', [])) ? 'selected' : '' }}>
                                {{ $banca->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-body-secondary">Ano</label>
                    <select name="ano_id[]" id="selectAno" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" {{ in_array($ano->id, request('ano_id', [])) ? 'selected' : '' }}>
                                {{ $ano->ano }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">Cargo</label>
                    <select name="cargo_id[]" id="selectCargo" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ in_array($cargo->id, request('cargo_id', [])) ? 'selected' : '' }}>
                                {{ $cargo->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">Matéria</label>
                    <select name="materia_id[]" id="selectMateria" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ in_array($materia->id, request('materia_id', [])) ? 'selected' : '' }}>
                                {{ $materia->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FILTRO: GRUPO DE ASSUNTO --}}
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">
                        Grupo de Assunto
                    </label>

                    <select
                        name="grupo_assunto_id[]"
                        id="selectGrupoAssunto"
                        class="form-select form-select-sm tom-select"
                        multiple
                        placeholder="Selecione uma matéria..."
                    >
                    </select>
                </div>

                {{-- FILTRO: ASSUNTO --}}
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-body-secondary">
                        Assunto
                    </label>

                    <select
                        name="assunto_id[]"
                        id="selectAssunto"
                        class="form-select form-select-sm tom-select"
                        multiple
                        placeholder="Selecione uma matéria..."
                    >
                    </select>
                </div>

                {{-- Linha de Ações e Filtros Salvos --}}
                <div class="col-12 mt-4 border-top border-secondary-subtle pt-3">
                    <div class="row g-3 align-items-end">
                        
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-body-secondary">Filtros Salvos</label>
                            <div class="input-group">
                                <select id="selectFiltrosSalvos" class="form-select form-select-sm">
                                    <option value="">Carregar um filtro salvo...</option>
                                    @foreach($filtrosSalvos as $filtro)
                                        <option value="{{ $filtro->id }}" 
                                                data-filtros="{{ json_encode($filtro->filtros) }}"
                                                {{ request()->has('filtro_id') && request('filtro_id') == $filtro->id ? 'selected' : '' }}>
                                            {{ $filtro->nome }} {{ $filtro->is_padrao ? '(Padrão)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnCarregarFiltro" title="Carregar filtro">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-8 d-flex flex-wrap gap-2 justify-content-md-end">
                            <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                                <i class="fas fa-filter me-1"></i> Aplicar
                            </button>
                            
                            <a href="{{ route('responder') }}" class="btn btn-outline-secondary btn-sm px-4">
                                <i class="fas fa-eraser me-1"></i> Limpar
                            </a>

                            <button type="button" class="btn btn-outline-success btn-sm px-4" data-bs-toggle="modal" data-bs-target="#modalSalvarFiltro">
                                <i class="fas fa-save me-1"></i> Salvar
                            </button>

                            @if(request()->has('filtro_id') || $filtrosSalvos->count() > 0)
                                <form action="{{ route('filtros.excluir', request('filtro_id', 0)) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este filtro salvo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-4" {{ !$filtrosSalvos->count() ? 'disabled' : '' }} title="Excluir filtro selecionado">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif

                            <span class="badge sr-results-count ms-auto d-flex align-items-center px-3 py-2">
                                <i class="fas fa-list-ol me-2"></i>
                                {{ $questoes->total() }} {{ Str::plural('questão encontrada', $questoes->total()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PARA SALVAR FILTRO --}}
    <div class="modal fade" id="modalSalvarFiltro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('filtros.salvar') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom border-secondary-subtle bg-body-tertiary">
                        <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-save me-2 text-success"></i>Salvar Filtro Atual</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomeFiltro" class="form-label fw-semibold small text-body-secondary">Nome do Filtro</label>
                            <input type="text" class="form-control" id="nomeFiltro" name="nome_filtro" placeholder="Ex: Revisão TI Banco do Brasil" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="definirPadrao" name="definir_padrao" value="1">
                            <label class="form-check-label small" for="definirPadrao">
                                Definir como filtro padrão (carrega automaticamente)
                            </label>
                        </div>
                        
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle bg-body-tertiary">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-success fw-semibold">Salvar Filtro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FEEDBACK DE RESPOSTA --}}
    @if(session('resultado') !== null)
    <div class="alert {{ session('resultado') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        @if(session('resultado')) 
            <i class="fas fa-check-circle me-2"></i> Resposta correta! 
        @else 
            <i class="fas fa-times-circle me-2"></i> Resposta incorreta. 
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════
         LISTAGEM DE QUESTÕES
    ═══════════════════════════════════════════════ --}}
    @forelse($questoes as $questao)
    <div class="card sr-question mb-4">
        <div class="card-header bg-body-tertiary border-bottom border-secondary-subtle py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge sr-question-number px-3 py-2 fs-6 fw-bold shadow-sm">
                            #{{ $questao->numero ?? $questao->id }}
                        </span>
                        <span class="text-body-secondary small fw-medium">
                            {{ $questao->cargo->orgao->nome ?? 'Órgão não informado' }}
                        </span>
                    </div>
                    
                    <h5 class="mb-0 fw-bold text-body d-flex align-items-center gap-2">
                        {{ $questao->assunto->nome ?? 'Assunto não informado' }}
                    </h5>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-2 fw-medium">
                        {{ $questao->materia->nome ?? 'Sem matéria' }}
                    </span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 fw-medium">
                        {{ $questao->ano->ano ?? 'Ano' }}
                    </span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 fw-medium">
                        {{ $questao->banca->nome ?? 'Banca' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @if($questao->imagem)
            <div class="text-center mb-4">
                <img src="{{ asset('storage/' . $questao->imagem) }}" alt="Imagem da questão" class="img-fluid rounded border border-secondary-subtle" style="max-height: 300px;">
            </div>
            @endif

            @if($questao->texto_complementar_id && $questao->textoComplementar)
            <div class="d-grid gap-2 mb-3">
                <button class="btn sr-complementary d-flex align-items-center justify-content-between px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTexto{{ $questao->id }}">
                    <span class="fw-medium small">Ver Texto Complementar</span>
                    <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $questao->id }}">+</span>
                </button>
            </div>
            <div class="collapse" id="collapseTexto{{ $questao->id }}">
                <div class="sr-complementary-content p-3 mb-4">
                    {!! $questao->textoComplementar->conteudo !!}
                </div>
            </div>
            @endif

            @if($questao->tabela_html)
            <div class="table-responsive mb-4">{!! $questao->tabela_html !!}</div>
            @endif

            <div class="enunciado-content fs-5 text-body mb-4" style="line-height: 1.7;">
                {!! $questao->enunciado !!}
            </div>

            {{-- FORMULÁRIO DE RESPOSTA (DENTRO DO LOOP, PARA TER ACESSO A $questao) --}}
            <form method="POST" action="{{ route('questao.verificar') }}" class="mt-4">
                @csrf
                <input type="hidden" name="questao_id" value="{{ $questao->id }}">
                <input type="hidden" name="url_retorno" value="{{ request()->fullUrl() }}">
                
                <fieldset>
                    <legend class="fs-6 text-body-secondary mb-3 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Alternativas</legend>
                    @foreach($questao->alternativas->shuffle() as $alternativa)
                    <div class="form-check sr-alternative p-3 mb-3">
                        <input class="form-check-input mt-1" type="radio" name="alternativa_id" id="alt_{{ $questao->id }}_{{ $alternativa->letra }}" value="{{ $alternativa->id }}" required>
                        <label class="form-check-label w-100 ps-2" for="alt_{{ $questao->id }}_{{ $alternativa->letra }}">
                            <strong class="text-primary me-1">{{ $alternativa->letra }})</strong> 
                            {!! $alternativa->descricao !!}
                            
                            @if($alternativa->imagens && is_array($alternativa->imagens))
                                @foreach($alternativa->imagens as $img)
                                <br><img src="{{ asset('storage/' . $img) }}" class="img-fluid mt-2 rounded border border-secondary-subtle" style="max-width: 100%; max-height: 200px;">
                                @endforeach
                            @endif
                        </label>
                    </div>
                    @endforeach
                </fieldset>
                <div class="d-grid gap-2 d-md-block mt-4">
                    <button type="submit" class="btn btn-primary sr-submit px-5 fw-semibold shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Responder Questão
                    </button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="sr-empty text-center py-5">
        <i class="fas fa-search mb-2 d-block fs-1 text-warning-emphasis"></i>
        Nenhuma questão encontrada com os filtros selecionados.
    </div>
    @endforelse

    {{-- PAGINAÇÃO --}}
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $questoes->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- MODAL DE ERRO (Caderno de Erros) --}}
@if(session('show_error_modal') && session('erro_id'))
    <div class="modal fade show d-block" id="modalErro" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-secondary-subtle bg-danger-subtle">
                    <h5 class="modal-title fs-6 fw-bold text-danger-emphasis">
                        <i class="fas fa-exclamation-triangle me-2"></i>Registro de Erro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('caderno-erros.salvar-motivo', session('erro_id')) }}" method="POST">
                    @csrf
                    <input type="hidden" name="url_retorno" value="{{ request()->fullUrl() }}">
                    <div class="modal-body">
                        
                        {{-- NOVA DIV: QUESTÃO E ALTERNATIVA SELECIONADA --}}
                        @if(session('questao_enunciado') && session('alternativa_descricao'))
                        <div class="alert alert-light border mb-4">
                            <h6 class="fw-bold text-body-secondary mb-2">
                                <i class="fas fa-question-circle me-1"></i> Questão:
                            </h6>
                            <div class="mb-3 small text-body" style="line-height: 1.6;">
                                {!! session('questao_enunciado') !!}
                            </div>
                            
                            <h6 class="fw-bold text-danger mb-2">
                                <i class="fas fa-times-circle me-1"></i> Alternativa Selecionada (Incorreta):
                            </h6>
                            <div class="p-3 bg-danger-subtle border border-danger-subtle rounded small text-danger-emphasis">
                                <strong class="me-1">{{ session('alternativa_letra') }})</strong> 
                                {!! session('alternativa_descricao') !!}
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="foi_chute" id="foi_chute">
                                <label class="form-check-label" for="foi_chute">Foi chute (não sabia o conteúdo)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="erro_distraido" id="erro_distraido">
                                <label class="form-check-label" for="erro_distraido">Sabia a matéria, mas errei por distração no enunciado</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motivo_erro" class="form-label fw-semibold small text-body-secondary">
                                Por que você marcou essa alternativa incorreta?
                            </label>
                            <textarea class="form-control" name="motivo_erro" id="motivo_erro" rows="3" placeholder="Ex: Confundi o conceito de X com Y..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle bg-body-tertiary">
                        <a href="{{ request()->fullUrl() }}" class="btn btn-sm btn-outline-secondary">Pular por enquanto</a>
                        <button type="submit" class="btn btn-sm btn-danger fw-semibold">Salvar no Caderno de Erros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalErro'));
            modal.show();
        });
    </script>
@endif

<script>
    // Toggle do texto complementar
    document.addEventListener('DOMContentLoaded', function() {
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

        document.querySelectorAll('.sr-alternative input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const fieldset = this.closest('fieldset');
                fieldset.querySelectorAll('.sr-alternative').forEach(alternative => {
                    alternative.classList.remove('selected');
                });
                this.closest('.sr-alternative').classList.add('selected');
            });
        });
    });
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dadosGrupos = {{ \Illuminate\Support\Js::from($dadosGruposAssunto) }};

    const selecaoInicial = {
        materia_id: {{ \Illuminate\Support\Js::from(request('materia_id', [])) }},
        grupo_assunto_id: {{ \Illuminate\Support\Js::from(request('grupo_assunto_id', [])) }},
        assunto_id: {{ \Illuminate\Support\Js::from(request('assunto_id', [])) }}
    };

    const tomSelectInstances = {};

    // ==========================================
    // 1. INICIALIZAÇÃO DOS SELECTS
    // ==========================================

    document.querySelectorAll('.tom-select').forEach(el => {
        tomSelectInstances[el.id] = new TomSelect(el, {
            plugins: ['remove_button'],
            placeholder: 'Selecione...',
            maxItems: null,
            searchField: ['text', 'value']
        });
    });

    const selectMateria = tomSelectInstances.selectMateria;
    const selectGrupo = tomSelectInstances.selectGrupoAssunto;
    const selectAssunto = tomSelectInstances.selectAssunto;

    function valoresArray(valores) {
        if (valores === null || valores === undefined || valores === '') {
            return [];
        }

        return (Array.isArray(valores) ? valores : [valores])
            .map(String);
    }

    function atualizarPlaceholder(select, texto) {
        select.settings.placeholder = texto;
        select.inputState();
        select.refreshOptions(false);
    }

    // ==========================================
    // 2. ATUALIZAR GRUPOS CONFORME MATÉRIA
    // ==========================================

    function atualizarGrupos(selecionados = null) {

        const materias = new Set(
            valoresArray(selectMateria.getValue())
        );

        const anteriores = valoresArray(
            selecionados ?? selectGrupo.getValue()
        );

        selectGrupo.clear(true);
        selectGrupo.clearOptions();

        if (materias.size === 0) {
            atualizarPlaceholder(
                selectGrupo,
                'Selecione uma matéria...'
            );

            atualizarAssuntos([]);
            return;
        }

        const gruposPermitidos = new Set();

        dadosGrupos.forEach(grupo => {

            const possuiAssunto = grupo.assuntos.some(assunto =>
                materias.has(String(assunto.materia_id))
            );

            if (!possuiAssunto) return;

            const id = String(grupo.id);

            gruposPermitidos.add(id);

            selectGrupo.addOption({
                value: id,
                text: grupo.nome
            });
        });

        const validos = anteriores.filter(id =>
            gruposPermitidos.has(id)
        );

        if (validos.length > 0) {
            selectGrupo.setValue(validos, true);
        }

        atualizarPlaceholder(
            selectGrupo,
            'Selecione um grupo...'
        );

        atualizarAssuntos();
    }

    // ==========================================
    // 3. ATUALIZAR ASSUNTOS CONFORME
    //    MATÉRIA E GRUPO
    // ==========================================

    function atualizarAssuntos(selecionados = null) {

        const materias = new Set(
            valoresArray(selectMateria.getValue())
        );

        const grupos = new Set(
            valoresArray(selectGrupo.getValue())
        );

        const anteriores = valoresArray(
            selecionados ?? selectAssunto.getValue()
        );

        selectAssunto.clear(true);
        selectAssunto.clearOptions();
        selectAssunto.clearOptionGroups();

        if (materias.size === 0) {
            atualizarPlaceholder(
                selectAssunto,
                'Selecione uma matéria...'
            );
            return;
        }

        const idsPermitidos = new Set();

        dadosGrupos.forEach(grupo => {

            const grupoId = String(grupo.id);

            if (grupos.size > 0 && !grupos.has(grupoId)) {
                return;
            }

            const assuntosPermitidos = grupo.assuntos.filter(assunto =>
                materias.has(String(assunto.materia_id))
            );

            if (assuntosPermitidos.length === 0) {
                return;
            }

            const chaveGrupo = 'grupo_' + grupoId;

            selectAssunto.addOptionGroup(chaveGrupo, {
                label: grupo.nome
            });

            assuntosPermitidos.forEach(assunto => {

                const id = String(assunto.id);

                idsPermitidos.add(id);

                selectAssunto.addOption({
                    value: id,
                    text: assunto.nome,
                    optgroup: chaveGrupo
                });
            });
        });

        const validos = anteriores.filter(id =>
            idsPermitidos.has(id)
        );

        if (validos.length > 0) {
            selectAssunto.setValue(validos, true);
        }

        atualizarPlaceholder(
            selectAssunto,
            'Selecione um assunto...'
        );
    }

    // ==========================================
    // 4. EVENTOS DOS FILTROS DEPENDENTES
    // ==========================================

    selectMateria.on('change', function () {
        atualizarGrupos();
    });

    selectGrupo.on('change', function () {
        atualizarAssuntos();
    });

    // Inicialização da página com os filtros
    // recebidos pela URL ou pelo filtro padrão.

    atualizarGrupos(selecaoInicial.grupo_assunto_id);
    atualizarAssuntos(selecaoInicial.assunto_id);

    // ==========================================
    // 5. MAPEAMENTO DOS FILTROS SALVOS
    // ==========================================

    const mapeamento = {
        orgao_id: 'selectOrgao',
        banca_id: 'selectBanca',
        ano_id: 'selectAno',
        cargo_id: 'selectCargo',
        materia_id: 'selectMateria',
        grupo_assunto_id: 'selectGrupoAssunto',
        assunto_id: 'selectAssunto'
    };

    // ==========================================
    // 6. CARREGAR FILTRO SALVO
    // ==========================================

    const btnCarregar = document.getElementById('btnCarregarFiltro');

    if (btnCarregar) {
        btnCarregar.addEventListener('click', function () {

            const select = document.getElementById('selectFiltrosSalvos');
            const selectedOption = select.options[select.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                return;
            }

            let filtros;

            try {
                filtros = JSON.parse(selectedOption.dataset.filtros || '{}');
            } catch (erro) {
                console.error('Erro ao carregar filtro:', erro);
                return;
            }

            // Primeiro, os filtros independentes.

            [
                'orgao_id',
                'banca_id',
                'ano_id',
                'cargo_id'
            ].forEach(chave => {

                const ts = tomSelectInstances[mapeamento[chave]];

                if (!ts) return;

                ts.clear(true);
                ts.setValue(valoresArray(filtros[chave]), true);
            });

            // Depois, a matéria, pois os outros
            // dois filtros dependem dela.

            selectMateria.clear(true);
            selectMateria.setValue(
                valoresArray(filtros.materia_id),
                true
            );

            // Recria grupos válidos e restaura
            // somente os grupos pertencentes à matéria.

            atualizarGrupos(
                valoresArray(filtros.grupo_assunto_id)
            );

            // Recria assuntos válidos e restaura
            // a seleção anteriormente salva.

            atualizarAssuntos(
                valoresArray(filtros.assunto_id)
            );
        });
    }

    // ==========================================
    // 7. SALVAR FILTRO ATUAL
    // ==========================================

    const formSalvar = document.querySelector(
        '#modalSalvarFiltro form'
    );

    if (formSalvar) {

        formSalvar.addEventListener('submit', function(e) {

            e.preventDefault();

            document.querySelectorAll(
                '.input-filtro-dinamico'
            ).forEach(el => el.remove());

            for (const [chave, idSelect] of Object.entries(mapeamento)) {

                const ts = tomSelectInstances[idSelect];

                if (!ts) continue;

                const valores = valoresArray(ts.getValue());

                valores.forEach(valor => {

                    const input = document.createElement('input');

                    input.type = 'hidden';
                    input.name = chave + '[]';
                    input.value = valor;
                    input.classList.add('input-filtro-dinamico');

                    formSalvar.appendChild(input);
                });
            }

            formSalvar.submit();
        });
    }

});
</script>
@endpush
<style>
    #formFiltros .ts-wrapper.multi .ts-control > div {
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
        background: rgba(var(--bs-primary-rgb), .10);
        color: var(--bs-body-color);
    }

    #formFiltros .ts-dropdown {
        border: 1px solid var(--bs-border-color);
        border-radius: 10px;
        background: var(--bs-body-bg);
        box-shadow: 0 16px 36px rgba(0,0,0,.22);
    }

    #formFiltros .ts-dropdown .option.active,
    #formFiltros .ts-dropdown .option.selected {
        background: rgba(var(--bs-primary-rgb), .12);
        color: var(--bs-primary);
    }
</style>