<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    //
    protected $primaryKey = 'IDAplicacion';
    public $timestamps = false;
    protected $fillable = ['IDDesempleado', 'IDOferta', 'Estado', 'FechaAplicacion'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
    public function oferta() { return $this->belongsTo(OfertaEmpleo::class, 'IDOferta');}
}
