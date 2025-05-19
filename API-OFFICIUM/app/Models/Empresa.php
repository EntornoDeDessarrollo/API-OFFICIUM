<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Empresa extends Model
{
    //
    protected $primaryKey = 'IDEmpresa';
    public $timestamps = true;
    protected $fillable = [
        'IDUsuario',
        'NombreEmpresa',
        'CIF',
        'IDSector',
        'Ubicacion',
        'SitioWeb',
        'Foto'];
        protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function sector() { return $this->belongsTo(Sector::class, 'IDSector'); }
    public function ofertas() { return $this->hasMany(OfertaEmpleo::class, 'IDEmpresa'); }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Empresa $empresa) {
            if ($empresa->user) {
                $empresa->user->delete();
            }
        });
    }
}

