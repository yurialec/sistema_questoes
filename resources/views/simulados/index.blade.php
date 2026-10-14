@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ═════════════ Cabeçalho ═════════════ --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div>
                <h4 class="mb-0 fw-semibold">Simulados</h4>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('simulados.estatisticas') }}"
               class="btn btn-outline-primary btn-sm px-4 fw-semibold">
                <i class="fas fa-chart-line me-1"></i> Ver estatísticas
            </a>
            <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#modalNovoSimulado">
                <i class="fas fa-plus me-1"></i> Novo Simulado
            </button>
        </div>
    </div>

    <div class="row g-4">

        {{-- ═════════════ Filtros ═════════════ --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm sticky-lg-top" style="top: 1rem;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-semibold text-body-secondary mb-0 text-uppercase small"
                            style="letter-spacing: 0.05em;">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </h6>
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                            {{ $simulados->total() }}
                        </span>
                    </div>

                    <form method="GET" action="{{ route('simulados.index') }}">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-body-secondary">Nome</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary border-end-0">
                                    <i class="fas fa-search text-body-tertiary"></i>
                                </span>
                                <input type="text" name="nome" class="form-control border-start-0"
                                       value="{{ request('nome') }}" placeholder="Buscar por nome...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-body-secondary">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="nao_iniciado" @selected(request('status') === 'nao_iniciado')>Não iniciado</option>
                                <option value="em_andamento" @selected(request('status') === 'em_andamento')>Em andamento</option>
                                <option value="pausado" @selected(request('status') === 'pausado')>Pausado</option>
                                <option value="finalizado" @selected(request('status') === 'finalizado')>Finalizado</option>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-body-secondary">De</label>
                                <input type="date" name="data_inicio" class="form-control form-control-sm"
                                       value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold text-body-secondary">Até</label>
                                <input type="date" name="data_fim" class="form-control form-control-sm"
                                       value="{{ request('data_fim') }}">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="fas fa-filter me-1"></i> Aplicar filtros
                            </button>
                            <a href="{{ route('simulados.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eraser me-1"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ═════════════ Lista ═════════════ --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-body-tertiary">
                            <tr class="text-uppercase small text-body-secondary" style="letter-spacing: 0.04em;">
                                <th class="ps-3 py-3 border-0">Nome</th>
                                <th class="py-3 border-0">Data</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="text-center py-3 border-0">Acertos</th>
                                <th class="text-center py-3 border-0">Erros</th>
                                <th class="text-center py-3 border-0">Progresso</th>
                                <th class="text-end pe-3 py-3 border-0">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($simulados as $simulado)
                                @php
                                    $badge = [
                                        'nao_iniciado' => 'secondary',
                                        'em_andamento' => 'primary',
                                        'pausado'      => 'warning',
                                        'finalizado'   => 'success',
                                    ][$simulado->status] ?? 'secondary';
                                    $label = [
                                        'nao_iniciado' => 'Não iniciado',
                                        'em_andamento' => 'Em andamento',
                                        'pausado'      => 'Pausado',
                                        'finalizado'   => 'Finalizado',
                                    ][$simulado->status] ?? $simulado->status;

                                    $total = $simulado->total_questoes ?: 1;
                                    $respondidas = $simulado->respondidas_count ?? 0;
                                    $percent = (int) round($respondidas / $total * 100);
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-body">
                                            {{ $simulado->nome ?? 'Simulado #' . $simulado->id }}
                                        </div>
                                    </td>
                                    <td class="text-body-secondary small">
                                        <i class="far fa-calendar me-1 opacity-75"></i>
                                        {{ $simulado->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}-emphasis border border-{{ $badge }}-subtle px-3 py-2">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold {{ $simulado->total_acertos !== null ? 'text-success' : 'text-body-tertiary' }}">
                                            {{ $simulado->total_acertos ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold {{ $simulado->total_erros !== null ? 'text-danger' : 'text-body-tertiary' }}">
                                            {{ $simulado->total_erros ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="min-width: 140px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary"
                                                     role="progressbar"
                                                     style="width: {{ $percent }}%"
                                                     aria-valuenow="{{ $percent }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="small text-body-secondary text-nowrap">
                                                {{ $respondidas }}/{{ $simulado->total_questoes }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('simulados.show', $simulado) }}"
                                               class="btn btn-outline-primary btn-sm"
                                               title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('simulados.destroy', $simulado) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Excluir este simulado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 border-0">
                                        <div class="text-body-tertiary mb-3">
                                            <i class="fas fa-inbox fa-3x opacity-50"></i>
                                        </div>
                                        <h6 class="fw-semibold text-body-secondary mb-1">
                                            Nenhum simulado encontrado
                                        </h6>
                                        <p class="text-body-tertiary small mb-3">
                                            Ajuste os filtros ou crie um novo simulado.
                                        </p>
                                        <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold"
                                                data-bs-toggle="modal" data-bs-target="#modalNovoSimulado">
                                            <i class="fas fa-plus me-1"></i> Criar simulado
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($simulados->hasPages())
                    <div class="card-footer bg-body border-top border-secondary-subtle">
                        {{ $simulados->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═════════════ Modal Novo Simulado ═════════════ --}}
<div class="modal fade" id="modalNovoSimulado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary-subtle">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary text-white"
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-semibold mb-0 text-primary-emphasis">Novo Simulado</h5>
                        <div class="small text-primary-emphasis opacity-75">
                            Monte um simulado a partir de uma prova real
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form method="POST" action="{{ route('simulados.store') }}" id="formNovoSimulado">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 d-flex gap-2 small mb-4">
                        <i class="fas fa-circle-info mt-1"></i>
                        <div>
                            Selecione órgão, cargo, banca e ano. Todas as questões dessa prova serão incluídas no simulado.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-body-secondary">Órgão</label>
                            <select name="orgao_id" class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                @foreach($orgaos as $orgao)
                                    <option value="{{ $orgao->id }}">{{ $orgao->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-body-secondary">Cargo</label>
                            <select name="cargo_id" class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-body-secondary">Banca</label>
                            <select name="banca_id" class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                @foreach($bancas as $banca)
                                    <option value="{{ $banca->id }}">{{ $banca->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-body-secondary">Ano</label>
                            <select name="ano_id" class="form-select form-select-sm" required>
                                <option value="">Selecione...</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano->id }}">{{ $ano->ano }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 opacity-25">
                            <label class="form-label small fw-semibold text-body-secondary">
                                Nome do simulado <span class="text-body-tertiary fw-normal">(opcional)</span>
                            </label>
                            <input type="text" name="nome" class="form-control form-control-sm"
                                   placeholder="Ex: TJSP 2024 - Analista Judiciário">
                            <div class="form-text">
                                Se deixar em branco, geramos um nome automaticamente.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top border-secondary-subtle bg-body-tertiary">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-check me-1"></i> Criar simulado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection