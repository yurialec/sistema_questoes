<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrupoAssunto extends Model
{
    use HasFactory;

    protected $table = 'grupos_assunto';

    protected $fillable = [
        'nome',
    ];

    public function assuntos(): HasMany
    {
        return $this->hasMany(Assunto::class, 'grupo_assunto_id');
    }
}