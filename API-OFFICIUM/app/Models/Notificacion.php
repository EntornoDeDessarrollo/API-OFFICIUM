<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    //
    protected $primaryKey = 'IDNotificacion';
    public $timestamps = false;
    protected $fillable = ['IDUsuario', 'Titulo', 'Mensaje', 'Leido', 'FechaNotificacion'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
}
