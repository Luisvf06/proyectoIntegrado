<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ausencia extends Model
{
    use HasFactory;
    protected $fillable = ['descripcion'];

    public function horarios(): BelongsTo{
        return $this->belongsTo(Horario::class);
    }

    public function users():BelongsTo{
        return $this->belongsTo(User::class);
    }

    
}