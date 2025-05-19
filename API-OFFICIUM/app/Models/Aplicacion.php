<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    //
    protected $primaryKey = 'IDAplicacion';
    public $timestamps = true;
    protected $fillable = ['IDDesempleado', 'IDOferta', 'Estado', 'FechaAplicacion'];
    protected $hidden = ['created_at','updated_at'];

    public function desempleado() { return $this->belongsTo(Desempleado::class, 'IDDesempleado'); }
    public function oferta() { return $this->belongsTo(OfertaEmpleo::class, 'IDOferta');}
}
//Abieta, pediente y rechazada
