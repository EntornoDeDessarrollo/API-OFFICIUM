<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaEmpleo extends Model
{
    //
    protected $primaryKey = 'IDOferta';
    public $timestamps = false;
    protected $fillable = ['IDEmpresario', 'IDCategoria', 'Titulo', 'Descripcion', 'Ubicacion', 'Estado', 'FechaPublicacion'];

    public function empresa() { return $this->belongsTo(Empresa::class, 'IDEmpresa'); }
    public function categoria() { return $this->belongsTo(Categoria::class, 'IDCategoria'); }
    public function aplicaciones() { return $this->hasMany(Aplicacion::class, 'IDOferta'); }
}
