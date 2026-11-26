@extends('layouts.app')

@section('title', 'Cadastrar prova')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h4 fw-bold mb-0">Cadastrar prova</h1>
        <a href="{{ route('admin.provas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar para provas
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.provas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="arquivo" class="form-label">Arquivo JSON</label>
                    <input
                        type="file"
                        id="arquivo"
                        name="arquivo"
                        class="form-control"
                        accept=".json,application/json"
                        required
                    >
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i>
                    Importar prova
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
