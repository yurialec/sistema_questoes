<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container-fluid">

        {{-- Logo / Home (Limpo e direto) --}}
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

            {{-- Navegação Principal (Texto limpo, sem ícones desnecessários) --}}
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
                    <a class="nav-link" href="{{ route('reaplicacao.index') }}">Reaplicação</a>
                </li>
                
                {{-- Grade de Estudos (Mantido pronto para quando ativar, de forma limpa) --}}
                {{-- <li class="nav-item d-none">
                    <a class="nav-link" href="{{ route('grade.index') }}">Grade de Estudos</a>
                </li> --}}

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

                {{-- Ação Principal (Único destaque real na navbar) --}}
                <a href="{{ route('responder') }}" class="btn btn-primary btn-sm fw-semibold px-3 shadow-sm">
                    <i class="fas fa-play me-1"></i> Responder
                </a>

                {{-- Separador Vertical (Apenas em telas grandes) --}}
                <div class="vr mx-2 d-none d-lg-block"></div>

                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm border-0">
                    <i class="fas fa-user me-1"></i> Perfil
                </a>

                {{-- Resetar (Ação discreta para evitar cliques acidentais e poluição visual) --}}
                <form action="{{ url('/dashboard/resetar') }}" method="POST" class="m-0" onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-secondary p-1" title="Zerar estatísticas">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

                {{-- Logout --}}
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