<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => (string) $this->id,
            'role_id'    => $this->role_id,
            'username'   => $this->username,
            'email'      => $this->email,
            'avatar_url' => $this->avatar_url,
            // whenLoaded evita el error si la relación no fue cargada (ej: oyentes)
            'artist'     => $this->whenLoaded('artist', fn() =>
                $this->artist ? new ArtistResource($this->artist) : null
            ),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}
