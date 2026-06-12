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
}