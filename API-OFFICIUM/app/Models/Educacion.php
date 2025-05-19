<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Educacion extends Model
{
    //
    protected $primaryKey = 'IDEducacion';
    public $timestamps = true;
    protected $fillable = ['IDDesempleado', 'Institucion', 'Titulo', 'Finalizacion'];
    protected $hidden = ['created_at','updated_at'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
}
