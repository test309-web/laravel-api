<?php
// app/Http/Resources/ArtistResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'genre' => $this->genre,
            'country' => $this->country,
            'albums_count' => $this->whenLoaded('albums', function() {
                return $this->albums->count();
            }),
            'albums' => AlbumResource::collection($this->whenLoaded('albums')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
