<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container-fluid">
        <a href="{{ route('dashboard') }}" class="navbar-brand fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-regular fa-house text-primary"></i>
            <span>Home</span>
        </a>
        <button class="navbar-toggler border-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            {{-- Navegação Principal --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('responder') }}">
                        <i class="fas fa-play me-1"></i> Responder
                    </a>
                </li>

                {{-- Dropdown Relatórios --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Relatórios
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.desempenho-materia') }}">
                                <i class="fas fa-chart-bar me-2 text-primary"></i> Desempenho
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('relatorios.curva') }}">
                                <i class="fas fa-chart-line me-2 text-primary"></i> Curva de Aprendizagem
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('caderno-erros.index') }}">Caderno de Erros</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('simulados.index') }}">Simulado</a>
                </li>

                @if(auth()->user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-medium" href="{{ route('admin.questoes.index') }}">
                            <i class="fas fa-cog me-1"></i> Admin
                        </a>
                    </li>
                @endif
            </ul>

            {{-- Dropdown do Usuário (canto direito) --}}
            <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none text-secondary p-0 border-0 dropdown-toggle-hide"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        title="Minha conta">
                        <i class="fa-regular fa-circle-user fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user text-secondary"></i>
                                <span>Meu Perfil</span>
                            </a>
                        </li>
                        <li>
                            <form action="{{ url('/dashboard/resetar') }}" method="POST" class="m-0" onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 w-100 border-0 bg-transparent">
                                    <i class="fas fa-trash-alt text-secondary"></i>
                                    <span>Zerar Estatísticas</span>
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 w-100 border-0 bg-transparent text-danger">
                                    <i class="fas fa-arrow-right-from-bracket text-danger"></i>
                                    <span>Sair</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Estilo para efeito hover azul nos botões e links de navegação --}}
<style>
    /* Efeito hover azul nos nav-links */
    .navbar-nav .nav-link {
        transition: color 0.2s ease, background-color 0.2s ease;
        border-radius: 0.375rem;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .navbar-nav .nav-link:hover {
        color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.08);
    }

    /* Dropdown Relatórios - hover nos itens */
    .dropdown-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--bs-primary);
    }

    /* Dropdown do usuário - hover nos itens */
    .dropdown-menu .dropdown-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--bs-primary);
    }

    /* Exceção: item "Sair" mantém cor vermelha no hover */
    .dropdown-menu .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.08);
        color: var(--bs-danger) !important;
    }

    /* Esconder a seta do dropdown do ícone do usuário */
    .dropdown-toggle-hide::after {
        display: none !important;
    }

    /* Ícone do usuário com transição suave */
    .dropdown-toggle-hide i {
        transition: color 0.2s ease;
    }

    .dropdown-toggle-hide:hover i {
        color: var(--bs-primary) !important;
    }

    /* Exceção: Admin não muda de cor no hover (permanece warning) */
    .navbar-nav .nav-link.text-warning:hover {
        color: var(--bs-warning) !important;
        background-color: rgba(var(--bs-warning-rgb), 0.08);
    }
</style>

<!-- Modal Meta de Aprovação -->
<div class="modal fade" id="modalMetaAprovacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('metas-aprovacao.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold">Definir Meta de Aprovação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small mb-3">
                        Selecione o cargo desejado. O sistema criará automaticamente um filtro e acompanhará seu progresso até a aprovação.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-uppercase text-muted">
                            Cargo
                        </label>
                        <select class="form-select form-select-sm" name="cargo_id" required>
                            <option value="" disabled selected>Selecione um cargo</option>
                            @foreach (\App\Models\Cargo::orderBy('nome')->get() as $cargo)
                                <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-uppercase text-muted d-flex justify-content-between align-items-center">
                            <span>Anos de referência</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="toggleAnos">Marcar/Desmarcar</button>
                        </label>
                        <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
                            @forelse (\App\Models\Ano::orderBy('ano', 'desc')->get() as $ano)
                                <div class="form-check">
                                    <input class="form-check-input ano-checkbox" type="checkbox"
                                        name="anos[]" value="{{ $ano->id }}" id="ano-{{ $ano->id }}">
                                    <label class="form-check-label small" for="ano-{{ $ano->id }}">
                                        {{ $ano->ano }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small mb-0">Nenhum ano cadastrado.</p>
                            @endforelse
                        </div>
                        <small class="text-muted d-block mt-1">
                            Se nenhum for marcado, considera todos os anos.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-floppy-disk fa-xs"></i> Criar Meta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('toggleAnos');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const checks = document.querySelectorAll('.ano-checkbox');
        const allChecked = [...checks].every(c => c.checked);
        checks.forEach(c => c.checked = !allChecked);
    });
});
</script>
@endpush