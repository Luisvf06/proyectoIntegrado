<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Horario extends Model
{   
    
    use HasFactory;
    protected $guarded = [];
    //protected $fillable = [ 'horario_cod','dia',];
    public function ausencias(): HasMany{
        return $this->hasMany(Ausencia::class);
    }

    public function franjas(): BelongsTo{
        return $this->belongsTo(Franja::class);
    }

    public function asignaturas(): BelongsTo{
        return $this->belongsTo(Asignatura::class);
    }

    public function grupos(): BelongsTo{
        return $this->belongsTo(Grupo::class);
    }

    public function users(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function aulas():BelongsTo{
        return $this->belongsTo(Aula::class);
    }
    public function periodos():BelongsTo{
        return $this->belongsTo(Periodo::class);
    }
    

}