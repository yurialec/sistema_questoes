<?php

use App\Http\Controllers\AdminQuestaoController;
use App\Http\Controllers\AlternativaController;
use App\Http\Controllers\AnoController;
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\BancaController;
use App\Http\Controllers\CadernoErrosController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ConcursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeEstudoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MetaAprovacaoController;
use App\Http\Controllers\OrgaoController;
use App\Http\Controllers\ReaplicacaoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

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
    Route::post('/metas-aprovacao', [MetaAprovacaoController::class, 'store'])->name('metas-aprovacao.store');
    Route::get('/metas-aprovacao', [MetaAprovacaoController::class, 'show'])->name('metas-aprovacao.show');
    Route::delete('/metas-aprovacao', [MetaAprovacaoController::class, 'destroy'])->name('metas-aprovacao.destroy');

    Route::get('/caderno-erros', [CadernoErrosController::class, 'index'])->name('caderno-erros.index');
    Route::post('/caderno-erros/{erro}/salvar-motivo', [ConcursoController::class, 'salvarMotivoErro'])->name('caderno-erros.salvar-motivo');
    Route::patch('/caderno-erros/{erro}/resolver', [CadernoErrosController::class, 'updateComoResolver'])->name('caderno-erros.update-resolver');

    Route::get('/reaplicacao', [ReaplicacaoController::class, 'index'])->name('reaplicacao.index');
    Route::get('/reaplicacao/iniciar', [ReaplicacaoController::class, 'iniciar'])->name('reaplicacao.iniciar');
    Route::post('/reaplicacao/{erro}/verificar', [ReaplicacaoController::class, 'verificar'])->name('reaplicacao.verificar');

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/questoes', [AdminQuestaoController::class, 'index'])->name('admin.questoes.index');
        Route::get('/questoes/{id}/edit', [AdminQuestaoController::class, 'edit'])->name('admin.questoes.edit');
        Route::put('/questoes/{id}', [AdminQuestaoController::class, 'update'])->name('admin.questoes.update');
        Route::post('/admin/upload-imagem', [AdminQuestaoController::class, 'uploadImagem'])->name('admin.upload-imagem');
    });

    Route::post('/filtros/salvar', [ConcursoController::class, 'salvarFiltro'])->name('filtros.salvar');
    Route::delete('/filtros/{id}', [ConcursoController::class, 'excluirFiltro'])->name('filtros.excluir');

    Route::get('/grade', [GradeEstudoController::class, 'index'])->name('grade.index');
    
    Route::post('/grade/grupos', [GradeEstudoController::class, 'storeGrupo'])->name('grade.grupos.store');
    Route::post('/grade/grupos/{grupo}/materias', [GradeEstudoController::class, 'addMateria'])->name('grade.materias.add');
    Route::delete('/grade/grupos/{grupo}/materias/{materia}', [GradeEstudoController::class, 'removeMateria'])->name('grade.materias.remove');
    
    Route::post('/grade/dias', [GradeEstudoController::class, 'storeDia'])->name('grade.dias.store');
    Route::delete('/grade/dias/{id}', [GradeEstudoController::class, 'destroyDia'])->name('grade.dias.destroy');

    Route::delete('/grade/grupos/{grupo}', [GradeEstudoController::class, 'destroyGrupo'])->name('grade.grupos.destroy');

    Route::get('/relatorios/curva-aprendizagem', [RelatorioController::class, 'curvaAprendizagem'])->name('relatorios.curva');
});

require __DIR__ . '/auth.php';
