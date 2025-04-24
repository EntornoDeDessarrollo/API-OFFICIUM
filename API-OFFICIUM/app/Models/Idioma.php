<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    //
    protected $primaryKey = 'IDIdiomas';
    public $timestamps = false;
    protected $fillable = ['Idioma'];

    public function niveles() { return $this->hasMany(IdiomaNivel::class, 'IDIdioma'); }
}
