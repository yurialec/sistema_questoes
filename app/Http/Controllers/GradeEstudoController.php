<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoEstudo;
use App\Models\Materia;
use App\Models\GradeEstudo;
use Illuminate\Support\Facades\Auth;

class GradeEstudoController extends Controller
{
    /**
     * Exibe a tela de gerenciamento da grade.
     * Carrega os grupos do usuário com suas respectivas matérias 
     * e a grade atual (dias da semana preenchidos).
     */
    public function index()
    {
        $user = Auth::user();

        // Busca todos os grupos criados pelo usuário e suas matérias
        $grupos = GrupoEstudo::where('user_id', $user->id)
            ->with('materias')
            ->orderBy('nome')
            ->get();

        // Busca a grade atual do usuário (quais grupos estão em quais dias)
        $grade = GradeEstudo::where('user_id', $user->id)
            ->with('grupo')
            ->orderBy('dia_semana')
            ->get()
            ->keyBy('dia_semana'); // Organiza por dia (1 a 7) para facilitar na View

        // Busca todas as matérias disponíveis para o usuário poder adicionar aos grupos
        $todasMaterias = Materia::orderBy('nome')->get();

        return view('grade.index', compact('grupos', 'grade', 'todasMaterias'));
    }

    /**
     * Cria um novo grupo de estudo.
     */
    public function storeGrupo(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        GrupoEstudo::create([
            'user_id' => Auth::id(),
            'nome' => $request->nome,
        ]);

        return redirect()->back()->with('success', 'Grupo criado com sucesso!');
    }

    /**
     * Adiciona uma matéria a um grupo específico.
     */
    public function addMateria(Request $request, GrupoEstudo $grupo)
    {
        // Segurança: Garante que o grupo pertence ao usuário logado
        if ($grupo->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        // Adiciona a matéria ao grupo (evita duplicatas automaticamente)
        $grupo->materias()->attach($request->materia_id);

        return redirect()->back()->with('success', 'Matéria adicionada ao grupo!');
    }

    /**
     * Remove uma matéria de um grupo específico.
     */
    public function removeMateria(GrupoEstudo $grupo, Materia $materia)
    {
        // Segurança: Garante que o grupo pertence ao usuário logado
        if ($grupo->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $grupo->materias()->detach($materia->id);

        return redirect()->back()->with('success', 'Matéria removida do grupo!');
    }

    /**
     * Associa um grupo a um dia da semana (1 = Segunda, 7 = Domingo).
     * Se já houver um grupo neste dia, ele será substituído.
     */
    public function storeDia(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos_estudo,id',
            'dia_semana' => 'required|integer|min:1|max:7',
        ]);

        $grupo = GrupoEstudo::findOrFail($request->grupo_id);

        // Segurança: Garante que o grupo pertence ao usuário logado
        if ($grupo->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Remove qualquer grupo que já esteja atribuído a este dia da semana
        GradeEstudo::where('user_id', Auth::id())
            ->where('dia_semana', $request->dia_semana)
            ->delete();

        // Cria a nova atribuição
        GradeEstudo::create([
            'user_id' => Auth::id(),
            'grupo_id' => $request->grupo_id,
            'dia_semana' => $request->dia_semana,
        ]);

        return redirect()->back()->with('success', 'Dia da semana atualizado com sucesso!');
    }

    /**
     * Remove a atribuição de um grupo em um dia específico.
     */
    public function destroyDia($id)
    {
        $grade = GradeEstudo::where('user_id', Auth::id())->findOrFail($id);
        $grade->delete();

        return redirect()->back()->with('success', 'Dia liberado na grade!');
    }

    public function destroyGrupo(GrupoEstudo $grupo)
    {
        // Segurança: Garante que o grupo pertence ao usuário logado
        if ($grupo->user_id !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // O cascadeOnDelete da migration apagará automaticamente 
        // as matérias (grupo_materia) e os dias (grade_estudos) vinculados.
        $grupo->delete();

        return redirect()->back()->with('success', 'Grupo e seus vínculos excluídos com sucesso!');
    }
}
