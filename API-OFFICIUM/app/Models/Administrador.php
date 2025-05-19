<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    //
    protected $primaryKey = 'IDAdministrador';
    public $timestamps = true;
    protected $fillable = ['IDUsuario', 'PermisosEspeciales', 'Activo'];
    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
}
