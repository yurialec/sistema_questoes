<nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-3 shadow-sm px-3 py-2 mt-3 mb-4">

    <div class="container-fluid p-0">

        {{-- Logo / Home --}}
        <a href="{{ route('dashboard') }}"
            class="navbar-brand d-flex align-items-center gap-2 fw-semibold">
            <i class="fas fa-house"></i>
            <span>Dashboard</span>
        </a>

        {{-- Botão mobile --}}
        <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <div class="d-flex align-items-center gap-2 flex-wrap ms-lg-4 mt-3 mt-lg-0">

                {{-- Desempenho --}}
                <a href="{{ route('dashboard.desempenho-materia') }}"
                    class="btn btn-dark border border-secondary btn-sm px-3 d-flex align-items-center gap-2">
                    <i class="fas fa-chart-bar text-info"></i>
                    <span>Desempenho</span>
                </a>

                {{-- Caderno de erros --}}
                <a href="{{ route('caderno-erros.index') }}"
                    class="btn btn-dark border border-secondary btn-sm px-3 d-flex align-items-center gap-2">
                    <i class="fas fa-book-open text-warning"></i>
                    <span>Caderno de Erros</span>
                </a>

                {{-- Reaplicação --}}
                <a href="{{ route('reaplicacao.index') }}"
                    class="btn btn-dark border border-secondary btn-sm px-3 d-flex align-items-center gap-2">
                    <i class="fas fa-arrows-rotate text-success"></i>
                    <span>Reaplicação</span>
                </a>

                {{-- Separador --}}
                <div class="vr d-none d-lg-block mx-1 text-secondary"></div>

                {{-- Ação principal --}}
                <a href="{{ route('responder') }}"
                    class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-2 fw-semibold shadow-sm">
                    <i class="fas fa-play"></i>
                    <span>Responder questões</span>
                </a>

            </div>

            {{-- Ações secundárias --}}
            <div class="d-flex align-items-center gap-2 ms-lg-auto mt-3 mt-lg-0">

                {{-- Resetar --}}
                <form action="{{ url('/dashboard/resetar') }}"
                    method="POST"
                    class="m-0"
                    onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
                    @csrf

                    <button type="submit"
                        class="btn btn-dark border border-secondary btn-sm px-3 d-flex align-items-center gap-2"
                        title="Zerar estatísticas">
                        <i class="fas fa-rotate-left text-danger"></i>
                        <span>Resetar</span>
                    </button>
                </form>

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf

                    <button type="submit"
                        class="btn btn-outline-light btn-sm px-3 d-flex align-items-center gap-2">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                        <span>Sair</span>
                    </button>
                </form>

            </div>

        </div>
    </div>
</nav>