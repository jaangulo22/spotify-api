<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use App\Http\Resources\AlbumResource;

class AlbumController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|uuid|exists:artists,id',
            'genre_id' => 'required|integer|exists:genres,id',
            'title' => 'required|string|max:255',
            'cover_url' => 'required|string',
            'release_date' => 'nullable|date',
            'album_type' => 'required|string|max:50',
        ]);

        $album = Album::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Álbum creado exitosamente',
            'data' => new AlbumResource($album)
        ], 201);
    }
}
