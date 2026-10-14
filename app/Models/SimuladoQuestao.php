<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimuladoQuestao extends Model
{
    protected $table = 'simulado_questoes';

    protected $fillable = [
        'simulado_id',
        'questao_id',
        'materia_id',
        'ordem',
        'alternativa_id',
        'correta',
        'respondida_em',
    ];

    protected $casts = [
        'correta' => 'boolean',
        'respondida_em' => 'datetime',
    ];

    public function simulado()
    {
        return $this->belongsTo(Simulado::class);
    }

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function alternativa()
    {
        return $this->belongsTo(Alternativa::class);
    }
}