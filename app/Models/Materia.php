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

    // Adicione este método na classe Materia
    public function grupos()
    {
        return $this->belongsToMany(GrupoEstudo::class, 'grupo_materia', 'materia_id', 'grupo_id')->withTimestamps();
    }
}
