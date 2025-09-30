<?php
// database/seeders/MusicBoxSeeder.php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Album;
use App\Models\Song;
use Illuminate\Database\Seeder;

class MusicBoxSeeder extends Seeder
{
    public function run()
    {
        // إنشاء فنانين
        $artist1 = Artist::create([
            'name' => 'Amr Diab',
            'genre' => 'Pop',
            'country' => 'Egypt'
        ]);

        $artist2 = Artist::create([
            'name' => 'Nancy Ajram',
            'genre' => 'Pop',
            'country' => 'Lebanon'
        ]);

        $artist3 = Artist::create([
            'name' => 'Mohamed Ramadan',
            'genre' => 'Hip Hop',
            'country' => 'Egypt'
        ]);

        // إنشاء ألبومات
        $album1 = Album::create([
            'title' => 'Wayah',
            'year' => 2020,
            'artist_id' => $artist1->id
        ]);

        $album2 = Album::create([
            'title' => 'Nancy 10',
            'year' => 2022,
            'artist_id' => $artist2->id
        ]);

        // إنشاء أغاني
        Song::create([
            'title' => 'Wayah',
            'duration' => 240, // 4 دقائق
            'album_id' => $album1->id
        ]);

        Song::create([
            'title' => 'El Donia Helwa',
            'duration' => 210, // 3.5 دقيقة
            'album_id' => $album1->id
        ]);

        Song::create([
            'title' => 'Sah Sah',
            'duration' => 180, // 3 دقائق
            'album_id' => $album2->id
        ]);
    }
}