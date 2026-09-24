<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CadernoErro extends Model
{
    protected $fillable = [
        'user_id',
        'questao_id',
        'alternativa_id',
        'foi_chute',
        'erro_distraido',
        'motivo_erro',
        'como_resolver',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function questao()
    {
        return $this->belongsTo(Questao::class)->with(['materia', 'textoComplementar']);
    }
    public function alternativa()
    {
        return $this->belongsTo(Alternativa::class);
    }
}
