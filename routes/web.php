<?php

use App\Http\Controllers\AlternativaController;
use App\Http\Controllers\AnoController;
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\BancaController;
use App\Http\Controllers\CadernoErrosController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ConcursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\OrgaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReaplicacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/responder', [ConcursoController::class, 'responder'])->name('responder');

    Route::resource('orgaos', OrgaoController::class);
    Route::resource('bancas', BancaController::class);
    Route::resource('anos', AnoController::class);
    Route::resource('cargos', CargoController::class);
    Route::resource('materias', MateriaController::class);
    Route::resource('assuntos', AssuntoController::class);
    Route::resource('alternativas', AlternativaController::class);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/desempenho-materia', [DashboardController::class, 'desempenhoPorMateria'])->name('dashboard.desempenho-materia');
    Route::post('/dashboard/resetar', [DashboardController::class, 'resetar']);
    // Route::get('/questao/{id}/responder', [ConcursoController::class, 'responder']);
    Route::post('/questao/verificar', [ConcursoController::class, 'verificar'])->name('questao.verificar');

    Route::get('/caderno-erros', [CadernoErrosController::class, 'index'])->name('caderno-erros.index');
    Route::post('/caderno-erros/{erro}/salvar-motivo', [ConcursoController::class, 'salvarMotivoErro'])->name('caderno-erros.salvar-motivo');
    Route::patch('/caderno-erros/{erro}/resolver', [CadernoErrosController::class, 'updateComoResolver'])->name('caderno-erros.update-resolver');

    Route::get('/reaplicacao', [ReaplicacaoController::class, 'index'])->name('reaplicacao.index');
    Route::get('/reaplicacao/iniciar', [ReaplicacaoController::class, 'iniciar'])->name('reaplicacao.iniciar');
    Route::post('/reaplicacao/{erro}/verificar', [ReaplicacaoController::class, 'verificar'])->name('reaplicacao.verificar');
});

require __DIR__ . '/auth.php';
