@extends('layouts.app')

@section('title', 'Filtrar Questões')

@section('content')
<div class="container py-4">
    
    {{-- ══════════════════════════════════════════════
         FORMULÁRIO DE FILTROS
    ═══════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-sm mb-4 bg-body-tertiary">
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

                            <span class="badge bg-body text-body-secondary border border-secondary-subtle ms-auto d-flex align-items-center px-3 py-2">
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
                        
                        <input type="hidden" name="orgao_id" id="hiddenOrgao">
                        <input type="hidden" name="banca_id" id="hiddenBanca">
                        <input type="hidden" name="ano_id" id="hiddenAno">
                        <input type="hidden" name="cargo_id" id="hiddenCargo">
                        <input type="hidden" name="materia_id" id="hiddenMateria">
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
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body-tertiary border-bottom border-secondary-subtle py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-primary px-3 py-2 fs-6 fw-bold shadow-sm">
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
                <button class="btn btn-outline-info d-flex align-items-center justify-content-between px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTexto{{ $questao->id }}">
                    <span class="fw-medium small">Ver Texto Complementar</span>
                    <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $questao->id }}">+</span>
                </button>
            </div>
            <div class="collapse" id="collapseTexto{{ $questao->id }}">
                <div class="bg-body-tertiary p-3 rounded border-start border-4 border-info-subtle mb-4">
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

            <form method="POST" action="{{ route('questao.verificar') }}" class="mt-4">
                @csrf
                <input type="hidden" name="questao_id" value="{{ $questao->id }}">
                <fieldset>
                    <legend class="fs-6 text-body-secondary mb-3 fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Alternativas</legend>
                    @foreach($questao->alternativas->shuffle() as $alternativa)
                    <div class="form-check p-3 mb-3 rounded border border-secondary-subtle bg-body-tertiary">
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
                    <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                        <i class="fas fa-check-circle me-1"></i> Responder Questão
                    </button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="alert alert-warning text-center py-5 border-0 shadow-sm">
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
    @php $erroAtual = \App\Models\CadernoErro::find(session('erro_id')); @endphp
    
    <div class="modal fade show d-block" id="modalErro" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom border-secondary-subtle bg-danger-subtle">
                    <h5 class="modal-title fs-6 fw-bold text-danger-emphasis"><i class="fas fa-exclamation-triangle me-2"></i>Registro de Erro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('caderno-erros.salvar-motivo', $erroAtual->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-body-secondary small mb-3">Entender o motivo do erro é o primeiro passo para não cometê-lo novamente.</p>
                        
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
                            <label for="motivo_erro" class="form-label fw-semibold small text-body-secondary">Por que você marcou essa alternativa incorreta?</label>
                            <textarea class="form-control" name="motivo_erro" id="motivo_erro" rows="3" placeholder="Ex: Confundi o conceito de X com Y..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary-subtle bg-body-tertiary">
                        <a href="{{ route('responder') }}" class="btn btn-sm btn-outline-secondary">Pular por enquanto</a>
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
    });
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inicializar Tom Select
        const tomSelectInstances = {};
        document.querySelectorAll('.tom-select').forEach(el => {
            tomSelectInstances[el.id] = new TomSelect(el, {
                plugins: ['remove_button'],
                placeholder: 'Selecione...',
                maxItems: null,
                searchField: ['text', 'value']
            });
        });

        // 2. Lógica para Carregar Filtro Salvo
        document.getElementById('btnCarregarFiltro').addEventListener('click', function() {
            const select = document.getElementById('selectFiltrosSalvos');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const filtros = JSON.parse(selectedOption.dataset.filtros);
                const mapeamento = {
                    'orgao_id': 'selectOrgao',
                    'banca_id': 'selectBanca',
                    'ano_id': 'selectAno',
                    'cargo_id': 'selectCargo',
                    'materia_id': 'selectMateria'
                };

                for (const [chave, idSelect] of Object.entries(mapeamento)) {
                    const valores = filtros[chave] || [];
                    const ts = tomSelectInstances[idSelect];
                    if (ts) {
                        ts.clear();
                        ts.setValue(valores);
                    }
                }
            }
        });

        // 3. Lógica para o Modal de Salvar
        const formSalvar = document.querySelector('#modalSalvarFiltro form');
        if(formSalvar) {
            formSalvar.addEventListener('submit', function(e) {
                e.preventDefault();
                const mapeamento = {
                    'orgao_id': 'selectOrgao',
                    'banca_id': 'selectBanca',
                    'ano_id': 'selectAno',
                    'cargo_id': 'selectCargo',
                    'materia_id': 'selectMateria'
                };

                document.querySelectorAll('.input-filtro-dinamico').forEach(el => el.remove());

                for (const [chave, idSelect] of Object.entries(mapeamento)) {
                    const ts = tomSelectInstances[idSelect];
                    if (ts) {
                        const valores = ts.getValue();
                        valores.forEach(valor => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = chave + '[]';
                            input.value = valor;
                            input.classList.add('input-filtro-dinamico');
                            formSalvar.appendChild(input);
                        });
                    }
                }
                formSalvar.submit();
            });
        }
    });
</script>
@endpush