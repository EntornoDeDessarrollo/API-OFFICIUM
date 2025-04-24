<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $primaryKey = 'IDCategoria';
    public $timestamps = false;
    protected $fillable = ['Nombre'];

    public function suscriptores() { return $this->belongsToMany(Desempleado::class, 'suscripcion', 'IDCategoria', 'IDDesempleado'); }
    public function ofertas() { return $this->hasMany(OfertaEmpleo::class, 'IDCategoria'); }
}
