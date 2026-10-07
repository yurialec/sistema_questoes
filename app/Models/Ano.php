<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ano extends Model
{
    protected $fillable = [
        'ano',
    ];

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}