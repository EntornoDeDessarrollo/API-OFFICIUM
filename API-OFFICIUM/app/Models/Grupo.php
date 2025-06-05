<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    //
    protected $primaryKey = 'IDGrupo';
    public $timestamps = true;
    protected $fillable = ['Nombre', 'Descripcion', 'Privacidad','Foto','Propietario'];
    protected $hidden = ['updated_at'];

    public function users() { return $this->belongsToMany(User::class, 'usuario_grupos', 'IDGrupo', 'IDUsuario')->withPivot('EstadoMiembro')->withTimestamps(); }
    public function propietario(){ return $this->belongsTo(User::class, 'Propietario'); }
    public function publicaciones() { return $this->hasMany(Publicacion::class, 'IDGrupo', 'IDGrupo'); }
    public function miembrosUnidos()
    {
        return $this->belongsToMany(User::class, 'usuario_grupos', 'IDGrupo', 'IDUsuario')
                    ->wherePivot('EstadoMiembro', 'Unido');
    }
    public function miembrosPendientes()
    {
        return $this->belongsToMany(User::class, 'usuario_grupos', 'IDGrupo', 'IDUsuario')
                    ->wherePivot('EstadoMiembro', 'Pendiente');
    }
}
