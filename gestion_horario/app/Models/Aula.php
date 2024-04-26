<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Aula extends Model
{
    use HasFactory;

    protected $fillable = [ 'aula_cod','descripcion'];
    public function horarios():HasMany{
        return $this->hasMany(Horario::class);
    }

}