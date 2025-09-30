<?php
// database/migrations/2024_01_01_000001_create_artists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtistsTable extends Migration
{
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('genre');
            $table->string('country');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('artists');
    }
}