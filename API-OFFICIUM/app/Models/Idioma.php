<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    //
    protected $primaryKey = 'IDIdiomas';
    public $timestamps = true;
    protected $fillable = ['Idioma'];
    protected $hidden = ['created_at','updated_at'];

    public function niveles() { return $this->hasMany(IdiomaNivel::class, 'IDIdioma'); }
}
