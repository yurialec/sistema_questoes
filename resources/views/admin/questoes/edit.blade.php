@extends('layouts.app')

@section('title', 'Editar Questão #' . $questao->numero)

@section('content')
<div class="container py-4">
    
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="h4 fw-bold text-body mb-1">Editando Questão #{{ $questao->numero }}</h2>
            <p class="text-body-secondary small mb-0">
                ID: {{ $questao->id }} <span class="mx-1">•</span> Matéria: {{ $questao->materia->nome ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admin.questoes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar para Lista
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="fw-semibold mb-1"><i class="fas fa-exclamation-circle me-2"></i>Corrija os erros abaixo:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.questoes.update', $questao->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- SEÇÃO 1: DADOS GERAIS -->
        <div class="accordion mb-4" id="accDadosGerais">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDados">
                        1. Dados Gerais e Classificação
                    </button>
                </h2>
                <div id="collapseDados" class="accordion-collapse collapse show" data-bs-parent="#accDadosGerais">
                    <div class="accordion-body bg-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="materia_id" class="form-label small fw-semibold text-body-secondary">Matéria</label>
                                <select name="materia_id" id="materia_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($materias as $m)
                                        <option value="{{ $m->id }}" {{ $questao->materia_id == $m->id ? 'selected' : '' }}>{{ $m->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="assunto_id" class="form-label small fw-semibold text-body-secondary">Assunto</label>
                                <select name="assunto_id" id="assunto_id" class="form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($assuntos as $a)
                                        <option value="{{ $a->id }}" {{ $questao->assunto_id == $a->id ? 'selected' : '' }}>{{ $a->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="cargo_id" class="form-label small fw-semibold text-body-secondary">Cargo</label>
                                <select name="cargo_id" id="cargo_id" class="form-select" required>
                                    <option value="">Selecione o cargo...</option>
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->id }}" {{ $questao->cargo_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->nome }} — {{ $c->orgao->nome ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Ano da Prova</label>
                                <input type="text" class="form-control" value="{{ $questao->ano->ano ?? '—' }}" readonly>
                                <div class="form-text text-body-secondary">Definido na importação, não editável.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-body-secondary">Banca</label>
                                <input type="text" class="form-control" value="{{ $questao->banca->nome ?? '—' }}" readonly>
                                <div class="form-text text-body-secondary">Definida na importação, não editável.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 2: ENUNCIADO E COMPLEMENTAR -->
        <div class="accordion mb-4" id="accEnunciado">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnunciado">
                        2. Enunciado e Texto Complementar
                    </button>
                </h2>
                <div id="collapseEnunciado" class="accordion-collapse collapse" data-bs-parent="#accEnunciado">
                    <div class="accordion-body bg-body">
                        
                        <div class="mb-4">
                            <label for="enunciado" class="form-label small fw-semibold text-primary">Enunciado da Questão</label>
                            <textarea name="enunciado" id="enunciado" class="form-control tinymce-editor" rows="10">{{ old('enunciado', $questao->enunciado) }}</textarea>
                        </div>

                        <hr class="border-secondary-subtle my-4">

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="checkTextoComp" name="tem_texto_complementar" 
                                {{ old('tem_texto_complementar', $questao->texto_complementar_id) ? 'checked' : '' }}
                                onchange="document.getElementById('boxTextoComp').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label fw-semibold text-body" for="checkTextoComp">
                                Esta questão possui Texto Complementar?
                            </label>
                        </div>

                        <div id="boxTextoComp" class="{{ $questao->texto_complementar_id ? '' : 'd-none' }}">
                            <label for="texto_complementar" class="form-label small fw-semibold text-info-emphasis">Conteúdo do Texto Complementar</label>
                            <textarea name="texto_complementar" id="texto_complementar" class="form-control tinymce-editor" rows="6">{{ old('texto_complementar', $questao->textoComplementar->conteudo ?? '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 3: ALTERNATIVAS -->
        <div class="accordion mb-4" id="accAlternativas">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlts">
                        3. Alternativas e Gabarito
                    </button>
                </h2>
                <div id="collapseAlts" class="accordion-collapse collapse" data-bs-parent="#accAlternativas">
                    <div class="accordion-body bg-body">
                        <p class="text-body-secondary small mb-4">
                            <i class="fas fa-info-circle me-1"></i> Preencha o conteúdo de cada alternativa. Marque o botão de opção ao lado da alternativa correta.
                        </p>
                        
                        @php $letras = ['A', 'B', 'C', 'D', 'E']; @endphp
                        
                        @foreach($letras as $letra)
                            @php
                                $altExistente = $questao->alternativas->firstWhere('letra', $letra);
                            @endphp
                            
                            <div class="card mb-3 border border-secondary-subtle bg-body-tertiary">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Radio do Gabarito -->
                                        <div class="mt-1">
                                            <div class="form-check">
                                                <input class="form-check-input mt-1" type="radio" name="alternativas[{{ $letra }}][correta]" value="1" id="gab_{{ $letra }}"
                                                    {{ old("alternativas.{$letra}.correta", $altExistente?->correta) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-success-emphasis ms-1" for="gab_{{ $letra }}">
                                                    Gabarito
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Conteúdo da Alternativa -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-body text-body fw-bold border border-secondary-subtle me-2" style="min-width: 28px; text-align: center;">{{ $letra }}</span>
                                                <span class="text-body-secondary small fw-semibold">Conteúdo da Alternativa</span>
                                            </div>
                                            
                                            <input type="hidden" name="alternativas[{{ $letra }}][id]" value="{{ $altExistente?->id }}">
                                            <input type="hidden" name="alternativas[{{ $letra }}][letra]" value="{{ $letra }}">

                                            <textarea name="alternativas[{{ $letra }}][descricao]" class="form-control tinymce-editor-alt-{{ $letra }}" rows="4">{{ old("alternativas.{$letra}.descricao", $altExistente?->descricao) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        <!-- BOTÕES DE AÇÃO -->
        <div class="d-flex justify-content-end gap-2 mt-4 mb-5 pt-3 border-top border-secondary-subtle">
            <a href="{{ route('admin.questoes.index') }}" class="btn btn-outline-secondary px-4">
                Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                <i class="fas fa-save me-2"></i> Salvar Alterações
            </button>
        </div>

    </form>
</div>

<!-- Script Específico para esta View -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa TinyMCE para os campos de alternativas (IDs dinâmicos)
        ['A', 'B', 'C', 'D', 'E'].forEach(letra => {
            tinymce.init({
                selector: `.tinymce-editor-alt-${letra}`,
                height: 200,
                menubar: false,
                plugins: 'lists link image code codesample table paste',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | codesample | image | table | removeformat',
                paste_data_images: true,
                codesample_languages: [
                    {text: 'SQL', value: 'sql'}, {text: 'Java', value: 'java'}, 
                    {text: 'Python', value: 'python'}, {text: 'JavaScript', value: 'javascript'}
                ],
                // Garante que o editor do TinyMCE respeite o tema escuro se necessário (opcional, mas recomendado)
                content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; }'
            });
        });

        // Forçar salvamento do conteúdo do TinyMCE nos textareas antes do submit
        document.querySelector('form').addEventListener('submit', function(e) {
            tinymce.triggerSave();
        });
    });
</script>
@endpush
@endsection