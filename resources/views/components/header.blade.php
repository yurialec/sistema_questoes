<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container-fluid">

        {{-- Logo / Home --}}
        <a href="{{ route('dashboard') }}" class="navbar-brand fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-layer-group text-primary"></i>
            <span>Questões</span>
        </a>

        {{-- Botão mobile --}}
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
                    <a class="nav-link" href="{{ route('dashboard.desempenho-materia') }}">Desempenho</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('relatorios.curva') }}">Curva de Aprendizagem</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('caderno-erros.index') }}">Caderno de Erros</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalMetaAprovacao">
                        <i class="fa-solid fa-bullseye fa-xs"></i>
                        <span>Meta desejada</span>
                    </a>
                </li>
                
                @if(auth()->user()->is_admin)
                    <li class="nav-item">
                        <a class="nav-link text-warning fw-medium" href="{{ route('admin.questoes.index') }}">
                            <i class="fas fa-cog me-1"></i> Admin
                        </a>
                    </li>
                @endif
            </ul>

            {{-- Ações e Utilitários --}}
            <div class="d-flex align-items-center gap-2 flex-wrap mt-3 mt-lg-0">

                <a href="{{ route('responder') }}" class="btn btn-primary btn-sm fw-semibold px-3 shadow-sm">
                    <i class="fas fa-play me-1"></i> Responder
                </a>

                <div class="vr mx-2 d-none d-lg-block"></div>

                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm border-0">
                    <i class="fas fa-user me-1"></i> Perfil
                </a>

                <form action="{{ url('/dashboard/resetar') }}" method="POST" class="m-0" onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-secondary p-1" title="Zerar estatísticas">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-secondary p-1" title="Sair do sistema">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</nav>

<!-- Modal Meta de Aprovação -->
<div class="modal fade" id="modalMetaAprovacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        {{-- Formulário padrão do Laravel envolvendo o conteúdo do modal --}}
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
                                <option value="{{ $cargo->id }}">
                                    {{ $cargo->nome }}
                                </option>
                            @endforeach
                        </select>
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