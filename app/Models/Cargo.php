<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'orgao_id',
        'nome',
    ];

    public function orgao()
    {
        return $this->belongsTo(Orgao::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}