<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdiomaNivel extends Model
{
    //
    protected $primaryKey = 'IDIdiomaNivel';
    public $timestamps = false;
    protected $fillable = ['IDIdioma', 'Nivel'];

    public function idioma() { return $this->belongsTo(Idioma::class, 'IDIdioma'); }
    public function desempleados() { return $this->belongsToMany(Desempleado::class, 'desempleado_idiomas', 'IDIdiomaNivel', 'IDDesempleado'); }
}
