
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top" id="mainNavigation">
    <div class="container-fluid">

        <a href="{{ route('dashboard') }}"
           class="navbar-brand fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-house text-primary"></i>
            <span>Home</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none"
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
                        <i class="fas fa-play me-2"></i>
                        Responder
                    </a>
                </li>

                {{-- Dropdown Relatórios --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="fas fa-chart-bar me-2 d-lg-none"></i>
                        Relatórios
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('dashboard.desempenho-materia') }}">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>
                                Desempenho
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="{{ route('relatorios.curva') }}">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                Curva de Aprendizagem
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('caderno-erros.index') }}">
                        <i class="fas fa-book me-2 d-lg-none"></i>
                        Caderno de Erros
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('simulados.index') }}">
                        <i class="fas fa-clipboard-check me-2 d-lg-none"></i>
                        Simulado
                    </a>
                </li>

                @if(auth()->user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-medium"
                           href="{{ route('admin.questoes.index') }}">
                            <i class="fas fa-cog me-2"></i>
                            Admin
                        </a>
                    </li>
                @endif

            </ul>

            {{-- Menu do Usuário --}}
            <div class="user-navigation ms-lg-3">

                <div class="dropdown">

                    <button class="btn btn-link text-decoration-none text-secondary border-0 dropdown-toggle-hide user-menu-button"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Minha conta">

                        <i class="fa-regular fa-circle-user fa-2x"></i>

                        <span class="d-lg-none fw-medium">
                            Minha Conta
                        </span>

                        <i class="fas fa-chevron-down fa-xs d-lg-none ms-auto"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-lg-end shadow-sm">

                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2"
                               href="{{ route('profile.edit') }}">
                                <i class="fas fa-user text-secondary"></i>
                                <span>Meu Perfil</span>
                            </a>
                        </li>

                        <li>
                            <form action="{{ url('/dashboard/resetar') }}"
                                  method="POST"
                                  class="m-0"
                                  onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
                                @csrf

                                <button type="submit"
                                        class="dropdown-item d-flex align-items-center gap-2 w-100 border-0 bg-transparent">
                                    <i class="fas fa-trash-alt text-secondary"></i>
                                    <span>Zerar Estatísticas</span>
                                </button>
                            </form>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form action="{{ route('logout') }}"
                                  method="POST"
                                  class="m-0">
                                @csrf

                                <button type="submit"
                                        class="dropdown-item d-flex align-items-center gap-2 w-100 border-0 bg-transparent text-danger">
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

<style>
    /* Navegação geral */
    #mainNavigation .navbar-nav .nav-link {
        transition: color 0.2s ease, background-color 0.2s ease;
        border-radius: 0.375rem;
        padding: 0.65rem 0.75rem !important;
    }

    #mainNavigation .navbar-nav .nav-link:hover {
        color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.08);
    }

    #mainNavigation .dropdown-item {
        border-radius: 0.375rem;
        padding: 0.65rem 0.85rem;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    #mainNavigation .dropdown-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--bs-primary);
    }

    #mainNavigation .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.08);
        color: var(--bs-danger) !important;
    }

    #mainNavigation .dropdown-toggle-hide::after {
        display: none !important;
    }

    #mainNavigation .dropdown-toggle-hide i {
        transition: color 0.2s ease;
    }

    #mainNavigation .dropdown-toggle-hide:hover i {
        color: var(--bs-primary) !important;
    }

    #mainNavigation .nav-link.text-warning:hover {
        color: var(--bs-warning) !important;
        background-color: rgba(var(--bs-warning-rgb), 0.08);
    }

    /* Mobile e tablets */
    @media (max-width: 991.98px) {

        #mainNavigation .navbar-brand {
            font-size: 1.1rem;
        }

        #mainNavigation .navbar-toggler {
            padding: 0.45rem;
        }

        #mainNavbar {
            padding-top: 0.75rem;
            padding-bottom: 0.5rem;
            max-height: calc(100dvh - 65px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        #mainNavigation .navbar-nav {
            gap: 0.25rem !important;
            margin-bottom: 0.5rem !important;
        }

        #mainNavigation .navbar-nav .nav-link {
            display: flex;
            align-items: center;
            min-height: 44px;
            padding: 0.8rem 0.9rem !important;
            font-size: 0.95rem;
        }

        #mainNavigation .navbar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Dropdown de Relatórios */
        #mainNavigation .navbar-nav .dropdown-menu {
            position: static;
            float: none;
            width: 100%;
            margin: 0.25rem 0 0.5rem;
            padding: 0.35rem;
            border: 0;
            box-shadow: none;
            background-color: rgba(var(--bs-secondary-rgb), 0.06);
            border-radius: 0.5rem;
        }

        #mainNavigation .navbar-nav .dropdown-item {
            white-space: normal;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        /* Área do usuário */
        #mainNavigation .user-navigation {
            border-top: 1px solid var(--bs-border-color);
            padding-top: 0.75rem;
            margin-top: 0.5rem;
        }

        #mainNavigation .user-menu-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            min-height: 48px;
            padding: 0.65rem 0.9rem;
            text-align: left;
            border-radius: 0.375rem;
        }

        #mainNavigation .user-menu-button:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.08);
        }

        #mainNavigation .user-menu-button .fa-circle-user {
            font-size: 1.5rem;
        }

        #mainNavigation .user-navigation .dropdown-menu {
            position: static !important;
            transform: none !important;
            float: none;
            width: 100%;
            margin-top: 0.25rem;
            padding: 0.35rem;
            border: 0;
            box-shadow: none !important;
            background-color: rgba(var(--bs-secondary-rgb), 0.06);
            border-radius: 0.5rem;
        }

        #mainNavigation .user-navigation .dropdown-item {
            min-height: 44px;
            white-space: normal;
            padding: 0.75rem 1rem;
        }

        #mainNavigation .user-navigation .dropdown-item i {
            width: 20px;
            text-align: center;
        }
    }

    /* Telas muito pequenas */
    @media (max-width: 375px) {
        #mainNavigation .navbar-nav .nav-link {
            font-size: 0.9rem;
        }

        #mainNavigation .dropdown-item {
            font-size: 0.875rem;
        }
    }

    /* Modal mobile */
    @media (max-width: 575.98px) {
        #modalMetaAprovacao .modal-dialog {
            margin: 0.75rem;
        }

        #modalMetaAprovacao .modal-content {
            border-radius: 0.85rem;
        }

        #modalMetaAprovacao .modal-header,
        #modalMetaAprovacao .modal-body,
        #modalMetaAprovacao .modal-footer {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        #modalMetaAprovacao .modal-title {
            font-size: 1rem;
        }

        #modalMetaAprovacao .modal-footer {
            display: flex;
            gap: 0.5rem;
        }

        #modalMetaAprovacao .modal-footer .btn {
            flex: 1;
            min-height: 42px;
        }

        #modalMetaAprovacao #toggleAnos {
            font-size: 0.75rem;
        }
    }
