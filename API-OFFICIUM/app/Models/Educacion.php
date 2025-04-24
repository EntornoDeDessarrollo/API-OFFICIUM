<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Educacion extends Model
{
    //
    protected $primaryKey = 'IDEducacion';
    public $timestamps = false;
    protected $fillable = ['IDDesempleado', 'Institucion', 'Titulo', 'Finalizacion'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
}
