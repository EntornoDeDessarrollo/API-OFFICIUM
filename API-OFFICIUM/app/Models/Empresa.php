<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    //
    protected $primaryKey = 'IDEmpresa';
    public $timestamps = false;
    protected $fillable = [
        'IDUsuario',
        'NombreEmpresa',
        'CIF',
        'IDSector',
        'Ubicacion',
        'SitioWeb',
        'Foto'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function sector() { return $this->belongsTo(Sector::class, 'IDSector'); }
    public function ofertas() { return $this->hasMany(OfertaEmpleo::class, 'IDEmpresa'); }
}

