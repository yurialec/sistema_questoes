@php
    $usuarioAtual = auth()->user();
    $nomeUsuario = $usuarioAtual->name ?: 'Minha conta';
    $inicialUsuario = mb_strtoupper(mb_substr(trim($nomeUsuario), 0, 1));
@endphp

<nav class="navbar navbar-expand-lg sticky-top" id="mainNavigation" aria-label="Navegação principal">
    <div class="container-fluid px-3 px-lg-4">

        <a href="{{ route('dashboard') }}"
           class="navbar-brand d-flex align-items-center gap-2"
           aria-label="Sistema de Questões — ir para o dashboard">
            <span class="brand-mark" aria-hidden="true">
                <i class="fas fa-graduation-cap"></i>
            </span>
            <span class="brand-copy">
                <span class="brand-title">Sistema de Questões</span>
                <small class="brand-caption">Painel de estudos</small>
            </span>
        </a>

        <button class="navbar-toggler sq-navbar-toggler shadow-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Alternar navegação">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            {{-- Navegação Principal --}}
            <ul class="navbar-nav main-navigation-links me-auto mb-2 mb-lg-0 gap-1 ms-lg-3">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('responder') ? 'active' : '' }}" href="{{ route('responder') }}" @if(request()->routeIs('responder')) aria-current="page" @endif>
                        <i class="nav-icon fas fa-circle-play" aria-hidden="true"></i>
                        Responder
                    </a>
                </li>

                {{-- Dropdown Relatórios --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.desempenho-materia', 'relatorios.curva') ? 'active' : '' }}"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="nav-icon fas fa-chart-column" aria-hidden="true"></i>
                        Relatórios
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('dashboard.desempenho-materia') ? 'active' : '' }}"
                               href="{{ route('dashboard.desempenho-materia') }}"
                               @if(request()->routeIs('dashboard.desempenho-materia')) aria-current="page" @endif>
                                <i class="fas fa-chart-bar me-2 text-primary"></i>
                                Desempenho
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item {{ request()->routeIs('relatorios.curva') ? 'active' : '' }}"
                               href="{{ route('relatorios.curva') }}"
                               @if(request()->routeIs('relatorios.curva')) aria-current="page" @endif>
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                Curva de Aprendizagem
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('caderno-erros.index') ? 'active' : '' }}" href="{{ route('caderno-erros.index') }}" @if(request()->routeIs('caderno-erros.index')) aria-current="page" @endif>
                        <i class="nav-icon fas fa-book-open" aria-hidden="true"></i>
                        Caderno de Erros
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('simulados.*') ? 'active' : '' }}" href="{{ route('simulados.index') }}" @if(request()->routeIs('simulados.*')) aria-current="page" @endif>
                        <i class="nav-icon fas fa-clipboard-check" aria-hidden="true"></i>
                        Simulados
                    </a>
                </li>

                @if($usuarioAtual->is_admin)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning fw-medium {{ request()->routeIs('admin.*') ? 'active-admin' : '' }}"
                           href="#"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <i class="nav-icon fas fa-shield-halved" aria-hidden="true"></i>
                            Admin
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.questoes.*') ? 'active' : '' }}"
                                   href="{{ route('admin.questoes.index') }}"
                                   @if(request()->routeIs('admin.questoes.*')) aria-current="page" @endif>
                                    <i class="fas fa-pen-to-square me-2 text-primary"></i>
                                    Editar questões
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('admin.provas.*') ? 'active' : '' }}"
                                   href="{{ route('admin.provas.cadastrar') }}"
                                   @if(request()->routeIs('admin.provas.*')) aria-current="page" @endif>
                                    <i class="fas fa-file-circle-plus me-2 text-primary"></i>
                                    Cadastrar provas
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>

            {{-- Menu do Usuário --}}
            <div class="user-navigation ms-lg-3">

                <div class="dropdown">

                    <button class="btn text-decoration-none dropdown-toggle-hide user-menu-button"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Abrir menu da conta de {{ $nomeUsuario }}">

                        <span class="user-avatar" aria-hidden="true">{{ $inicialUsuario }}</span>

                        <span class="user-menu-copy">
                            <span class="user-name">{{ $nomeUsuario }}</span>
                            <small class="user-role">{{ $usuarioAtual->is_admin ? 'Administrador' : 'Estudante' }}</small>
                        </span>

                        <i class="fas fa-chevron-down user-menu-chevron" aria-hidden="true"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-lg-end user-dropdown-menu">

                        <li class="user-dropdown-summary">
                            <span class="user-avatar user-avatar--large" aria-hidden="true">{{ $inicialUsuario }}</span>
                            <span class="min-w-0">
                                <strong class="d-block text-truncate">{{ $nomeUsuario }}</strong>
                                <small class="d-block text-body-secondary text-truncate">{{ $usuarioAtual->email }}</small>
                            </span>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                               href="{{ route('profile.edit') }}"
                               @if(request()->routeIs('profile.edit')) aria-current="page" @endif>
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
                                        class="dropdown-item d-flex align-items-center gap-2 w-100 border-0 bg-transparent text-danger">
                                    <i class="fas fa-trash-alt text-danger"></i>
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

