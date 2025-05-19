<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    //
    protected $primaryKey = 'IDNotificacion';
    public $timestamps = true;
    protected $fillable = ['IDUsuario', 'Titulo', 'Mensaje', 'Leido', 'FechaNotificacion','Ruta'];
    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
}

