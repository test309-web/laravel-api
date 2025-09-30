<?php
// app/Http/Resources/SongResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'album_id' => $this->album_id,
            'album' => new AlbumResource($this->whenLoaded('album')),
            'artist' => $this->whenLoaded('album.artist', function() {
                return new ArtistResource($this->album->artist);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
