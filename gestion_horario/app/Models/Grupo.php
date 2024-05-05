<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;
    protected $guarded = [];
    //protected $fillable = [ 'grupo_cod','descripcion'];
    public function horarios():HasMany{
        return $this->hasMany(Horario::class);
    }
}