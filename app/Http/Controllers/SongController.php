<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;


class SongController extends Controller
{
    public function index()
    {
        $songs = Song::with('album.artist', 'album.genre')->get();

        return response()->json([
            'success' => true,
            'data' => SongResource::collection($songs)
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'album_id' => 'required|uuid|exists:albums,id',
            'title' => 'required|string|max:255',
            'duration_seconds' => 'required|integer',
            'audio_url' => 'required|string',
            'track_number' => 'nullable|integer',
        ]);

        $song = Song::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Canción creada exitosamente',
            'data' => new SongResource($song)
        ], 201);
    }

    // obtener la info de una cancion especifica
    public function show(Song $song)
    {
        return response()->json([
            'success' => true,
            'data' => new SongResource($song)
        ]);
    }

    public function update(Request $request, Song $song)
    {
        // es dueño de la cancion o admin?
        $ownerId = $song->album->artist->user_id;
        
        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta canción.'
            ], 403);
        }

        $validated = $request->validate([
            'album_id' => 'sometimes|required|uuid|exists:albums,id',
            'title' => 'sometimes|required|string|max:255',
            'duration_seconds' => 'sometimes|required|integer',
            'audio_url' => 'sometimes|required|string',
            'track_number' => 'nullable|integer',
        ]);

        $song->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Canción actualizada correctamente',
            'data' => new SongResource($song)
        ], 200);
    }

    public function destroy(Request $request, Song $song)
    {
        // es dueño de la cancion o admin?
        $ownerId = $song->album->artist->user_id;
        
        if ($request->user()->id !== $ownerId && $request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta canción.'
            ], 403);
        }

        $song->delete();
        return response()->json([
            'success' => true,
            'message' => 'Canción eliminada correctamente'
        ], 200);

    }
    
}
