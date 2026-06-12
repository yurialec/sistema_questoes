<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orgao extends Model
{
    protected $fillable = [
        'nome'
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }
}
