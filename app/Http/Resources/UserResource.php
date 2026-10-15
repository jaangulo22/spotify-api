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
            'id' => (string) $this->id,
            'role_id' => $this->role_id,
            'username' => $this->username,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            
            // 'role' => new RoleResource($this->whenLoaded('role')), 
            // 'artist_profile' => new ArtistResource($this->whenLoaded('artist')),
            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
