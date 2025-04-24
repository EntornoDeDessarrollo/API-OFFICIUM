<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experiencia extends Model
{
    //
    protected $primaryKey = 'IDExperiencia';
    public $timestamps = false;
    protected $fillable = ['IDDesempleado', 'Empresa', 'Puesto', 'Duracion'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
}
