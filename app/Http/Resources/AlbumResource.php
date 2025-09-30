<?php
// app/Http/Resources/AlbumResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'artist_id' => $this->artist_id,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'songs_count' => $this->whenLoaded('songs', function() {
                return $this->songs->count();
            }),
            'songs' => SongResource::collection($this->whenLoaded('songs')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}