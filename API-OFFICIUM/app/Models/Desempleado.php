<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Desempleado extends Model
{
    //
    protected $primaryKey = 'IDDesempleado';
    public $timestamps = true;
    protected $fillable = [
        'IDUsuario',
        'Nombre',
        'Apellido',
        'DNI',
        'Porfolios',
        'Disponibilidad',
        'Ubicacion',
        'Foto'
    ];

    protected $hidden = ['created_at','updated_at'];

    public function user() { return $this->belongsTo(User::class, 'IDUsuario'); }
    public function experiencias() { return $this->hasMany(Experiencia::class, 'IDDesempleado'); }
    public function educaciones() { return $this->hasMany(Educacion::class, 'IDDesempleado'); }
    public function habilidades() { return $this->belongsToMany(Habilidad::class, 'desempleado_habilidades', 'IDDesempleado', 'IDHabilidad'); }
    public function idiomas() { return $this->belongsToMany(IdiomaNivel::class, 'desempleado_idiomas', 'IDDesempleado', 'IDIdiomaNivel'); }
    public function suscripciones() { return $this->belongsToMany(Categoria::class, 'suscripcion', 'IDDesempleado', 'IDCategoria'); }
    public function aplicaciones() { return $this->hasMany(Aplicacion::class, 'IDDesempleado'); }
    public function ofertasAplicadas() {
        return $this->belongsToMany(OfertaEmpleo::class, 'aplicacions', 'IDDesempleado', 'IDOferta')
                    ->withPivot('Estado', 'FechaAplicacion', 'IDAplicacion') // Incluye columnas adicional 'pivote'
                    ->withTimestamps();
    }

        protected static function boot()
    {
        parent::boot();

        static::deleting(function (Desempleado $desempleado) {
            if ($desempleado->user) {
                $desempleado->user->delete();
            }
        });
    }

}
