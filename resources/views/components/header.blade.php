<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3 mb-4">
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm px-3 d-flex align-items-center gap-2">
            <i class="fas fa-house"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
        <a href="{{ route('dashboard.desempenho-materia') }}"
            class="btn btn-outline-primary btn-sm px-3 d-flex align-items-center gap-2">
            <i class="fas fa-chart-bar"></i>
            <span>Desempenho por Matéria</span>
        </a>

        <a href="{{ route('caderno-erros.index') }}"
            class="btn btn-outline-warning btn-sm px-3 d-flex align-items-center gap-2">
            <i class="fas fa-book-open"></i>
            <span>Caderno de Erros</span>
        </a>

        <a href="{{ route('reaplicacao.index') }}"
            class="btn btn-outline-success btn-sm px-3 d-flex align-items-center gap-2">
            <i class="fas fa-arrows-rotate"></i>
            <span>Reaplicação</span>
        </a>

        <a href="{{ route('responder') }}"
            class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-2">
            <i class="fas fa-play"></i>
            <span>Responder questões</span>
        </a>

        <form action="{{ url('/dashboard/resetar') }}" method="POST" class="m-0"
            onsubmit="return confirm('Tem certeza que deseja zerar todas as estatísticas? Esta ação não pode ser desfeita.');">
            @csrf

            <button type="submit"
                class="btn btn-outline-danger btn-sm px-3 d-flex align-items-center gap-2">
                <i class="fas fa-rotate-left"></i>
                <span>Resetar</span>
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf

            <button type="submit"
                class="btn btn-light border btn-sm px-3 d-flex align-items-center gap-2 text-secondary">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Sair</span>
            </button>
        </form>
    </div>
</div>