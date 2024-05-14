<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Asignatura extends Model
{
    use HasFactory;

    //protected $fillable = [ 'asignatura_cod','descripcion'];
    protected $guarded = [];#esto es lo contrario que fillable, es decir, no hay ninguno guardado por lo que se pueden rellenar todos
    public function horarios(): HasMany{
        return $this->hasMany(Horario::class);
    }
}