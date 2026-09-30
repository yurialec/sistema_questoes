@extends('layouts.app')
@section('title', 'Meu Perfil')


@section('content')
<div class="container">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar"
            ></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Não foi possível salvar.</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Fechar"
            ></button>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name ?? '') }}"
                required
            >

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email ?? '') }}"
                required
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                minlength="6"
            >

            <small class="text-muted">
                Deixe em branco para manter a senha atual.
            </small>

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input
                type="checkbox"
                name="is_admin"
                id="is_admin"
                value="1"
                class="form-check-input"
                {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}
            >

            <label for="is_admin" class="form-check-label">
                Administrador
            </label>
        </div>

        <button type="submit" class="btn btn-primary">
            Salvar
        </button>
    </form>
</div>
@endsection
