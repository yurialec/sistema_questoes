<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alternativa extends Model
{
    protected $fillable = [
        'questao_id',
        'letra',
        'descricao',
        'correta'
    ];

    protected $casts = [
        'correta' => 'boolean',
        'imagens' => 'array'
    ];

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }
}
