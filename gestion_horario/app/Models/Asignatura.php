<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asignatura extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'asignatura_horario', 'asignatura_id', 'horario_id');
    }
}

