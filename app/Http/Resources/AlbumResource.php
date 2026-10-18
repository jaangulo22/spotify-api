<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'cover_url' => url($this->cover_url),
            'release_date' => $this->release_date,
            'album_type' => $this->album_type,
            
            // cargar al artista relacionado con el album
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            
            
            'genre' => $this->whenLoaded('genre', function () {
                return [
                    'id' => $this->genre->id,
                    'name' => $this->genre->name
                ];
            }),
        ];
    }
}
