<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdiomaNivel extends Model
{
    //
    protected $primaryKey = 'IDIdiomaNivel';
    public $timestamps = true;
    protected $fillable = ['IDIdioma', 'Nivel'];
    protected $hidden = ['created_at','updated_at'];

    public function idioma() { return $this->belongsTo(Idioma::class, 'IDIdioma'); }
    public function desempleados() { return $this->belongsToMany(Desempleado::class, 'desempleado_idiomas', 'IDIdiomaNivel', 'IDDesempleado'); }
}
