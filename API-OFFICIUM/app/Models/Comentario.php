<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    //
    protected $primaryKey = 'IDComentario';
    public $timestamps = false;
    protected $fillable = ['IDUsuario', 'IDPublicacion', 'Contenido', 'FechaComentario'];
    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function publicacion() { return $this->belongsTo(Publicacion::class, 'IDPublicacion'); }
}
