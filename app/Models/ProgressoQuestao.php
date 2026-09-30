<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressoQuestao extends Model
{
    protected $table = 'progresso_questoes';

    protected $fillable = [
        'user_id',
        'questao_id',
        'caixa_leitner',
        'proxima_revisao',
        'ultima_resposta',
    ];

    protected $casts = [
        'caixa_leitner' => 'integer',
        'proxima_revisao' => 'datetime',
        'ultima_resposta' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questao()
    {
        return $this->belongsTo(Questao::class)->with(['materia', 'assunto']);
    }
}
