<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'stage_name',
        'verification_status',
        'rejection_reason',
    ];
    
    // un artista puede tener muchos albunes
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    // un artista pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
