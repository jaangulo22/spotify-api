<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Http\Resources\ArtistResource;
use Illuminate\Validation\Rule;

class ArtistController extends Controller
{
    public function store(Request $request)
    {
        // evitar que un usuario artista o en proceso del mismo, que no pueda hacer mas solicitudes
        if ($request->user()->artist) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una solicitud en proceso o ya eres artista.'
            ], 403);
        }

        $validated = $request->validate([
            'stage_name' => ['required', 'string', 'max:255', Rule::unique('artists', 'stage_name')],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['verification_status'] = 'pending';

        $artist = Artist::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Artista creado exitosamente',
            'data' => new ArtistResource($artist)
        ], 201);
    }
}