<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assunto extends Model
{
    protected $fillable = [
        'materia_id',
        'nome'
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }

    public function grupoAssunto()
    {
        return $this->belongsTo(GrupoAssunto::class, 'grupo_assunto_id');
    }
}
