<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Asignatura extends Model
{
    use HasFactory;
/*
protected $fillable -> indican los campos del objetos que serán cumplimentables 
protected $guard -> los complementarios a fillable, son los que no son cumplimentables por el ususario, no hace falta indicarlo si se indican los fillable
protected $casts -> Castea datos que se reciban, por ejemplo que descripcion sea un string
protected $hidden -> Oculta datos cuando serializamos 
*/
    //protected $fillable = [ 'asignatura_cod','descripcion'];
    protected $guarded = [];#esto es lo contrario que fillable, es decir, no hay ninguno guardado por lo que se pueden rellenar todos
    public function horarios(): HasMany{
        return $this->hasMany(Horario::class);
    }
}