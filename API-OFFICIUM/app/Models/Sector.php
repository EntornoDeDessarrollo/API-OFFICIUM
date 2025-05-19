<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    //
    protected $primaryKey = 'IDSector';
    public $timestamps = true;
    protected $fillable = ['Nombre'];

    protected $hidden = [
        'updated_at',
        'created_at',

    ];

    public function empresas() { return $this->hasMany(Empresa::class, 'IDSector');}
}
