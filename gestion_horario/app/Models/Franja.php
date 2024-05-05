<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franja extends Model
{
    use HasFactory;
    protected $guarded = [];
    //protected $fillable = [ 'franja_cod','descripcion','horadesde','horahasta'];
    public function horarios():HasMany{
        return $this->hasMany(Horario::class);
    }
}