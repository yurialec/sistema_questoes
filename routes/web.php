<?php

use App\Http\Controllers\AlternativaController;
use App\Http\Controllers\AnoController;
use App\Http\Controllers\AssuntoController;
use App\Http\Controllers\BancaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ConcursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\OrgaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('orgaos', OrgaoController::class);
Route::resource('bancas', BancaController::class);
Route::resource('anos', AnoController::class);
Route::resource('cargos', CargoController::class);
Route::resource('materias', MateriaController::class);
Route::resource('assuntos', AssuntoController::class);
Route::resource('questoes', QuestaoController::class);
Route::resource('alternativas', AlternativaController::class);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/resetar', [DashboardController::class, 'resetar']);
Route::get('/questao/{id}/responder', [ConcursoController::class, 'responder']);
Route::post('/questao/verificar', [ConcursoController::class, 'verificar']);

require __DIR__ . '/auth.php';
