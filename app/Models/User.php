<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'role_id',
        'username',
        'email',
        'avatar_url',
        'password',
    ];

    // etso oculta los datos sencibles de los usuarios
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function favoriteSongs()
    {
        return $this->belongsToMany(Song::class, 'favorites')
            ->withTimestamps();
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
}