<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_url',
    ];

    // una playlist pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // una playlist puede tener muchas canciones
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_song')->withTimestamps();
    }
}
