<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextoComplementar extends Model
{
    protected $table = 'textos_complementares';

    protected $fillable = [
        'conteudo'
    ];
}
