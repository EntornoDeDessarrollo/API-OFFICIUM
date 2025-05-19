<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $primaryKey = 'IDCategoria';
    public $timestamps = true;
    protected $fillable = ['Nombre'];
    protected $hidden = ['created_at','updated_at'];

    public function suscriptores() { return $this->belongsToMany(Desempleado::class, 'suscripcion', 'IDCategoria', 'IDDesempleado'); }
    public function ofertas() { return $this->hasMany(OfertaEmpleo::class, 'IDCategoria'); }
}
