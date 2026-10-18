<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Http\Resources\ArtistResource;

class ArtistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stage_name' => 'required|string|max:255',
            'verification_status' => 'required|string|max:50',
        ]);

        $validated['user_id'] = $request->user()->id;

        $artist = Artist::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Artista creado exitosamente',
            'data' => new ArtistResource($artist)
        ], 201);
    }
}
