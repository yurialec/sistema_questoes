<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'orgao_id',
        'banca_id',
        'ano_id',
        'nome'
    ];

    public function orgao()
    {
        return $this->belongsTo(Orgao::class);
    }

    public function banca()
    {
        return $this->belongsTo(Banca::class);
    }

    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}
