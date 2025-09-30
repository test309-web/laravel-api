<?php
// app/Http/Controllers/ArtistController.php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\AlbumResource;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="MusicBox API", version="1.0.0")
 */
class ArtistController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/artists",
     *     summary="Get all artists with pagination and filtering",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Filter by music genre",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
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
        $query = Artist::with(['albums' => function($query) {
            $query->withCount('songs');
        }]);

        if ($request->has('genre') && $request->genre != '') {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        $perPage = $request->get('per_page', 10);
        $artists = $query->paginate($perPage);

        return ArtistResource::collection($artists);
    }

    /**
     * @OA\Post(
     *     path="/api/artists",
     *     summary="Create a new artist",
     *     tags={"Artists"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","genre","country"},
     *             @OA\Property(property="name", type="string", example="Amr Diab"),
     *             @OA\Property(property="genre", type="string", example="Pop"),
     *             @OA\Property(property="country", type="string", example="Egypt")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Artist created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'country' => 'required|string|max:255'
        ]);

        $artist = Artist::create($validated);

        return new ArtistResource($artist);
    }

    /**
     * @OA\Get(
     *     path="/api/artists/{id}",
     *     summary="Get artist details with albums and songs",
     *     tags={"Artists"},
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
    public function show($id)
    {
        $artist = Artist::with(['albums.songs'])->findOrFail($id);
        return new ArtistResource($artist);
    }

    /**
     * @OA\Put(
     *     path="/api/artists/{id}",
     *     summary="Update an artist",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *             @OA\Property(property="genre", type="string", example="Updated Genre"),
     *             @OA\Property(property="country", type="string", example="Updated Country")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $artist = Artist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'genre' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255'
        ]);

        $artist->update($validated);

        return new ArtistResource($artist);
    }

    /**
     * @OA\Delete(
     *     path="/api/artists/{id}",
     *     summary="Delete an artist",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Artist deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $artist = Artist::findOrFail($id);
        $artist->delete();

        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/artists/{id}/albums",
     *     summary="Get artist's albums",
     *     tags={"Artists"},
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
    public function albums($id)
    {
        $artist = Artist::with('albums.songs')->findOrFail($id);
        return AlbumResource::collection($artist->albums);
    }
}

