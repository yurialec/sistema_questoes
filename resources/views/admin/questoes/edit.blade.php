@extends('layouts.app')

@section('title', 'Editar Questão #' . $questao->numero)

@section('content')
<div class="container py-4">
    
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">✏️ Editando Questão #{{ $questao->numero }}</h2>
            <p class="text-muted small mb-0">ID: {{ $questao->id }} | Matéria: {{ $questao->materia->nome ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('admin.questoes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar para Lista
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.questoes.update', $questao->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- SEÇÃO 1: DADOS GERAIS -->
        <div class="accordion mb-3" id="accDadosGerais">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDados">
                        1. Dados Gerais e Classificação
                    </button>
                </h2>
                <div id="collapseDados" class="accordion-collapse collapse show" data-bs-parent="#accDadosGerais">
                    <div class="accordion-body bg-white">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Matéria</label>
                                <select name="materia_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    @foreach($materias as $m)
                                        <option value="{{ $m->id }}" {{ $questao->materia_id == $m->id ? 'selected' : '' }}>{{ $m->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Assunto</label>
                                <select name="assunto_id" class="form-select">
                                    <option value="">Selecione...</option>
                                    @foreach($assuntos as $a)
                                        <option value="{{ $a->id }}" {{ $questao->assunto_id == $a->id ? 'selected' : '' }}>{{ $a->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Cargo / Prova (Órgão - Banca - Ano)</label>
                                <select name="cargo_id" class="form-select" required>
                                    <option value="">Selecione o cargo...</option>
                                    @foreach($cargos as $c)
                                        <option value="{{ $c->id }}" {{ $questao->cargo_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->orgao->nome ?? '' }} - {{ $c->banca->nome ?? '' }} ({{ $c->ano->ano ?? '' }}) - {{ $c->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Caminho da Imagem do Enunciado (Opcional)</label>
                                <input type="text" name="imagem" class="form-control" value="{{ old('imagem', $questao->imagem) }}" placeholder="ex: questoes/bb2023/ti/66.png">
                                <small class="text-muted">Deixe vazio se não houver imagem principal.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 2: ENUNCIADO E COMPLEMENTAR -->
        <div class="accordion mb-3" id="accEnunciado">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEnunciado">
                        2. Enunciado e Texto Complementar
                    </button>
                </h2>
                <div id="collapseEnunciado" class="accordion-collapse collapse" data-bs-parent="#accEnunciado">
                    <div class="accordion-body bg-white">
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-primary">Enunciado da Questão</label>
                            <textarea name="enunciado" class="form-control tinymce-editor" rows="10">{{ old('enunciado', $questao->enunciado) }}</textarea>
                        </div>

                        <hr>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="checkTextoComp" name="tem_texto_complementar" 
                                {{ old('tem_texto_complementar', $questao->texto_complementar_id) ? 'checked' : '' }}
                                onchange="document.getElementById('boxTextoComp').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label fw-bold" for="checkTextoComp">
                                Esta questão possui Texto Complementar?
                            </label>
                        </div>

                        <div id="boxTextoComp" class="{{ $questao->texto_complementar_id ? '' : 'd-none' }}">
                            <label class="form-label small fw-bold text-info">Conteúdo do Texto Complementar</label>
                            <textarea name="texto_complementar" class="form-control tinymce-editor" rows="6">{{ old('texto_complementar', $questao->textoComplementar->conteudo ?? '') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO 3: ALTERNATIVAS -->
        <div class="accordion mb-3" id="accAlternativas">
            <div class="accordion-item border-0 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlts">
                        3. Alternativas e Gabarito
                    </button>
                </h2>
                <div id="collapseAlts" class="accordion-collapse collapse" data-bs-parent="#accAlternativas">
                    <div class="accordion-body bg-white">
                        <p class="text-muted small mb-3">Preencha o conteúdo de cada alternativa. Marque o radio button ao lado da alternativa correta.</p>
                        
                        @php $letras = ['A', 'B', 'C', 'D', 'E']; @endphp
                        
                        @foreach($letras as $letra)
                            @php
                                // Busca a alternativa existente para esta letra
                                $altExistente = $questao->alternativas->firstWhere('letra', $letra);
                            @endphp
                            
                            <div class="card mb-3 border-light bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Radio do Gabarito -->
                                        <div class="mt-4">
                                            <div class="form-check">
                                                <input class="form-check-input fs-4" type="radio" name="alternativas[{{ $letra }}][correta]" value="1" id="gab_{{ $letra }}"
                                                    {{ old("alternativas.{$letra}.correta", $altExistente?->correta) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold text-success" for="gab_{{ $letra }}">
                                                    Gabarito
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Conteúdo da Alternativa -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-dark fs-6 me-2">{{ $letra }}</span>
                                                <span class="text-muted small">Conteúdo da Alternativa</span>
                                            </div>
                                            
                                            <!-- Input hidden para manter o ID se for edição -->
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
        <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
            <a href="{{ route('admin.questoes.index') }}" class="btn btn-secondary px-4">Cancelar</a>
            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
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
                height: 200, // Menor que o enunciado
                menubar: false,
                plugins: 'lists link image code codesample table paste',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | codesample | image | table | removeformat',
                paste_data_images: true,
                codesample_languages: [
                    {text: 'SQL', value: 'sql'}, {text: 'Java', value: 'java'}, 
                    {text: 'Python', value: 'python'}, {text: 'JavaScript', value: 'javascript'}
                ]
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