<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiltroSalvo extends Model
{
    protected $table = 'filtros_salvos';

    protected $fillable = [
        'user_id',
        'nome',
        'filtros',
        'is_padrao',
    ];

    protected $casts = [
        'filtros' => 'array',
        'is_padrao' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metaAprovacao()
    {
        return $this->hasOne(MetaAprovacao::class, 'filtro_salvo_id');
    }
}