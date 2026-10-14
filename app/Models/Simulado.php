<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulado extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'filtros',
        'status',
        'total_questoes',
        'total_acertos',
        'total_erros',
        'tempo_limite_segundos',
        'iniciado_em',
        'finalizado_em',
    ];

    protected $casts = [
        'filtros' => 'array',
        'iniciado_em' => 'datetime',
        'finalizado_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questoes()
    {
        return $this->hasMany(SimuladoQuestao::class);
    }

    public function getPercentualAcertoAttribute(): ?float
    {
        if (!$this->total_questoes) {
            return null;
        }

        return round(($this->total_acertos ?? 0) / $this->total_questoes * 100, 1);
    }

    public function getTempoTotalSegundosAttribute(): ?int
    {
        if (!$this->iniciado_em) {
            return null;
        }

        $fim = $this->finalizado_em ?? now();

        return $this->iniciado_em->diffInSeconds($fim);
    }
}
