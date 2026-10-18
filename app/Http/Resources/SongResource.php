<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'duration_seconds' => $this->duration_seconds,
            'audio_url' => url($this->audio_url),
            'track_number' => $this->track_number,
            'play_count' => $this->play_count,
            'album' => new AlbumResource($this->whenLoaded('album')),
        ];
    }
}
