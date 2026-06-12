<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HistoricoResposta;
use App\Models\Materia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $acertos = HistoricoResposta::where(
            'acertou',
            true
        )->count();

        $erros = HistoricoResposta::where(
            'acertou',
            false
        )->count();

        $materias = Materia::withCount([
            'questoes as total_questoes'
        ])->get();

        return view(
            'dashboard.index',
            compact(
                'acertos',
                'erros',
                'materias'
            )
        );
    }

    public function resetar()
    {
        HistoricoResposta::truncate();

        return redirect()
            ->back()
            ->with(
                'success',
                'Estatísticas zeradas.'
            );
    }
}
