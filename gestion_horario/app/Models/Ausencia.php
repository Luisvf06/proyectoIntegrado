<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;
    protected $table = 'horario';
    protected $fillable = ['ausencia_cod','descripcion'];

    public function horarios(){
        return $this->belongsTo(Horario::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }
}
