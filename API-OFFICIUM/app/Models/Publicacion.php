<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    //
    protected $primaryKey = 'IDPublicacion';
    public $timestamps = true;
    protected $fillable = ['IDUsuario', 'IDGrupo', 'Contenido', 'FechaPublicacion', 'Like','Archivo','TipoArchivo'];
    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function comentarios() { return $this->hasMany(Comentario::class, 'IDPublicacion'); }
    public function documentos() { return $this->hasMany(Documento::class, 'IDPublicacion', 'IDPublicacion'); }
    public function grupo(){ return $this->belongsTo(Grupo::class, 'IDGrupo', 'IDGrupo');}
    public function likes(){ return $this->belongsToMany(User::class, 'likes', 'IDPublicacion', 'IDUsuario'); }
    public function likesCount() { return $this->likes()->count(); }
}
