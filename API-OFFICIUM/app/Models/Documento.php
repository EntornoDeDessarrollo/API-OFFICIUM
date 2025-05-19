<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    //
    protected $primaryKey = 'IDDocumento';
    public $timestamps = true;
    protected $fillable = ['IDUsuario', 'IDPublicacion', 'Tipo', 'NombreArchivo', 'URL', 'FechaSubida','Descripcion'];
    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function publicacion() { return $this->belongsTo(Publicacion::class, 'IDPublicacion'); }
}
