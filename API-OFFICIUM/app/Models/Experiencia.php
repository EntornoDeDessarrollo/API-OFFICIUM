<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experiencia extends Model
{
    //
    protected $primaryKey = 'IDExperiencia';
    public $timestamps = true;
    protected $fillable = ['IDDesempleado', 'Empresa', 'Puesto', 'Duracion'];
    protected $hidden = ['created_at','updated_at'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
}
