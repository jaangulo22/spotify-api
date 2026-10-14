<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'artist_id',
        'genre_id',
        'title',
        'cover_url',
        'release_date',
        'album_type',
    ];

    // un albun puede tener muchas canciones
    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
