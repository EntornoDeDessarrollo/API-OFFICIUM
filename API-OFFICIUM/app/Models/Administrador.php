<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    //
    protected $primaryKey = 'IDAdministrador';
    public $timestamps = false;
    protected $fillable = ['IDUsuario', 'PermisosEspeciales', 'Activo'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
}
