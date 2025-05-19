<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    //
    protected $primaryKey = 'IDGrupo';
    public $timestamps = true;
    protected $fillable = ['Nombre', 'Descripcion', 'Privacidad','Foto','Propietario'];
    protected $hidden = ['created_at','updated_at'];

    public function users() { return $this->belongsToMany(User::class, 'usuario_grupos', 'IDGrupo', 'IDUsuario'); }
    public function propietario(){ return $this->belongsTo(User::class, 'Propietario'); }
    public function publicaciones() { return $this->hasMany(Publicacion::class, 'IDGrupo', 'IDGrupo'); }
}
