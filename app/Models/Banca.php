<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banca extends Model
{
    protected $fillable = [
        'nome',
    ];

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}