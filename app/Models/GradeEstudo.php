<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeEstudo extends Model
{
    protected $fillable = [
        'user_id',
        'materia_id',
        'dia_semana',
        'ordem'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}