</style>

<!-- Modal Meta de Aprovação -->
<div class="modal fade"
     id="modalMetaAprovacao"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

        <form action="{{ route('metas-aprovacao.store') }}"
              method="POST"
              class="w-100">
            @csrf

            <div class="modal-content">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold">
                        Definir Meta de Aprovação
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Fechar">
                    </button>
                </div>

                <div class="modal-body pt-2">

                    <p class="text-muted small mb-3">
                        Selecione o cargo desejado. O sistema criará
                        automaticamente um filtro e acompanhará seu
                        progresso até a aprovação.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-uppercase text-muted">
                            Cargo
                        </label>

                        <select class="form-select"
                                name="cargo_id"
                                required>

                            <option value="" disabled selected>
                                Selecione um cargo
                            </option>

                            @foreach (\App\Models\Cargo::orderBy('nome')->get() as $cargo)
                                <option value="{{ $cargo->id }}">
                                    {{ $cargo->nome }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">

                        <label class="form-label small fw-semibold text-uppercase text-muted d-flex justify-content-between align-items-center gap-2">
                            <span>Anos de referência</span>

                            <button type="button"
                                    class="btn btn-link btn-sm p-0 text-decoration-none"
                                    id="toggleAnos">
                                Marcar/Desmarcar
                            </button>
                        </label>

                        <div class="border rounded p-2"
                             style="max-height: 180px; overflow-y: auto;">

                            @forelse (\App\Models\Ano::orderBy('ano', 'desc')->get() as $ano)

                                <div class="form-check py-1">

                                    <input class="form-check-input ano-checkbox"
                                           type="checkbox"
                                           name="anos[]"
                                           value="{{ $ano->id }}"
                                           id="ano-{{ $ano->id }}">

                                    <label class="form-check-label small"
                                           for="ano-{{ $ano->id }}">
                                        {{ $ano->ano }}
                                    </label>

                                </div>

                            @empty

                                <p class="text-muted small mb-0">
                                    Nenhum ano cadastrado.
                                </p>

                            @endforelse

                        </div>

                        <small class="text-muted d-block mt-1">
                            Se nenhum for marcado, considera todos os anos.
                        </small>

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="fa-solid fa-floppy-disk fa-xs"></i>
                        Criar Meta
                    </button>

                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* Marcar/Desmarcar anos */
    const btn = document.getElementById('toggleAnos');

    if (btn) {
        btn.addEventListener('click', () => {
            const checks = document.querySelectorAll('.ano-checkbox');

            const allChecked = [...checks].every(c => c.checked);

            checks.forEach(c => {
                c.checked = !allChecked;
            });
        });
    }

    /* Fechar navbar mobile ao clicar em links */
    const navbar = document.getElementById('mainNavbar');

    if (navbar && typeof bootstrap !== 'undefined') {

        navbar.querySelectorAll('a.nav-link, a.dropdown-item').forEach(link => {

            link.addEventListener('click', () => {

                if (window.innerWidth >= 992) return;

                if (link.hasAttribute('data-bs-toggle')) return;

                if (!navbar.classList.contains('show')) return;

                const collapse = bootstrap.Collapse.getOrCreateInstance(
                    navbar,
                    { toggle: false }
                );

                collapse.hide();
            });

        });
    }

});
</script>
@endpush
