<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeEstudo extends Model
{
    protected $table = 'grade_estudos';

    protected $fillable = [
        'user_id',
        'grupo_id',
        'dia_semana',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grupo()
    {
        return $this->belongsTo(GrupoEstudo::class);
    }
}