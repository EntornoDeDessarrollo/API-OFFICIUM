<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'IDUsuario';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'rol',
        'email_verified_at',
        'password',
        'verificationCode',
        'codeExpiresAt'
    ];
    public function empresa() { return $this->hasOne(Empresa::class, 'IDUsuario'); }
    public function desempleado() { return $this->hasOne(Desempleado::class, 'IDUsuario'); }

    public function administrador() { return $this->hasOne(Administrador::class, 'IDUsuario'); }
    public function publicaciones() { return $this->hasMany(Publicacion::class, 'IDUsuario'); }
    public function comentarios() { return $this->hasMany(Comentario::class, 'IDUsuario'); }
    public function documentos() { return $this->hasMany(Documento::class, 'IDUsuario'); }
    public function notificaciones() { return $this->hasMany(Notificacion::class, 'IDUsuario'); }
    public function grupos() { return $this->belongsToMany(Grupo::class, 'usuario_grupos', 'IDUsuario', 'IDGrupo'); }
    public function likedPublicaciones() { return $this->belongsToMany(Publicacion::class, 'likes', 'IDUsuario', 'IDPublicacion'); }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verificationCode',
        'codeExpiresAt'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }
}