<!-- Modal Meta de Aprovação -->
<div class="modal fade"
     id="modalMetaAprovacao"
     tabindex="-1"
     aria-labelledby="modalMetaAprovacaoTitulo"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

        <form action="{{ route('metas-aprovacao.store') }}"
              method="POST"
              class="w-100">
            @csrf

            <div class="modal-content">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold" id="modalMetaAprovacaoTitulo">
                        Definir Meta de Aprovação
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Fechar">
                    </button>
                </div>

                <div class="modal-body pt-2">

                    <p class="text-body-secondary small mb-3">
                        Selecione o cargo desejado. O sistema criará
                        automaticamente um filtro e acompanhará seu
                        progresso até a aprovação.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-uppercase text-body-secondary"
                               for="metaCargo">
                            Cargo
                        </label>

                        <select class="form-select @error('cargo_id') is-invalid @enderror"
                                id="metaCargo"
                                name="cargo_id"
                                required>

                            <option value="" disabled @selected(!old('cargo_id'))>
                                Selecione um cargo
                            </option>

                            @foreach (\App\Models\Cargo::orderBy('nome')->get() as $cargo)
                                <option value="{{ $cargo->id }}" @selected((string) old('cargo_id') === (string) $cargo->id)>
                                    {{ $cargo->nome }}
                                </option>
                            @endforeach

                        </select>

                        @error('cargo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">

                        <div class="form-label small fw-semibold text-uppercase text-body-secondary d-flex justify-content-between align-items-center gap-2">
                            <span>Anos de referência</span>

                            <button type="button"
                                    class="btn btn-link btn-sm p-0 text-decoration-none"
                                    id="toggleAnos">
                                Marcar/Desmarcar
                            </button>
                        </div>

                        <div class="year-options border rounded p-2"
                             aria-describedby="metaAnosAjuda">

                            @forelse (\App\Models\Ano::orderBy('ano', 'desc')->get() as $ano)

                                <div class="form-check py-1">

                                    <input class="form-check-input ano-checkbox"
                                           type="checkbox"
                                           name="anos[]"
                                           value="{{ $ano->id }}"
                                           id="ano-{{ $ano->id }}"
                                           @checked(in_array((string) $ano->id, array_map('strval', (array) old('anos', [])), true))>

                                    <label class="form-check-label small"
                                           for="ano-{{ $ano->id }}">
                                        {{ $ano->ano }}
                                    </label>

                                </div>

                            @empty

                                <p class="text-body-secondary small mb-0">
                                    Nenhum ano cadastrado.
                                </p>

                            @endforelse

                        </div>

                        <small class="text-body-secondary d-block mt-1" id="metaAnosAjuda">
                            Se nenhum for marcado, considera todos os anos.
                        </small>

                        @error('anos')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        @error('anos.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">

                    <button type="button"
                            class="btn btn-outline-secondary"
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
