<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoResposta extends Model
{
    protected $table = 'historico_respostas';

    protected $fillable = [
        'questao_id',
        'alternativa_id',
        'acertou',
        'respondido_em',
        'user_id',
    ];

    protected $casts = [
        'acertou' => 'boolean',
        'respondido_em' => 'datetime'
    ];

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }

    public function alternativa()
    {
        return $this->belongsTo(Alternativa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
