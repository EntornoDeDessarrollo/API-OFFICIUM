<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    //
    protected $primaryKey = 'IDGrupo';
    public $timestamps = false;
    protected $fillable = ['Nombre', 'Descripcion', 'Privacidad'];

    public function user() { return $this->belongsToMany(User::class, 'usuario_grupos', 'IDGrupo', 'IDUsuario'); }
}
