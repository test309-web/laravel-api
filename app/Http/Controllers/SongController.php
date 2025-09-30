<?php
// app/Http/Controllers/SongController.php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Http\Resources\SongResource;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/songs",
     *     summary="Get all songs with pagination and filtering",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="album_id",
     *         in="query",
     *         description="Filter by album ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="duration_min",
     *         in="query",
     *         description="Filter by minimum duration (seconds)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="duration_max",
     *         in="query",
     *         description="Filter by maximum duration (seconds)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Song::with(['album.artist']);

        if ($request->has('album_id') && $request->album_id != '') {
            $query->where('album_id', $request->album_id);
        }

        if ($request->has('duration_min') && $request->duration_min != '') {
            $query->where('duration', '>=', $request->duration_min);
        }

        if ($request->has('duration_max') && $request->duration_max != '') {
            $query->where('duration', '<=', $request->duration_max);
        }

        $perPage = $request->get('per_page', 15);
        $songs = $query->paginate($perPage);

        return SongResource::collection($songs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'album_id' => 'required|exists:albums,id'
        ]);

        $song = Song::create($validated);

        return new SongResource($song->load('album.artist'));
    }

    public function show($id)
    {
        $song = Song::with(['album.artist'])->findOrFail($id);
        return new SongResource($song);
    }

    public function update(Request $request, $id)
    {
        $song = Song::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer|min:1',
            'album_id' => 'sometimes|exists:albums,id'
        ]);

        $song->update($validated);

        return new SongResource($song->load('album.artist'));
    }

    public function destroy($id)
    {
        $song = Song::findOrFail($id);
        $song->delete();

        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/songs/search/{query}",
     *     summary="Search songs by title or artist",
     *     tags={"Songs"},
     *     @OA\Parameter(
     *         name="query",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function search($query)
    {
        $songs = Song::with(['album.artist'])
            ->where('title', 'like', '%' . $query . '%')
            ->orWhereHas('album.artist', function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->paginate(15);

        return SongResource::collection($songs);
    }
}