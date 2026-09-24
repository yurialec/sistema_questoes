@extends('layouts.app')

@section('title', 'Filtrar Questões')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold">Banco de Questões</h2>

    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
            <i class="fas fa-arrow-left me-1"></i> Retornar ao Dashboard
        </a>
    </div>

    {{-- FORMULÁRIO DE FILTROS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('responder') }}" class="row g-3">

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Órgão</label>
                    <select name="orgao_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($orgaos as $orgao)
                        <option value="{{ $orgao->id }}" {{ request('orgao_id') == $orgao->id ? 'selected' : '' }}>
                            {{ $orgao->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Banca</label>
                    <select name="banca_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($bancas as $banca)
                        <option value="{{ $banca->id }}" {{ request('banca_id') == $banca->id ? 'selected' : '' }}>
                            {{ $banca->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted">Ano</label>
                    <select name="ano_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($anos as $ano)
                        <option value="{{ $ano->id }}" {{ request('ano_id') == $ano->id ? 'selected' : '' }}>
                            {{ $ano->ano }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted">Cargo</label>
                    <select name="cargo_id" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach($cargos as $cargo)
                        <option value="{{ $cargo->id }}" {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>
                            {{ $cargo->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted">Matéria</label>
                    <select name="materia_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        @foreach($materias as $materia)
                        <option value="{{ $materia->id }}" {{ request('materia_id') == $materia->id ? 'selected' : '' }}>
                            {{ $materia->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 mt-3 align-items-center">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('questoes.index') }}" class="btn btn-outline-secondary btn-sm px-4">
                        <i class="fas fa-eraser me-1"></i> Limpar Filtros
                    </a>
                    <span class="badge bg-light text-dark border ms-auto">
                        <i class="fas fa-list-ol me-1"></i>
                        {{ $questoes->total() }} {{ Str::plural('questão encontrada', $questoes->total()) }}
                    </span>
                </div>
            </form>
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
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-semibold">Questão {{ $questao->numero ?? $questao->id }}</h5>
            <div>
                <span class="badge bg-secondary me-1">{{ $questao->materia->nome ?? 'Sem matéria' }}</span>
                <span class="badge bg-info text-dark">{{ $questao->cargo->ano->ano ?? '' }} - {{ $questao->cargo->banca->nome ?? '' }}</span>
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
                    {!! nl2br(e($questao->textoComplementar->conteudo)) !!}
                </div>
            </div>
            <hr class="my-4">
            @endif

            @if($questao->tabela_html)
            <div class="table-responsive mb-3">{!! $questao->tabela_html !!}</div>
            @endif

            <p class="lead fs-6">{!! nl2br(e($questao->enunciado)) !!}</p>

            <form method="POST" action="{{ route('questao.verificar') }}" class="mt-4">
                @csrf
                <input type="hidden" name="questao_id" value="{{ $questao->id }}">
                <fieldset>
                    <legend class="fs-6 text-muted mb-3">Alternativas:</legend>
                    @foreach($questao->alternativas->shuffle() as $alternativa)
                    <div class="form-check mb-2 p-2 rounded hover-bg-light">
                        <input class="form-check-input" type="radio" name="alternativa_id" id="alt_{{ $questao->id }}_{{ $alternativa->letra }}" value="{{ $alternativa->id }}" required>
                        <label class="form-check-label w-100 ps-2" for="alt_{{ $questao->id }}_{{ $alternativa->letra }}">
                            <strong>{{ $alternativa->letra }})</strong> {{ $alternativa->descricao }}
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
                    <button type="submit" class="btn btn-primary px-4">Responder Questão</button>
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
                        <a href="{{ route('questoes.index') }}" class="btn btn-outline-secondary">Pular por enquanto</a>
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