<?php

namespace App\Http\Controllers;

use App\Services\MetaAprovacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaAprovacaoController extends Controller
{
    protected $metaAprovacaoService;

    public function __construct(MetaAprovacaoService $metaAprovacaoService)
    {
        $this->metaAprovacaoService = $metaAprovacaoService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'cargo_id' => 'required|exists:cargos,id',
        ]);

        try {
            $this->metaAprovacaoService->criarMeta(Auth::id(), $request->cargo_id);

            return redirect()->back()->with('success', 'Meta de aprovação criada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy()
    {
        try {
            $this->metaAprovacaoService->excluirMeta(Auth::id());

            return redirect()->back()->with('success', 'Meta excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
