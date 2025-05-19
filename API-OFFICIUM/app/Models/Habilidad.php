<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habilidad extends Model
{
    //
    protected $primaryKey = 'IDHabilidad';
    public $timestamps = true;
    protected $fillable = ['Tipo', 'Habilidad'];
    protected $hidden = ['created_at','updated_at'];

    public function desempleados() { return $this->belongsToMany(Desempleado::class, 'desempleado_habilidades', 'IDHabilidad', 'IDDesempleado'); }
}
