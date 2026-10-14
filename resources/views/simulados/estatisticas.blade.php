@extends('layouts.app')

@section('title', 'Estatísticas de Simulados')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-semibold">
                <i class="fas fa-chart-line me-2 text-primary"></i> Estatísticas
            </h4>
            <p class="text-body-secondary small mb-0">Evolução dos seus simulados finalizados.</p>
        </div>
        <a href="{{ route('simulados.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>

    @if($totalSimulados === 0)
        <div class="alert alert-info border-0 shadow-sm text-center py-5">
            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
            Você ainda não finalizou nenhum simulado.
        </div>
    @else

        {{-- ═══ Cards gerais ═══ --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-clipboard-check fa-2x text-primary mb-2"></i>
                        <h3 class="fw-bold mb-0">{{ $totalSimulados }}</h3>
                        <div class="text-body-secondary small">Simulados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-list-ol fa-2x text-info mb-2"></i>
                        <h3 class="fw-bold mb-0">{{ $totalQuestoes }}</h3>
                        <div class="text-body-secondary small">Questões respondidas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-percent fa-2x text-success mb-2"></i>
                        <h3 class="fw-bold mb-0 text-success">{{ $aproveitamentoMedio }}%</h3>
                        <div class="text-body-secondary small">Aproveitamento médio</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-stopwatch fa-2x text-warning mb-2"></i>
                        <h3 class="fw-bold mb-0">{{ gmdate('H:i', $tempoMedioSegundos) }}</h3>
                        <div class="text-body-secondary small">Tempo médio</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Evolução ═══ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i> Evolução do aproveitamento
                </h6>
                <div style="position: relative; height: 300px;">
                    <canvas id="graficoEvolucao"></canvas>
                </div>
            </div>
        </div>

        {{-- ═══ Acertos x Erros por matéria ═══ --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-chart-bar me-2 text-primary"></i> Acertos x Erros por matéria
                </h6>
                <div style="position: relative; height: 350px;">
                    <canvas id="graficoMaterias"></canvas>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
@if($totalSimulados > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ====== Dados vindos do backend ======
    const evolucao = @json($evolucao);
    const porMateria = @json($porMateria);

    // ====== Cores do tema (Bootstrap) ======
    const corSucesso   = '#198754';
    const corSucessoBg = 'rgba(25, 135, 84, 0.15)';
    const corPerigo    = '#dc3545';
    const corPrimaria  = '#0d6efd';

    // ====== 1) Gráfico de evolução ======
    const ctxEvolucao = document.getElementById('graficoEvolucao');
    if (ctxEvolucao) {
        new Chart(ctxEvolucao, {
            type: 'line',
            data: {
                labels: evolucao.map(e => e.data),
                datasets: [{
                    label: 'Aproveitamento (%)',
                    data: evolucao.map(e => e.perc),
                    borderColor: corPrimaria,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: corPrimaria,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const i = ctx.dataIndex;
                                const e = evolucao[i];
                                return [
                                    `Aproveitamento: ${e.perc}%`,
                                    `Acertos: ${e.acertos} / ${e.total}`,
                                    `Erros: ${e.erros}`,
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: v => v + '%' }
                    }
                }
            }
        });
    }

    // ====== 2) Gráfico por matéria (barras empilhadas) ======
    const ctxMaterias = document.getElementById('graficoMaterias');
    if (ctxMaterias) {
        new Chart(ctxMaterias, {
            type: 'bar',
            data: {
                labels: porMateria.map(m => m.nome),
                datasets: [
                    {
                        label: 'Acertos',
                        data: porMateria.map(m => m.acertos),
                        backgroundColor: corSucesso,
                        stack: 'questoes',
                    },
                    {
                        label: 'Erros',
                        data: porMateria.map(m => m.erros),
                        backgroundColor: corPerigo,
                        stack: 'questoes',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return `${ctx.dataset.label}: ${ctx.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endpush