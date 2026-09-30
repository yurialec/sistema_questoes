<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEstudo extends Model
{
    protected $table = 'grupos_estudo';

    protected $fillable = [
        'user_id',
        'nome',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação N:N com Matérias
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupo_materia', 'grupo_id', 'materia_id')->withTimestamps();
    }

    // Relação com a Grade (dias da semana)
    public function grade()
    {
        return $this->hasMany(GradeEstudo::class);
    }
}
