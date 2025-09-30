<?php
// routes/api.php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;

Route::apiResource('artists', ArtistController::class);
Route::apiResource('albums', AlbumController::class);
Route::apiResource('songs', SongController::class);

// Routes supplémentaires
Route::get('artists/{id}/albums', [ArtistController::class, 'albums']);
Route::get('albums/{id}/songs', [AlbumController::class, 'songs']);
Route::get('songs/search/{query}', [SongController::class, 'search']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);