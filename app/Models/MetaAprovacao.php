<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaAprovacao extends Model
{
    protected $table = 'metas_aprovacao';

    protected $fillable = [
        'user_id',
        'cargo_id',
        'filtro_salvo_id',
        'porcentagem',
        'rank',
    ];

    protected $casts = [
        'porcentagem' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class); 
    }

    public function filtroSalvo()
    {
        return $this->belongsTo(FiltroSalvo::class, 'filtro_salvo_id');
    }
}