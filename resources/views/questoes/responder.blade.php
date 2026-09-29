@extends('layouts.app')

@section('title', 'Filtrar Questões')

@section('content')
<div class="container mt-4">
        {{-- FORMULÁRIO DE FILTROS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('responder') }}" id="formFiltros" class="row g-3">

                {{-- Filtros Principais (Múltipla Seleção) --}}
                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Órgão</label>
                    <select name="orgao_id[]" id="selectOrgao" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($orgaos as $orgao)
                            <option value="{{ $orgao->id }}" {{ in_array($orgao->id, request('orgao_id', [])) ? 'selected' : '' }}>
                                {{ $orgao->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Banca</label>
                    <select name="banca_id[]" id="selectBanca" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($bancas as $banca)
                            <option value="{{ $banca->id }}" {{ in_array($banca->id, request('banca_id', [])) ? 'selected' : '' }}>
                                {{ $banca->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Ano</label>
                    <select name="ano_id[]" id="selectAno" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($anos as $ano)
                            <option value="{{ $ano->id }}" {{ in_array($ano->id, request('ano_id', [])) ? 'selected' : '' }}>
                                {{ $ano->ano }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted">Cargo</label>
                    <select name="cargo_id[]" id="selectCargo" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ in_array($cargo->id, request('cargo_id', [])) ? 'selected' : '' }}>
                                {{ $cargo->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted">Matéria</label>
                    <select name="materia_id[]" id="selectMateria" class="form-select form-select-sm tom-select" multiple placeholder="Selecione...">
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}" {{ in_array($materia->id, request('materia_id', [])) ? 'selected' : '' }}>
                                {{ $materia->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Linha de Ações e Filtros Salvos --}}
                <div class="col-12 mt-4 border-top pt-3">
                    <div class="row g-3 align-items-end">
                        
                        {{-- Gerenciamento de Filtros Salvos --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Filtros Salvos</label>
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
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnCarregarFiltro">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="col-md-8 d-flex flex-wrap gap-2 justify-content-md-end">
                            <button type="submit" class="btn btn-primary btn-sm px-4">
                                <i class="fas fa-filter me-1"></i> Aplicar
                            </button>
                            
                            <a href="{{ route('responder') }}" class="btn btn-outline-secondary btn-sm px-4">
                                <i class="fas fa-eraser me-1"></i> Limpar
                            </a>

                            <button type="button" class="btn btn-success btn-sm px-4" data-bs-toggle="modal" data-bs-target="#modalSalvarFiltro">
                                <i class="fas fa-save me-1"></i> Salvar Filtro
                            </button>

                            @if(request()->has('filtro_id') || $filtrosSalvos->count() > 0)
                                <form action="{{ route('filtros.excluir', request('filtro_id', 0)) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este filtro salvo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-4" {{ !$filtrosSalvos->count() ? 'disabled' : '' }}>
                                        <i class="fas fa-trash me-1"></i> Excluir Selecionado
                                    </button>
                                </form>
                            @endif

                            <span class="badge bg-light text-dark border ms-auto d-flex align-items-center px-3 py-2">
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
            <div class="modal-content">
                <form action="{{ route('filtros.salvar') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-save me-2"></i>Salvar Filtro Atual</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomeFiltro" class="form-label fw-bold">Nome do Filtro</label>
                            <input type="text" class="form-control" id="nomeFiltro" name="nome_filtro" placeholder="Ex: Revisão TI Banco do Brasil" required>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="definirPadrao" name="definir_padrao" value="1">
                            <label class="form-check-label" for="definirPadrao">
                                Definir como filtro padrão (carrega automaticamente ao entrar na página)
                            </label>
                        </div>
                        
                        {{-- Inputs hidden para enviar o estado atual dos selects --}}
                        <input type="hidden" name="orgao_id" id="hiddenOrgao">
                        <input type="hidden" name="banca_id" id="hiddenBanca">
                        <input type="hidden" name="ano_id" id="hiddenAno">
                        <input type="hidden" name="cargo_id" id="hiddenCargo">
                        <input type="hidden" name="materia_id" id="hiddenMateria">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- FEEDBACK DE RESPOSTA (Mantido do seu código anterior) --}}
    @if(session('resultado') !== null)
    <div class="alert {{ session('resultado') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mb-4" role="alert">
        @if(session('resultado')) ✅ Resposta correta! @else ❌ Resposta incorreta. @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- LISTAGEM DE QUESTÕES --}}
    @forelse($questoes as $questao)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                
                <!-- Lado Esquerdo: Número, Órgão e Assunto -->
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-primary px-3 py-2 fs-6 fw-bold shadow-sm">
                            Questão {{ $questao->numero ?? $questao->id }}
                        </span>
                        <span class="text-muted small d-flex align-items-center gap-1 fw-medium">
                            <i class="fas fa-building text-secondary"></i> 
                            {{ $questao->cargo->orgao->nome ?? 'Órgão não informado' }}
                        </span>
                    </div>
                    
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-tag text-primary fs-6"></i> 
                        {{ $questao->assunto->nome ?? 'Assunto não informado' }}
                    </h5>
                </div>

                <!-- Lado Direito: Metadados (Matéria, Ano, Banca) -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 fw-medium">
                        <i class="fas fa-book me-1"></i> {{ $questao->materia->nome ?? 'Sem matéria' }}
                    </span>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 fw-medium">
                        <i class="fas fa-calendar me-1"></i> {{ $questao->cargo->ano->ano ?? 'Ano' }}
                    </span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 fw-medium">
                        <i class="fas fa-university me-1"></i> {{ $questao->cargo->banca->nome ?? 'Banca' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($questao->imagem)
            <div class="text-center mb-3">
                <img src="{{ asset('storage/' . $questao->imagem) }}" alt="Imagem da questão" class="img-fluid rounded border" style="max-height: 300px;">
            </div>
            @endif

            @if($questao->texto_complementar_id && $questao->textoComplementar)
            <div class="d-grid gap-2 mb-3">
                <button class="btn btn-outline-info d-flex align-items-center justify-content-between px-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTexto{{ $questao->id }}">
                    <span class="fw-medium">📄 Ver Texto Complementar</span>
                    <span class="icon-toggle ms-2 fs-5 fw-bold lh-1" id="iconText{{ $questao->id }}">+</span>
                </button>
            </div>
            <div class="collapse" id="collapseTexto{{ $questao->id }}">
                <div class="bg-light p-3 rounded border-start border-4 border-info mb-3">
                    {{-- REMOVIDO O e() E nl2br() AQUI --}}
                    {!! $questao->textoComplementar->conteudo !!}
                </div>
            </div>
            <hr class="my-4">
            @endif

            @if($questao->tabela_html)
            <div class="table-responsive mb-3">{!! $questao->tabela_html !!}</div>
            @endif

            {{-- REMOVIDO O e() E nl2br() AQUI, USANDO DIV EM VEZ DE P --}}
            <div class="enunciado-content lead fs-6">
                {!! $questao->enunciado !!}
            </div>

            <form method="POST" action="{{ route('questao.verificar') }}" class="mt-4">
                @csrf
                <input type="hidden" name="questao_id" value="{{ $questao->id }}">
                <fieldset>
                    <legend class="fs-6 text-muted mb-3 fw-semibold">Alternativas:</legend>
                    @foreach($questao->alternativas->shuffle() as $alternativa)
                    <div class="form-check mb-3 p-3 rounded border hover-bg-light">
                        <input class="form-check-input mt-1" type="radio" name="alternativa_id" id="alt_{{ $questao->id }}_{{ $alternativa->letra }}" value="{{ $alternativa->id }}" required>
                        <label class="form-check-label w-100 ps-2" for="alt_{{ $questao->id }}_{{ $alternativa->letra }}">
                            <strong class="text-primary">{{ $alternativa->letra }})</strong> 
                            {{-- REMOVIDO O e() AQUI PARA RENDERIZAR HTML/IMAGENS --}}
                            {!! $alternativa->descricao !!}
                            
                            @if($alternativa->imagens && is_array($alternativa->imagens))
                                @foreach($alternativa->imagens as $img)
                                <br><img src="{{ asset('storage/' . $img) }}" class="img-fluid mt-2 rounded border" style="max-width: 100%; max-height: 200px;">
                                @endforeach
                            @endif
                        </label>
                    </div>
                    @endforeach
                </fieldset>
                <div class="d-grid gap-2 d-md-block mt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Responder Questão
                    </button>
                </div>
            </form>
        </div>
    </div>
    @empty
    <div class="alert alert-warning text-center py-5">
        Nenhuma questão encontrada com os filtros selecionados.
    </div>
    @endforelse

    {{-- PAGINAÇÃO (O appends é crucial aqui!) --}}
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $questoes->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@if(session('show_error_modal') && session('erro_id'))
    @php $erroAtual = \App\Models\CadernoErro::find(session('erro_id')); @endphp
    
    <div class="modal fade show d-block" id="modalErro" tabindex="-1" style="background: rgba(0,0,0,0.5);" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">⚠️ Registro de Erro</h5>
                </div>
                <form action="{{ route('caderno-erros.salvar-motivo', $erroAtual->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small">Entender o motivo do erro é o primeiro passo para não cometê-lo novamente.</p>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="foi_chute" id="foi_chute">
                                <label class="form-check-label" for="foi_chute">Foi chute (não sabia o conteúdo)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="erro_distraido" id="erro_distraido">
                                <label class="form-check-label" for="erro_distraido">Sabia a matéria, mas errei por distração no enunciado</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motivo_erro" class="form-label fw-semibold">Por que você marcou essa alternativa incorreta?</label>
                            <textarea class="form-control" name="motivo_erro" id="motivo_erro" rows="3" placeholder="Ex: Confundi o conceito de X com Y..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('responder') }}" class="btn btn-outline-secondary">Pular por enquanto</a>
                        <button type="submit" class="btn btn-danger">Salvar no Caderno de Erros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Foca no modal ao carregar
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalErro'));
            modal.show();
        });
    </script>
@endif

{{-- Script para o toggle do texto complementar (do seu código anterior) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inicializar Tom Select em todos os selects com a classe .tom-select
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
                
                // Atualiza os selects e o Tom Select
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
                        ts.clear(); // Limpa a seleção atual
                        ts.setValue(valores); // Define os novos valores
                    }
                }

                // Opcional: Redirecionar automaticamente ou apenas preencher para o usuário clicar em Aplicar
                // window.location.href = "{{ route('responder') }}?" + new URLSearchParams(filtros).toString();
            }
        });

        // 3. Lógica para o Modal de Salvar (Cria inputs dinâmicos para o Laravel)
        const formSalvar = document.querySelector('#modalSalvarFiltro form');
        if(formSalvar) {
            formSalvar.addEventListener('submit', function(e) {
                e.preventDefault(); // Previne o envio padrão para manipular os dados
                
                const mapeamento = {
                    'orgao_id': 'selectOrgao',
                    'banca_id': 'selectBanca',
                    'ano_id': 'selectAno',
                    'cargo_id': 'selectCargo',
                    'materia_id': 'selectMateria'
                };

                // Remove inputs antigos se houver
                document.querySelectorAll('.input-filtro-dinamico').forEach(el => el.remove());

                for (const [chave, idSelect] of Object.entries(mapeamento)) {
                    const ts = tomSelectInstances[idSelect];
                    if (ts) {
                        const valores = ts.getValue();
                        valores.forEach(valor => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = chave + '[]'; // Formato array para o Laravel
                            input.value = valor;
                            input.classList.add('input-filtro-dinamico');
                            formSalvar.appendChild(input);
                        });
                    }
                }
                
                formSalvar.submit(); // Envia o formulário agora
            });
        }
    });
</script>
@endpush