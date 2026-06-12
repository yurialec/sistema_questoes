<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ano extends Model
{
    protected $fillable = [
        'ano'
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }
}
