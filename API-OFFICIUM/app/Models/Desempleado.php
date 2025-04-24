<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desempleado extends Model
{
    //
    protected $primaryKey = 'IDDesempleado';
    public $timestamps = false;
    protected $fillable = ['IDUsuario', 'Nombre', 'Apellido', 'DNI', 'Porfolios', 'Disponibilidad','Foto'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function experiencias() { return $this->hasMany(Experiencia::class, 'IDDesempleado'); }
    public function educaciones() { return $this->hasMany(Educacion::class, 'IDDesempleado'); }
    public function habilidades() { return $this->belongsToMany(Habilidad::class, 'desempleado_habilidades', 'IDDesempleado', 'IDHabilidad'); }
    public function idiomas() { return $this->belongsToMany(IdiomaNivel::class, 'desempleado_idiomas', 'IDDesempleado', 'IDIdiomaNivel'); }
    public function suscripciones() { return $this->belongsToMany(Categoria::class, 'suscripcion', 'IDDesempleado', 'IDCategoria'); }
    public function aplicaciones() { return $this->hasMany(Aplicacion::class, 'IDDesempleado'); }

}
