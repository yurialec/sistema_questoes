<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questao extends Model
{
    protected $table = 'questoes';

    protected $fillable = [
        'cargo_id',
        'materia_id',
        'assunto_id',
        'texto_complementar_id',
        'enunciado'
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function assunto()
    {
        return $this->belongsTo(Assunto::class);
    }

    public function textoComplementar()
    {
        return $this->belongsTo(TextoComplementar::class);
    }

    public function alternativas()
    {
        return $this->hasMany(Alternativa::class);
    }

    public function respostas()
    {
        return $this->hasMany(HistoricoResposta::class);
    }
}