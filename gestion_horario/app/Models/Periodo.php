<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;
    protected $dateFormat = 'Y-m-d';
    protected $guarded = [];
    //protected $fillable = [ 'periodo_cod','descripcion','desdefecha','hastafecha'];
    public function horarios():BelongsTo{
        return $this->belongsTo(Horario::class);
    }
}