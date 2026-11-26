<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DominioBanca extends Model
{
    protected $fillable = [
        'user_id',
        'banca_id',
        'qtd_erros',
        'qtd_acertos',
        'total_questoes_banca',
    ];

    protected $casts = [
        'qtd_erros' => 'integer',
        'qtd_acertos' => 'integer',
        'total_questoes_banca' => 'integer',
    ];

    public function banca()
    {
        return $this->belongsTo(Banca::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function porcentagemDominio(): int
    {
        if ($this->total_questoes_banca < 1) {
            return 0;
        }

        $saldo = $this->qtd_acertos - $this->qtd_erros;

        return max(0, min(100, (int) round($saldo / $this->total_questoes_banca * 100)));
    }
}
