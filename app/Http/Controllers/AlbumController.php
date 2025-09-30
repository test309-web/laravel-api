<?php
// app/Http/Controllers/AlbumController.php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\SongResource;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/albums",
     *     summary="Get all albums with pagination and filtering",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by release year",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="artist_id",
     *         in="query",
     *         description="Filter by artist ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
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
        $query = Album::with(['artist', 'songs']);

        if ($request->has('year') && $request->year != '') {
            $query->where('year', $request->year);
        }

        if ($request->has('artist_id') && $request->artist_id != '') {
            $query->where('artist_id', $request->artist_id);
        }

        $perPage = $request->get('per_page', 10);
        $albums = $query->paginate($perPage);

        return AlbumResource::collection($albums);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'artist_id' => 'required|exists:artists,id'
        ]);

        $album = Album::create($validated);

        return new AlbumResource($album->load('artist'));
    }

    public function show($id)
    {
        $album = Album::with(['artist', 'songs'])->findOrFail($id);
        return new AlbumResource($album);
    }

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|min:1900|max:' . date('Y'),
            'artist_id' => 'sometimes|exists:artists,id'
        ]);

        $album->update($validated);

        return new AlbumResource($album->load('artist'));
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();

        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/albums/{id}/songs",
     *     summary="Get album's songs",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function songs($id)
    {
        $album = Album::with('songs')->findOrFail($id);
        return SongResource::collection($album->songs);
    }
}