<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habilidad extends Model
{
    //
    protected $primaryKey = 'IDHabilidad';
    public $timestamps = false;
    protected $fillable = ['Tipo', 'Habilidad'];

    public function desempleados() { return $this->belongsToMany(Desempleado::class, 'desempleado_habilidades', 'IDHabilidad', 'IDDesempleado'); }
}
