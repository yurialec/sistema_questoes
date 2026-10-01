@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="container py-4">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <h2 class="h4 fw-bold text-body mb-4">Configurações da Conta</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="fw-semibold mb-1"><i class="fas fa-exclamation-circle me-2"></i>Não foi possível salvar as alterações:</div>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold text-body-secondary small">Nome completo</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name ?? '') }}"
                                required
                                placeholder="Seu nome"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-body-secondary small">Endereço de E-mail</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email ?? '') }}"
                                required
                                placeholder="seu@email.com"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold text-body-secondary small">Nova Senha</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="6"
                                placeholder="Deixe em branco para manter a atual"
                            >
                            <div class="form-text text-body-secondary small mt-1">
                                <i class="fas fa-info-circle me-1"></i> A senha deve ter no mínimo 6 caracteres. Deixe em branco para não alterar.
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="border-secondary-subtle my-4">

                        {{-- Configuração de Administrador (Destacada) --}}
                        <div class="mb-4 p-3 bg-body-tertiary rounded-3 border border-secondary-subtle">
                            <div class="form-check form-switch mb-1">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_admin"
                                    id="is_admin"
                                    value="1"
                                    {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-semibold text-body" for="is_admin">
                                    Conta de Administrador
                                </label>
                            </div>
                            <div class="small text-body-secondary ms-4">
                                Concede acesso total às áreas de gerenciamento de questões, filtros e relatórios do sistema.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2">
                            <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection