<?php
// app/Models/Song.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'duration', 'album_id'];
    
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
    
    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}