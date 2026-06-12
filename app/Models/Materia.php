<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = [
        'nome',
        'tipo'
    ];

    public function assuntos()
    {
        return $this->hasMany(Assunto::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}