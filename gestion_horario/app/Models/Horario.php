<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{   
    
    use HasFactory;
    protected $fillable = [ 'horario_cod','dia',];
    public function ausencias(){
        return $this->hasMany(Ausencia::class);
    }

    public function franjas(){
        return $this->belongsTo(Franja::class);
    }

    public function asignaturas(){
        return $this->belongsTo(Asignatura::class);
    }

    public function grupos(){
        return $this->belongsTo(Grupo::class);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function aulas(){
        return $this->belongsTo(Aula::class);
    }
    public function periodos(){
        return $this->belongsTo(Periodo::class);
    }
    

}