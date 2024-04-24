<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = [ 'aula_cod','descripcion'];
    public function horarios(){
        return $this->hasMany(Horario::class);
    }

}