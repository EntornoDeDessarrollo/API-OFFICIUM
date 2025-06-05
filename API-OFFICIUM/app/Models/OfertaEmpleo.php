<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaEmpleo extends Model
{
    //
    protected $primaryKey = 'IDOferta';
    public $timestamps = true;
    protected $fillable = ['IDEmpresa', 'IDCategoria', 'Titulo', 'Descripcion', 'Ubicacion', 'Estado', 'FechaPublicacion'];
    protected $hidden = ['created_at','updated_at'];

    public function empresa() { return $this->belongsTo(Empresa::class, 'IDEmpresa')->with('sector'); }
    public function categoria() { return $this->belongsTo(Categoria::class, 'IDCategoria'); }
    public function aplicaciones() { return $this->hasMany(Aplicacion::class, 'IDOferta'); }
    public function desempleadosAplicados() {
        return $this->belongsToMany(Desempleado::class, 'aplicacions', 'IDOferta', 'IDDesempleado')
                    ->withPivot('Estado', 'FechaAplicacion', 'IDAplicacion')
                    ->withTimestamps();
    }
}
// Abierta, Cerrada, En Proceso
